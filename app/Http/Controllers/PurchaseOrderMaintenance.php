<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

use App\XpoMstr;
use App\Imports\POImport;
use App\XpodDet;
use App\Imports\PODetImport;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use File;
use App;
use Log;
use App\Notifications\eventNotification;

use App\Jobs\SendWaJobs;
use App\Jobs\EmailPoApproval;

class PurchaseOrderMaintenance extends Controller
{
    public function loadwsapo(\CheckBudget $service){
        try{
            DB::beginTransaction();

            // Update Last Sync
            DB::table('com_mstr')
            ->update([
                'com_last_sync' => Carbon::now()->toDateTimeString(),
            ]);

            // ============ Loading Data Header
            $wsa = DB::table('wsas')
                    ->first();

            $qxUrl          = $wsa->wsas_url;
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';
            $timeout        = 0;

            $domain         = $wsa->wsas_domain;

            $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                            <Body>
                            <supp_po_mstr xmlns="'.$wsa->wsas_path.'">
                            <inpdomain>'.$domain.'</inpdomain>
                            </supp_po_mstr>
                            </Body>
                            </Envelope>';

            $curlOptions = array(CURLOPT_URL => $qxUrl,
                                    CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                    CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                    CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                    CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                    CURLOPT_POST => true,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_SSL_VERIFYHOST => false);
                            
            $getInfo = '';
            $httpCode = 0;
            $curlErrno = 0;
            $curlError = '';
            $qdocResponse = '';

            $curl = curl_init();
            if ($curl) {
                curl_setopt_array($curl, $curlOptions);
                $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                $curlErrno    = curl_errno($curl);
                $curlError    = curl_error($curl);
                $first        = true;
            
                foreach (curl_getinfo($curl) as $key=>$value) {
                    if (gettype($value) != 'array') {
                        if (! $first) $getInfo .= ", ";
                        $getInfo = $getInfo . $key . '=>' . $value;
                        $first = false;
                        if ($key == 'http_code') $httpCode = $value;
                    }
                }
                curl_close($curl);
            }
            
            $xmlResp = simplexml_load_string($qdocResponse);       
        
            $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path); 

            $dataloop1    = $xmlResp->xpath('//ns1:tempRow');
            $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];
            
