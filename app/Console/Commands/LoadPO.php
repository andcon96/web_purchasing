<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Jobs\EmailPoApproval;
use App\Notifications\eventNotification;
use App;
use Log;

use Illuminate\Console\Command;

class LoadPO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:po';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Purchase Order Daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
                        // ada data
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
                                    DB::table('xpo_mstrs')
                                        ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                        ->update([
                                            'xpo_app_flg' => '1',
                                            'xpo_status' => 'UnConfirm'
                                        ]);

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

                                    if($general->count() > 0){
                                        DB::table('xpo_mstrs')
                                            ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                            ->update([
                                                'xpo_app_flg' => '1',
                                                'xpo_status' => 'UnConfirm'
                                            ]);

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

                            $alertpo = DB::Table('xalert_mstrs')
                                ->where('xalert_mstrs.xalert_supp','=',$dataloop->t_vend)
                                ->first();

                            // buat subquery alt approver
                            $altapp = DB::table('users')
                                ->groupBy('users.id');

                            if($alertpo->xalert_po_app == 'Yes'){
                                // Butuh Approval --> cek General / Specific

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
                                // Masukin Table Approval & Email
                                if($mainapp->count() > 0){
                                    // Butuh Approval Specific
                                    DB::table('xpo_mstrs')
                                        ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                        ->update([
                                            'xpo_app_flg' => '1',
                                            'xpo_status' => 'UnConfirm'
                                        ]);

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
                                        DB::table('xpo_mstrs')
                                            ->where('xpo_mstrs.xpo_nbr','=',$dataloop->t_nbr)
                                            ->update([
                                                'xpo_app_flg' => '1',
                                                'xpo_status' => 'UnConfirm'
                                            ]);

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
}