            if($qdocResult == 'true'){

                foreach($dataloop1 as $dataloop){
                    // cek apakah ada data po di web
                    $datapo = DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$dataloop->t_nbr)
                                    ->first();
                    
                    if($datapo){
                        // ada data df
                        if($dataloop->status == 'delete'){
                            $datadet = DB::table('xpod_dets')
                                                        ->join('xpo_mstrs','xpod_nbr','=','xpo_nbr')
                                                        ->where('xpod_nbr','=',$dataloop->t_nbr)
                                                        ->get();
                
                            foreach($datadet as $datadet){
                                // Create History 
                                $createhist = DB::table('xpo_hist')
                                    ->insert([
                                            'xpo_domain' => $datadet->xpod_domain,
                                            'xpo_nbr' => $datadet->xpod_nbr,
                                            'xpo_line' => $datadet->xpod_line,
                                            'xpo_part' => $datadet->xpod_part,
                                            'xpo_desc' => $datadet->xpod_desc,
                                            'xpo_um' => $datadet->xpod_um,
                                            'xpo_qty_ord' => $datadet->xpod_qty_ord,
                                            'xpo_qty_rcvd' => $datadet->xpod_qty_rcvd,
                                            'xpo_qty_open' => $datadet->xpod_qty_open,
                                            'xpo_qty_prom' => $datadet->xpod_qty_prom,
                                            'xpo_price' => $datadet->xpod_price,
                                            'xpo_loc' => $datadet->xpod_loc,
                                            'xpo_lot' => $datadet->xpod_lot,
                                            'xpo_due_date' => $datadet->xpod_due_date,
                                            'xpo_vend' => $datadet->xpo_vend,
                                            'xpo_status' => 'Closed',
                                            'created_at' => Carbon::now('')->toDateTimeString(),
                                            'updated_at' => Carbon::now('')->toDateTimeString(),
                                    ]);   
                            }                             

                            DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$dataloop->t_nbr)
                                    ->delete();
                            DB::table('xpod_dets')
                                    ->where('xpod_nbr','=',$dataloop->t_nbr)
                                    ->delete();
                            
                            // Tutup SJ yang ada PO itu
                            DB::table('xsj_mstr')
                                    ->where('xsj_po_nbr','=',$dataloop->t_nbr)
                                    ->update([
                                            'xsj_status' => 'Closed',
                                    ]);
                        }elseif($datapo->xpo_total != str_replace(',', '.', $dataloop->t_hitung)){
                            // ada update di detail
                            
                            $revision = (int)$datapo->xpo_rev;
                
                            $newrev = $revision + 1;
                            DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$dataloop->t_nbr)
                                    ->update([
                                        'xpo_total' => str_replace(',', '.', $dataloop->t_hitung),
                                        'xpo_vend'=>$dataloop->t_vend,
                                        'xpo_ship'=>$dataloop->t_ship,      
                                        'xpo_curr'=>$dataloop->t_curr,
                                        'xpo_due_date'=>$dataloop->t_lvt_due,  
                                        'xpo_rev' => $newrev,
                                    ]);
                             
                            $listpo = $service->listPO()->where('ponbr',$dataloop->t_nbr);
                            
                            if($listpo->count() > 0){
                                // PO Melebihi Budget
                                DB::table('xpo_mstrs')
                                    ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                    ->update([
                                        'xpo_app_flg' => '1',
                                        'xpo_status' => 'UnConfirm'
                                    ]);

                                //pindain data trans ke hist klo blom selesai di trans
                                $listtrans = DB::table('xpo_app_trans')
                                        ->where('xpo_app_nbr','=',$dataloop->t_nbr)
                                        ->where('xpo_app_status','!=','0')
                                        ->get();

                                foreach($listtrans as $listtrans){
                                    DB::table('xpo_app_hist')
                                        ->insert([
                                            'xpo_app_nbr' => $dataloop->t_nbr,
                                            'xpo_app_approver' => $listtrans->xpo_app_approver,
                                            'xpo_app_order' => $listtrans->xpo_app_order, // urutan Approval
                                            'xpo_app_status' => $listtrans->xpo_app_status, // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $listtrans->xpo_app_alt_approver
                                        ]);  
                                }

                                // Delete data di xpo_app_trans
                                DB::table('xpo_app_trans')
                                        ->where('xpo_app_nbr','=',$dataloop->t_nbr)
                                        ->delete();
                                
                                // buat subquery alt approver
                                $altapp = DB::table('users')
                                        ->groupBy('users.id');

                                $mainapp =  DB::table('approver_budget')
                                                ->join('users','approver_budget.approver_budget','=','users.id')
                                                ->joinSub($altapp, 'altapp', function($join){
                                                    $join->on('approver_budget.alt_approver_budget','=','altapp.id');
                                                })
                                                ->select('approver_budget.approver_budget',
                                                        'users.email',
                                                        'approver_budget.alt_approver_budget',
                                                        'altapp.email as emailalt')
                                                ->get();
                                // Masukin Table Approval & Email
                                if($mainapp->count() > 0){
                                    // Butuh Approval Specific
                                    $i = 0;
                                    foreach($mainapp as $mainapp1){
                                        $i++;
                
                                        $result[$i] = [
                                            'xpo_app_nbr' => $dataloop->t_nbr,
                                            'xpo_app_approver' => $mainapp1->approver_budget,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $mainapp1->alt_approver_budget
                                        ];
                                        // Pindah Table 24072020
                                        DB::table('xpo_app_trans')->insert($result[$i]);
                
                                    } 

                                    // Kirim Email
                                    $email2app = $mainapp[0]->email.','.$mainapp[0]->emailalt;
                    
                                    $array_email = explode(',', $email2app);

                                    $com = DB::table('com_mstr')
                                                ->first();

                                    $sendmail = (new EmailPoApproval(
                                        'There is an update on an old PO that exceeds Budget :',
                                        (string)$dataloop->t_nbr,
                                        (string)$dataloop->t_lvt_ord,
                                        (string)$dataloop->t_lvt_due, 
                                        number_format((int)$dataloop->t_hitung,2 ),
                                        'PO Exceeds Budget. Please check.',
                                        $array_email,
                                        $com->com_name,
                                        $com->com_email))
                                        ->delay(Carbon::now()->addSeconds(3));
                                    dispatch($sendmail);

                                    $user = App\User::where('id','=', $mainapp[0]->approver_budget)->first(); // user siapa yang terima notif (lewat id)
                                    $useralt = App\User::where('id','=', $mainapp[0]->alt_approver_budget)->first();
                                    $details = [
                                                'body' => 'There is an update on an old PO that exceeds Budget',
                                                'url' => 'poappbrowse',
                                                'nbr' => (string)$dataloop->t_nbr,
                                                'note' => 'Approval is needed, Please check'
    
                                    ]; // isi data yang dioper
                                
                                
                                    $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                    $useralt->notify(new \App\Notifications\eventNotification($details));
                                }
                            }else{
                                // PO Tidak Melebihi Budget
                                $alertpo = DB::Table('xalert_mstrs')
                                    ->where('xalert_mstrs.xalert_supp','=',$dataloop->t_vend)
                                    ->first();

                                $reapp = DB::table('xpo_control')
                                    ->where('supp_code','=',$dataloop->t_vend)
                                    ->first();

                                // buat subquery alt approver
                                $altapp = DB::table('users')
                                    ->groupBy('users.id');

                                if($alertpo->xalert_po_app == 'Yes' && $reapp->reapprove == 'Yes'){
                                    // Butuh Approval --> cek General / Specific
                                    DB::table('xpo_mstrs')
                                        ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                        ->update([
                                            'xpo_app_flg' => '1',
                                            'xpo_status' => 'UnConfirm'
                                        ]);

                                    //pindain data trans ke hist klo blom selesai di trans
                                    $listtrans = DB::table('xpo_app_trans')
                                            ->where('xpo_app_nbr','=',$dataloop->t_nbr)
                                            ->where('xpo_app_status','!=','0')
                                            ->get();
            
                                    foreach($listtrans as $listtrans){
                                        DB::table('xpo_app_hist')
                                            ->insert([
                                                'xpo_app_nbr' => $dataloop->t_nbr,
                                                'xpo_app_approver' => $listtrans->xpo_app_approver,
                                                'xpo_app_order' => $listtrans->xpo_app_order, // urutan Approval
                                                'xpo_app_status' => $listtrans->xpo_app_status, // 0 Waiting , 1 Approved , 2 Reject
                                                'xpo_app_alt_approver' => $listtrans->xpo_app_alt_approver
                                            ]);  
                                    }
            
                                    // Delete data di xpo_app_trans
                                    DB::table('xpo_app_trans')
                                            ->where('xpo_app_nbr','=',$dataloop->t_nbr)
                                            ->delete();

                                    $mainapp =  DB::table('xpo_mstrs')
                                                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                                    ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                                    ->joinSub($altapp, 'altapp', function($join){
                                                        $join->on('xpo_control.xpo_alt_app','=','altapp.id');
                                                    })
                                                    ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                                    ->whereRaw(''.str_replace(',', '.', $dataloop->t_hitung).' between min_amt and max_amt')
                                                    ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                            ,'xpo_control.xpo_approver','users.email','xpo_control.xpo_alt_app','altapp.email as emailalt')
                                                    ->orderBy('min_amt','asc')
                                                    ->get();

                                    // Masukin Table Approval & Email
                                    if($mainapp->count() > 0){
                                        // Butuh Approval Specific
                                        $i = 0;
                                        foreach($mainapp as $mainapp1){
                                            $i++;
                    
                                            $result[$i] = [
                                                'xpo_app_nbr' => $mainapp1->xpo_nbr,
                                                'xpo_app_approver' => $mainapp1->xpo_approver,
                                                'xpo_app_order' => $i, // urutan Approval
                                                'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                                'xpo_app_alt_approver' => $mainapp1->xpo_alt_app
                                            ];
                                            // Pindah Table 24072020
                                            DB::table('xpo_app_trans')->insert($result[$i]);
                    
                                        } 

                                        // Kirim Email
                                        $email2app = $mainapp[0]->email.','.$mainapp[0]->emailalt;
                        
                                        $array_email = explode(',', $email2app);

                                        $com = DB::table('com_mstr')
                                                    ->first();

                                        $sendmail = (new EmailPoApproval(
                                            'There is an update on an old Purchase Order :',
                                            (string)$dataloop->t_nbr,
                                            (string)$dataloop->t_lvt_ord,
                                            (string)$dataloop->t_lvt_due, 
                                            number_format((int)$dataloop->t_hitung,2 ),
                                            'Please check.',
                                            $array_email,
                                            $com->com_name,
                                            $com->com_email))
                                            ->delay(Carbon::now()->addSeconds(3));
                                        dispatch($sendmail);

                                        $user = App\User::where('id','=', $mainapp[0]->xpo_approver)->first(); // user siapa yang terima notif (lewat id)
                                        $useralt = App\User::where('id','=', $mainapp[0]->xpo_alt_app)->first();
                                        $details = [
                                                    'body' => 'There is an update on an old Purchase Order',
                                                    'url' => 'poappbrowse',
                                                    'nbr' => (string)$dataloop->t_nbr,
                                                    'note' => 'Approval is needed, Please check'
        
                                        ]; // isi data yang dioper
                                    
                                    
                                        $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                        $useralt->notify(new \App\Notifications\eventNotification($details));
                                    }else{
                                        // Approver General
                                        $general = DB::table('xpo_control')
                                                            ->join('users','users.id','=','xpo_control.xpo_approver')
                                                            ->joinSub($altapp, 'altapp', function($join){
                                                                $join->on('xpo_control.xpo_alt_app','=','altapp.id');
                                                            })
                                                            ->where('supp_code','=','General')
                                                            ->whereRaw(''.str_replace(',', '.', $dataloop->t_hitung).' >= min_amt and '.str_replace(',', '.', $dataloop->t_hitung).' < max_amt')
                                                            ->selectRaw('*, altapp.email as emailalt, users.email as "emailmain"')
                                                            ->orderBy('min_amt','ASC')
                                                            ->get();
                                        $i = 0;
                                        
                                        foreach($general as $general1){
                                            $i++;
                    
                                            $result[$i] = [
                                                'xpo_app_nbr' => $dataloop->t_nbr,
                                                'xpo_app_approver' => $general1->xpo_approver,
                                                'xpo_app_order' => $i, // urutan Approval
                                                'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                                'xpo_app_alt_approver' => $general1->xpo_alt_app
                                            ];
                                            // Pindah Table 24072020
                                            DB::table('xpo_app_trans')->insert($result[$i]);
                                        }

                                        // Kirim Email
                                        $email2app = $general[0]->emailmain.','.$general[0]->emailalt;
                        
                                        $array_email = explode(',', $email2app);

                                        $com = DB::table('com_mstr')
                                                    ->first();

                                        $sendmail = (new EmailPoApproval(
                                            'There is an update on an old Purchase Order :',
                                            (string)$dataloop->t_nbr,
                                            (string)$dataloop->t_lvt_ord,
                                            (string)$dataloop->t_lvt_due, 
                                            number_format((int)$dataloop->t_hitung,2 ),
                                            'Please check.',
                                            $array_email,
                                            $com->com_name,
                                            $com->com_email))
                                            ->delay(Carbon::now()->addSeconds(3));
                                        dispatch($sendmail);
                                        
                                        $user = App\User::where('id','=', $general[0]->xpo_approver)->first(); // user siapa yang terima notif (lewat id)
                                        $useralt = App\User::where('id','=', $general[0]->xpo_alt_app)->first();
                                        $details = [
                                                    'body' => 'There is an update on an old Purchase Order',
                                                    'url' => 'poappbrowse',
                                                    'nbr' => (string)$dataloop->t_nbr,
                                                    'note' => 'Approval is needed, Please check'
        
                                        ]; // isi data yang dioper
                                    
                                    
                                        $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                        $useralt->notify(new \App\Notifications\eventNotification($details));
                                    }
                                }
                            }
                        }
                    }else{
                        // tidak ada data
                        if($dataloop->status != 'delete'){
                            // Insert Data -> Cek apakah butuh Approval
                            $data1 = array(
                                'xpo_domain'=>$dataloop->t_domain,
                                'xpo_nbr'=>$dataloop->t_nbr,
                                'xpo_ord_date'=>$dataloop->t_lvt_ord,
                                'xpo_vend'=>$dataloop->t_vend,
                                'xpo_ship'=>$dataloop->t_ship,      
                                'xpo_curr'=>$dataloop->t_curr,
                                'xpo_due_date'=>$dataloop->t_lvt_due,    
                                'created_at'=> Carbon::now()->toDateTimeString(),    
                                'updated_at'=> Carbon::now()->toDateTimeString(),
                                'xpo_last_conf'=> null,
                                'xpo_total_conf'=> '0',
                                'xpo_total'=>str_replace(',', '.', $dataloop->t_hitung),
                                'xpo_crt_date'=> Carbon::now()->toDateString(),
                                'xpo_status'=>'Approved',    // UnConfirm,Approved,Closed
                                'xpo_ppn' => str_replace(',', '.', $dataloop->t_ppn),     
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString()
                            );
                            DB::table('xpo_mstrs')->insert($data1);

                            $listpo = $service->listPO()->where('ponbr',$dataloop->t_nbr);
                            // dd($listpo);
                            if($listpo->count() > 0){
                                // PO Melebihi Budget
                                DB::table('xpo_mstrs')
                                    ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                    ->update([
                                        'xpo_app_flg' => '1',
                                        'xpo_status' => 'UnConfirm'
                                    ]);
                                
                                // buat subquery alt approver
                                $altapp = DB::table('users')
                                        ->groupBy('users.id');

                                $mainapp =  DB::table('approver_budget')
                                                ->join('users','approver_budget.approver_budget','=','users.id')
                                                ->joinSub($altapp, 'altapp', function($join){
                                                    $join->on('approver_budget.alt_approver_budget','=','altapp.id');
                                                })
                                                ->select('approver_budget.approver_budget',
                                                        'users.email',
                                                        'approver_budget.alt_approver_budget',
                                                        'altapp.email as emailalt')
                                                ->get();
                                // Masukin Table Approval & Email
                                if($mainapp->count() > 0){
                                    // Butuh Approval Specific
                                    $i = 0;
                                    foreach($mainapp as $mainapp1){
                                        $i++;
                
                                        $result[$i] = [
                                            'xpo_app_nbr' => $dataloop->t_nbr,
                                            'xpo_app_approver' => $mainapp1->approver_budget,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $mainapp1->alt_approver_budget
                                        ];
                                        // Pindah Table 24072020
                                        DB::table('xpo_app_trans')->insert($result[$i]);
                
                                    } 

                                    // Kirim Email
                                    $email2app = $mainapp[0]->email.','.$mainapp[0]->emailalt;
                    
                                    $array_email = explode(',', $email2app);

                                    $com = DB::table('com_mstr')
                                                ->first();

                                    $sendmail = (new EmailPoApproval(
                                        'There is a new Purchase Order :',
                                        (string)$dataloop->t_nbr,
                                        (string)$dataloop->t_lvt_ord,
                                        (string)$dataloop->t_lvt_due, 
                                        number_format((int)$dataloop->t_hitung,2 ),
                                        'PO Exceeds Budget. Please check.',
                                        $array_email,
                                        $com->com_name,
                                        $com->com_email))
                                        ->delay(Carbon::now()->addSeconds(3));
                                    dispatch($sendmail);

                                    $user = App\User::where('id','=', $mainapp[0]->approver_budget)->first(); // user siapa yang terima notif (lewat id)
                                    $useralt = App\User::where('id','=', $mainapp[0]->alt_approver_budget)->first();
                                    $details = [
                                                'body' => 'There is new PO that exceeds Budget that you need to approve',
                                                'url' => 'poappbrowse',
                                                'nbr' => (string)$dataloop->t_nbr,
                                                'note' => 'Approval is needed, Please check'
    
                                    ]; // isi data yang dioper
                                
                                
                                    $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                    $useralt->notify(new \App\Notifications\eventNotification($details));
                                }
                            }else{
                                $alertpo = DB::Table('xalert_mstrs')
                                    ->where('xalert_mstrs.xalert_supp','=',$dataloop->t_vend)
                                    ->first();

                                // buat subquery alt approver
                                $altapp = DB::table('users')
                                    ->groupBy('users.id');

                                if($alertpo->xalert_po_app == 'Yes'){
                                    // Butuh Approval --> cek General / Specific
                                    DB::table('xpo_mstrs')
                                        ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                        ->update([
                                            'xpo_app_flg' => '1',
                                            'xpo_status' => 'UnConfirm'
                                        ]);
                                    

                                    $mainapp =  DB::table('xpo_control')
                                                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_control.supp_code')
                                                    // ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                                    ->joinSub($altapp, 'altapp', function($join){
                                                        $join->on('xpo_control.xpo_alt_app','=','altapp.id');
                                                    })
                                                    // ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                                    ->where('xpo_control.supp_code','=',$dataloop->t_vend)
                                                    ->whereRaw(''.str_replace(',', '.', $dataloop->t_hitung).' between min_amt and max_amt')
                                                    ->select('xpo_control.min_amt','xpo_control.max_amt'
                                                            ,'xpo_control.xpo_approver','users.email','xpo_control.xpo_alt_app','altapp.email as emailalt')
                                                    ->orderBy('min_amt','asc')
                                                    ->get();
                                    // dd($mainapp,$dataloop->t_hitung);
                                    // Masukin Table Approval & Email
                                    if($mainapp->count() > 0){
                                        
                                        // Butuh Approval Specific
                                        $i = 0;
                                        foreach($mainapp as $mainapp1){
                                            $i++;
                    
                                            $result[$i] = [
                                                'xpo_app_nbr' => $dataloop->t_nbr,
                                                'xpo_app_approver' => $mainapp1->xpo_approver,
                                                'xpo_app_order' => $i, // urutan Approval
                                                'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                                'xpo_app_alt_approver' => $mainapp1->xpo_alt_app
                                            ];
                                            // Pindah Table 24072020
                                            DB::table('xpo_app_trans')->insert($result[$i]);
                    
                                        } 

                                        // Kirim Email
                                        $email2app = $mainapp[0]->email.','.$mainapp[0]->emailalt;
                        
                                        $array_email = explode(',', $email2app);

                                        $com = DB::table('com_mstr')
                                                    ->first();

                                        $sendmail = (new EmailPoApproval(
                                            'There is a new Purchase Order :',
                                            (string)$dataloop->t_nbr,
                                            (string)$dataloop->t_lvt_ord,
                                            (string)$dataloop->t_lvt_due, 
                                            number_format((int)$dataloop->t_hitung,2 ),
                                            'Please check.',
                                            $array_email,
                                            $com->com_name,
                                            $com->com_email))
                                            ->delay(Carbon::now()->addSeconds(3));
                                        dispatch($sendmail);

                                        $user = App\User::where('id','=', $mainapp[0]->xpo_approver)->first(); // user siapa yang terima notif (lewat id)
                                        $useralt = App\User::where('id','=', $mainapp[0]->xpo_alt_app)->first();
                                        $details = [
                                                    'body' => 'There is new PO that you need to approve',
                                                    'url' => 'poappbrowse',
                                                    'nbr' => (string)$dataloop->t_nbr,
                                                    'note' => 'Approval is needed, Please check'
        
                                        ]; // isi data yang dioper
                                    
                                    
                                        $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                        $useralt->notify(new \App\Notifications\eventNotification($details));
                                    }else{
                                        // Approver General
                                        $general = DB::table('xpo_control')
                                                            ->join('users','users.id','=','xpo_control.xpo_approver')
                                                            ->joinSub($altapp, 'altapp', function($join){
                                                                $join->on('xpo_control.xpo_alt_app','=','altapp.id');
                                                            })
                                                            ->where('supp_code','=','General')
                                                            ->whereRaw(''.str_replace(',', '.', $dataloop->t_hitung).' >= min_amt and '.str_replace(',', '.', $dataloop->t_hitung).' < max_amt')
                                                            ->selectRaw('*, altapp.email as emailalt, users.email as emailmain')
                                                            ->orderBy('min_amt','ASC')
                                                            ->get();
                                        $i = 0;
                                        
                                        if($general->count() > 0){
                                            foreach($general as $general1){
                                                $i++;
                        
                                                $result[$i] = [
                                                    'xpo_app_nbr' => $dataloop->t_nbr,
                                                    'xpo_app_approver' => $general1->xpo_approver,
                                                    'xpo_app_order' => $i, // urutan Approval
                                                    'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                                    'xpo_app_alt_approver' => $general1->xpo_alt_app
                                                ];
                                                // Pindah Table 24072020
                                                DB::table('xpo_app_trans')->insert($result[$i]);
                                            }
                                            // Kirim Email
                                            $email2app = $general[0]->emailmain.','.$general[0]->emailalt;
                            
                                            $array_email = explode(',', $email2app);
        
                                            $com = DB::table('com_mstr')
                                                        ->first();
        
                                            $sendmail = (new EmailPoApproval(
                                                'There is a new Purchase Order :',
                                                (string)$dataloop->t_nbr,
                                                (string)$dataloop->t_lvt_ord,
                                                (string)$dataloop->t_lvt_due, 
                                                number_format((int)$dataloop->t_hitung,2 ),
                                                'Please check.',
                                                $array_email,
                                                $com->com_name,
                                                $com->com_email))
                                                ->delay(Carbon::now()->addSeconds(3));
                                            dispatch($sendmail);

                                            $user = App\User::where('id','=', $general[0]->xpo_approver)->first(); // user siapa yang terima notif (lewat id)
                                            $useralt = App\User::where('id','=', $general[0]->xpo_alt_app)->first();
                                            $details = [
                                                        'body' => 'There is new PO that you need to approve',
                                                        'url' => 'poappbrowse',
                                                        'nbr' => (string)$dataloop->t_nbr,
                                                        'note' => 'Approval is needed, Please check'
            
                                            ]; // isi data yang dioper
                                        
                                        
                                            $user->notify(new \App\Notifications\eventNotification($details)); // syntax laravel
                                            $useralt->notify(new \App\Notifications\eventNotification($details));
                                        }
                                        
                                    }
                                }

                                // dd('stop');
                            }
                        }
                        
                    }

                }
            }



            // ============ Import Data Detail
            $wsa = DB::table('wsas')
                    ->first();

            $qxUrl          = $wsa->wsas_url;
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';
            $timeout        = 0;

            $domain         = $wsa->wsas_domain;

            $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                            <Body>
                            <supp_pod_det xmlns="'.$wsa->wsas_path.'">
                            <inpdomain>'.$domain.'</inpdomain>
                            </supp_pod_det>
                            </Body>
                            </Envelope>';

            $curlOptions = array(CURLOPT_URL => $qxUrl,
                                    CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                    CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                    CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                    CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                    CURLOPT_POST => true,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_SSL_VERIFYHOST => false);
                            
            $getInfo = '';
            $httpCode = 0;
            $curlErrno = 0;
            $curlError = '';
            $qdocResponse = '';

            $curl = curl_init();
            if ($curl) {
                curl_setopt_array($curl, $curlOptions);
                $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                $curlErrno    = curl_errno($curl);
                $curlError    = curl_error($curl);
                $first        = true;
            
                foreach (curl_getinfo($curl) as $key=>$value) {
                    if (gettype($value) != 'array') {
                        if (! $first) $getInfo .= ", ";
                        $getInfo = $getInfo . $key . '=>' . $value;
                        $first = false;
                        if ($key == 'http_code') $httpCode = $value;
                    }
                }
                curl_close($curl);
            }
            
            $xmlResp = simplexml_load_string($qdocResponse);       
        
            $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path); 

            $dataloopdet    = $xmlResp->xpath('//ns1:tempRow');
            $qdocResult     = (string) $xmlResp->xpath('//ns1:outOK')[0];

            if($qdocResult == 'true'){
                foreach($dataloopdet as $dataloop){
                    if($dataloop->t_lvt_ord == null ){
                        $newdate = '2020-01-01';
                    }else{
                        //$date = date_create_from_format('d/m/Y', $importData[10]);
                        //$newdate = $date->format('Y-m-d');
                        $newdate = $dataloop->t_lvt_ord;
                    }
    
                    //dd($newdate);
                    if($dataloop->t_stats == 'delete'){
    
                        // Delete Row, Masukin Hist
                        $datadetail = DB::table('xpo_mstrs')
                                        ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                                        ->where('xpod_nbr','=',$dataloop->t_nbr)
                                        ->where('xpod_line','=',$dataloop->t_line)
                                        ->get();
    
                        if($datadetail){
                            // ada data
                            foreach($datadetail as $datadetail){
                            DB::table('xpo_hist')
                                ->insert([
                                        'xpo_domain' => $datadetail->xpod_domain,
                                        'xpo_nbr' => $datadetail->xpod_nbr,
                                        'xpo_line' => $datadetail->xpod_line,
                                        'xpo_part' => $datadetail->xpod_part,
                                        'xpo_desc' => $datadetail->xpod_desc,
                                        'xpo_um' => $datadetail->xpod_um,
                                        'xpo_qty_ord' => $datadetail->xpod_qty_ord,
                                        'xpo_qty_rcvd' => $datadetail->xpod_qty_rcvd,
                                        'xpo_qty_open' => $datadetail->xpod_qty_open,
                                        'xpo_qty_prom' => $datadetail->xpod_qty_prom,
                                        'xpo_price' => $datadetail->xpod_price,
                                        'xpo_loc' => $datadetail->xpod_loc,
                                        'xpo_lot' => $datadetail->xpod_lot,
                                        'xpo_due_date' => $datadetail->xpod_due_date,
                                        'xpo_vend' => $datadetail->xpo_vend,
                                        'xpo_status' => 'Closed',
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(), 
                                ]);
    
                            }
    
                            DB::table('xpod_dets')
                                        ->where('xpod_nbr','=',$dataloop->t_nbr)
                                        ->where('xpod_line','=',$dataloop->t_line)
                                        ->delete();
    
                            // Tutup SJ yang ada PO & Line itu
                            DB::table('xsj_mstr')
                                    ->where('xsj_po_nbr','=',$dataloop->t_nbr)
                                    ->where('xsj_line','=',$dataloop->t_line)
                                    ->update([
                                            'xsj_status' => 'Closed',
                                    ]);
                        }
                    }else{
                        DB::table('xpod_dets')->updateOrInsert(
                            ['xpod_domain' => $dataloop->t_domain, 'xpod_nbr' => $dataloop->t_nbr, 'xpod_line' => $dataloop->t_line ],
                            ['xpod_part' => $dataloop->t_part, 
                             'xpod_desc' => $dataloop->t_desc,
                             'xpod_um' => $dataloop->t_um,
                             'xpod_qty_ord' => $dataloop->t_qty_ord,
                             'xpod_qty_rcvd' => '0',
                             'xpod_qty_open' => $dataloop->t_qty_ord,
                             'xpod_qty_prom' => $dataloop->t_qty_ord,
                             'xpod_price' => $dataloop->t_price,
                             'xpod_loc' => $dataloop->t_loc,
                             'xpod_lot' => $dataloop->t_lot,
                             'xpod_due_date' => $dataloop->t_lvt_due,
                             'xpod_date' => $newdate,
                             'created_at' => Carbon::now()->toDateTimeString(),
                             'updated_at' =>  Carbon::now()->toDateTimeString()
                         ]);
    
    
                        $suppname = DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$dataloop->t_nbr)
                                    ->get();
                        
                        foreach($suppname as $suppname){
                            $vendor = $suppname->xpo_vend;
                            $status = $suppname->xpo_status;
                            if(is_null($newdate)){
                                $newdate = '2020-01-1';
                            }
    
                            // Create History 
                            $cekdata = DB::table('xpo_hist')
                                        ->where('xpo_hist.xpo_nbr','=',$dataloop->t_nbr)
                                        ->where('xpo_hist.xpo_line','=',$dataloop->t_line)
                                        ->where('xpo_hist.xpo_part','=',$dataloop->t_part)
                                        ->where('xpo_hist.xpo_qty_ord','=',$dataloop->t_qty_ord)
                                        ->where('xpo_hist.xpo_price','=',$dataloop->t_price)
                                        ->first();
    
                            if(is_null($cekdata)){
                                // tidak ada data masukin klo ada lewat
                                DB::table('xpo_hist')
                                    ->insert([
                                            'xpo_domain' => $dataloop->t_domain,
                                            'xpo_nbr' => $dataloop->t_nbr,
                                            'xpo_line' => $dataloop->t_line,
                                            'xpo_part' => $dataloop->t_part,
                                            'xpo_desc' => $dataloop->t_desc,
                                            'xpo_um' => $dataloop->t_um,
                                            'xpo_qty_ord' => $dataloop->t_qty_ord,
                                            'xpo_qty_rcvd' => '0',
                                            'xpo_qty_open' => $dataloop->t_qty_ord,
                                            'xpo_qty_prom' => $dataloop->t_qty_ord,
                                            'xpo_price' => $dataloop->t_price,
                                            'xpo_loc' => $dataloop->t_loc,
                                            'xpo_lot' => $dataloop->t_lot,
                                            'xpo_due_date' => $dataloop->t_lvt_due,
                                            'xpo_vend' => $vendor,
                                            'xpo_status' => $status,
                                            'created_at' => Carbon::now()->toDateTimeString(),
                                            'updated_at' => Carbon::now()->toDateTimeString(),
                                    ]);
                            }
                        }
                    }
                }
            
            }

            DB::commit();

        }catch(exception $e){
            DB::rollback();
            log::channel('errorpo')->info('Load PO Failed');
        }
    }

    public function viewpo(Request $req){

        $totalpo = DB::table('xpo_mstrs')
                    ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                    ->count();

        $unapppo = DB::table('xpo_mstrs')
                    ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                    ->where('xpo_status','=','unconfirm')
                    ->count();

        $shippo = DB::table('xpo_mstrs')
                    ->selectRaw('count(DISTINCT xpo_nbr)')
                    ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                    ->join('xsj_mstr','xsj_mstr.xsj_po_nbr','=','xpo_mstrs.xpo_nbr')
                    ->distinct('xpo_nbr')
                    ->count('xpo_nbr');  

        $updatedat = DB::table('com_mstr')
                    ->first();

        if($updatedat == null){
            $updatedat = 0;
        }

        if($req->ajax()){
            if(Session::get('supp_code') != null){
                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                ->whereRaw('(xpo_status = "Approved" or xpo_status = "Confirm")')
                ->orderBy('xpo_mstrs.created_at','DESC')
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10); 

                return view('/po/tablepo',['users'=>$users,'totalpo'=>$totalpo,'unapppo'=>$unapppo,'shippo'=>$shippo,'updatedat'=>$updatedat]);

            }else{
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                ->orderBy('xpo_mstrs.created_at','DESC')
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10); 

                return view('/po/tablepo',['users'=>$users,'totalpo'=>$totalpo,'unapppo'=>$unapppo,'shippo'=>$shippo,'updatedat'=>$updatedat]);
            }

        }else{
            if(Session::get('supp_code') != null)
            {
                $totalpo = DB::table('xpo_mstrs')
                            ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->count();

                $unapppo = DB::table('xpo_mstrs')
                            ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                            ->where('xpo_status','=','unconfirm')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->count();

                $shippo = DB::table('xpo_mstrs')
                            ->selectRaw('count(DISTINCT xpo_nbr)')
                            ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                            ->join('xsj_mstr','xsj_mstr.xsj_po_nbr','=','xpo_mstrs.xpo_nbr')
                            ->distinct('xpo_nbr')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->count('xpo_nbr');  
                /*
                $updatedat = DB::table('xpo_mstrs')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->orderBy('updated_at','DESC')
                            ->first();
                */
                $updatedat = DB::table('com_mstr')
                            ->first();

                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")
                    ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                    ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                    ->whereRaw('(xpo_status = "Approved" or xpo_status = "Confirm")')
                    ->orderBy('xpo_mstrs.created_at','DESC')
                    ->orderBy('xpod_dets.xpod_nbr')
                    ->orderBy('xpod_dets.xpod_part','ASC')
                    ->paginate(10);  
                
                return view('/po/pobrowse',['users'=>$users,'totalpo'=>$totalpo,'unapppo'=>$unapppo,'shippo'=>$shippo,'updatedat'=>$updatedat]);
            }else{
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                ->orderBy('xpo_mstrs.created_at','DESC')
                ->orderBy('xpod_dets.xpod_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10);  

                return view('/po/pobrowse',['users'=>$users,'totalpo'=>$totalpo,'unapppo'=>$unapppo,'shippo'=>$shippo,'updatedat'=>$updatedat]);
            }

        }
    }
    
	public function viewpocf(Request $req){

        if($req->ajax()){
           
            if(Session::get('supp_code') != null){
                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")              
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
				->where('xpo_status','approved' )
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->paginate(10); 

             

                return view('/po/tablepocf',['users'=>$users]);

            }else{
               
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")  
				->where('xpo_status','approved' )
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->paginate(10); 

                return view('/po/tablepocf',['users'=>$users]);
            }

        }else{
            if(Session::get('supp_code') != null)
            {
                
                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
				    ->where('xpo_status','approved' )
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->paginate(10);  
                
                $totpo = DB::table("xpo_mstrs")
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
				    ->where('xpo_status','approved' )->count();
                
                return view('/po/poconf',['users'=>$users,'totpo'=>$totpo]);
            }else{

                
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")
				->where('xpo_status','Approved' ) 
				 ->orderBy('xpo_mstrs.xpo_nbr')
                ->paginate(10);  
                
                     $totpo = DB::table("xpo_mstrs")
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
				    ->where('xpo_status','approved' )->count();
                
                return view('/po/poconf',['users'=>$users,'totpo'=>$totpo]);
            }

        }
	}	

    public function searchpo(Request $req){
        if($req->ajax()){
            $ponbr = $req->nbr;
            $itemcode = $req->code;
            $supplier = $req->supp;
            $status = $req->status;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;
            /*
            $updatedat = DB::table('xpo_mstrs')
                    ->orderBy('updated_at','DESC')
                    ->first();*/
            $updatedat = DB::table('com_mstr')
                    ->first();

            if($ponbr == null && $itemcode == null && $supplier == null && $status == null 
                && $datefrom == null && $dateto == null ){

                if(Session::get('supp_code') != null){
                    $users = DB::table("xpo_mstrs")
                    ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                    ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                    ->whereRaw('(xpo_status = "Approved" or xpo_status = "Confirm")')
                    ->orderBy('xpo_mstrs.created_at','DESC')
                    ->orderBy('xpo_mstrs.xpo_nbr')
                    ->orderBy('xpod_dets.xpod_part')
                    ->paginate(10);  

                    return view('/po/tablepo',['users'=>$users,'updatedat'=>$updatedat]);
                }else{
                    $users = DB::table("xpo_mstrs")
                    ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                    ->orderBy('xpo_mstrs.created_at','DESC')
                    ->orderBy('xpo_mstrs.xpo_nbr')
                    ->orderBy('xpod_dets.xpod_part')
                    ->paginate(10);  

                    return view('/po/tablepo',['users'=>$users,'updatedat'=>$updatedat]);
                }                
            }

            // if($datefrom > $dateto)
            // {   
            //     echo 'test';
            //     return redirect()->back()->with(['error'=>'Date From Lebih Besar dari Date To']);
            // }

            if($req->datefrom == null){
                $datefrom = '2000-12-31';
            }else{
                 // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                // ubah ke int
                $new_date_from = strtotime($new_format_date_from);

                // ubah ke format date
                $datefrom = date('Y-m-d',$new_date_from);
            }

            if($req->dateto == null){
                $dateto = '3000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_to = str_replace('/', '-', $req->dateto); 

                // ubah ke int
                $new_date_to = strtotime($new_format_date_to);

                // ubah ke format date
                $dateto = date('Y-m-d',$new_date_to);
            }

            try{
            $query = "xpo_mstrs.xpo_due_date >= '".$datefrom."' 
                        AND xpo_mstrs.xpo_due_date <= '".$dateto."'";

            if($ponbr != null){
                $query .= " AND xpo_mstrs.xpo_nbr like '".$ponbr."%'";
            }
            if($supplier != null){
                $query .= " AND xpo_mstrs.xpo_vend like '".$supplier."%'";
            }
            if($itemcode != null){
                $query .= " AND xpod_dets.xpod_part like '".$itemcode."%'";
            }
            if($status != null){
                $query .= " AND xpo_mstrs.xpo_status = '".$status."'";
            }


            if(Session::get('supp_code') == null){
                // Non Supplier
                $users=DB::table("xpo_mstrs")
                            ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            ->whereRaw($query)
                            ->orderBy('xpo_mstrs.created_at','DESC')
                            ->paginate(10);   
                            
                return view('/po/tablepo',['users'=>$users,'updatedat'=>$updatedat]);
            }else{
                // Supplier
                $query .= " AND xpo_mstrs.xpo_vend like '".Session::get('supp_code')."%'";
                
                
                $users=DB::table("xpo_mstrs")
                            ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            ->whereRaw('(xpo_mstrs.xpo_status = "Approved" or xpo_mstrs.xpo_status = "Confirm")')
                            ->whereRaw($query)
                            ->orderBy('xpo_mstrs.created_at','DESC')
                            ->paginate(10);   

                //echo $query;
                return view('/po/tablepo',['users'=>$users,'updatedat'=>$updatedat]);
            }
            

            }catch(\InvalidArgumentException $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            }catch(\Exception $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            }catch(\Error $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }
        }
    }
	
	public function searchpocf(Request $req){
        if($req->ajax()){

            $ponbr = $req->nbr;
            $itemcode = $req->code;
            $supplier = $req->supp;
            $status = $req->status;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            if($ponbr == null && $itemcode == null && $supplier == null && $status == null 
                && $datefrom == null && $dateto == null ){

                if(Session::get('supp_code') != null){
                    $users = DB::table("xpo_mstrs")
					->where('xpo_status','Approved' )
                    ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                    ->orderBy('xpo_mstrs.xpo_nbr')

                    ->paginate(10);  

                    return view('/po/tablepocf',['users'=>$users]);
                }else{
                    $users = DB::table("xpo_mstrs")
					->where('xpo_status','Approved' )
                    ->orderBy('xpo_mstrs.xpo_nbr')
                    ->paginate(10);  

                    return view('/po/tablepocf',['users'=>$users]);
                }                
            }

            if($datefrom > $dateto)
            {
                return redirect()->back()->with(['error'=>'Date From is greater than Date To']);
            }

            if($req->datefrom == null){
                $datefrom = '2000-12-31';
            }else{
                 // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                // ubah ke int
                $new_date_from = strtotime($new_format_date_from);

                // ubah ke format date
                $datefrom = date('Y-m-d',$new_date_from);
            }

            if($req->dateto == null){
                $dateto = '3000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_to = str_replace('/', '-', $req->dateto); 

                // ubah ke int
                $new_date_to = strtotime($new_format_date_to);

                // ubah ke format date
                $dateto = date('Y-m-d',$new_date_to);
            }

            try{
            $query = "xpo_mstrs.xpo_due_date >= '".$datefrom."' 
                        AND xpo_mstrs.xpo_due_date <= '".$dateto."'";

            if($ponbr != null){
                $query .= " AND xpo_mstrs.xpo_nbr like '%".$ponbr."%'";
            }
            
            if($status != null){
                $query .= " AND xpo_mstrs.xpo_status like '%".$status."%'";
            }


            if(Session::get('supp_code') != null){
                $users=DB::table("xpo_mstrs")                            
                            ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
							->where('xpo_status','Approved' )
                            ->whereRaw($query)
                            ->paginate(100);   
            
                return view('/po/tablepocf',['users'=>$users]);
            }else{
                
                if($supplier != null){
                    $query .= " AND xpo_mstrs.xpo_vend like '%".$supplier."%'";
                }
                
                $users=DB::table("xpo_mstrs")                            
                            ->whereRaw($query)
                            ->paginate(100);   
            
                return view('/po/tablepocf',['users'=>$users]);
            }
            

            }catch(\InvalidArgumentException $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            }catch(\Exception $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            }catch(\Error $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }
        }
    }


    
    // PO Receipt

    public function indexreceipt(Request $req){

            $totalpo = DB::table('xpo_mstrs')
                        ->count();

            $unapppo = DB::table('xpo_mstrs')
                        ->where('xpo_status','=','unconfirm')
                        ->count();

            $shippo = DB::table('xpo_mstrs')
                        ->selectRaw('count(DISTINCT xpo_nbr)')
                        ->join('xsj_mstr','xsj_mstr.xsj_po_nbr','=','xpo_mstrs.xpo_nbr')
                        ->distinct('xpo_nbr')
                        ->count('xpo_nbr');

            $sjopen = DB::Table('xsj_mstr')
                        ->where('xsj_mstr.xsj_status','=','Created')
                        ->join("xpod_dets", function($join){
                            $join->on('xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
                                 ->on('xsj_mstr.xsj_line','=','xpod_dets.xpod_line');                         
                        })
                        //->groupBy('xsj_id')
                        ->groupBy('xsj_sj')
                        ->get();
            
            $users = DB::table("xpo_mstrs")
                        ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                        ->join("xsj_mstr",'xpod_dets.xpod_line','=','xsj_mstr.xsj_line')
                        ->where('xpo_nbr','=','') // biar kosong field awal 
                        ->orderBy('xpo_mstrs.xpo_nbr')
                        ->orderBy('xpod_dets.xpod_part')
                        ->get();  
            //dd($sjopen);
            $date = Carbon::now('ASIA/JAKARTA')->format('ymd');
            
            //return view('/po/poreceipt',['users'=>$users]);
            return view('/po/poreceipt', compact('users','date','totalpo','unapppo','shippo','sjopen'));
    }

    public function searchreceipt(Request $req){
        
        // Validasi ke QAD
        
        $validasi = DB::table('xsj_mstr')
                        ->join('xpod_dets','xpod_dets.xpod_nbr','=','xsj_po_nbr')
                        //->where('xsj_id','=',$req->sjnbr)
                        ->where('xsj_sj','=',$req->sjnbr)
                        ->whereRaw('xpod_dets.xpod_line = xsj_mstr.xsj_line')
                        ->get();

        //dd($validasi);

        /*$validasi = DB::table('xpo_receipt')
                        ->join('xpod_dets','xpod_dets.xpod_nbr','=','xpo_receipt.xpo_nbr')
                        ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                        ->whereRaw("xpod_dets.xpod_line = xpo_receipt.xpo_line")
                        ->selectRaw("xpod_nbr, xpod_line, xpod_qty_rcvd, xpod_qty_ord, xpo_domain")
                        ->groupBy("xpod_nbr")
                        ->groupBy("xpod_line")
                        ->get();
        */
        $note = '';

        foreach($validasi as $validasi){
            // Validasi WSA --> qty rcvd & qty ord
            $qty_rcvd = $validasi->xpod_qty_rcvd;
            $qty_ord  = $validasi->xpod_qty_ord;

            $domain = $validasi->xpod_domain;
            $ponbr  = $validasi->xpod_nbr;
            $line   = $validasi->xpod_line;
            
            $wsa = DB::table('wsas')
                        ->first();

            // Validasi WSA
                //$qxUrl          = 'http://192.168.1.150:9399/wsasim/services';  /*services/wsdl*/
                //$qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                //$qxUrl          = 'http://qadmmii.site:8080/wsasim/wsa1';
                $qxUrl          = $wsa->wsas_url;
                $qxReceiver     = '';
                $qxSuppRes      = 'false';
                $qxScopeTrx     = '';
                $qdocName       = '';
                $qdocVersion    = '';
                $dsName         = '';
                $timeout        = 0;

                $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                 '<Body>'.
                                // '<porcp1 xmlns="urn:iris.co.id:wsatest">'.
                                 '<porcp1 xmlns="'.$wsa->wsas_path.'">'.
                                 '<inpdomain>'.$domain.'</inpdomain>'.
                                 '<innbr>'.$ponbr.'</innbr>'.
                                 '<inline>'.$line.'</inline>'.
                                 '</porcp1>'.
                                 '</Body>'.
                                 '</Envelope>';

                //dd($qdocRequest);              
                $curlOptions = array(CURLOPT_URL => $qxUrl,
                                     CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                     CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                     CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                     CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                     CURLOPT_POST => true,
                                     CURLOPT_RETURNTRANSFER => true,
                                     CURLOPT_SSL_VERIFYPEER => false,
                                     CURLOPT_SSL_VERIFYHOST => false);
                             
                $getInfo = '';
                $httpCode = 0;
                $curlErrno = 0;
                $curlError = '';
                $qdocResponse = '';

                $curl = curl_init();
                if ($curl) {
                    curl_setopt_array($curl, $curlOptions);
                    $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                    $curlErrno    = curl_errno($curl);
                    $curlError    = curl_error($curl);
                    $first        = true;
                
                    foreach (curl_getinfo($curl) as $key=>$value) {
                        if (gettype($value) != 'array') {
                            if (! $first) $getInfo .= ", ";
                            $getInfo = $getInfo . $key . '=>' . $value;
                            $first = false;
                            if ($key == 'http_code') $httpCode = $value;
                        }
                    }
                    curl_close($curl);
                }
                
                $xmlResp = simplexml_load_string($qdocResponse);       
            
                //$xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');   
                // $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsasim');   
                $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);

                $dataloop = $xmlResp->xpath('//ns1:tempRow');          
                $result = (string) $xmlResp->xpath('//ns1:outOK')[0];
                
                $flag = 0;

                //dd($result);

                if($result == 'true'){
                    foreach($dataloop as $data) { 
                        // qty rcvd & order QAD
                        $qdocResult = $data->t_rcvd;
                        $qdocResult1 = $data->t_ord;
                        $qdocResult2 = $data->t_status;



                        if($qdocResult != $qty_rcvd){
                            // Data tidak sesuai
                            $note .= 'Qty Received = '.$ponbr.' Line = '.$line.' is different, QAD = '.$qdocResult.' --- Web = '.$qty_rcvd.' ';
                        }else if($qdocResult1 != $qty_ord){
                            // Data tidak sesuai
                            $note .= 'Qty Order = '.$ponbr.' Line = '.$line.' is different, QAD = '.$qdocResult1.'--- Web = '.$qty_ord.' ';
                        }else if($qdocResult2 == 'c'){
                            // Data tidak sesuai, PO Closed id QAD
                            $note .= 'Status Purchase Order = '.$ponbr.' Line = '.$line.' is Closed';
                        }else{
                            // Data Sesuai

                        }
                        
                        $flag = $flag + 1;
                    }
                }else{
                    $note .= 'No Data in QAD for PO : '.$ponbr.'- Line : '.$line.', ';
                }    
                
        }

        if($note == ''){
            // Tidak ada error 
            // Validasi Surat jalan -> PO Details
            //$query = "xsj_mstr.xsj_id = '".$req->sjnbr."' AND xsj_mstr.xsj_status = 'Created' AND xpod_line = xsj_line";
            $query = "xsj_mstr.xsj_sj = '".$req->sjnbr."' AND xsj_mstr.xsj_status = 'Created' AND xpod_line = xsj_line";


            $users=DB::table("xsj_mstr")
                            ->join("xpo_mstrs",'xpo_mstrs.xpo_nbr','=','xsj_mstr.xsj_po_nbr')
                            ->join("xpod_dets",'xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
                            ->whereRaw($query)
                            ->get();  

            
            if(count($users) > 0){
                // ada isi
                // Hapus table khusus load untuk user akses
                DB::table("xpo_receipt")
                            ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                            ->delete();
                // Insert Ke talbe khusus load
                foreach($users as $users){
                    
                    $data1 = array(
                        'xpo_domain'=>$users->xpod_domain,   
                        'xpo_sj_id'=>$users->xsj_sj,
                        'xpo_nbr'=>$users->xpod_nbr,   
                        'xpo_line'=>$users->xpod_line,   
                        'xpo_part'=>$users->xpod_part,   
                        'xpo_desc'=>$users->xpod_desc,   
                        'xpo_um'=>$users->xpod_um,   
                        'xpo_qty_ord'=>$users->xpod_qty_ord,   
                        'xpo_qty_rcvd'=>$users->xsj_qty_ship,   
                        'xpo_qty_open'=>$users->xpod_qty_open,   
                        'xpo_qty_ship'=>$users->xsj_qty_ship, // 11062020   
                        'xpo_ship_date'=>$users->xpod_ship_date,   
                        'xpo_due_date'=>$users->xpod_due_date,   
                        'xpo_lot'=>$users->xpod_lot,
                        'xpo_loc'=>$users->xpod_loc,   
                        'xpo_ref'=>$users->xpod_ref,   
                        'xpo_site'=>$users->xpod_site,   
                        'xpo_user'=>Session::get('userid'),   
                        'xpo_created'=> Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                        //'xpo_crt_date'=> Carbon::now()->toDateString(),               
                    );

                    DB::table('xpo_receipt')->insert($data1);       
                }


                return redirect()->route('showreceiptrow');   

            }else{
                // tidak ada isi
                return redirect()->back()->with(['error'=>'No Shipper available for Shipper No. : '.$req->sjnbr.', Please check!']);
            }
        }else{
            // Error 
                return redirect()->back()->with(['error'=>$note]);

        }
    }

    public function showreceiptrow(Request $req){
       // dd('123');
        $value = DB::table("xpo_receipt")
                    ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                    ->orderBy('xpo_nbr','ASC')
                    ->orderby('xpo_line','ASC')
                    ->orderby('xpo_status','ASC')
                    ->orderby('xpo_created','ASC')
                    ->get();

        $site = DB::table('xsite_mstr')
                ->get();
        //dd($value);
        return view('/po/poreceiptview', compact('value','site'));   
    }

    public function deleterow(Request $req){
        //dd($req->all());
        DB::table('xpo_receipt')
                ->where('xpo_receipt.xpo_rcp_id','=',$req->t_deleteid)
                ->delete();

        return redirect()->back()->with(['updated'=>'Data is successfully deleted']);
    }

    public function newreceiptrow(Request $req){
        //dd($req->all());
        
        $data1 = array(
                'xpo_domain'=>$req->domain,   
                'xpo_nbr'=>$req->nopo,      
                'xpo_sj_id'=>$req->sj_id,   
                'xpo_line'=>$req->line,   
                'xpo_part'=>$req->part,   
                'xpo_desc'=>$req->desc,   
                'xpo_um'=>$req->um,   
                'xpo_qty_ord'=>$req->qtyord, 
                'xpo_qty_open'=>$req->qtyopen,
                'xpo_qty_ship'=>$req->qtyship,    
                //'xpo_loc'=>$req->xpo_loc, ambil data dari master tar
                //'xpo_lot'=>$req->xpo_lot,   
                //'xpo_ref'=>$req->xpo_ref,   
                'xpo_site'=>$req->site,   
                'xpo_user'=>Session::get('userid'),   
                'xpo_created'=> Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                'xpo_status'=>'newrow', // Created ga boleh delete, newrow boleh delete
                //'xpo_crt_date'=> Carbon::now()->toDateString(),               
            );

            DB::table('xpo_receipt')->insert($data1); 

        return redirect()->back();
    }

    public function updaterow(Request $req){

        //dd($req->all());

        $id = $req->input('rcpid');
        $ponbr = $req->input('m_ponbr');
        $sj = $req->input('m_sj');
        $line = $req->input('m_line');
        $itemcode = $req->input('m_itemcode');
        $qtyord = $req->input('m_qtyord');
        $itemdesc = $req->input('m_itemdesc');
        $qtyship = $req->input('m_qtyship');

        $qtyopen = $req->input('m_qtyopen'); //---
        $qtyrec = $req->input('m_qtyrec'); //---
        
        $um = $req->input('m_um');
        $site = $req->input('m_site');
        $loc = $req->input('m_loc');
        $lot = $req->input('m_lot');
        $ref = $req->input('m_ref');


        $old_effdate = $req->input('effdate');
        $old_shipdate = $req->input('shipdate');

        // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
        $new_format_effdate = str_replace('/', '-', $old_effdate); 
        $new_format_shipdate = str_replace('/', '-', $old_shipdate); 

        // ubah ke int
        $new_effdate = strtotime($new_format_effdate);
        $new_shipdate = strtotime($new_format_shipdate);

        // ubah ke format date
        $effdate = date('Y-m-d',$new_effdate);
        $shipdate = date('Y-m-d',$new_shipdate);

        // Hitung Sisa Qty Open
        $new_qtyopen = $qtyopen - $qtyrec;

        // Hitung New Qty Rec
        $old_data = DB::table('xpod_dets')
                        ->where('xpod_line', $line)
                        ->where('xpod_nbr', $ponbr)
                        ->select('xpod_qty_rcvd')
                        ->first();
        $new_qtyrec = $qtyrec;

        DB::table('xpo_receipt')
                ->where('xpo_receipt.xpo_rcp_id','=',$id)
                ->update([
                    'xpo_eff_date' => $effdate,
                    'xpo_ship_date' => $shipdate,
                    'xpo_qty_rcvd' => $new_qtyrec,
                    'xpo_um' => $um,
                    'xpo_site' => $site,
                    'xpo_loc' => $loc,
                    'xpo_lot' => $lot,
                    'xpo_ref' => $ref
                ]);

        return redirect()->back()->with(['updated'=>'Data is successfully updated']);        
    }

    
    // public function receiptqad(Request $req){
        
        //    $data = DB::table('xpo_receipt')
        //                 ->selectRaw('sum(xpo_qty_rcvd) as total, xpo_qty_ship, xpo_nbr, xpo_line, xpo_domain, xpo_line')
        //                 ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
        //                 ->groupBy('xpo_nbr')
        //                 ->groupBy('xpo_line')
        //                 ->get();

        //     $chr_tmp = '';

        //     foreach($data as $data){
                
        //         if($data->total != $data->xpo_qty_ship){
        //                 $chr_tmp .= 'PO : '.$data->xpo_nbr.' Line : '.$data->xpo_line.' Qty Received is not equal to Qty Shipped, '.$data->total.'-'.$data->xpo_qty_ship;
        //         }

        //     }


        //     if($chr_tmp != ''){
        //         // Error balikin ke menu

        //         return redirect()->back()->with(['error'=>$chr_tmp]);

        //     }else{
        //         // Tidak Error Masukin QAD -- CIM + Validasi WSA 
        //         // ambil data inputan user
        //         $validasi = DB::table('xpo_receipt')
        //                     ->join('xpod_dets','xpod_dets.xpod_nbr','=','xpo_receipt.xpo_nbr')
        //                     ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
        //                     ->whereRaw("xpod_dets.xpod_line = xpo_receipt.xpo_line")
        //                     ->selectRaw("xpod_nbr, xpod_line, xpod_qty_rcvd, xpod_qty_ord, xpo_domain")
        //                     ->groupBy("xpod_nbr")
        //                     ->groupBy("xpod_line")
        //                     ->get();

        //         $note = '';
        //         $content = '';

        //         //dd($validasi);

        //         foreach($validasi as $validasi){
        //             // Validasi WSA --> qty rcvd & qty ord
        //             $qty_rcvd = $validasi->xpod_qty_rcvd;
        //             $qty_ord  = $validasi->xpod_qty_ord;

        //             $domain = $validasi->xpo_domain;
        //             $ponbr  = $validasi->xpod_nbr;
        //             $line   = $validasi->xpod_line;

        //             //dd($domain, $ponbr, $line, $qty_rcvd, $qty_ord);

        //             // Validasi WSA
        //                 $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';  /*services/wsdl*/
        //                 $qxReceiver     = '';
        //                 $qxSuppRes      = 'false';
        //                 $qxScopeTrx     = '';
        //                 $qdocName       = '';
        //                 $qdocVersion    = '';
        //                 $dsName         = '';
        //                 $timeout        = 0;

        //                 $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
        //                                  '<Body>'.
        //                                  '<porcp1 xmlns="urn:iris.co.id:wsatest">'.
        //                                  '<inpdomain>'.$domain.'</inpdomain>'.
        //                                  '<innbr>'.$ponbr.'</innbr>'.
        //                                  '<inline>'.$line.'</inline>'.
        //                                  '</porcp1>'.
        //                                  '</Body>'.
        //                                  '</Envelope>';
                                        
        //                 //dd($qdocRequest);              
        //                 $curlOptions = array(CURLOPT_URL => $qxUrl,
        //                                      CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
        //                                      CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
        //                                      CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
        //                                      CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
        //                                      CURLOPT_POST => true,
        //                                      CURLOPT_RETURNTRANSFER => true,
        //                                      CURLOPT_SSL_VERIFYPEER => false,
        //                                      CURLOPT_SSL_VERIFYHOST => false);
                                    
        //                 $getInfo = '';
        //                 $httpCode = 0;
        //                 $curlErrno = 0;
        //                 $curlError = '';
        //                 $qdocResponse = '';

        //                 $curl = curl_init();
        //                 if ($curl) {
        //                     curl_setopt_array($curl, $curlOptions);
        //                     $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
        //                     $curlErrno    = curl_errno($curl);
        //                     $curlError    = curl_error($curl);
        //                     $first        = true;
                        
        //                     foreach (curl_getinfo($curl) as $key=>$value) {
        //                         if (gettype($value) != 'array') {
        //                             if (! $first) $getInfo .= ", ";
        //                             $getInfo = $getInfo . $key . '=>' . $value;
        //                             $first = false;
        //                             if ($key == 'http_code') $httpCode = $value;
        //                         }
        //                     }
        //                     curl_close($curl);
        //                 }
                        
        //                 $xmlResp = simplexml_load_string($qdocResponse);       
                    
        //                 $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');          
        //                 $dataloop = $xmlResp->xpath('//ns1:tempRow');          
        //                 $result = (string) $xmlResp->xpath('//ns1:outOK')[0];   

                        
        //                 $flag = 0;

        //                 if($result == 'true'){
        //                     foreach($dataloop as $data) { 
        //                         // qty rcvd & order QAD
        //                         //$qdocResult = (string) $xmlResp->xpath('//ns1:t_rcvd')[$flag];   
        //                         //$qdocResult1 = (string) $xmlResp->xpath('//ns1:t_ord')[$flag]; 
        //                         //$qdocResult2 = (string) $xmlResp->xpath('//ns1:t_status')[$flag];

        //                         $qdocResult = $data->t_rcvd;
        //                         $qdocResult1 = $data->t_ord;
        //                         $qdocResult2 = $data->t_status;



        //                         if($qdocResult != $qty_rcvd){
        //                             // Data tidak sesuai
        //                             $note .= 'Qty Received = '.$ponbr.' Line = '.$line.' is different, QAD = '.$qdocResult.' --- Web = '.$qty_rcvd.' ';
        //                         }else if($qdocResult1 != $qty_ord){
        //                             // Data tidak sesuai
        //                             $note .= 'Qty Order = '.$ponbr.' Line = '.$line.' is different, QAD = '.$qdocResult1.'--- Web = '.$qty_ord.' ';
        //                         }else if($qdocResult2 == 'c'){
        //                             // Data tidak sesuai, PO Closed id QAD
        //                             $note .= 'Status Purchase Order = '.$ponbr.' Line = '.$line.' is Closed';
        //                         }else{
        //                             // Data Sesuai

        //                         }
                                
        //                         $flag = $flag + 1;
        //                     }
        //                 }else{
        //                     $note .= 'No Data in QAD for PO : '.$ponbr.'- Line : '.$line.', ';
        //                 }                    
                        
        //         }

        //         if($note == ''){
        //             // Bkin Cim Load

        //             // Header No PO  -- > Data 1
        //             $header = DB::table('xpo_receipt')
        //                         ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
        //                         ->selectRaw('xpo_nbr,xpo_sj_id,xpo_eff_date,xpo_ship_date,xpo_line')
        //                         ->groupBy('xpo_nbr')
        //                         ->get();
        //             //dd($header);
        //             foreach($header as $header){
        //                 // Header
        //                 $new_format_effdate = $header->xpo_eff_date;
        //                 $new_format_shipdate = $header->xpo_ship_date;


        //                 $new_effdate = strtotime($new_format_effdate);
        //                 $new_shipdate = strtotime($new_format_shipdate);


        //                 $file_effdate = date('m/d/y',$new_effdate); 
        //                 $file_shipdate = date('m/d/y',$new_shipdate);

        //                 if(is_null($header->xpo_eff_date) or is_null($header->xpo_ship_date)){
        //                     return redirect()->back()->with(['error'=>'Ship date or Eff Date must be filled for each row']);
        //                 }

        //                 $content .= '"'.$header->xpo_nbr.'"'.PHP_EOL.
        //                             '"'.$header->xpo_sj_id.'"'.' '.'-'.' '.$file_effdate.' '.'-'.' '.'"NO"'.' '.'-'.' '.'-'.' '.$file_shipdate.PHP_EOL;


        //                 $dataline = DB::table('xpo_receipt')
        //                                 ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
        //                                 ->where("xpo_receipt.xpo_nbr",'=',$header->xpo_nbr)
        //                                 ->selectRaw('xpo_nbr,xpo_line,xpo_rcp_id,xpo_qty_ord,xpo_site,xpo_loc,xpo_lot,xpo_ref,xpo_qty_rcvd')
        //                                 ->orderBy('xpo_nbr')
        //                                 ->orderBy('xpo_line')
        //                                 ->groupBy('xpo_nbr')
        //                                 ->groupBy('xpo_line')
        //                                 ->get();  

        //                 foreach($dataline as $dataline){
                            
        //                     // Data Loc/Lot
        //                     $data = DB::table('xpo_receipt')
        //                                 ->selectRaw('xpo_line, xpo_part, xpo_qty_ord, xpo_qty_rcvd, xpo_um, xpo_site, xpo_loc, xpo_lot, xpo_ref,xpo_rcp_id, xpo_qty_rcvd, xpo_qty_ship')
        //                                 ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
        //                                 ->where('xpo_receipt.xpo_nbr','=',$header->xpo_nbr)
        //                                 ->where('xpo_receipt.xpo_line','=',$dataline->xpo_line)
        //                                 //->where('xpo_receipt.xpo_rcp_id','!=',$dataline->xpo_rcp_id)
        //                                 ->orderby('xpo_nbr')
        //                                 ->orderby('xpo_line')
        //                                 ->get();
                            
        //                     // Multi Entry Loc/Lot
        //                     $content .= $dataline->xpo_line.PHP_EOL.
        //                             '"'.$dataline->xpo_qty_rcvd.'"'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'"'.$dataline->xpo_loc.'"'.' '.'"'.$dataline->xpo_lot.'"'.' '.'"'.$dataline->xpo_ref.'"'.' '.'-'.' '.'"YES"'.' '.'NO'.' '.'NO'.PHP_EOL;
                        
        //                     foreach($data as $data){
        //                         $content .= '"'.$data->xpo_loc.'"'.' '.'"'.$data->xpo_lot.'"'.' '.'"'.$data->xpo_ref.'"'.' '.'-'.' '.PHP_EOL.'"'.$data->xpo_qty_rcvd.'"'.PHP_EOL;
        //                     }

        //                         $content .= '.'.PHP_EOL;

        //                 }   
        //                     $content .= '.'.PHP_EOL;

        //             }

        //             File::put('cim/xxcimporcp.cim',$content); 

        //             // dd('ok');

        //             // buat jalanin sim ke QAD
        //             exec("start cmd /c cimporcpt.bat");

        //             // Update Details -> Closed klo qty Rec = qty ord

        //             //dd('123');

        //             $data =  DB::table('xpo_receipt')
        //                         ->where('xpo_user','=',Session::get('userid'))
        //                         ->selectRaw('xpo_nbr,xpo_line,xpo_rcp_id,xpo_sj_id,sum(xpo_qty_rcvd) as tot_rcvd ,xpo_qty_ord')
        //                         ->groupBy('xpo_nbr')
        //                         ->groupBy('xpo_line')
        //                         ->get();

        //             foreach($data as $data){
        //                 $datapo = DB::table('xpo_mstrs')
        //                             ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
        //                             ->where('xpod_dets.xpod_line','=',$data->xpo_line)
        //                             ->where('xpod_nbr','=',$data->xpo_nbr)
        //                             ->first();
                        
        //                 $totalqtyafter = $datapo->xpod_qty_rcvd + $data->tot_rcvd;


        //                 if($totalqtyafter >= $data->xpo_qty_ord){
        //                     DB::table('xpod_dets')
        //                         ->where('xpod_nbr','=',$data->xpo_nbr)
        //                         ->where('xpod_line','=',$data->xpo_line)
        //                         ->update([
        //                                 'xpod_qty_rcvd' => $totalqtyafter,
        //                                 'xpod_status' => 'Closed'
        //                         ]);

        //                     // Ubah Master jadi closed klo smua detail closed.
        //                     $mstr = DB::table('xpo_mstrs')
        //                                 ->join('xpod_dets','xpod_nbr','=','xpo_nbr')
        //                                 ->where('xpo_nbr','=',$data->xpo_nbr)
        //                                 ->where('xpod_dets.xpod_status','!=','Closed')
        //                                 ->first();

        //                     if(is_null($mstr)){
        //                         // Semua detail closed.
        //                         DB::table('xpo_mstrs')
        //                                 ->where('xpo_nbr','=',$data->xpo_nbr)
        //                                 ->update([
        //                                         'xpo_status' => 'Closed'
        //                                 ]); 
        //                     }


        //                     $updatedata = DB::table('xpod_dets')
        //                                     ->join('xpo_mstrs','xpo_nbr','=','xpod_nbr')
        //                                     ->where('xpod_nbr','=',$data->xpo_nbr)
        //                                     ->where('xpod_line','=',$data->xpo_line)
        //                                     ->first();

        //                     if(!is_null($updatedata)){
        //                         DB::table('xpo_hist')
        //                             ->insert([
        //                                     'xpo_domain' => $updatedata->xpo_domain,
        //                                     'xpo_nbr' => $updatedata->xpo_nbr,
        //                                     'xpo_line' => $updatedata->xpod_line,
        //                                     'xpo_part' => $updatedata->xpod_part,
        //                                     'xpo_desc' => $updatedata->xpod_desc,
        //                                     'xpo_um' => $updatedata->xpod_um,
        //                                     'xpo_qty_ord' => $updatedata->xpod_qty_ord,
        //                                     'xpo_qty_rcvd' => $totalqtyafter,
        //                                     'xpo_qty_open' => $updatedata->xpod_qty_open,
        //                                     'xpo_qty_prom' => $updatedata->xpod_qty_prom,
        //                                     'xpo_price' => $updatedata->xpod_price,
        //                                     'xpo_loc' => $updatedata->xpod_loc,
        //                                     'xpo_lot' => $updatedata->xpod_lot,
        //                                     'xpo_due_date' => $updatedata->xpo_due_date,
        //                                     'xpo_vend' => $updatedata->xpo_vend,
        //                                     'xpo_status' => 'Closed',
        //                                     'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        //                                     'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        //                             ]);
        //                     }

        //                 }else{
        //                     DB::table('xpod_dets')
        //                         ->where('xpod_nbr','=',$data->xpo_nbr)
        //                         ->where('xpod_line','=',$data->xpo_line)
        //                         ->update([
        //                                 'xpod_qty_rcvd' => $totalqtyafter,
        //                         ]);

        //                     $updatedata = DB::table('xpod_dets')
        //                                     ->join('xpo_mstrs','xpo_nbr','=','xpod_nbr')
        //                                     ->where('xpod_nbr','=',$data->xpo_nbr)
        //                                     ->where('xpod_line','=',$data->xpo_line)
        //                                     ->first();
                                            
        //                     if(!is_null($updatedata)){
        //                         DB::table('xpo_hist')
        //                             ->insert([
        //                                     'xpo_domain' => $updatedata->xpo_domain,
        //                                     'xpo_nbr' => $updatedata->xpo_nbr,
        //                                     'xpo_line' => $updatedata->xpod_line,
        //                                     'xpo_part' => $updatedata->xpod_part,
        //                                     'xpo_desc' => $updatedata->xpod_desc,
        //                                     'xpo_um' => $updatedata->xpod_um,
        //                                     'xpo_qty_ord' => $updatedata->xpod_qty_ord,
        //                                     'xpo_qty_rcvd' => $totalqtyafter,
        //                                     'xpo_qty_open' => $updatedata->xpod_qty_open,
        //                                     'xpo_qty_prom' => $updatedata->xpod_qty_prom,
        //                                     'xpo_price' => $updatedata->xpod_price,
        //                                     'xpo_loc' => $updatedata->xpod_loc,
        //                                     'xpo_lot' => $updatedata->xpod_lot,
        //                                     'xpo_due_date' => $updatedata->xpo_due_date,
        //                                     'xpo_vend' => $updatedata->xpo_vend,
        //                                     'xpo_status' => 'Open', // Statusnya masi buka blom closed
        //                                     'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        //                                     'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        //                             ]);
        //                     }
        //                 }            
        //             }
    

        //             // Hapus Table PO_receipt
        //             DB::table('xpo_receipt')
        //                     ->where('xpo_user','=',Session::get('userid'))
        //                     ->delete();

        //             // Update Table SJ --> Closed
        //             DB::table('xsj_mstr')
        //                     //->where('xsj_id','=',$header->xpo_sj_id)
        //                     ->where('xsj_sj','=',$header->xpo_sj_id)
        //                     ->update([
        //                             'xsj_status' => 'Closed' // SJ Sudah selesai
        //                     ]);

        //             $date = Carbon::now('ASIA/JAKARTA')->format('ymd');
        //             // session()->flash("updated","Data is successfully updated to QAD");
        //             alert()->success('Success','Data is succesfully Updated to QAD');
                    
        //             return redirect()->route('poreceipt');  
        //             //return redirect()->back()->with(['updated'=>'Data Berhasil Diupdate ke QAD']);

        //             //return view('/po/poreceipt',['date'=>$date]);

        //         }else{
        //             // Error balikin menu awal
        //             return redirect()->back()->with(['error'=>$note]);
        //         }

        //     }
    // } Moved

    public function detailreceipt(Request $req){
        if($req->ajax()){
            $data = db::table('xpod_dets')
                    ->where('xpod_dets.xpod_nbr','=',$req->ponbr)
                    ->where('xpod_dets.xpod_line','=',$req->line)
                    ->get();

            $array = json_decode(json_encode($data), true);

            return response()->json($array);
        }
    }

    // public function receiptupdate(Request $req){
        //     //dd($req->all());

        //     $domain = $req->input('m_domain');
        //     $ponbr = $req->input('m_ponbr');
        //     $sj = $req->input('m_sj');
        //     $line = $req->input('m_line');
        //     $itemcode = $req->input('m_itemcode');
        //     $qtyord = $req->input('m_qtyord');
        //     $itemdesc = $req->input('m_itemdesc');
        //     $qtyship = $req->input('m_qtyship');

        //     $qtyopen = $req->input('m_qtyopen'); //---
        //     $qtyrec = $req->input('m_qtyrec'); //---
        //     //$boflg = $req->input('boflg'); //---

        //     $um = $req->input('m_um');
        //     $site = $req->input('m_site');
        //     $loc = $req->input('m_loc');
        //     $lot = $req->input('m_lot');
        //     $ref = $req->input('m_ref');
        //     $old_effdate = $req->input('effdate');
        //     $old_shipdate = $req->input('shipdate');


        //     // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
        //     $new_format_effdate = str_replace('/', '-', $old_effdate); 
        //     $new_format_shipdate = str_replace('/', '-', $old_shipdate); 

        //     // ubah ke int
        //     $new_effdate = strtotime($new_format_effdate);
        //     $new_shipdate = strtotime($new_format_shipdate);

        //     // ubah ke format date
        //     $effdate = date('Y-m-d',$new_effdate);
        //     $shipdate = date('Y-m-d',$new_shipdate);

        //     // Hitung Sisa Qty Open
        //     $new_qtyopen = $qtyopen - $qtyrec;

        //     // Hitung New Qty Rec
        //     $old_data = DB::table('xpod_dets')
        //                     ->where('xpod_line', $line)
        //                     ->where('xpod_nbr', $ponbr)
        //                     ->select('xpod_qty_rcvd')
        //                     ->first();
        //     $new_qtyrec = $qtyrec + $old_data->xpod_qty_rcvd;
            

        //     // Validasi WSA
        //     $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';  /*services/wsdl*/
        //     $qxReceiver     = '';
        //     $qxSuppRes      = 'false';
        //     $qxScopeTrx     = '';
        //     $qdocName       = '';
        //     $qdocVersion    = '';
        //     $dsName         = '';
        //     $timeout        = 0;

        //     $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
        //                      '<Body>'.
        //                      '<porcp xmlns="urn:iris.co.id:wsatest">'.
        //                      '<inpdomain>'.$domain.'</inpdomain>'.
        //                      '<innbr>'.$ponbr.'</innbr>'.
        //                      '<inline>'.$line.'</inline>'.
        //                      '</porcp>'.
        //                      '</Body>'.
        //                      '</Envelope>';
                            
        //     //dd($qdocRequest);              
        //     $curlOptions = array(CURLOPT_URL => $qxUrl,
        //                          CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
        //                          CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
        //                          CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
        //                          CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
        //                          CURLOPT_POST => true,
        //                          CURLOPT_RETURNTRANSFER => true,
        //                          CURLOPT_SSL_VERIFYPEER => false,
        //                          CURLOPT_SSL_VERIFYHOST => false);
                        
        //     $getInfo = '';
        //     $httpCode = 0;
        //     $curlErrno = 0;
        //     $curlError = '';
        //     $qdocResponse = '';

        //     $curl = curl_init();
        //     if ($curl) {
        //         curl_setopt_array($curl, $curlOptions);
        //         $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
        //         $curlErrno    = curl_errno($curl);
        //         $curlError    = curl_error($curl);
        //         $first        = true;
            
        //         foreach (curl_getinfo($curl) as $key=>$value) {
        //             if (gettype($value) != 'array') {
        //                 if (! $first) $getInfo .= ", ";
        //                 $getInfo = $getInfo . $key . '=>' . $value;
        //                 $first = false;
        //                 if ($key == 'http_code') $httpCode = $value;
        //             }
        //         }
        //         curl_close($curl);
        //     }
            
        //     $xmlResp = simplexml_load_string($qdocResponse);
            
        //     $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');
            
        //     //$qdocResult[] =  $xmlResp->xpath('//ns1:t_rcvd');
        //     //dd($qdocResult[0]);
            
        //     $qdocResult = '';
        //     foreach($xmlResp->xpath('//ns1:t_rcvd') as $data) {
        //         $qdocResult = (string) $xmlResp->xpath('//ns1:t_rcvd')[0];             
        //     }
        //     // cari qty rcvd di web 
        //     $qtyrcvd_web = DB::table('xpod_dets')
        //                     ->where('xpod_dets.xpod_nbr','=',$ponbr)
        //                     ->where('xpod_dets.xpod_line','=',$line)
        //                     ->where('xpod_dets.xpod_domain','=',$domain)
        //                     ->first();

        //     if($qdocResult == $qtyrcvd_web->xpod_qty_rcvd){
        //         // validasi klo sesuai lanjut
        //         // Update Data Ke DB
        //         // qty open > 0 data masih muncul
        //         if($new_qtyopen > 0){
                    
        //             // POD Detail
        //             DB::table('xpod_dets')
        //                 ->where('xpod_nbr', $ponbr)
        //                 ->where('xpod_line', $line)
        //                 ->update([
        //                         'xpod_qty_open' => $new_qtyopen,
        //                         'xpod_qty_rcvd' => $new_qtyrec,
        //                         //'xpod_cancel' => $boflg,
        //                         'xpod_um' => $um,
        //                         'xpod_site' => $site,
        //                         'xpod_loc' => $loc,
        //                         'xpod_lot' => $lot,
        //                         'xpod_ref' => $ref,
        //                         'xpod_eff_date' => $effdate,
        //                         'xpod_ship_date' => $shipdate,
        //                 ]);

        //             // SJ Mstr
        //             DB::table('xsj_mstr')
        //                 ->where('xsj_po_nbr', $ponbr)
        //                 ->where('xsj_line', $line)
        //                 ->update([
        //                         'xsj_qty_open' => $new_qtyopen
        //                         //'xsj_qty_ship' => $new_qtyrec
        //                 ]);
                
        //             //$query = "xsj_mstr.xsj_id like '%".$sj."%' AND xsj_mstr.xsj_status = 'Created' ";

        //             $query = "xsj_mstr.xsj_sj like '%".$sj."%' AND xsj_mstr.xsj_status = 'Created' ";


        //             $users=DB::table("xsj_mstr")
        //                             ->join("xpo_mstrs",'xpo_mstrs.xpo_nbr','=','xsj_mstr.xsj_po_nbr')
        //                             ->join("xpod_dets", function($join){
        //                                 $join->on('xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
        //                                      ->on('xsj_mstr.xsj_line','=','xpod_dets.xpod_line');
        //                             })
        //                             ->whereRaw($query)
        //                             ->get();  

        //             $date = Carbon::now('ASIA/JAKARTA')->format('ymd');
        //             return view('/po/poreceipt', compact('users','date'));


        //         }else{
                    
        //             DB::table('xpod_dets')
        //                 ->where('xpod_nbr', $ponbr)
        //                 ->where('xpod_line', $line)
        //                 ->update([
        //                         //'xpod_qty_open' => $new_qtyopen,
        //                         'xpod_qty_open' => '0',
        //                         'xpod_qty_rcvd' => $new_qtyrec,
        //                         //'xpod_cancel' => $boflg,
        //                         'xpod_um' => $um,
        //                         'xpod_site' => $site,
        //                         'xpod_loc' => $loc,
        //                         'xpod_lot' => $lot,
        //                         'xpod_ref' => $ref,
        //                         'xpod_eff_date' => $effdate,
        //                         'xpod_ship_date' => $shipdate,
        //                         'xpod_status' => 'Closed',
        //                 ]);

        //             DB::table('xpo_mstrs')
        //                 ->where('xpo_nbr', $ponbr)
        //                 ->update([
        //                         'xpo_status' => 'Closed',
        //                 ]);    

        //             DB::table('xsj_mstr')
        //                 ->where('xsj_po_nbr', $ponbr)
        //                 ->where('xsj_line', $line)
        //                 ->update([
        //                         'xsj_status' => 'Closed',
        //                 ]);  
        //         }



        //         // Disini Kirim Data Ke QAD, Lewat Textfile pake .Bat kirim ke Server QAD
        //         $content = '';

        //         //$file_effdate = date('d/m/y',$new_effdate);
        //         //$file_shipdate = date('d/m/y',$new_shipdate);
        //         $file_effdate = date('m/d/y',$new_effdate); 
        //         $file_shipdate = date('m/d/y',$new_shipdate);

        //         $content .= '"'.$ponbr.'"'
        //                     .PHP_EOL.'"'.$sj.'"'.' '.'-'.' '.$file_effdate.' '.'"no"'.' '.'"no"'.' '.'"no"'.' '.'"no"'.' '.$file_shipdate
        //                     .PHP_EOL.'"'.$line.'"'
        //                     .PHP_EOL.'"'.$qtyrec.'"'.' '.'-'.' '.'"no"'.' '.'"'.$um.'"'.' '.'-'.' '.'-'.' '.'"'.$site.'"'.' '.'"'.$loc.'"'.' '.'"'.$lot.'"'.' '.'"'.$ref.'"'.' '.'-'.' '    .'-'.' '.'"no"'.' '.'"no"'.' '.'"no"'
        //                     .PHP_EOL.'.'
        //                     .PHP_EOL.'.';

        //         File::put('cim/xxcimporcp.cim',$content); 

        //         exec("start cmd /c cimporcpt.bat");

        //         // session()->flash("updated","PO Receipt is Successful");
        //         alert()->success('Success','PO Receipt is Successful');
                    
        //         return back();
        //     }else{
        //         // validasi error , qty rcvd web != qad
                
        //         // session()->flash("error","Qty Received Web is different from QAD, Refresh PO / Contact your Admin");
        //         alert()->error('Error','Qty Received Web is different from QAD, Refresh PO / Contact your Admin');

        //         return back();

        //     }
    // } Moved

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }



    public function controlindex(Request $req){

        if($req->ajax()){
            $users = DB::table('xalert_mstrs')
                ->leftJoin('xpo_control','xpo_control.supp_code','=','xalert_mstrs.xalert_supp')
                ->where('xalert_mstrs.xalert_active','=','Yes')
                ->groupBy('xalert_supp')
                ->paginate(10);

            $names = DB::table('users')
                            ->distinct()
                            ->select('id','name','role_type')
                            ->where('role','=','Purchasing')
                            ->orderBy('role_type')
                            ->orderBy('name')
                            ->get();

            $names1 = DB::table('users')
                            ->distinct()
                            ->select('id','name','role_type')
                            ->where('role','=','Purchasing')
                            ->orderBy('role_type')
                            ->orderBy('name')
                            ->get();

            
            
            return view('/setting/tablepo', compact('names','users','names1'));

        }else{
            $users = DB::table('xalert_mstrs')
                ->leftJoin('xpo_control','xpo_control.supp_code','=','xalert_mstrs.xalert_supp')
                ->where('xalert_mstrs.xalert_active','=','Yes')
                ->groupBy('xalert_supp')
                ->paginate(10);
        
            $names = DB::table('users')
                            ->distinct()
                            ->select('id','name','role_type')
                            ->where('role','=','Purchasing')
                            ->orderBy('role_type')
                            ->orderBy('name')
                            ->get();

            $names1 = DB::table('users')
                            ->distinct()
                            ->select('id','name','role_type')
                            ->where('role','=','Purchasing')
                            ->orderBy('role_type')
                            ->orderBy('name')
                            ->get();
            //dd($users);

            $intv_general = DB::table('xpo_control')
                            ->where('supp_code','=','General')
                            ->first();

            if($intv_general == null){
                $intv_general = '0';
            }

            return view('/setting/poappcontrol', compact('names','users','names1','intv_general'));
        }

        
        //dd($users);
    }

    public function controlcreatenew(Request $req){
        //dd($req->all());

        try{
            // table po control
            if(count($req->appname) >= 0){
                foreach($req->appname as $item=>$v){
                    
                    $data2=array(
                        'xpo_approver'=>$req->appname[$item],
                        'min_amt'=>$req->min_amt[$item],
                        'max_amt'=>$req->max_amt[$item],
                    );                
                        DB::table('xpo_control')->insert($data2);
                }
            }

            // session()->flash("updated","Purchase Order control is successfully Created !");
            alert()->success('Success','Purchase Order Conbrol is Succesfully Created');
              
            return back();

        }catch(\InvalidArgumentException $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        }catch(\Exception $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        }catch(\Error $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }

    public function searchcontrol(Request $req){

        // dd($req->all());
        if($req->ajax()){
            $output="";
            $flg = 0;

            $users=DB::table("xpo_control")
                            ->join('users','xpo_control.xpo_approver','=','users.id')
                            //->join('xalert_mstrs','xpo_control.supp_code','=','xalert_mstrs.xalert_supp')
                            ->where("xpo_control.supp_code","=",$req->search)
                            ->get();

            $newdata = DB::table("users")
                    ->where("users.role","=",'Purchasing')
                    ->orderBy('role_type')
                    ->orderBy('name')
                    ->get();

            $data1 = DB::table("users")
                    ->where("users.role","=",'Purchasing')
                    ->orderBy('role_type')
                    ->orderBy('name')
                    ->get();

            // dd($users->all());

            if($users)
                {

                foreach ($users as $key => $users) {
                
                $output.= "<tr>".

                "<td>
                    <select id='suppname[]' class='form-control suppname' name='suppname[]' required autofocus>";
                    foreach($newdata as $data):
                        if($users->xpo_approver == $data->id):
                        $output .= '<option value='.$data->id.' Selected >'.$data->name.' - '.$data->role_type.'</option>';
                        else:
                        $output .= '<option value='.$data->id.' >'.$data->name.' - '.$data->role_type.'</option>';
                        endif;
                    endforeach;
                $output .= "</select>
                </td>".

                "<td> 
                    <input type='text' class='form-control minnbr' Autocomplete='Off' id='minamt[]' name='min_amt[]' style='height:38px' value='".$users->min_amt."' required/>
                </td>".


                "<td> 
                    <input type='text' class='form-control maxnbr' Autocomplete='Off' id='minamt[]' name='max_amt[]' style='height:38px' value='".$users->max_amt."' required/>
                </td>".


                "<td> 
                    <select id='altname[]' class='form-control altname' name='altname[]' required autofocus>";
                    foreach($data1 as $new):
                        if($users->xpo_alt_app == $new->id):
                        $output .= '<option value='.$new->id.' Selected>'.$new->name.' - '.$new->role_type.'</option>';
                        else:
                        $output .= '<option value='.$new->id.' >'.$new->name.' - '.$new->role_type.'</option>';
                        endif;
                    endforeach;
                $output .= "</select>
                </td>".


                "<td data-title='Action'><input type='button' class='ibtnDel btn btn-danger'  value='delete'></td>".


                "</tr>";

                $flg = $flg + 1;
                }
            return Response($output);
                } 
        }
    }

    public function editcontrol(Request $req){
        //dd($req->all());

        $item_days = $req->int_rem;
        $reapprove = $req->reapprove;

        /*
        if($req->suppname == null){
            session()->flash("error","Table Must Have at Least 1 Row");
            return back(); 
        }*/

        $suppname = $req->app_name;
        $suppname = substr($suppname, 0, strpos($suppname, ' '));
        $flg = 0;
        $listapprover = '';

        if(is_null($req->suppname)){

        }else{
            foreach($req->suppname as $data){
                $flg += 1;
            }   

            for($x = 0; $x < $flg; $x++){
                if($req->suppname[$x] == $req->altname[$x]){
                    // Altname == Approver kirim error
                    // session()->flash("error","Approver and Alternate Cannot be The Same ");
                    alert()->error('Error','Approver and Alternate Cannot be The Same');
                    return back(); 
                }

                if(strpos($listapprover,$req->suppname[$x]) !== false){
                    // Approver sama kirim error
                    // session()->flash("error","Approver cannot be the same");
                    alert()->error('Error','Approver cannot be the same');
                    return back(); 
                }

                if(!is_numeric($req->min_amt[$x]) or !is_numeric($req->max_amt[$x])){
                    // session()->flash("error","Min or Max value must be digit");
                    alert()->error('Error','Min or Max value must be digit');
                    return back(); 
                }

                $listapprover .= $req->suppname[$x];
            }
        }

        try{
            if($req->edit_id == 'General'){

                //Hapus Control buat General
                DB::table('xpo_control')
                        ->where('xpo_control.supp_code','=','General')
                        ->delete();
                
                if(is_null($req->suppname)){

                    // session()->flash("updated","Purchase order control successfully deleted for supplier : ".$suppname);
                    alert()->success('Success','Purchase order control successfully deleted for supplier : '.$suppname);
                    return back(); 
                    
                }else{
                    // Insert buat Control
                    if(count($req->suppname) >= 0){
                        foreach($req->suppname as $item=>$v){
                            $data2=array(
                                'supp_code'=>'General',
                                'xpo_approver'=>$req->suppname[$item],
                                'min_amt'=>$req->min_amt[$item],
                                'max_amt'=>$req->max_amt[$item],
                                'xpo_alt_app'=>$req->altname[$item],
                                'xpo_status'=>'UnConfirm',
                                'reapprove'=>$reapprove,
                                'intv_rem'=>$item_days,
                            );                
                                DB::table('xpo_control')->insert($data2);
                        }
                    }
                }

                // session()->flash("updated","PO Control Successfully Created For General");
                alert()->success('Success','PO Control Successfully Created For General');
                return back(); 

            }else{
                //Hapus Control buat Supplier itu
                DB::table('xpo_control')
                        ->where('xpo_control.supp_code','=',$suppname)
                        ->delete();

                if(is_null($req->suppname)){

                    // session()->flash("updated","Purchase order control successfully deleted for supplier : ".$suppname);
                    alert()->success('Success','Purchase order control successfully deleted for supplier : '.$suppname);
                    return back(); 

                }else{
                    // Insert buat Control
                    if(count($req->suppname) >= 0){
                        foreach($req->suppname as $item=>$v){
                            $data2=array(
                                'supp_code'=>$suppname,
                                'xpo_approver'=>$req->suppname[$item],
                                'min_amt'=>$req->min_amt[$item],
                                'max_amt'=>$req->max_amt[$item],
                                'xpo_alt_app'=>$req->altname[$item],
                                'xpo_status'=>'UnConfirm',
                                'reapprove'=>$reapprove,
                                'intv_rem'=>$item_days,
                            );                
                                DB::table('xpo_control')->insert($data2);
                        }
                    }
                }
                
            }

        }catch(\InvalidArgumentException $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        }catch(\Exception $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        }catch(\Error $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }




        // session()->flash("updated","Purchase order control successfully created for supplier : ".$suppname);
        alert()->success('Success','Purchase order control successfully created for supplier : '.$suppname);
        return back(); 
    }

    public function deleteuser(Request $req){
        //dd($req->all());
        $names = DB::table('xpo_control')
                        ->where('id','=',$req->delete_id)
                        ->delete();

        session()->flash("updated","Purchase order control successfully deleted !");
        alert()->success('Success','Purchase order control successfully deleted ');
        return back();
    }

    public function edituser(Request $req){
        $names = DB::table('xpo_control')
                        ->where('id','=',$req->edit_id)
                        ->update([
                            'min_amt' => $req->e_min_amt,
                            'max_amt' => $req->e_max_amt
                        ]);

        // session()->flash("updated","Purchase order control successfully updated !");
        alert()->success('Success','Purchase order control successfully updated');
        return back();
    }

    public function poappbrowse(Request $req){
        // dd('123');
        $now = Carbon::now('ASIA/JAKARTA')->toDateTimeString();

        $approver = DB::table("users")->get();

        if($req->ajax()){
            
            if(Session::get('user_role') == 'Admin'){

                // create temp table
                Schema::create('temp_table', function($table)
                {
                    $table->string('xpo_domain');
                    $table->string('xpo_nbr');
                    $table->date('xpo_ord_date');
                    $table->string('xpo_vend');
                    $table->string('xpo_ship');
                    $table->date('xpo_due_date');
                    $table->string('xpo_status');
                    $table->date('xpo_crt_date');
                    $table->string('xpo_app_approver');
                    $table->string('xpo_app_alt_approver');
                    $table->string('xpo_app_reason')->nullable();
                    $table->string('xpo_app_date')->nullable();
                    $table->string('xpo_app_status');
                    $table->string('xpo_app_order');
                    $table->string('name');
                    $table->string('xpo_total');
                    $table->timestamps();
                    $table->temporary();
                });

                // Ambil Nomor PO
                /*$data = DB::table("xpo_mstrs")
                            ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->whereRaw("(xpo_app_approver = '37' or xpo_app_alt_approver = '37')")
                            ->select('xpo_nbr')
                            ->distinct('xpo_nbr')
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                            ->get();*/ 
                // Ubah Table 24072020
                
                $data = DB::table("xpo_mstrs")
                            ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->whereRaw("(xpo_app_approver = '37' or xpo_app_alt_approver = '37')")
                            ->select('xpo_nbr')
                            ->distinct('xpo_nbr')
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                            ->get(); 
                
                // Masukin data ke temp_table
                foreach($data as $data){
                    /*$newdata = DB::table("xpo_mstrs")
                            ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->where('xpo_app_nbr','=',$data->xpo_nbr)
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                            ->get();*/

                    // Ubah Table 24072020
                    
                    $newdata = DB::table("xpo_mstrs")
                            ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->where('xpo_app_nbr','=',$data->xpo_nbr)
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                            ->get();
                    

                        foreach($newdata as $newdata){
                            $data1 = array(
                                    'xpo_domain'=>$newdata->xpo_domain,
                                    'xpo_nbr'=>$newdata->xpo_nbr,
                                    'xpo_ord_date'=>$newdata->xpo_ord_date,
                                    'xpo_vend'=>$newdata->xpo_vend,
                                    'xpo_ship'=>$newdata->xpo_ship,
                                    'xpo_due_date'=>$newdata->xpo_due_date,
                                    'xpo_status'=>$newdata->xpo_status,
                                    'xpo_crt_date'=>$newdata->xpo_crt_date,
                                    'xpo_app_approver'=>$newdata->xpo_app_approver,
                                    'xpo_app_alt_approver'=>$newdata->xpo_app_alt_approver,
                                    'xpo_app_reason'=>$newdata->xpo_app_reason,
                                    'xpo_app_date'=>$newdata->xpo_app_date,
                                    'xpo_app_status'=>$newdata->xpo_app_status, 
                                    'xpo_app_order'=>$newdata->xpo_app_order, 
                                    'xpo_total'=>$newdata->xpo_total,
                                    'name'=>$newdata->name,        
                                );

                            DB::table('temp_table')->insert($data1);    
                        }       
                }

                // get data 
                $users = DB::table('temp_table')
                            ->where('xpo_app_status','=','0')
                            ->orderBy('xpo_nbr','DESC')
                            ->orderBy('xpo_app_order','ASC')
                            ->groupBy('xpo_nbr')
                            ->paginate(10);

                // drop temp table
                Schema::drop('temp_table');

                return view('/po/tablepoapp', compact('users','now','approver'));

            }else if(Session::get('user_role') == 'Purchasing'){
                    // create temp table
                    Schema::create('temp_table', function($table)
                    {
                        $table->string('xpo_domain');
                        $table->string('xpo_nbr');
                        $table->date('xpo_ord_date');
                        $table->string('xpo_vend');
                        $table->string('xpo_ship');
                        $table->date('xpo_due_date');
                        $table->string('xpo_status');
                        $table->date('xpo_crt_date');
                        $table->string('xpo_app_approver');
                        $table->string('xpo_app_alt_approver');
                        $table->string('xpo_app_reason')->nullable();
                        $table->string('xpo_app_date')->nullable();
                        $table->string('xpo_app_status');
                        $table->string('xpo_app_order');
                        $table->string('name');
                        $table->string('xpo_total');
                        $table->timestamps();
                        $table->temporary();
                    });

                    // Ambil Nomor PO
                    /*$data = DB::table("xpo_mstrs")
                                ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->whereRaw("(xpo_app_approver = '".Session::get('userid')."' or xpo_app_alt_approver = '".Session::get('userid')."')")
                                ->select('xpo_nbr')
                                ->distinct('xpo_nbr')
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                                ->get(); */

                    // Ubah Table 24072020
                    
                    $data = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->whereRaw("(xpo_app_approver = '".Session::get('userid')."' or xpo_app_alt_approver = '".Session::get('userid')."')")
                                ->select('xpo_nbr')
                                ->distinct('xpo_nbr')
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->get(); 
                    

                    // Masukin data ke temp_table
                    foreach($data as $data){
                        /*$newdata = DB::table("xpo_mstrs")
                                ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->where('xpo_app_nbr','=',$data->xpo_nbr)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                                ->get();*/

                        // Ubah Table 24072020
                        
                        $newdata = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->where('xpo_app_nbr','=',$data->xpo_nbr)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->get();
                        

                            foreach($newdata as $newdata){
                                $data1 = array(
                                        'xpo_domain'=>$newdata->xpo_domain,
                                        'xpo_nbr'=>$newdata->xpo_nbr,
                                        'xpo_ord_date'=>$newdata->xpo_ord_date,
                                        'xpo_vend'=>$newdata->xpo_vend,
                                        'xpo_ship'=>$newdata->xpo_ship,
                                        'xpo_due_date'=>$newdata->xpo_due_date,
                                        'xpo_status'=>$newdata->xpo_status,
                                        'xpo_crt_date'=>$newdata->xpo_crt_date,
                                        'xpo_app_approver'=>$newdata->xpo_app_approver,
                                        'xpo_app_alt_approver'=>$newdata->xpo_app_alt_approver,
                                        'xpo_app_reason'=>$newdata->xpo_app_reason,
                                        'xpo_app_date'=>$newdata->xpo_app_date,
                                        'xpo_app_status'=>$newdata->xpo_app_status, 
                                        'xpo_app_order'=>$newdata->xpo_app_order,
                                        'name'=>$newdata->name,
                                        'xpo_total'=>$newdata->xpo_total,
                                    );

                                DB::table('temp_table')->insert($data1);    
                            }       
                    }

                    // get data 
                    $users = DB::table('temp_table')
                                ->where('xpo_app_status','=','0')
                                ->orderBy('xpo_nbr','DESC')
                                ->orderBy('xpo_app_order','ASC')
                                ->groupBy('xpo_nbr')
                                ->paginate(10);

                    // drop temp table
                    Schema::drop('temp_table');

                    return view('/po/tablepoapp', compact('users','now','approver'));
               
            }    

        }else{
            if(Session::get('user_role') == 'Admin'){

                // create temp table
                Schema::create('temp_table', function($table)
                {
                    $table->string('xpo_domain');
                    $table->string('xpo_nbr');
                    $table->date('xpo_ord_date');
                    $table->string('xpo_vend');
                    $table->string('xpo_ship');
                    $table->date('xpo_due_date');
                    $table->string('xpo_status');
                    $table->date('xpo_crt_date');
                    $table->string('xpo_app_approver');
                    $table->string('xpo_app_alt_approver');
                    $table->string('xpo_app_reason')->nullable();
                    $table->string('xpo_app_date')->nullable();
                    $table->string('xpo_app_status');
                    $table->string('xpo_app_order');
                    $table->string('name');
                    $table->string('xpo_total');
                    $table->timestamps();
                    $table->temporary();
                });

                // Ambil Nomor PO
                /*$data = DB::table("xpo_mstrs")
                            ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->select('xpo_nbr')
                            ->distinct('xpo_nbr')
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                            ->get(); */

                // Ubah Table 24072020
                
                $data = DB::table("xpo_mstrs")
                            ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->select('xpo_nbr')
                            ->distinct('xpo_nbr')
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                            ->get(); 
                // dd($data);
                
                // Masukin data ke temp_table
                foreach($data as $data){
                    /*$newdata = DB::table("xpo_mstrs")
                            ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->where('xpo_app_nbr','=',$data->xpo_nbr)
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                            ->get();*/

                    // Ubah Table 24072020
                    
                    $newdata = DB::table("xpo_mstrs")
                            ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                            ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                            ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                            ->where("xpo_app_status","<=",'2')
                            ->where('xpo_app_nbr','=',$data->xpo_nbr)
                            ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                            ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                            ->get();
                    

                        foreach($newdata as $newdata){
                            $data1 = array(
                                    'xpo_domain'=>$newdata->xpo_domain,
                                    'xpo_nbr'=>$newdata->xpo_nbr,
                                    'xpo_ord_date'=>$newdata->xpo_ord_date,
                                    'xpo_vend'=>$newdata->xpo_vend,
                                    'xpo_ship'=>$newdata->xpo_ship,
                                    'xpo_due_date'=>$newdata->xpo_due_date,
                                    'xpo_status'=>$newdata->xpo_status,
                                    'xpo_crt_date'=>$newdata->xpo_crt_date,
                                    'xpo_app_approver'=>$newdata->xpo_app_approver,
                                    'xpo_app_alt_approver'=>$newdata->xpo_app_alt_approver,
                                    'xpo_app_reason'=>$newdata->xpo_app_reason,
                                    'xpo_app_date'=>$newdata->xpo_app_date,
                                    'xpo_app_status'=>$newdata->xpo_app_status, 
                                    'xpo_app_order'=>$newdata->xpo_app_order, 
                                    'xpo_total'=>$newdata->xpo_total,
                                    'name'=>$newdata->name,        
                                );

                            DB::table('temp_table')->insert($data1);    
                        }       
                }

                // get data 
                $users = DB::table('temp_table')
                            ->where('xpo_app_status','=','0')
                            ->orderBy('xpo_nbr','DESC')
                            ->orderBy('xpo_app_order','ASC')
                            ->groupBy('xpo_nbr')
                            ->paginate(10);

                // drop temp table
                Schema::drop('temp_table');

                return view('/po/poappbrowse', compact('users','now','approver'));

            }else if(Session::get('user_role') == 'Purchasing'){
                    // create temp table
                    Schema::create('temp_table', function($table)
                    {
                        $table->string('xpo_domain');
                        $table->string('xpo_nbr');
                        $table->date('xpo_ord_date');
                        $table->string('xpo_vend');
                        $table->string('xpo_ship');
                        $table->date('xpo_due_date');
                        $table->string('xpo_status');
                        $table->date('xpo_crt_date');
                        $table->string('xpo_app_approver');
                        $table->string('xpo_app_alt_approver');
                        $table->string('xpo_app_reason')->nullable();
                        $table->string('xpo_app_date')->nullable();
                        $table->string('xpo_app_status');
                        $table->string('xpo_app_order');
                        $table->string('name');
                        $table->string('xpo_total');
                        $table->timestamps();
                        $table->temporary();
                    });

                    // Ambil Nomor PO
                    /*$data = DB::table("xpo_mstrs")
                                ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->whereRaw("(xpo_app_approver = '".Session::get('userid')."' or xpo_app_alt_approver = '".Session::get('userid')."')")
                                ->select('xpo_nbr')
                                ->distinct('xpo_nbr')
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                                ->get(); */

                    // Ubah Table 24072020
                    
                    $data = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->whereRaw("(xpo_app_approver = '".Session::get('userid')."' or xpo_app_alt_approver = '".Session::get('userid')."')")
                                ->select('xpo_nbr')
                                ->distinct('xpo_nbr')
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->get(); 
                    


                    //DD($data);


                    // Masukin data ke temp_table
                    foreach($data as $data){
                        /*$newdata = DB::table("xpo_mstrs")
                                ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->where('xpo_app_nbr','=',$data->xpo_nbr)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_hist.xpo_app_order','ASC')
                                ->get();*/

                        // Ubah Table 24072020
                        
                        $newdata = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","<=",'2')
                                ->where('xpo_app_nbr','=',$data->xpo_nbr)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->get();
                        

                            foreach($newdata as $newdata){
                                $data1 = array(
                                        'xpo_domain'=>$newdata->xpo_domain,
                                        'xpo_nbr'=>$newdata->xpo_nbr,
                                        'xpo_ord_date'=>$newdata->xpo_ord_date,
                                        'xpo_vend'=>$newdata->xpo_vend,
                                        'xpo_ship'=>$newdata->xpo_ship,
                                        'xpo_due_date'=>$newdata->xpo_due_date,
                                        'xpo_status'=>$newdata->xpo_status,
                                        'xpo_crt_date'=>$newdata->xpo_crt_date,
                                        'xpo_app_approver'=>$newdata->xpo_app_approver,
                                        'xpo_app_alt_approver'=>$newdata->xpo_app_alt_approver,
                                        'xpo_app_reason'=>$newdata->xpo_app_reason,
                                        'xpo_app_date'=>$newdata->xpo_app_date,
                                        'xpo_app_status'=>$newdata->xpo_app_status, 
                                        'xpo_app_order'=>$newdata->xpo_app_order,
                                        'name'=>$newdata->name,    
                                        'xpo_total'=>$newdata->xpo_total,    
                                    );

                                DB::table('temp_table')->insert($data1);    
                            }       
                    }

                    // get data 
                    $users = DB::table('temp_table')
                                ->where('xpo_app_status','=','0')
                                ->orderBy('xpo_nbr','DESC')
                                ->orderBy('xpo_app_order','ASC')
                                ->groupBy('xpo_nbr')
                                ->paginate(10);

                    // drop temp table
                    Schema::drop('temp_table');

                    return view('/po/poappbrowse', compact('users','now','approver'));
               
            }      
        }   
    }

    public function poappsearch(Request $req){
        $nbr = $req->nbr;
        $approver = $req->approver;
        $datefrom = $req->datefrom;
        $dateto = $req->dateto;
        $status = $req->status;
        $altapp = $req->altapp;

        // dd($req->all());

        if($req->ajax()){

            if($nbr == null && $approver == null and $datefrom == null and $dateto == null && $status == null && $altapp == null){
                
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin

                    // Ubah Table 24072020
                    
                    $users = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","=",'0')
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->groupBy('xpo_mstrs.xpo_nbr')
                                ->paginate(10); 
                    

                    return view('/po/tablepoapp',['users'=>$users]);
                }else{
                    // Ubah Table 24072020
                    
                    $users = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","=",'0')
                                ->where("xpo_mstrs.xpo_vend",'=',Session::get('supp_code'))
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->groupBy('xpo_mstrs.xpo_nbr')
                                ->paginate(10); 
                    

                    return view('/po/tablepoapp',['users'=>$users]);
                }
            }


            if($req->datefrom == null){
                $datefrom = '2000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                // ubah ke int
                $new_date_from = strtotime($new_format_date_from);

                // ubah ke format date
                $datefrom = date('Y-m-d',$new_date_from);
            }

            if($req->dateto == null){
                $dateto = '3000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_to = str_replace('/', '-', $req->dateto); 

                // ubah ke int
                $new_date_to = strtotime($new_format_date_to);

                // ubah ke format date
                $dateto = date('Y-m-d',$new_date_to);
            }


            try{

                $query = 'xpo_due_date >= "'.$datefrom.'" AND xpo_due_date <= "'.$dateto.'"';

                if($approver != null){
                    // Ubah Table 24072020
                     $query .= 'AND xpo_app_trans.xpo_app_approver = "'.$approver.'"';
                }

                if($status != null){
                    // Ubah Table 24072020
                     $query .= 'AND xpo_app_trans.xpo_app_status LIKE "'.$status.'%"';
                }

                if($nbr != null){
                    // Ubah Table 24072020
                     $query .= 'AND xpo_app_trans.xpo_app_nbr LIKE "'.$nbr.'%"';
                }

                if($altapp != null){
                    // Ubah Table 24072020
                     $query .= 'AND xpo_app_trans.xpo_app_alt_approver = "'.$altapp.'"';
                }


                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin

                    // Ubah Table 24072020
                    
                    $users = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","=",'0')
                                ->whereRaw($query)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->groupBy('xpo_mstrs.xpo_nbr')
                                ->paginate(10); 
                    

                    return view('/po/tablepoapp',['users'=>$users]);
                }else{
                    // Ubah Table 24072020
                    
                    $users = DB::table("xpo_mstrs")
                                ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where("xpo_mstrs.xpo_status","=",'UnConfirm')
                                ->where("xpo_app_status","=",'0')
                                ->where("xpo_mstrs.xpo_vend",'=',Session::get('supp_code'))
                                ->whereRaw($query)
                                ->orderBy('xpo_mstrs.xpo_nbr','DESC')
                                ->orderBy('xpo_app_trans.xpo_app_order','ASC')
                                ->groupBy('xpo_mstrs.xpo_nbr')
                                ->paginate(10); 
                    

                    return view('/po/tablepoapp',['users'=>$users]);
                }


            }catch(\InvalidArgumentException $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            }catch(\Exception $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            }catch(\Error $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }
            
        }
    }

    public function searchhistapp(Request $req){
        if($req->ajax()){
            $data1 = DB::table('xpo_app_hist')
                        ->where('xpo_app_nbr','=',$req->search)
                        ->where('xpo_app_status','<=','3')
                        ->get();

            $data = DB::select('select approver1.xpo_app_approver, approver1.name, approver1.xpo_app_alt_approver, users.name as  "nama", approver1.xpo_app_user, approver1.xpo_app_flag, approver1.xpo_app_reason, approver1.xpo_app_date , approver1.xpo_app_status,approver1.xpo_app_nbr,approver1.xpo_app_order
            from
            (select users.id, xpo_app_trans.xpo_app_approver, users.name,xpo_app_trans.xpo_app_alt_approver,                    xpo_app_trans.xpo_app_user,xpo_app_trans.xpo_app_flag,xpo_app_trans.xpo_app_reason,xpo_app_trans.xpo_app_date,xpo_app_trans.xpo_app_status, xpo_app_trans.xpo_app_nbr,xpo_app_trans.xpo_app_order
                                 from xpo_app_trans 
                                 join users 
                                 on xpo_app_trans.xpo_app_approver = users.id)approver1
                                 JOIN
                                 users on users.id = approver1.xpo_app_alt_approver
                                 where approver1.xpo_app_status <= 3
                                 AND approver1.xpo_app_nbr = "'.$req->search.'"
                                 UNION
                                select approver.xpo_app_approver, approver.name, approver.xpo_app_alt_approver, users.name as  "nama", approver.xpo_app_user, approver.xpo_app_flag, approver.xpo_app_reason, approver.xpo_app_date , approver.xpo_app_status,approver.xpo_app_nbr,approver.xpo_app_order
            from
            (select users.id, xpo_app_hist.xpo_app_approver, users.name,xpo_app_hist.xpo_app_alt_approver,                    xpo_app_hist.xpo_app_user,xpo_app_hist.xpo_app_flag,xpo_app_hist.xpo_app_reason,xpo_app_hist.xpo_app_date,xpo_app_hist.xpo_app_status, xpo_app_hist.xpo_app_nbr,xpo_app_hist.xpo_app_order
                                 from xpo_app_hist 
                                 join users 
                                 on xpo_app_hist.xpo_app_approver = users.id)approver
                                 JOIN
                                 users on users.id = approver.xpo_app_alt_approver
                                 where approver.xpo_app_status <= 3
                                 AND approver.xpo_app_nbr = "'.$req->search.'"
                                 ');

            // Udah Baca Ke History Bukan Transaksi

            $output = '';

            if(count($data) != 0){
                foreach($data as $data){

                    $dateapp = strtotime($data->xpo_app_date);

                    // ubah ke format date
                    $newdateapp = date('Y-m-d',$dateapp);

                     $output.= "<tr>".

                     "<td>"
                        .$data->xpo_app_order.
                    "</td>".

                    "<td>"
                        .$data->name.
                    "</td>".

                    "<td>"
                        .$data->nama.
                    "</td>".

                    "<td>"
                        .$data->xpo_app_user.
                    "</td>".

                    "<td>";
                    if($data->xpo_app_status == '3'):
                        $output .= 'History';
                    elseif($data->xpo_app_status == '2') :
                        $output .= 'Rejected';
                    elseif($data->xpo_app_status == '1') :
                        $output .= 'Approved';
                    endif;
                    $output .= "</td>".

                    "<td>"
                        .$data->xpo_app_reason.
                    "</td>".

                    "<td>".
                         $data->xpo_app_date.
                    "</td>".

                    /*
                    "<td>";
                    if($data->xpo_app_date != ''):
                        //$output .= $newdateapp;
                        $output = $data->xpo_app_date;
                    endif;
                    $output .= "</td>".
                    */

                    "</tr>";

                }
                return Response($output);
            } // IF

        } // Ajax
    } // Function

    public function searchdetailapppo(Request $req){
        if($req->ajax()){
            $detail = DB::table("xpo_mstrs")
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                        ->where('xpo_nbr','=',$req->search)
                        ->get();

            $output = '';
            foreach($detail as $detail){

                $total = (int)$detail->xpod_qty_ord * (int)$detail->xpod_price;

                $output .= '<tr>'.
                        '<td>'.$detail->xpod_line.'</td>'.
                        '<td>'.$detail->xpod_desc.'</td>'.
                        '<td>'.$detail->xpod_price.'</td>'.
                        '<td>'.$detail->xpod_qty_ord.'</td>'.
                        '<td>'.$total.'</td>'.
                        '</tr>';

            }
            return Response($output);
        }
    }

    public function approvepo(Request $req){
        //dd($req->all());
        switch ($req->input('action')) {
            case 'reject':
                $ponbr = $req->po_nbr;
                $apporder = $req->apporder;

                $users= DB::table('users')
                            ->where('users.id','=',Session::get('userid'))
                            ->first();
                
                DB::table('xpo_app_trans')
                    ->where('xpo_app_nbr','=',$req->po_nbr)
                    ->where('xpo_app_order','=',$apporder)
                    ->where('xpo_app_status','=','0')
                    ->update([
                            'xpo_app_status' => '2', // 0 Created, 1 Approved, 2 Reject, 3 History
                            'xpo_app_date' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                            'xpo_app_reason' => $req->e_reason,
                            'xpo_app_user' => $users->name
                    ]);
                
                // Data Dari Transaksi Dioper Ke History
                
                $data = DB::table('xpo_app_trans')
                            ->where('xpo_app_nbr','=',$req->po_nbr)
                            ->where('xpo_app_status','!=','0') // Yang ada action dipindain 
                            ->get();
                
                foreach($data as $data){
                    $inputandata = array(
                        'xpo_app_nbr' => $data->xpo_app_nbr,
                        'xpo_app_approver' => $data->xpo_app_approver,
                        'xpo_app_alt_approver' => $data->xpo_app_alt_approver,
                        'xpo_app_user' => $data->xpo_app_user,
                        'xpo_app_order' => $data->xpo_app_order,
                        'xpo_app_reason' => $data->xpo_app_reason,
                        'xpo_app_date' => $data->xpo_app_date,
                        'xpo_app_status' => $data->xpo_app_status,
                    );

                    DB::table('xpo_app_hist')->insert($inputandata);
                }
                
                DB::table('xpo_app_trans')
                            ->where('xpo_app_nbr','=',$req->po_nbr)
                            ->delete();

                DB::table('xpo_mstrs')
                    ->where('xpo_mstrs.xpo_nbr','=',$req->po_nbr)
                    ->update([
                        'xpo_mstrs.xpo_status' => 'Rejected'
                    ]);
                
                // email to purch
                $datapo = DB::table('xpo_mstrs')
                            ->where('xpo_mstrs.xpo_nbr','=',$req->po_nbr)
                            ->first();

                $email = DB::table('xalert_mstrs')
                        ->where('xalert_supp','=',$req->supplier)
                        ->first();        
                
                $array_email = explode(',', $email->xalert_not_pur);

                $com = DB::table('com_mstr')
                        ->first(); 

                $sendmail = (new EmailPoApproval(
                    'Following Purchase Order has been rejected :',
                    $ponbr,
                    $datapo->xpo_ord_date,
                    $datapo->xpo_due_date, 
                    number_format($datapo->xpo_total,2 ),
                    'Please check.',
                    $array_email,
                    $com->com_name,
                    $com->com_email))
                    ->delay(Carbon::now()->addSeconds(3));
                dispatch($sendmail);

                // Mail::send('email.emailapproval', 
                //     ['pesan' => 'Following Purchase Order has been rejected :',
                //      'note1' => $ponbr,
                //      'note2' => $datapo->xpo_ord_date, // Ord Date
                //      'note3' => $datapo->xpo_due_date, // Due Date
                //      'note4' => number_format($datapo->xpo_total,2 ), // Total
                //      'note5' => 'Please check.'], 
                //     function ($message) use ($ponbr,$array_email,$datapo,$com)
                // {
                //     $message->subject('PhD - Purchase Order Approval Rejected - '.$com->com_name);
                //     $message->from($com->com_email); // Email Admin Fix
                //     $message->to($array_email);
                // });
                
                $user = App\User::where('supp_id','=', $email->xalert_supp)->first(); // user siapa yang terima notif (lewat id)
                                      
                $details = [
                    'body' => 'Following Purchase Order has been rejected',
                    'url' => 'pobrowse',
                    'nbr' => $ponbr,
                    'note' => 'Please check'
                ]; // isi data yang dioper
                                    
                                
                $user->notify(new \App\Notifications\eventNotification($details));


                // session()->flash("updated","PO Number : ".$req->po_nbr." is Rejected");
                alert()->success('Success','PO Number : '.$req->po_nbr.' is Rejected');
                return redirect()->route('poappbrowse');
            break;

            case 'confirm':
                // Email ke Next Approver atau status confirm
                $ponbr = $req->po_nbr;
                //dd($req->all());    

                $users= DB::table('users')
                            ->where('users.id','=',Session::get('userid'))
                            ->first();

                /*$poapprove = DB::table('xpo_app_hist')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->where('xpo_app_order','=',$req->apporder)
                                ->update([
                                        'xpo_app_status' => '1', // 0 New, 1 Approve, 2 Reject, 3 History
                                        'xpo_app_reason' => $req->e_reason,
                                        'xpo_app_user' => $users->name,
                                        'xpo_app_date' => Carbon::now()->format('yy-m-d h:m:s'),
                                ]);*/
                // Ubah Table 24072020
                
                $poapprove = DB::table('xpo_app_trans')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->where('xpo_app_order','=',$req->apporder)
                                ->update([
                                        'xpo_app_status' => '1', // 0 New, 1 Approve, 2 Reject, 3 History
                                        'xpo_app_reason' => $req->e_reason,
                                        'xpo_app_user' => $users->name,
                                        'xpo_app_date' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                ]);
                


                // Create Email
                /*$nextapprover = DB::table('xpo_app_hist')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_approver')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->select('users.id','name','email','xpo_app_nbr','xpo_app_order')
                                ->orderBy('xpo_app_order')
                                ->first();

                $nextaltapprover = DB::table('xpo_app_hist')
                                ->join('users','users.id','=','xpo_app_hist.xpo_app_alt_approver')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->select('users.id','name','email','xpo_app_nbr','xpo_app_order')
                                ->orderBy('xpo_app_order')
                                ->first();*/
                // Ubah Table 24072020
                
                $nextapprover = DB::table('xpo_app_trans')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_approver')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->select('users.id','name','email','xpo_app_nbr','xpo_app_order', 'xpo_app_approver')
                                ->orderBy('xpo_app_order')
                                ->first();

                $nextaltapprover = DB::table('xpo_app_trans')
                                ->join('users','users.id','=','xpo_app_trans.xpo_app_alt_approver')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->where('xpo_app_status','=','0')
                                ->select('users.id','name','email','xpo_app_nbr','xpo_app_order', 'xpo_app_alt_approver')
                                ->orderBy('xpo_app_order')
                                ->first();
                

                if($nextapprover == null){
                    // Kosong ubah PO jadi Confirm
                    DB::table('xpo_mstrs')
                            ->where('xpo_nbr','=',$ponbr)
                            ->update([
                                'xpo_status' => 'Approved'
                            ]);

                    // 24072020 -- Pindahin Table 
                        $data = DB::table('xpo_app_trans')
                                ->where('xpo_app_nbr','=',$ponbr)
                                ->get();
                        foreach($data as $data){
                            $inputandata = array(
                                'xpo_app_nbr' => $data->xpo_app_nbr,
                                'xpo_app_approver' => $data->xpo_app_approver,
                                'xpo_app_alt_approver' => $data->xpo_app_alt_approver,
                                'xpo_app_user' => $data->xpo_app_user,
                                'xpo_app_order' => $data->xpo_app_order,
                                'xpo_app_reason' => $data->xpo_app_reason,
                                'xpo_app_date' => $data->xpo_app_date,
                                'xpo_app_status' => $data->xpo_app_status,
                            );

                            DB::table('xpo_app_hist')->insert($inputandata);
                        }
                        
                        DB::table('xpo_app_trans')
                                    ->where('xpo_app_nbr','=',$req->po_nbr)
                                    ->delete();

                    // Create Histroy 
                    $data = DB::table('xpo_mstrs')
                                ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                                ->where('xpod_nbr','=',$ponbr)
                                ->get();
                    //dd($data);
                    $vendor = '';

                    foreach($data as $data){
                        DB::table('xpo_hist')
                                ->insert([
                                        'xpo_domain' => $data->xpo_domain,
                                        'xpo_nbr' => $data->xpo_nbr,
                                        'xpo_line' => $data->xpod_line,
                                        'xpo_part' => $data->xpod_part,
                                        'xpo_desc' => $data->xpod_desc,
                                        'xpo_um' => $data->xpod_um,
                                        'xpo_qty_ord' => $data->xpod_qty_ord,
                                        'xpo_qty_rcvd' => $data->xpod_qty_rcvd,
                                        'xpo_qty_open' => $data->xpod_qty_open,
                                        'xpo_qty_prom' => $data->xpod_qty_prom,
                                        'xpo_price' => $data->xpod_price,
                                        'xpo_loc' => $data->xpod_loc,
                                        'xpo_lot' => $data->xpod_lot,
                                        'xpo_due_date' => $data->xpo_due_date,
                                        'xpo_vend' => $data->xpo_vend,
                                        'xpo_status' => 'Confirm',
                                        'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                        'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                ]);
                        $vendor = $data->xpo_vend;
                    }


                    // Kirim email Supplier & purchasing
                    
                    $emailpurch = DB::table('xalert_mstrs')
                                    ->where('xalert_mstrs.xalert_supp','=',$vendor)
                                    ->first();  

                    $supp_po = DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$ponbr)
                                    ->first();

                    $listuser = DB::table('users')
                                    ->where('users.supp_id','=',$supp_po->xpo_vend)
                                    ->get();

                    $emailsupp = '';
                    foreach($listuser as $listuser){
                            $emailsupp .= $listuser->email.',';
                    }

                    $emailsupp .= $emailpurch->xalert_not_pur;

                    $array_email = explode(',', $emailsupp); 

                    $com = DB::table('com_mstr')
                        ->first(); 

                    $sendmail = (new EmailPoApproval(
                        'New Purchase Order available for you',
                        $ponbr,
                        $supp_po->xpo_ord_date,
                        $supp_po->xpo_due_date, 
                        number_format($supp_po->xpo_total,2 ),
                        'Please check.',
                        $array_email,
                        $com->com_name,
                        $com->com_email))
                        ->delay(Carbon::now()->addSeconds(3));
                    dispatch($sendmail);

                    // Mail::send('email.emailapproval', 
                    //     ['pesan' => 'New purchase order available for you',
                    //      'note1' => $ponbr,
                    //      'note2' => $supp_po->xpo_ord_date, // Ord Date
                    //      'note3' => $supp_po->xpo_due_date, // Due Date
                    //      'note4' => number_format($supp_po->xpo_total,2 ), // Total
                    //      'note5' => 'Please check. '], 
                    //     function ($message) use ($ponbr,$array_email,$supp_po,$com)
                    // {
                    //     $message->subject('PhD - New Purchase Order - '.$com->com_name);
                    //     $message->from($com->com_email); // Email Admin Fix
                    //     $message->to($array_email);
                    // });

                    $user = App\User::where('supp_id','=', $emailpurch->xalert_supp)->first();
                    $details = [
                        'body' => 'New purchase order available for you',
                        'url' => 'poreceipt',
                        'nbr' => $ponbr,
                        'note' => 'Please check'
                    ]; // isi data yang dioper
                                        
                                    
                    $user->notify(new \App\Notifications\eventNotification($details));

                    // session()->flash("updated","PO Number : ".$req->po_nbr." is Approved");
                    alert()->success('Success','PO Number : '.$req->po_nbr.' is Approved');
                    return redirect()->route('poappbrowse');

                }else{

                    // Tidak kosong masi ada next approver
                    $listemail = $nextapprover->email.','.$nextaltapprover->email;

                    $array_email = explode(',', $listemail); 

                    $supp_po = DB::table('xpo_mstrs')
                                    ->where('xpo_nbr','=',$ponbr)
                                    ->first();
                    $com = DB::table('com_mstr')
                        ->first(); 

                    $sendmail = (new EmailPoApproval(
                        'There is a PO awaiting for approval',
                        $ponbr,
                        $supp_po->xpo_ord_date,
                        $supp_po->xpo_due_date, 
                        number_format($supp_po->xpo_total,2 ),
                        'Please check.',
                        $array_email,
                        $com->com_name,
                        $com->com_email))
                        ->delay(Carbon::now()->addSeconds(3));
                    dispatch($sendmail);

                    // Mail::send('email.emailapproval', 
                    //     ['pesan' => 'There is a PO awaiting for approval',
                    //      'note1' => $ponbr,
                    //      'note2' => $supp_po->xpo_ord_date, // Ord Date
                    //      'note3' => $supp_po->xpo_due_date, // Due Date
                    //      'note4' => number_format($supp_po->xpo_total,2 ), // Total
                    //      'note5' => 'Please Check.'], 
                    //     function ($message) use ($ponbr,$array_email,$com)
                    // {
                    //     $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                    //     $message->from($com->com_email); // Email Admin Fix
                    //     $message->to($array_email);
                    // });

                    $user = App\User::where('id','=', $nextapprover->xpo_app_approver)->first(); // user siapa yang terima notif (lewat id)
                    $useralt = App\User::where('id','=', $nextaltapprover->xpo_app_alt_approver)->first();                 
                    $details = [
                        'body' => 'There is a PO awaiting for approval',
                        'url' => 'pobrowse',
                        'nbr' => $ponbr,
                        'note' => 'Please check'
                    ]; // isi data yang dioper
                                        
                                    
                    $user->notify(new \App\Notifications\eventNotification($details));
                    $useralt->notify(new \App\Notifications\eventNotification($details));


                    // session()->flash("updated","PO Number : ".$req->po_nbr." is Approved, Waiting For Next Approval");
                    alert()->success('Success','PO Number : '.$req->po_nbr.' is Approved, Waiting For Next Approval');
                    return redirect()->route('poappbrowse');
                }
            break;

            case 'close':
                return redirect()->route('poappbrowse');  
            break;

        }
    }

    // Menu baru
    public function viewmenupo(){

        if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){
            $unconfpo = DB::table('xpo_mstrs')
                            ->where('xpo_status','=','Approved')
                            ->count();
            $unapproved = DB::table('xpo_mstrs')
                            ->where('xpo_status','=','UnConfirm')
                            ->count();
            $shipmentconf = DB::table('xsj_mstr')
                            ->select(DB::raw('Count(*) as Total'))
                            ->where('xsj_status','!=','Closed')
                            ->first();
        }else{
            $unconfpo = DB::table('xpo_mstrs')
                            ->where('xpo_status','=','Approved')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->count();
            $unapproved = DB::table('xpo_mstrs')
                            ->where('xpo_status','=','UnConfirm')
                            ->where('xpo_vend','=',Session::get('supp_code'))
                            ->count();
            $shipmentconf = DB::table('xsj_mstr')
                            ->select(DB::raw('Count(*) as Total'))
                            ->where('xsj_status','!=','Closed')
                            ->where('xsj_supp','=',Session::get('supp_code'))
                            ->first();
        }
        
        //dd($unconfpo, $unapproved, $shipmentconf);

        return view('/po/pomenu',['unconfpo'=>$unconfpo,'unapproved'=>$unapproved,'shipmentconf'=>$shipmentconf]);
    }

    public function detailpoapp(Request $req,$id){

        $data = DB::table("xpo_mstrs")
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                        ->where('xpo_nbr','=',$id)
                        ->get();

        $nopo = DB::table("xpo_mstrs")
                        ->where('xpo_nbr','=',$id)
                        ->first();

        /*$approver = DB::table('xpo_mstrs')
                        ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                        ->where('xpo_mstrs.xpo_nbr','=',$id)
                        ->where('xpo_app_status','=','0')
                        ->orderBy('xpo_app_order','ASC')
                        ->first();*/
        // Ubah Table 24072020
        
        $approver = DB::table('xpo_mstrs')
                        ->join('xpo_app_trans','xpo_app_trans.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                        ->where('xpo_mstrs.xpo_nbr','=',$id)
                        ->where('xpo_app_status','=','0')
                        ->orderBy('xpo_app_order','ASC')
                        ->first();
        

        return view('/po/detailpoappbrowse',['data' => $data, 'nopo' => $nopo, 'approver' => $approver]);
    }

    public function resetapprove(Request $req){

        /*$data = DB::table('xpo_app_hist')
                ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                ->where('xpo_app_hist.xpo_app_status','=','2')
                ->paginate(10);*/

        // Ubah Table 24072020
        
        $data = DB::table('xpo_app_hist')
                ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                // ->where('xpo_app_hist.xpo_app_status','=','2')
                ->where('xpo_status',"=","Rejected")
                ->selectRaw('distinct(xpo_app_nbr),xpo_vend,xalert_nama,xpo_due_date,xpo_total,xpo_app_user')
                ->groupBy('xpo_app_nbr')
                ->paginate(10);
                
        // dd($data);

        if($req->ajax()){
            return view('/setting/tableresetapproval', ['data' => $data]);

        }else{
            return view('/setting/resetapproval', ['data' => $data]);

        }

    }

    public function searchresetapprove(Request $req){
        if($req->ajax())
        {
            $ponbr = $req->rfq;
            $supplier = $req->code;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            if($ponbr == null && $supplier == null and $datefrom == null and $dateto == null){
                
                /*$alert = DB::table('xpo_app_hist')
                            ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            ->where('xpo_app_hist.xpo_app_status','=','2')
                            ->paginate(10);*/

                // Ubah Table 24072020
                
                $alert = DB::table('xpo_app_hist')
                            ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            // ->where('xpo_app_hist.xpo_app_status','=','2')
                            ->where('xpo_status',"=","Rejected")
                            ->selectRaw('distinct(xpo_app_nbr),xpo_vend,xalert_nama,xpo_due_date,xpo_total,xpo_app_user')
                            ->groupBy('xpo_app_nbr')
                            ->paginate(10);
                
                return view('/setting/tableresetapproval',['data'=>$alert]);
                
            }


            if($req->datefrom == null){
                $datefrom = '2000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                // ubah ke int
                $new_date_from = strtotime($new_format_date_from);

                // ubah ke format date
                $datefrom = date('Y-m-d',$new_date_from);
            }

            if($req->dateto == null){
                $dateto = '3000-12-31';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_to = str_replace('/', '-', $req->dateto); 

                // ubah ke int
                $new_date_to = strtotime($new_format_date_to);

                // ubah ke format date
                $dateto = date('Y-m-d',$new_date_to);
            }

            try{
            $query = "  xpo_mstrs.xpo_due_date >= '".$datefrom."' 
                        AND xpo_mstrs.xpo_due_date <= '".$dateto."'";

            if($ponbr != null){
                $query .= " AND xpo_mstrs.xpo_nbr like '".$ponbr."%'";
            }
            if($supplier != null){
                $query .= " AND xpo_mstrs.xpo_vend like '".$supplier."%'";
            }

            /*$alert = DB::table('xpo_app_hist')
                            ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            ->where('xpo_app_hist.xpo_app_status','=','2')
                            ->whereRaw($query)
                            ->paginate(200);*/
            // Ubah Table 24072020
            
            $alert = DB::table('xpo_app_hist')
                            ->join('xpo_mstrs','xpo_mstrs.xpo_nbr','=','xpo_app_hist.xpo_app_nbr')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                            // ->where('xpo_app_hist.xpo_app_status','=','2')
                            ->where('xpo_status',"=","Rejected")
                            ->whereRaw($query)
                            ->selectRaw('distinct(xpo_app_nbr),xpo_vend,xalert_nama,xpo_due_date,xpo_total,xpo_app_user')
                            ->groupBy('xpo_app_nbr')
                            ->paginate(10);
            
            return view('/setting/tableresetapproval', ['data' => $alert]);

            }catch(\InvalidArgumentException $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            }catch(\Exception $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            }catch(\Error $ex){
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }
        }
    }


    public function resetpoapproval(Request $req){
        //dd($req->all());

        $pohead = DB::table('xpo_mstrs')
                    ->where('xpo_nbr','=',$req->t_ponbr)
                    ->first();

        $listapprover = DB::table('xpo_control')
                ->join('xpo_mstrs','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                ->join('users','xpo_control.xpo_approver','=','users.id')
                ->where('xpo_mstrs.xpo_nbr','=',$req->t_ponbr)
                ->whereRaw(''.str_replace(',', '.', $pohead->xpo_total).' >= min_amt and '.str_replace(',', '.', $pohead->xpo_total).'< max_amt')
                ->select('xpo_mstrs.xpo_nbr','xpo_control.xpo_approver','xpo_control.xpo_alt_app')
                ->orderBy('min_amt','ASC')
                ->get();
        
        // dd($listapprover);

        if(count($listapprover) == 0){
            // Pake General

            $general = DB::table('xpo_control')
                            ->where('supp_code','=','General')
                            ->whereRaw(''.str_replace(',', '.', $pohead->xpo_total).' >= min_amt and '.str_replace(',', '.', $pohead->xpo_total).'< max_amt')
                            ->orderBy('min_amt','ASC')
                            ->get();
            $i = 0;

            foreach($general as $general){
                $i++;

                $result[$i] = [
                    'xpo_app_nbr' => $req->t_ponbr,
                    'xpo_app_approver' => $general->xpo_approver,
                    'xpo_app_order' => $i, // urutan Approval
                    'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                    'xpo_app_alt_approver' => $general->xpo_alt_app
                ];
                DB::table('xpo_app_trans')->insert($result[$i]);
            } 
             db::table("xpo_mstrs")
                            ->where("xpo_nbr","=",$req->t_ponbr)
                            ->update([
                                "xpo_status" => "UnConfirm",
                            ]);
            // session()->flash("updated","Approval has been updated.");
            alert()->success('Success','Approval has been updated');
            return back();

        }else{
            // Pake Specific dri $listapprover
            $i = 0;

            foreach($listapprover as $listapprover){
                $i++;

                $result[$i] = [
                    'xpo_app_nbr' => $listapprover->xpo_nbr,
                    'xpo_app_approver' => $listapprover->xpo_approver,
                    'xpo_app_order' => $i, // urutan Approval
                    'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                    'xpo_app_alt_approver' => $listapprover->xpo_alt_app
                ];
                DB::table('xpo_app_trans')->insert($result[$i]);

            }  
            db::table("xpo_mstrs")
                ->where("xpo_nbr","=",$req->t_ponbr)
                ->update([
                    "xpo_status" => "UnConfirm",
                ]);

            // session()->flash("updated","Approval has been updated.");
            alert()->success('Success','Approval has been updated');
            return back();

        }
    }


    // Audit Trail
    public function poaudit(Request $req){
        $data = DB::table('xpo_hist')
                    ->where('xpo_hist.xpo_nbr','=','1')
                    ->get();
        return view('po.poaudit',['data' => $data]);
    }

    public function poauditsearch(Request $req){
        if($req->ajax()){
            $nbr = $req->nbr;

            $data = DB::table('xpo_hist')
                        ->where('xpo_hist.xpo_nbr','=',$req->nbr)
                        ->get();

            if($nbr == null){
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    $users = DB::table('xpo_hist')
                            ->where('xpo_hist.xpo_nbr','=','1')
                            ->get();
                    

                    return view('/po/tableauditpo',['data'=>$users]);
                }else{
                    $users = DB::table('xpo_hist')
                            ->where('xpo_hist.xpo_nbr','=','1')
                            ->get();
                    

                    return view('/po/tableauditpo',['data'=>$users]);
                }
            }

            $query = '';

            if($nbr != null){
                 $query .= 'xpo_hist.xpo_nbr LIKE "'.$nbr.'"';
            }

            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    
                    $users = DB::table('xpo_hist')
                                ->whereRaw($query)
                                ->get();
                    

                    return view('/po/tableauditpo',['data'=>$users]);
            }else{
                
                $users = DB::table('xpo_hist')
                                        ->where("xpo_mstrs.xpo_vend",'=',Session::get('supp_code'))
                                        ->whereRaw($query)
                                        ->get();
                

                return view('/po/tableauditpo',['data'=>$users]);
            }
                
        }
    }


    // Audit Trail PO Approval
    public function poappaudit(Request $req){
        /*$data = DB::table('xpo_app_hist')
                    ->get();*/
        $data = db::select('select approver1.id,approver1.xpo_app_order, approver1.xpo_app_approver, approver1.name, approver1.xpo_app_alt_approver, users.name as  "nama", approver1.xpo_app_user, approver1.xpo_app_flag, approver1.xpo_app_reason, approver1.xpo_app_date , approver1.xpo_app_status,approver1.xpo_app_nbr,approver1.appid
            from
            (select users.id, xpo_app_hist.xpo_app_approver, users.name,xpo_app_hist.xpo_app_alt_approver,xpo_app_hist.xpo_app_user,xpo_app_hist.xpo_app_flag,xpo_app_hist.xpo_app_reason,xpo_app_hist.xpo_app_date,xpo_app_hist.xpo_app_status, xpo_app_hist.xpo_app_nbr,xpo_app_hist.xpo_app_order,xpo_app_hist.id as "appid"
                                 from xpo_app_hist 
                                 join users 
                                 on xpo_app_hist.xpo_app_approver = users.id)approver1
                                 JOIN
                                 users on users.id = approver1.xpo_app_alt_approver
                                 where approver1.xpo_app_status <= 3
                                 and approver1.xpo_app_nbr = "123"
                                 ');

        return view('po.poappaudit',['data' => $data]);
    }

    public function poappauditsearch(Request $req){
        if($req->ajax()){
            $nbr = $req->nbr;

            if($nbr == null){
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    $users = db::select('select approver1.id,approver1.xpo_app_order, approver1.xpo_app_approver, approver1.name, approver1.xpo_app_alt_approver, users.name as  "nama", approver1.xpo_app_user, approver1.xpo_app_flag, approver1.xpo_app_reason, approver1.xpo_app_date , approver1.xpo_app_status,approver1.xpo_app_nbr,approver1.appid
                            from
                            (select users.id, xpo_app_hist.xpo_app_approver, users.name,xpo_app_hist.xpo_app_alt_approver,xpo_app_hist.xpo_app_user,xpo_app_hist.xpo_app_flag,xpo_app_hist.xpo_app_reason,xpo_app_hist.xpo_app_date,xpo_app_hist.xpo_app_status, xpo_app_hist.xpo_app_nbr,xpo_app_hist.xpo_app_order,xpo_app_hist.id as "appid"
                                                 from xpo_app_hist 
                                                 join users 
                                                 on xpo_app_hist.xpo_app_approver = users.id)approver1
                                                 JOIN
                                                 users on users.id = approver1.xpo_app_alt_approver
                                                 where approver1.xpo_app_status <= 3
                                                 and approver1.xpo_app_nbr = "123"
                                                 ');
                    

                    return view('/po/tableauditpoapp',['data'=>$users]);
                }else{
                    $users = db::select('select approver1.id,approver1.xpo_app_order, approver1.xpo_app_approver, approver1.name, approver1.xpo_app_alt_approver, users.name as  "nama", approver1.xpo_app_user, approver1.xpo_app_flag, approver1.xpo_app_reason, approver1.xpo_app_date , approver1.xpo_app_status,approver1.xpo_app_nbr,approver1.appid
                            from
                            (select users.id, xpo_app_hist.xpo_app_approver, users.name,xpo_app_hist.xpo_app_alt_approver,xpo_app_hist.xpo_app_user,xpo_app_hist.xpo_app_flag,xpo_app_hist.xpo_app_reason,xpo_app_hist.xpo_app_date,xpo_app_hist.xpo_app_status, xpo_app_hist.xpo_app_nbr,xpo_app_hist.xpo_app_order,xpo_app_hist.id as "appid"
                                                 from xpo_app_hist 
                                                 join users 
                                                 on xpo_app_hist.xpo_app_approver = users.id)approver1
                                                 JOIN
                                                 users on users.id = approver1.xpo_app_alt_approver
                                                 where approver1.xpo_app_status <= 3
                                                 and approver1.xpo_app_nbr = "123"
                                                 ');
                    

                    return view('/po/tableauditpoapp',['data'=>$users]);
                }
            }

            $query = '';

            if($nbr != null){
                 $query .= 'xpo_hist.xpo_nbr LIKE "'.$nbr.'"';
            }
                    
            $users = db::select('select approver1.id,approver1.xpo_app_order, approver1.xpo_app_approver, approver1.name, approver1.xpo_app_alt_approver, users.name as  "nama", approver1.xpo_app_user, approver1.xpo_app_flag, approver1.xpo_app_reason, approver1.xpo_app_date , approver1.xpo_app_status,approver1.xpo_app_nbr,approver1.appid
                    from
                    (select users.id, xpo_app_hist.xpo_app_approver, users.name,xpo_app_hist.xpo_app_alt_approver,xpo_app_hist.xpo_app_user,xpo_app_hist.xpo_app_flag,xpo_app_hist.xpo_app_reason,xpo_app_hist.xpo_app_date,xpo_app_hist.xpo_app_status, xpo_app_hist.xpo_app_nbr,xpo_app_hist.xpo_app_order,xpo_app_hist.id as "appid"
                                         from xpo_app_hist 
                                         join users 
                                         on xpo_app_hist.xpo_app_approver = users.id)approver1
                                         JOIN
                                         users on users.id = approver1.xpo_app_alt_approver
                                         where approver1.xpo_app_status <= 3
                                         and approver1.xpo_app_nbr = "'.$nbr.'"
                                         ');
            

                    return view('/po/tableauditpoapp',['data'=>$users]);
                
        }
    }   


    // 2 Function Dibawah Buat Testing Tidak Dipakai di live
    public function temp_check(){



        // Excel::import(new POImport, public_path('pomstr.csv'));
        
        // create temp table
        /*Schema::create('temp_table', function($table)
        {
            $table->string('xpo_domain');
            $table->string('xpo_nbr');
            $table->date('xpo_ord_date');
            $table->string('xpo_vend');
            $table->string('xpo_ship');
            $table->string('xpo_curr');
            $table->date('xpo_due_date');
            $table->timestamps();
            $table->temporary();
        });*/
        
        //Excel::import(new PODetImport, public_path('poddet.csv'));
        //dd('sukses');

        // Open CSV File n Read
        $file = fopen(public_path('poddet.csv'),"r");

        $importData_arr = array();
          $i = 0;

          while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
             $num = count($filedata );
             
             // Skip first row (Remove below comment if you want to skip the first row)
             /*if($i == 0){
                $i++;
                continue; 
             }*/
             for ($c=0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata [$c];
             }
             $i++;
          }
          fclose($file);

        //dd($importData_arr);

        // Insert to MySQL database
        foreach($importData_arr as $importData){
            if($importData[10] == null ){
                $newdate = null;
            }else{
                $date = date_create_from_format('d/m/Y', $importData[10]);
                $newdate = $date->format('Y-m-d');
            }

            //dd($newdate);

            DB::table('xpod_dets')->updateOrInsert(
                ['xpod_domain' => $importData[0], 'xpod_nbr' => $importData[1], 'xpod_line' => $importData[2] ],
                ['xpod_part' => $importData[3], 
                 'xpod_desc' => $importData[4],
                 'xpod_um' => $importData[5],
                 'xpod_qty_ord' => $importData[6],
                 'xpod_qty_rcvd' => '0',
                 'xpod_qty_open' => $importData[6],
                 'xpod_qty_prom' => $importData[6],
                 'xpod_price' => $importData[7],
                 'xpod_loc' => $importData[8],
                 'xpod_lot' => $importData[9],
                 'xpod_date' => $newdate,
                 'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                 'updated_at' =>  Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                 'xpod_status' => 'Created'
             ]);

        }

        // get data 
        //$data = DB::table('temp_table')->get();

        // drop temp table
        //Schema::drop('temp_table');

        dd('Berhasil');
    }

    public function testing(\CheckBudget $service){
        // log::channel('errorpo')->info('test123');
        // log::channel('shippay')->info('test123');

        // dd('123');

        $altapp = DB::table('users')
                ->groupBy('users.id');

        $mainapp =  DB::table('approver_budget')
                        ->join('users','approver_budget.approver_budget','=','users.id')
                        ->joinSub($altapp, 'altapp', function($join){
                            $join->on('approver_budget.alt_approver_budget','=','altapp.id');
                        })
                        ->select('approver_budget.approver_budget',
                                'users.email',
                                'approver_budget.alt_approver_budget',
                                'altapp.email as emailalt')
                        ->get();
        
        // dd($mainapp);

        $services = $service->loadWSA();
        $listpo = $service->listPO()->where('ponbr','TESWS7');

        if($listpo->count() > 0){
            
        }

        if($services){
            foreach($services as $data){
                if($data->used_budget > $data->total_budget){
                    // Melebihi Budget kirim approval khusus

                }

            }
            // $cek_service = $service->where('po_nbr','TESWSA1')->get();

            dd($services,$listpo);
        }else{
            // returns false dari service
            dd('tidak ada data');
        }

        
        
    
    }    

}

