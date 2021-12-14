<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;
use App;
use Illuminate\Support\Facades\Schema;
use App\Jobs\EmailRFP;

class QxCimController extends Controller
{

    // RFQ -> PO
    public function purchupdate(Request $req){
        //dd($req->all());
        switch ($req->input('action')) {

            case 'reject':
                $rfqnbr = $req->d_rfqnbr;
                $suppid = $req->d_suppid;

                DB::table('xbid_det')
                    ->where('xbid_det.xbid_id','=',$rfqnbr)
                    ->where('xbid_det.xbid_supp','=',$suppid)
                    ->update([
                        'xbid_det.xbid_flag'=>'3' // 0 Dibuat Purch, 1 Dibid Supplier, 2 Diapprove Purch, 3 Direject Purch 
                    ]);


                $data = DB::Table('xbid_det')
                    ->join('xbid_mstr','xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->where('xbid_det.xbid_id','=',$rfqnbr)
                    ->where('xbid_det.xbid_supp','=',$suppid)
                    ->first();

                DB::table('xbid_hist')
                        ->insert([
                            'xbid_nbr'=>$data->xbid_id,
                            'xbid_qty_req'=>$data->xbid_qty,
                            'xbid_due_date'=>$data->xbid_date,
                            'xbid_part'=>$data->xbid_part,
                            'xbid_price_min'=>$data->xbid_price_min,
                            'xbid_price_max'=>$data->xbid_price_max,
                            'xbid_pro_qty' => $data->xbid_pro_qty,
                            'xbid_pro_date' => $data->xbid_pro_date,
                            'xbid_pro_price' => $data->xbid_pro_price,
                            'xbid_flag' => '3', // Reject by Purchasing
                            'xbid_hist_remarks'=>'RFQ Rejected by Purchasing',
                            'xbid_supp'=>$data->xbid_supp,
                            'xbid_site'=>$data->xbid_site,
                            'xbid_start_date'=>$data->xbid_start_date,
                            'xbid_um'=>$data->xbid_um,
                            'xbid_desc'=>$data->xbid_desc,
                        ]);

                // session()->flash("updated","RFQ No. : ".$rfqnbr." is Closed");
                alert()->success('Success','RFQ No. : '.$rfqnbr.' is Closed');
                return back();
                break;

            case 'confirm':
                $flg = $req->input('closerfq');
                $id = $req->input('edit_id');
                $purqty = $req->input('purqty');
                $old_purdate = $req->input('purdate');
                $rfqnbr = $req->input('m_rfqnbr');
                $qtyreq = str_replace(',', '', $req->input('m_qtyreq'));
                $duedate = $req->input('m_duedate');
                $itemcode = $req->input('m_itemcode');
                $pricemin = str_replace(',', '', $req->input('m_pricemin'));
                $pricemax = str_replace(',', '', $req->input('m_pricemax'));
                $proqty = str_replace(',', '', $req->input('m_proqty'));
                $prodate = $req->input('m_prodate');
                $proprice = str_replace(',', '', $req->input('m_proprice'));
                $suppid = $req->input('suppid');
                $site =$req->input('rfqsite');
                $startdate = $req->input('startdate');
                $notepurch = $req->input('m_note_purch');

                $decimal_purqty = number_format($purqty,2);

                $convertpo = $req->input('convert'); // 1 PO, 2 PR
                $createnew = $req->input('createnew');
                $linkpo = $req->input('linkpo');

                $itemdesc = '';
                if($req->m_itemdesc != null){
                    $itemdesc = $req->m_itemdesc;
                }

                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_pur_date = str_replace('/', '-', $old_purdate);
                
                // ubah ke int
                $new_pur_date = strtotime($new_format_pur_date);
                $new_due_date = strtotime($duedate);
                
                // ubah ke format date
                $purdate = date('Y-m-d',$new_pur_date);
                $cimduedate = date('d/m/y',$new_due_date);
                

                try{
                    $getpo = DB::table('xrfq_mstrs')
                                ->first();
                    $datenow = Carbon::now()->format('ym');

                    $new_no_po = $getpo->xrfq_po_prefix.$getpo->xrfq_po_nbr;
                    $new_no_pr = $getpo->xrfq_pr_prefix.$getpo->xrfq_pr_nbr;
                    

                    // Purchasing Confirm ---- Update Flag cma di Det
                    DB::table('xbid_det')
                        ->where('xbid_det_id', $id)
                        ->update([
                                'xbid_pur_qty' => $purqty,
                                'xbid_pur_date' => $purdate,
                                'xbid_no_po' => $new_no_po,
                                'xbid_flag' => '2', // 0 Dibuat Purch, 1 Dibid Supplier, 2 Diapprove Purch
                        ]);

                    // Tutup RFQ Mstr ga bisa input supp baru + tutup smua detail buat rfq itu
                    if($flg == 'Yes'){
                        
                        DB::table('xbid_mstr')
                                ->where('xbid_id',$rfqnbr)
                                ->update([
                                        'xbid_flag' => '2' // 0 Dibuat, 1 Supplier ada input , 2 Closed
                                ]);


                        DB::table('xbid_det')
                                ->where('xbid_id',$rfqnbr)
                                ->update([
                                    'xbid_flag' => '4' // 0 Dibuat Purch, 1 Dibid Supplier, 2 Diapprove Purch, 3 Direject Purch, 4 RFQ Closed by purchasing 
                                ]);

                        $validatehist = DB::table('xbid_det')
                                        ->where('xbid_id',$rfqnbr)
                                        ->where('xbid_supp','!=',$suppid)
                                        ->get();

                        foreach($validatehist as $validatehist){
                            DB::table('xbid_hist')
                                    //->where('xbid_nbr',$rfqnbr)
                                    //->where('xbid_supp','!=',$suppid)
                                    ->insert([
                                        'xbid_nbr'=>$rfqnbr,
                                        'xbid_qty_req'=>$qtyreq,
                                        'xbid_due_date'=>$duedate,
                                        'xbid_part'=>$itemcode,
                                        'xbid_price_min'=>$pricemin,
                                        'xbid_price_max'=>$pricemax,
                                        'xbid_pro_qty' => $proqty,
                                        'xbid_pro_date' => $prodate,
                                        'xbid_pro_price' => $proprice,
                                        'xbid_pur_qty' => $purqty,
                                        'xbid_pur_date' => $purdate,
                                        'xbid_flag' => '4',
                                        'xbid_hist_remarks'=>'Closed By Purchasing',
                                        'xbid_supp'=>$validatehist->xbid_supp,
                                        'xbid_site'=>$site,
                                        'xbid_start_date'=>$startdate,
                                        'xbid_no_po'=>$new_no_po,
                                        'xbid_desc'=>$itemdesc,
                                    ]);   
                        }

                    }

                    // Create Hist
                    $data3=array(
                            'xbid_nbr'=>$rfqnbr,
                            'xbid_qty_req'=>$qtyreq,
                            'xbid_due_date'=>$duedate,
                            'xbid_part'=>$itemcode,
                            'xbid_price_min'=>$pricemin,
                            'xbid_price_max'=>$pricemax,
                            'xbid_pro_qty' => $proqty,
                            'xbid_pro_date' => $prodate,
                            'xbid_pro_price' => $proprice,
                            'xbid_pur_qty' => $purqty,
                            'xbid_pur_date' => $purdate,
                            'xbid_flag' => '2',
                            'xbid_hist_remarks'=>'Purchasing Approve Propose',
                            'xbid_supp'=>$suppid,
                            'xbid_site'=>$site,
                            'xbid_start_date'=>$startdate,
                            'xbid_no_po'=>$new_no_po,
                            'xbid_desc'=>$itemdesc,
                            );                
                    DB::table('xbid_hist')->insert($data3);
                    

                    // Create PO / PR  --> Updated 19/10/2020
                    if($convertpo == '1'){ 
                        // Create CIM
                        //dd('PO');
                        $line = '1'; // pasti 1 karena generate PO baru

                        $content = '';
                        $content .= '"'.$new_no_po.'"'.PHP_EOL;
                        $content .= '"'.$suppid.'"'.PHP_EOL;
                        $content .= '"'.$suppid.'"'.PHP_EOL;
                        //$content .= Carbon::now()->format('d/m/y').' '.$cimduedate.' '.'-'.' '.'"'.$suppid.'"'.' "'.$rfqnbr.'" - - '.'"'.$notepurch.'"'.' - - - '.'"'.$site.'"'.' - '.'yes '.'no '.'- - - - - - - - - no'.PHP_EOL;
                        $content .= Carbon::now()->format('d/m/y').' '.$cimduedate.' - - - "'.$rfqnbr.'" - - - - - "'.$site.'" - - yes no - - - - - - - - - - no'.PHP_EOL;
                        //$content .= '- - - - -'.PHP_EOL;
                        $content .= '- - - - no'.PHP_EOL;
                        $content .= $line.PHP_EOL;
                        $content .= '"'.$site.'"'.PHP_EOL;
                        $content .= '- -'.PHP_EOL;
                        $content .= '"'.$itemcode.'"'.PHP_EOL;
                        $content .= $purqty. '-'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        $content .= $proprice.' -'.PHP_EOL;
                        // Khusus Memo
                        if($req->m_itemdesc != null){
                            $content .= '- -'.PHP_EOL;
                            $content .= '- -'.PHP_EOL;
                            $content .= '- - - - - - "Item not inventory" '.$cimduedate.' - - - - - - - - - - - no no - no'.PHP_EOL;
                        }else{
                            $content .= '- - - - - '.$cimduedate.' - - - - - - - - - - - no no - no'.PHP_EOL;
                        }
                        $content .= '-'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        $content .= '.';

                        File::put('cim/xxcimpo.cim',$content);

                        // buat jalanin sim ke QAD
                        exec("start cmd /c cimrfqpo.bat");


                        $new_po_nbr = Str::substr($new_no_po, strlen($new_no_po) - 6, 6);
                        $int_po_nbr = (int)$new_po_nbr + 1;

                        //dd($new_rfq_nbr);
                        
                        if($int_po_nbr < 10 ){
                            $string_po_nbr = strval("00000".$int_po_nbr);
                        }else if($int_po_nbr < 100 & $int_po_nbr >= 10){
                            $string_po_nbr = strval("0000".$int_po_nbr);
                        }else if($int_po_nbr < 1000 & $int_po_nbr >= 100){
                            $string_po_nbr = strval("000".$int_po_nbr);
                        }else if($int_po_nbr < 10000 & $int_po_nbr >= 1000){
                            $string_po_nbr = strval("00".$int_po_nbr);
                        }else if($int_po_nbr < 100000 & $int_po_nbr >= 10000){
                            $string_po_nbr = strval("0".$int_po_nbr);
                        }else{
                            $string_po_nbr = strval($int_po_nbr);
                        }

                        // update next po nbr
                        DB::table('xrfq_mstrs')
                            ->update([
                                'xrfq_po_nbr' => $string_po_nbr
                            ]);

                        // session()->flash("updated","PO is successfully created, PO No. : ".$new_no_po);
                        alert()->success('Success','PO is Succesfully created, PO No. '.$new_no_po);
                          
                        return back();
                    
                    }else if($convertpo == '2'){
                        // PR --> WSA Kirim ke QAD
                        // dd('PR');

                        $line = '1'; // pasti 1 karena generate PO baru

                        $content = '';
                        $content .= '"'.$new_no_pr.'"'.PHP_EOL;
                        $content .= '"'.$suppid.'"'.PHP_EOL;
                        $content .= '"'.$suppid.'"'.PHP_EOL;
                        $content .= Carbon::now()->format('d/m/y').' - '.$cimduedate.' - "mfg" '.$rfqnbr.' "'.$notepurch.'"'.' "" "'.$site.'" - - - - - - - - - no'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        $content .= '"'.$line.'"'.PHP_EOL;
                        $content .= '"'.$site.'"'.PHP_EOL;
                        $content .= '"'.$itemcode.'"'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        $content .= '- -'.PHP_EOL;
                        $content .= $purqty.' -'.PHP_EOL;
                        $content .= $proprice.' -'.PHP_EOL;
                        $content .= $cimduedate.' - - - - - - - - - - - - no'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        //$content .= '"S"'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '.'.PHP_EOL;

                        File::put('cim/xxcimpr.cim',$content);

                        exec("start cmd /c cimrfqpr.bat");

                        $new_pr_nbr = Str::substr($new_no_pr, strlen($new_no_pr) - 6, 6);
                        $int_pr_nbr = (int)$new_pr_nbr + 1;

                        //dd($new_rfq_nbr);
                        
                        if($int_pr_nbr < 10 ){
                            $string_pr_nbr = strval("00000".$int_pr_nbr);
                        }else if($int_pr_nbr < 100 & $int_pr_nbr >= 10){
                            $string_pr_nbr = strval("0000".$int_pr_nbr);
                        }else if($int_pr_nbr < 1000 & $int_pr_nbr >= 100){
                            $string_pr_nbr = strval("000".$int_pr_nbr);
                        }else if($int_pr_nbr < 10000 & $int_pr_nbr >= 1000){
                            $string_pr_nbr = strval("00".$int_pr_nbr);
                        }else if($int_pr_nbr < 100000 & $int_pr_nbr >= 10000){
                            $string_pr_nbr = strval("0".$int_pr_nbr);
                        }else{
                            $string_pr_nbr = strval($int_pr_nbr);
                        }

                        // update next po nbr
                        DB::table('xrfq_mstrs')
                            ->update([
                                'xrfq_pr_nbr' => $string_pr_nbr
                            ]);

                        // session()->flash("updated","PR is successfully Created, PR No. : ".$new_no_pr);
                        alert()->success('Success','PR is successfully Created, PR No. : '.$new_no_pr);
                          
                        return back();

                    }else if($convertpo == '3'){
                        // insert ke table pur plan

                        DB::table('xpurplan_mstrs')
                                ->insert([
                                    'rf_number' => $req->m_rfqnbr,
                                    'supp_code' => $req->suppid,
                                    'due_date' => $req->m_duedate,
                                    'site' => $req->rfqsite,
                                    'propose_date' => $req->m_prodate,
                                    'rf_from' => '1', // 1 = RFQ , 2 RFP
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString()
                                ]);

                        DB::table('xpurplan_dets')
                                ->insert([
                                    'rf_number' => $req->m_rfqnbr,
                                    'supp_code' => $req->suppid,
                                    'line' => '1', // pasti cuma 1 line
                                    'item_code' => $req->m_itemcode,
                                    'qty_req' => $req->m_qtyreq,
                                    'qty_pro' => $req->m_proqty,
                                    'qty_pur' => $req->purqty,
                                    'price' => $req->proprice,
                                    'due_date' => $req->m_duedate,
                                    'propose_date' => $req->m_prodate,
                                    'purchase_date' => $purdate,
                                    'item_desc'=>$itemdesc,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString()
                                ]);

                        // session()->flash("updated","Purchase Plan is successfully Created");
                        alert()->success('Success','Purchase Plan is successfully Created');
                          
                        return back();
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
                break;
        }

    }

    // RFP -> PP/RFQ/PR
    public function approverfp(Request $req){
        switch($req->input('action')){
            case 'reject' :
                $rfpnbr = $req->rfpnumber;
                $apporder = $req->apporder;

                $users = DB::table('users')
                            ->where('users.department' , '=', Session::get('department'))
                            ->where('users.id', '=', Session::get('userid'))
                            ->first();

                DB::table('xrfp_app_trans')
                    ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                    ->where('xrfp_app_order', '=', $apporder)
                    ->where('xrfp_app_status', '=', '0')
                    ->update([
                        'xrfp_app_status' => '2', //0 new, 1 approved, 2 reject
                        'create_at' => Carbon::now()->toDateTimeString(),
                        'xrfp_app_reason' => $req->e_reason,
                        'xrfp_app_user' => $users->name
                    ]);

                //data transaksi pindah ke history

                $data = DB::table('xrfp_app_trans')
                            ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                            ->where('xrfp_app_status', '!=', '0')
                            ->get();

                foreach($data as $data){
                    $inputthis = array(
                        'xrfp_app_nbr' => $data->xrfp_app_nbr,
                        'xrfp_app_approver' => $data->xrfp_app_approver,
                        'xrfp_app_alt_approver'=> $data->xrfp_app_alt_approver,
                        'xrfp_app_user' => $data->xrfp_app_user,
                        'xrfp_app_order' => $data->xrfp_app_order,
                        'xrfp_app_reason' => $data->xrfp_app_reason,
                        'xrfp_app_status' => $data->xrfp_app_status,
                        'create_at' => $data->create_at
                    );

                    DB::table('xrfp_app_hist')->insert($inputthis);
                }

                DB::table('xrfp_app_trans')
                        ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                        ->delete();

                DB::table('xrfp_mstrs')
                    ->where('xrfp_mstrs.xrfp_nbr', '=', $req->rfpnumber)
                    ->update([
                        'status' => 'Rejected' 
                    ]);

                $rfpmstrs = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $req->rfpnumber)
                            ->get();
                $line = 1;            
                foreach ($rfpmstrs as $mstr) {

                    $inputreject = array(
                        'rfp_hist_nbr' => $mstr->xrfp_nbr,
                        'rfp_hist_supp' => $mstr->xrfp_supp,
                        'rfp_hist_enduser' => $mstr->xrfp_enduser,
                        'rfp_hist_site' => $mstr->xrfp_site,
                        'rfp_hist_shipto' => $mstr->xrfp_shipto,
                        'rfp_dept' => $mstr->xrfp_dept,
                        'rfp_duedate_mstr' => $mstr->xrfp_duedate,
                        'rfp_create_by' => $mstr->created_by,
                        'rfp_create_at' => Carbon::now()->toDateTimeString(),
                        'rfp_status' => $mstr->status,
                        'line' => $line,
                        'itemcode_hist' => $mstr->itemcode,
                        'need_date_dets' => $mstr->need_date,
                        'due_date_dets' => $mstr->due_date,
                        'qty_order_hist' => $mstr->qty_order

                    );

                    DB::table('xrfp_hist')->insert($inputreject);
                    
                    $line ++;
                }

					
				// DB::table('xrfp_hist')
                //         ->where('rfp_hist_nbr', '=', $req->rfpnumber)
                //         ->update([
                //               'rfp_status' => 'Rejected'
                //         ]);

                //email ke yg request ngasih tau rfp nya di reject

                $datarfp = DB::table('xrfp_mstrs')
                            ->where('xrfp_nbr', '=', $req->rfpnumber)
                            ->first();

                $emailreject = DB::table('users')
                            ->join('xrfp_mstrs', 'xrfp_mstrs.created_by', '=', 'users.username')
                            ->where('status', '=', 'Rejected')
                            ->where('xrfp_nbr', '=', $rfpnbr)
                            // ->where('xrfp_sendmail', '=', 'Y')
                            ->select('email', 'username')
                            // ->select('email')
                            ->first();

                // dd($emailreject);
            
                $company = DB::table('com_mstr')
                                ->first();

                $rfp_duedate = $datarfp->xrfp_duedate;
                $created_by = $datarfp->created_by;
                $rfp_dept = $datarfp->xrfp_dept;
                $emailreject1 = $emailreject;
                $company1 = $company;
    
            
                EmailRFP::dispatch($rfpnbr,$rfp_duedate,$created_by,$rfp_dept,$emailreject1,$company1,'','','','1');
            
                // Mail::send('email.emailrfpapproval',
                //         [   'pesan' => 'Following Request for Purchasing has been rejected :',
                //             'note1' => $rfpnbr,
                //             'note2' => $datarfp->xrfp_duedate,
                //             'note3' => $datarfp->created_by,
                //             'note4' => $datarfp->xrfp_dept,
                //             'note5' => 'Please Check.'],
                // function ($message) use ($rfpnbr, $emailreject, $company)
                // {
                //     $message->subject('Notifikasi : Request for Purchasing Approval Rejected - '.$company->com_name);
                //     $message->from($company->com_email);
                //     $message->to($emailreject->email);
                // });

                // $user = App\User::where('username','=', $emailreject->username)->first(); // user siapa yang terima notif (lewat id)
                          
                // $details = [
                //     'body' => 'Following Request for Purchasing has been rejected',
                //     'url' => 'inputrfp',
                //     'nbr' => $rfpnbr,
                //     'note' => 'Please check'
                // ]; // isi data yang dioper
                                            
                                        
                // $user->notify(new \App\Notifications\eventNotification($details));

                // session()->flash("updated", "RFP Number : ".$req->rfpnumber." is Rejected");
                alert()->success('Success','RFP Number : '.$req->rfpnumber.' is Rejected');
                return redirect()->route('rfpapproval');
            break;

            case 'confirm' :

                $rfpnbr = $req->rfpnumber;
                $convert = $req->convertto;

                $users = DB::table('users')
                            ->where('users.department' , '=', Session::get('department'))
                            ->where('users.id', '=', Session::get('userid'))
                            ->first();

                $rfpapprove = DB::table('xrfp_app_trans')
                                ->where('xrfp_app_nbr', '=', $rfpnbr)
                                ->where('xrfp_app_status', '=', '0')
                                ->where('xrfp_app_order', '=', $req->apporder)
                                ->update([
                                    'xrfp_app_status' => '1',
                                    'xrfp_app_reason' => $req->e_reason,
                                    'xrfp_app_user' => $users->name,
                                    'create_at' => Carbon::now()->toDateTimeString()
                                ]);

                $nextapprover = DB::table('xrfp_app_trans')
                                    ->join('users', 'users.id', '=', 'xrfp_app_trans.xrfp_app_approver')
                                    ->where('xrfp_app_nbr', '=', $rfpnbr)
                                    ->where('xrfp_app_status', '=', '0')
                                    ->select('users.id', 'name', 'email', 'xrfp_app_nbr', 'xrfp_app_order', 'xrfp_app_approver')
                                    ->orderBy('xrfp_app_order')
                                    ->first();
                $nextaltapprover = DB::table('xrfp_app_trans')
                                    ->join('users', 'users.id', '=', 'xrfp_app_trans.xrfp_app_alt_approver')
                                    ->where('xrfp_app_nbr', '=', $rfpnbr)
                                    ->where('xrfp_app_status', '=', '0')
                                    ->select('users.id', 'name', 'email', 'xrfp_app_nbr', 'xrfp_app_order', 'xrfp_app_alt_approver')
                                    ->orderBy('xrfp_app_order')
                                    ->first();
                // dd($req->all());
                if($nextapprover == null){
                            
                    $data = DB::table('xrfp_app_trans')
                            ->where('xrfp_app_nbr', '=', $rfpnbr)
                            ->get();

                    foreach($data as $data){
                        $inputdata = array(
                            'xrfp_app_nbr' => $data->xrfp_app_nbr,
                            'xrfp_app_approver' => $data->xrfp_app_approver,
                            'xrfp_app_alt_approver' => $data->xrfp_app_alt_approver,
                            'xrfp_app_user' =>$data->xrfp_app_user,
                            'xrfp_app_order' => $data->xrfp_app_order,
                            'create_at' => $data->create_at,
                            'xrfp_app_status' => $data->xrfp_app_status,
                            'xrfp_app_reason' => $data->xrfp_app_reason,
                        );

                        DB::table('xrfp_app_hist')->insert($inputdata);
                    }

                    DB::table('xrfp_app_trans')
                        ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                        ->delete();
                        
                    if($req->convertto == 'PP'){

                        $data = DB::table('xrfp_mstrs')
                                ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                                ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->get();

                        DB::table('xpurplan_mstrs')
                                ->insert([
                                    'rf_number' => $req->rfpnumber,
                                    'supp_code' => $req->supp,
                                    'due_date' => $req->duedate,
                                    'site' => $req->site,
                                    // 'propose_date' => $req->m_prodate,
                                    'rf_from' => '2', // 1 = RFQ , 2 RFP
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                        $flg = 1;
                        foreach($data as $data){
                            DB::table('xpurplan_dets')
                                    ->insert([
                                        'rf_number' =>$data->xrfp_nbr,
                                        'supp_code' => $data->xrfp_supp,
                                        'line' => $flg, // pasti cuma 1 line
                                        'item_code' => $data->itemcode,
                                        'qty_req' => $data->qty_order,
                                        // 'qty_pro' => $req->m_proqty,
                                        // 'qty_pur' => $req->purqty,
                                        // 'price' => $req->proprice,
                                        'due_date' => $data->due_date,
                                        // 'propose_date' => $req->m_prodate,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);

                            $flg++;
                        }

                        DB::table('xrfp_mstrs')
                        ->where('xrfp_nbr', '=', $rfpnbr)
                        ->update([
                           'status' => 'Approved'
                        ]);
						
						
                        // session()->flash("updated","Purchase Plan is successfully Created");
                        alert()->success('Success','Purchase Plan is successfully Created');
                          
                        return redirect()->route('rfpapproval');

                    }else if($req->convertto == 'RFQ'){
                        // dd('test');
                        $data = DB::table('xrfp_mstrs')
                                ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                                ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->get();

                        $prefix = DB::table('xrfq_mstrs')
                                    ->first();

                       
                        // dd($data);

                        foreach($data as $data){
                            $prefix = DB::table('xrfq_mstrs')
                                    ->first();
                            $rfq_nbr = $prefix->xrfq_prefix.$prefix->xrfq_nbr;
                            DB::table('xbid_mstr') 
                                ->insert([
                                    'xbid_id' => $rfq_nbr,
                                    'xbid_site' => $data->xrfp_site,
                                    'xbid_part' => $data->itemcode,
                                    'xbid_qty_req' => $data->qty_order,
                                    'xbid_due_date' => $data->xrfp_duedate,
                                    'xbid_flag' => '0'
                                ]);

                            DB::table('xbid_det')
                                ->insert([
                                    'xbid_id' => $rfq_nbr,
                                    'xbid_qty' => $data->qty_order,
                                    'xbid_date' => $data->due_date,
                                    'xbid_supp' => $data->xrfp_supp,
                                    'xbid_apprv' => 'Yes',
                                    'xbid_flag' => '0'
                                ]);

                            $new_rfq_nbr = Str::substr($rfq_nbr, strlen($rfq_nbr) - 6, 6);
                            $int_rfq_nbr = (int)$new_rfq_nbr + 1;
    
                                if($int_rfq_nbr < 10 ){
                                    $string_rfq_nbr = strval("00000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 100 & $int_rfq_nbr >= 10){
                                    $string_rfq_nbr = strval("0000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 1000 & $int_rfq_nbr >= 100){
                                    $string_rfq_nbr = strval("000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 10000 & $int_rfq_nbr >= 1000){
                                    $string_rfq_nbr = strval("00".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 100000 & $int_rfq_nbr >= 10000){
                                    $string_rfq_nbr = strval("0".$int_rfq_nbr);
                                }else{
                                    $string_rfq_nbr = strval($int_rfq_nbr);
                                }
        
                                DB::table('xrfq_mstrs')
                                ->update([
                                        'xrfq_nbr' => $string_rfq_nbr,
                                ]);

                                // UPDATE 13112020
                                 DB::table("xrfp_mstrs")
                                    ->join("xrfp_dets","xrfp_mstrs.xrfp_nbr","=","xrfp_dets.rfp_nbr")
                                    ->where('xrfp_nbr','=',$rfpnbr)
                                    ->where('xrfp_dets.itemcode','=',$data->itemcode)
                                    ->update([
                                        'xrfp_no_po' => $rfq_nbr
                                    ]);

                        }

                        DB::table('xrfp_mstrs')
                        ->where('xrfp_nbr', '=', $rfpnbr)
                        ->update([
                            'status' => 'Close'
                        ]);
						
						//tommy 30/10/2020 
                        DB::table('xrfp_dets')
                            ->where('rfp_nbr', '=', $rfpnbr)
                            ->update([
                                'dets_flag' => 'Close'
                            ]);

                        
                        $rfpmstrs2 = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->get();
                        $line = 1;            
                        foreach ($rfpmstrs2 as $mstr2) {

                            $inputreject2 = array(
                                'rfp_hist_nbr' => $mstr2->xrfp_nbr,
                                'rfp_hist_supp' => $mstr2->xrfp_supp,
                                'rfp_hist_enduser' => $mstr2->xrfp_enduser,
                                'rfp_hist_site' => $mstr2->xrfp_site,
                                'rfp_hist_shipto' => $mstr2->xrfp_shipto,
                                'rfp_dept' => $mstr2->xrfp_dept,
                                'rfp_duedate_mstr' => $mstr2->xrfp_duedate,
                                'rfp_create_by' => $mstr2->created_by,
                                'rfp_create_at' => Carbon::now()->toDateTimeString(),
                                'rfp_status' => $mstr2->status,
                                'line' => $line,
                                'itemcode_hist' => $mstr2->itemcode,
                                'need_date_dets' => $mstr2->need_date,
                                'due_date_dets' => $mstr2->due_date,
                                'qty_order_hist' => $mstr2->qty_order,
                                'nbr_convert' => $mstr2->xrfp_no_po

                            );

                            DB::table('xrfp_hist')->insert($inputreject2);
                            
                            $line ++;
                        }

                        // session()->flash("updated", "RFQ No.".$rfq_nbr." is Successfully Created");
                        alert()->success('Success','RFQ No. '.$rfq_nbr.' is Successfully Created');
                        return redirect()->route('rfpapproval');

                    }else if($req->convertto == 'PR'){
                        //ditambahkan tommy
                        //menanti template stanley
                        //dd($req->all());   
                        $dataheader = DB::table('xrfp_mstrs')
                                       ->where('xrfp_nbr', '=', $rfpnbr)
                                       ->first();
                                       
                                       

                        $getpr = DB::table('xrfq_mstrs')
                              ->first();

                        $new_no_pr = $getpr->xrfq_pr_prefix.$getpr->xrfq_pr_nbr;

                        $new_due_date = strtotime($dataheader->xrfp_duedate);
                        $cimduedate = date('m/d/y',$new_due_date);

                        
                        //header RFP to PR
                        $content = '';
                        $content .= '"'.$new_no_pr.'"'.PHP_EOL;
                        $content .= '"'.$dataheader->xrfp_supp.'"'.PHP_EOL;
                        $content .= '"'.$dataheader->xrfp_site.'"'.PHP_EOL;
                        $content .= $cimduedate.' - - - "'.$dataheader->xrfp_enduser.'" '.$rfpnbr.' "" "'.$dataheader->xrfp_dept.'" - '.$dataheader->xrfp_site.' - - - - - - - - - yes'.PHP_EOL;
                        $content .= '"'.$req->e_remarks.'"'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        
                        $dataline = DB::table('xrfp_dets')
                                    ->where('rfp_nbr', '=', $rfpnbr)
                                    ->get();
                           
                        $line = 1;
                        foreach($dataline as $dataline){
                           $old_needdate = $dataline->need_date;
                           $new_needdate =  strtotime($old_needdate);
                           $new_dataline_needdate = date('m/d/y', $new_needdate);
                           
                           
                           $content .= '"'.$line.'"'.PHP_EOL;
                           $content .= '"'.$dataheader->xrfp_site.'"'.PHP_EOL;
                           $content .= '"'.$dataline->itemcode.'"'.PHP_EOL;
                           $content .= '-'.PHP_EOL;
                           $content .= '- -'.PHP_EOL;
                           $content .= $dataline->qty_order.' - '.PHP_EOL;
                           $content .= '- -'.PHP_EOL;
                           $content .= $new_dataline_needdate.' - - - - - - - - - - - - no'.PHP_EOL;
                           $content .= '.'.PHP_EOL;
                           $content .= '"S"'.PHP_EOL;
                           $content .= '.'.PHP_EOL;
                           
                           $line += 1;
                        }

                        File::put('cim/xxcimpr.cim',$content);
                        
                        exec("start cmd /c cimrfppr.bat");

                        $new_pr_nbr = Str::substr($new_no_pr, strlen($new_no_pr) - 6, 6);
                        $int_pr_nbr = (int)$new_pr_nbr + 1;

                        //dd($new_rfq_nbr);
                        
                        if($int_pr_nbr < 10 ){
                            $string_pr_nbr = strval("00000".$int_pr_nbr);
                        }else if($int_pr_nbr < 100 & $int_pr_nbr >= 10){
                            $string_pr_nbr = strval("0000".$int_pr_nbr);
                        }else if($int_pr_nbr < 1000 & $int_pr_nbr >= 100){
                            $string_pr_nbr = strval("000".$int_pr_nbr);
                        }else if($int_pr_nbr < 10000 & $int_pr_nbr >= 1000){
                            $string_pr_nbr = strval("00".$int_pr_nbr);
                        }else if($int_pr_nbr < 100000 & $int_pr_nbr >= 10000){
                            $string_pr_nbr = strval("0".$int_pr_nbr);
                        }else{
                            $string_pr_nbr = strval($int_pr_nbr);
                        }

                        // update next po nbr
                        DB::table('xrfq_mstrs')
                            ->update([
                                'xrfq_pr_nbr' => $string_pr_nbr
                            ]);

                        DB::table('xrfp_mstrs')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->update([
                                'status' => 'Close'
                            ]);
                            
                        DB::table("xrfp_mstrs")
                           ->join("xrfp_dets","xrfp_mstrs.xrfp_nbr","=","xrfp_dets.rfp_nbr")
                           ->where('xrfp_nbr','=',$rfpnbr)
                           ->update([
                                 'xrfp_no_po' => $new_no_pr
                           ]);
                            
                        
                        
                        $rfpmstrs2 = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->get();
                        $line = 1;
                           $inputprhist2=array();
                        foreach ($rfpmstrs2 as $mstr2) {

                            $inputprhist2 = array(
                                'rfp_hist_nbr' => $mstr2->xrfp_nbr,
                                'rfp_hist_supp' => $mstr2->xrfp_supp,
                                'rfp_hist_enduser' => $mstr2->xrfp_enduser,
                                'rfp_hist_site' => $mstr2->xrfp_site,
                                'rfp_hist_shipto' => $mstr2->xrfp_shipto,
                                'rfp_dept' => $mstr2->xrfp_dept,
                                'rfp_duedate_mstr' => $mstr2->xrfp_duedate,
                                'rfp_create_by' => $mstr2->created_by,
                                'rfp_create_at' => Carbon::now()->toDateTimeString(),
                                'rfp_status' => $mstr2->status,
                                'line' => $line,
                                'itemcode_hist' => $mstr2->itemcode,
                                'need_date_dets' => $mstr2->need_date,
                                'due_date_dets' => $mstr2->due_date,
                                'qty_order_hist' => $mstr2->qty_order,
                                'nbr_convert' => $mstr2->xrfp_no_po

                            );

                            DB::table('xrfp_hist')->insert($inputprhist2);
                            
                            $line ++;
                        }
                        
                        // session()->flash("updated","PR is successfully Created, PR No. : ".$new_no_pr);
                        alert()->success('Success','PR is sucessfully Created, PR No. '.$new_no_pr);
                        return redirect()->route('rfpapproval');
                    
                    }

                    session()->flash('updated', "RFP Number : ".$req->rfpnumber." is Approved");
                    return redirect()->route('rfpapproval');

                }else{
                    //jika masih ada next approver
                    $listemail = $nextapprover->email.','.$nextaltapprover->email;

                    $array_email = explode(',', $listemail);
					//dd($array_email);
                    $rfpdata2 = DB::table('xrfp_mstrs')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->first();

                    $company = DB::table('com_mstr')
                                ->first();

                    $rfp_duedate = $rfpdata2->xrfp_duedate;
                    $created_by = $rfpdata2->created_by;
                    $rfp_dept = $rfpdata2->xrfp_dept;
                    $array_email1 = $array_email;
                    $company1 = $company;
                    $nextapproval = $nextapprover->xrfp_app_approver;
                    $nextaltapp = $nextaltapprover->xrfp_app_alt_approver;

                    EmailRFP::dispatch($rfpnbr,$rfp_duedate,$created_by,$rfp_dept,'',$company1,$nextapproval,$nextaltapp,$array_email1,'2');

                    // Mail::send('email.emailrfpapproval',
                    //     [
                    //         'pesan' => 'There is a RFP awaiting for approval',
                    //         'note1' => $rfpnbr,
                    //         'note2' => $rfpdata2->xrfp_duedate,
                    //         'note3' => $rfpdata2->created_by,
                    //         'note4' => $rfpdata2->xrfp_dept,
                    //         'note5' => 'Please Check.'],
                    //         function($message) use ($rfpnbr, $array_email, $company)
                    //     {
                    //         $message->subject('PhD - RFP Approval Task -'.$company->com_name);
                    //         $message->from($company->com_email);
                    //         $message->to($array_email);
                    //     });

                    // // ditambahkan 03/11/2020
                    // $user = App\User::where('id','=', $nextapprover->xrfp_app_approver)->first(); // user siapa yang terima notif (lewat id)
                    // $useralt = App\User::where('id','=', $nextaltapprover->xrfp_app_alt_approver)->first(); 

                    // $details = [
                    //     'body' => 'There is a RFP awaiting for approval',
                    //     'url' => 'rfpapproval',
                    //     'nbr' => $rfpnbr,
                    //     'note' => 'Please check'
                    // ]; // isi data yang dioper
                                                    
                                                
                    // $user->notify(new \App\Notifications\eventNotification($details));
                    // $useralt->notify(new \App\Notifications\eventNotification($details));

                    // session()->flash('updated', "RFP Number : ".$req->rfpnumber." is Approved");
                    alert()->success('Success','RFP Number : '.$req->rfpnumber.' is Approved');
                    return redirect()->route('rfpapproval');
                }
            break;

            case 'close':
                return redirect()->route('rfpapproval');
            break;
        }
    }

    // PP -> PO
    public function cimloadpplan(Request $req){
        try{
            $id = $req->input('idrow');

            // dd($req->all());
            $datenow = Carbon::now()->format('d/m/y');

            // dd($datenow);

            $getpo = DB::table('xrfq_mstrs')
                        ->first();

            $new_no_po = $getpo->xrfq_po_prefix.$getpo->xrfq_po_nbr;

            $data = DB::table('xpurplan_temp')
                        ->join('xitemreq_mstr','xitemreq_part','=','xpurplan_temp.item_code')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_temp.supp_code')
                        ->where('username','=',Session::get('username'))
                        ->orderBy('rf_number','ASC')
                        ->orderBy('line','ASC')
                        ->get();

        
            foreach($data as $data){
                            
                if(is_null($data->purchase_date) or is_null($data->qty_pur) or is_null($data->price)){
                    return redirect()->back()->with(['error'=>'Pur. Date or Qty Purch. or Price must be filled for each row']);
                }
            }

            $content = '';

            $dataheader = DB::table('xpurplan_temp')
                        ->where('username', '=', Session::get('username'))
                        ->orderBy('due_date', 'DESC')
                        ->first();

             

            $old_dateformat = $dataheader->due_date;
            

            $new_dateformat = strtotime($old_dateformat);

        
            $new_duedate = date('d/m/y', $new_dateformat);

            // dd($new_duedate);

            $content .= '"'.$new_no_po.'"'.PHP_EOL.
                        '"'.$dataheader->supp_code.'"'.PHP_EOL.
                        '-'.PHP_EOL.
                        '"'.$datenow.'"'.' '.'"'.$new_duedate.'"'.' '.'-'.' '.'"'.$dataheader->site.'"'.' '.'-'.' '.'"'.$dataheader->rf_number.'"'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'yes'.' '.'no'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'no'.PHP_EOL.
                        '-'.' '.'-'.' '.'-'.' '.'-'.' '.'no'.PHP_EOL;

            
            /* ini cimload punya SBE */

            // $content .= '"'.$new_no_po.'"'.PHP_EOL.
            //             '"'.$dataheader->supp_code.'"'.PHP_EOL.
            //             '-'.PHP_EOL.
            //             '"'.$datenow.'"'.' '.'"'.$new_duedate.'"'.' '.'-'.' '.'"'.$dataheader->site.'"'.' '.'-'.' '.'"'.$dataheader->rf_number.'"'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'yes'.' '.'no'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'no'.PHP_EOL.
            //             '-'.' '.'-'.' '.'-'.' '.'-'.' '.'no'.PHP_EOL;


            /* ================================================================================================================ */


            $dataline = DB::table('xpurplan_temp')
                        ->join('xitemreq_mstr', 'xpurplan_temp.item_code', '=', 'xitemreq_mstr.xitemreq_part')
                        ->where('username', '=', Session::get('username'))
                        ->get();

            $flgtmp = 1;
            foreach($dataline as $dataline){
				
				$old_dateline = $dataline->due_date;
				
				$new_dateline = strtotime($old_dateline);
				
				$new_dateline_duedate = date('d/m/y', $new_dateline);
                
                $content .= $flgtmp.PHP_EOL.
                            '"'.$dataline->site.'"'.PHP_EOL.
                            '-'.' '.'-'.PHP_EOL.
                            '"'.$dataline->item_code.'"'.PHP_EOL.
                            $dataline->qty_req.' '.'-'.PHP_EOL.
                            $dataline->price.' '.'-'.PHP_EOL.
                            '-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'"'.$new_dateline_duedate.'"'.' '.'-'.' '.'yes'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'yes'.' '.'-'.' '.'no'.' '.'no'.' '.'-'.' '.'yes'.PHP_EOL.
                            '-'.PHP_EOL;

                /* cimload punya SBE */

                // $content .= $flgtmp.PHP_EOL.
                //             '"'.$dataline->site.'"'.PHP_EOL.
                //             '-'.' '.'-'.PHP_EOL.
                //             '"'.$dataline->item_code.'"'.PHP_EOL.
                //             $dataline->qty_req.' '.'-'.PHP_EOL.
                //             $dataline->price.' '.'-'.PHP_EOL.
                //             '-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'"'.$new_dateline_duedate.'"'.' '.'-'.' '.'-'.' '.'-'.' '.'yes'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'no'.' '.'no'.' '.'-'.' '.'yes'.PHP_EOL.
                //             '-'.PHP_EOL;

                /* =========================================================================================================================== */
                $flgtmp += 1;
            }

            $content .= '.'.PHP_EOL.
                        '.'.PHP_EOL.
                        '-'.PHP_EOL.
                        '-'.PHP_EOL.
						'.'.PHP_EOL;

            // dd($content);

            
            // Buat file yang akan di cimload
            File::put('cim/xxcimppp.cim', $content);

            // // Panggil .bat file buat lakukan cimload
            exec("start cmd /c cimportpp.bat");
            // dd('sampai disini');
            $new_po_nbr = Str::substr($new_no_po, strlen($new_no_po) - 6, 6);
            $int_po_nbr = (int)$new_po_nbr + 1;

            if($int_po_nbr < 10 ){
                $string_po_nbr = strval("00000".$int_po_nbr);
            }else if($int_po_nbr < 100 & $int_po_nbr >= 10){
                $string_po_nbr = strval("0000".$int_po_nbr);
            }else if($int_po_nbr < 1000 & $int_po_nbr >= 100){
                $string_po_nbr = strval("000".$int_po_nbr);
            }else if($int_po_nbr < 10000 & $int_po_nbr >= 1000){
                $string_po_nbr = strval("00".$int_po_nbr);
            }else if($int_po_nbr < 100000 & $int_po_nbr >= 10000){
                $string_po_nbr = strval("0".$int_po_nbr);
            }else{
                $string_po_nbr = strval($int_po_nbr);
            }

            DB::table('xrfq_mstrs')
                ->update([
                    'xrfq_po_nbr' => $string_po_nbr
                ]);
				
			// $test = DB::table('xpurplan_temp')
                // // ->where('username', '=', Session::get('username'))
                // ->get();
			// dd($test);
            //tempat validasi dari rfp/rfq
            $data1 = DB::table('xpurplan_temp')
                    ->join('xpurplan_mstrs', 'xpurplan_temp.rf_number', '=', 'xpurplan_mstrs.rf_number')
                    ->selectRaw('*, xpurplan_temp.rf_number as "rf_temp"')
					// ->where('xpurplan_dets.item_code', '=', 'xpurplan_temp.item_code')
                    ->get();
					
			//dd($data1);

                     
            foreach($data1 as $data1){
				if($data1->rf_from == '1'){
                // dd('ini buat rfq');
                DB::table('xbid_det')
                    ->where('xbid_id', '=', $data1->rf_temp)
                    ->update([
                        'xbid_no_po' => $new_no_po,
                        'xbid_flag' => '2'
                    ]);
				}elseif($data1->rf_from == '2'){
                // dd('ini buat rfp');

					DB::table('xrfp_dets')
					->where('rfp_nbr', '=', $data1->rf_temp)
					->where('itemcode', '=', $data1->item_code)
					->update([
						'xrfp_no_po' => $new_no_po,
						'dets_flag' => 'Close'
					]);
				}
				
				$data2 = DB::table('xrfp_mstrs')
						->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
						->where('xrfp_dets.rfp_nbr', '=', $data1->rf_temp)
						->where('xrfp_dets.dets_flag', '=','Open')
						->first();
					
				if(!$data2){
					DB::table('xrfp_mstrs')
						->where('xrfp_nbr', '=', $data1->rf_temp)
						->update([
							'status' => 'Close'
					]);
				}
				
				
				// update detail
				DB::table('xpurplan_dets')
					->where('rf_number', '=', $data1->rf_temp)
					->where('item_code', '=', $data1->item_code)
					->update([
						'status' => 'Close'
					]);
				// update mstr
				$data = DB::table('xpurplan_mstrs')
						->join('xpurplan_dets', 'xpurplan_mstrs.rf_number', '=', 'xpurplan_dets.rf_number')
						->where('xpurplan_dets.rf_number', '=', $data1->rf_temp)
						->where('xpurplan_dets.status', '=','New')
						->first();
					
				if(!$data){
					DB::table('xpurplan_mstrs')
						->where('rf_number', '=', $data1->rf_temp)
						->update([
							'status' => 'Close'
						]);
				}
						
            }
			

			
			
			// $lastcount = DB::table('xpurplan_temp')->count();
			
			$rfpmstrs1 = DB::table('xrfp_mstrs')
					->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
					->join("xpurplan_temp", function($join){
						$join->on('xrfp_dets.rfp_nbr', '=', 'xpurplan_temp.rf_number')
								->on('xrfp_dets.itemcode', '=', 'xpurplan_temp.item_code');
					})
					->where('xrfp_nbr' ,'=', $data1->rf_temp)
					->get();
					
					//dd($rfpmstrs1);
					$line = 1;            
			foreach ($rfpmstrs1 as $mstr1) {

				$inputreject1 = array(
					'rfp_hist_nbr' => $mstr1->xrfp_nbr,
					'rfp_hist_supp' => $mstr1->xrfp_supp,
					'rfp_hist_enduser' => $mstr1->xrfp_enduser,
					'rfp_hist_site' => $mstr1->xrfp_site,
					'rfp_hist_shipto' => $mstr1->xrfp_shipto,
					'rfp_dept' => $mstr1->xrfp_dept,
					'rfp_duedate_mstr' => $mstr1->xrfp_duedate,
					'rfp_create_by' => Session::get('username'),
					'rfp_create_at' => Carbon::now()->toDateTimeString(),
					'rfp_status' => $mstr1->dets_flag,
					'line' => $line,
					'itemcode_hist' => $mstr1->itemcode,
					'need_date_dets' => $mstr1->need_date,
					'due_date_dets' => $mstr1->due_date,
					'qty_order_hist' => $mstr1->qty_order,
					'nbr_convert' => $mstr1->xrfp_no_po

				);
					
				DB::table('xrfp_hist')->insert($inputreject1);
					
				$line ++;
			}
			
            
            //hapus xpurplan_temp setelah cimload
            DB::table('xpurplan_temp')
                ->where('username', '=', Session::get('username'))
                ->delete();
				
			// Schema::drop('xpurplan_temp');

            // session()->flash("updated", "Data PO ".$new_no_po." is successfully updated to QAD");
            alert()->success('Success','Data PO '.$new_no_po.' is successfully updated to QAD');

            return redirect()->route('viewdetailtmp');
        
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

    // PO Receipt
    public function receiptqad(Request $req){
        
        $data = DB::table('xpo_receipt')
                     ->selectRaw('sum(xpo_qty_rcvd) as total, xpo_qty_ship, xpo_nbr, xpo_line, xpo_domain, xpo_line')
                     ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                     ->groupBy('xpo_nbr')
                     ->groupBy('xpo_line')
                     ->get();
 
         $chr_tmp = '';
 
         foreach($data as $data){
             
             if($data->total != $data->xpo_qty_ship){
                     $chr_tmp .= 'PO : '.$data->xpo_nbr.' Line : '.$data->xpo_line.' Qty Received is not equal to Qty Shipped, '.$data->total.'-'.$data->xpo_qty_ship;
             }
 
         }
 
 
         if($chr_tmp != ''){
             // Error balikin ke menu
 
             return redirect()->back()->with(['error'=>$chr_tmp]);
 
         }else{
             // Tidak Error Masukin QAD -- CIM + Validasi WSA 
             // ambil data inputan user
             $validasi = DB::table('xpo_receipt')
                         ->join('xpod_dets','xpod_dets.xpod_nbr','=','xpo_receipt.xpo_nbr')
                         ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                         ->whereRaw("xpod_dets.xpod_line = xpo_receipt.xpo_line")
                         ->selectRaw("xpod_nbr, xpod_line, xpod_qty_rcvd, xpod_qty_ord, xpo_domain")
                         ->groupBy("xpod_nbr")
                         ->groupBy("xpod_line")
                         ->get();
 
             $note = '';
             $content = '';
 
             //dd($validasi);

             $wsa = DB::table('wsas')
                    ->first();
 
             foreach($validasi as $validasi){
                 // Validasi WSA --> qty rcvd & qty ord
                 $qty_rcvd = $validasi->xpod_qty_rcvd;
                 $qty_ord  = $validasi->xpod_qty_ord;
 
                 $domain = $validasi->xpo_domain;
                 $ponbr  = $validasi->xpod_nbr;
                 $line   = $validasi->xpod_line;
 
                 //dd($domain, $ponbr, $line, $qty_rcvd, $qty_ord);
 
                 // Validasi WSA
                     $qxUrl          = $wsa->wsas_url;  /*services/wsdl*/
                     $qxReceiver     = '';
                     $qxSuppRes      = 'false';
                     $qxScopeTrx     = '';
                     $qdocName       = '';
                     $qdocVersion    = '';
                     $dsName         = '';
                     $timeout        = 0;
                     

 
                     $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                      '<Body>'.
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
                 
                     $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);          
                     $dataloop = $xmlResp->xpath('//ns1:tempRow');          
                     $result = (string) $xmlResp->xpath('//ns1:outOK')[0];   
 
                     
                     $flag = 0;
 
                     if($result == 'true'){
                         foreach($dataloop as $data) { 
                             // qty rcvd & order QAD
                             //$qdocResult = (string) $xmlResp->xpath('//ns1:t_rcvd')[$flag];   
                             //$qdocResult1 = (string) $xmlResp->xpath('//ns1:t_ord')[$flag]; 
                             //$qdocResult2 = (string) $xmlResp->xpath('//ns1:t_status')[$flag];
 
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
                 // Bkin Cim Load
 
                 // Header No PO  -- > Data 1
                 $header = DB::table('xpo_receipt')
                             ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                             ->selectRaw('xpo_nbr,xpo_sj_id,xpo_eff_date,xpo_ship_date,xpo_line')
                             ->groupBy('xpo_nbr')
                             ->get();
                 //dd($header);
                 foreach($header as $header){
                     // Header
                     $new_format_effdate = $header->xpo_eff_date;
                     $new_format_shipdate = $header->xpo_ship_date;
 
 
                     $new_effdate = strtotime($new_format_effdate);
                     $new_shipdate = strtotime($new_format_shipdate);
 
 
                     $file_effdate = date('m/d/y',$new_effdate); 
                     $file_shipdate = date('m/d/y',$new_shipdate);
 
                     if(is_null($header->xpo_eff_date) or is_null($header->xpo_ship_date)){
                         return redirect()->back()->with(['error'=>'Ship date or Eff Date must be filled for each row']);
                     }
 
                     $content .= '"'.$header->xpo_nbr.'"'.PHP_EOL.
                                 '"'.$header->xpo_sj_id.'"'.' '.'-'.' '.$file_effdate.' '.'-'.' '.'"NO"'.' '.'-'.' '.'-'.' '.$file_shipdate.PHP_EOL;
 
 
                     $dataline = DB::table('xpo_receipt')
                                     ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                                     ->where("xpo_receipt.xpo_nbr",'=',$header->xpo_nbr)
                                     ->selectRaw('xpo_nbr,xpo_line,xpo_rcp_id,xpo_qty_ord,xpo_site,xpo_loc,xpo_lot,xpo_ref,xpo_qty_rcvd')
                                     ->orderBy('xpo_nbr')
                                     ->orderBy('xpo_line')
                                     ->groupBy('xpo_nbr')
                                     ->groupBy('xpo_line')
                                     ->get();  
 
                     foreach($dataline as $dataline){
                         
                         // Data Loc/Lot
                         $data = DB::table('xpo_receipt')
                                     ->selectRaw('xpo_line, xpo_part, xpo_qty_ord, xpo_qty_rcvd, xpo_um, xpo_site, xpo_loc, xpo_lot, xpo_ref,xpo_rcp_id, xpo_qty_rcvd, xpo_qty_ship')
                                     ->where("xpo_receipt.xpo_user",'=',Session::get('userid'))
                                     ->where('xpo_receipt.xpo_nbr','=',$header->xpo_nbr)
                                     ->where('xpo_receipt.xpo_line','=',$dataline->xpo_line)
                                     //->where('xpo_receipt.xpo_rcp_id','!=',$dataline->xpo_rcp_id)
                                     ->orderby('xpo_nbr')
                                     ->orderby('xpo_line')
                                     ->get();
                         
                         // Multi Entry Loc/Lot
                         $content .= $dataline->xpo_line.PHP_EOL.
                                 '"'.$dataline->xpo_qty_rcvd.'"'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'-'.' '.'"'.$dataline->xpo_loc.'"'.' '.'"'.$dataline->xpo_lot.'"'.' '.'"'.$dataline->xpo_ref.'"'.' '.'-'.' '.'"YES"'.' '.'NO'.' '.'NO'.PHP_EOL;
                     
                         foreach($data as $data){
                             $content .= '"'.$data->xpo_loc.'"'.' '.'"'.$data->xpo_lot.'"'.' '.'"'.$data->xpo_ref.'"'.' '.'-'.' '.PHP_EOL.'"'.$data->xpo_qty_rcvd.'"'.PHP_EOL;
                         }
 
                             $content .= '.'.PHP_EOL;
 
                     }   
                         $content .= '.'.PHP_EOL;
 
                 }
 
                 File::put('cim/xxcimporcp.cim',$content); 
 
                 // dd('ok');
 
                 // buat jalanin sim ke QAD
                 exec("start cmd /c cimporcpt.bat");
 
                 // Update Details -> Closed klo qty Rec = qty ord
 
                 //dd('123');
 
                 $data =  DB::table('xpo_receipt')
                             ->where('xpo_user','=',Session::get('userid'))
                             ->selectRaw('xpo_nbr,xpo_line,xpo_rcp_id,xpo_sj_id,sum(xpo_qty_rcvd) as tot_rcvd ,xpo_qty_ord')
                             ->groupBy('xpo_nbr')
                             ->groupBy('xpo_line')
                             ->get();
 
                 foreach($data as $data){
                     $datapo = DB::table('xpo_mstrs')
                                 ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                                 ->where('xpod_dets.xpod_line','=',$data->xpo_line)
                                 ->where('xpod_nbr','=',$data->xpo_nbr)
                                 ->first();
                     
                     $totalqtyafter = $datapo->xpod_qty_rcvd + $data->tot_rcvd;
 
 
                     if($totalqtyafter >= $data->xpo_qty_ord){
                         DB::table('xpod_dets')
                             ->where('xpod_nbr','=',$data->xpo_nbr)
                             ->where('xpod_line','=',$data->xpo_line)
                             ->update([
                                     'xpod_qty_rcvd' => $totalqtyafter,
                                     'xpod_status' => 'Closed'
                             ]);
 
                         // Ubah Master jadi closed klo smua detail closed.
                         $mstr = DB::table('xpo_mstrs')
                                     ->join('xpod_dets','xpod_nbr','=','xpo_nbr')
                                     ->where('xpo_nbr','=',$data->xpo_nbr)
                                     ->where('xpod_dets.xpod_status','!=','Closed')
                                     ->first();
 
                         if(is_null($mstr)){
                             // Semua detail closed.
                             DB::table('xpo_mstrs')
                                     ->where('xpo_nbr','=',$data->xpo_nbr)
                                     ->update([
                                             'xpo_status' => 'Closed'
                                     ]); 
                         }
 
 
                         $updatedata = DB::table('xpod_dets')
                                         ->join('xpo_mstrs','xpo_nbr','=','xpod_nbr')
                                         ->where('xpod_nbr','=',$data->xpo_nbr)
                                         ->where('xpod_line','=',$data->xpo_line)
                                         ->first();
 
                         if(!is_null($updatedata)){
                             DB::table('xpo_hist')
                                 ->insert([
                                         'xpo_domain' => $updatedata->xpo_domain,
                                         'xpo_nbr' => $updatedata->xpo_nbr,
                                         'xpo_line' => $updatedata->xpod_line,
                                         'xpo_part' => $updatedata->xpod_part,
                                         'xpo_desc' => $updatedata->xpod_desc,
                                         'xpo_um' => $updatedata->xpod_um,
                                         'xpo_qty_ord' => $updatedata->xpod_qty_ord,
                                         'xpo_qty_rcvd' => $totalqtyafter,
                                         'xpo_qty_open' => $updatedata->xpod_qty_open,
                                         'xpo_qty_prom' => $updatedata->xpod_qty_prom,
                                         'xpo_price' => $updatedata->xpod_price,
                                         'xpo_loc' => $updatedata->xpod_loc,
                                         'xpo_lot' => $updatedata->xpod_lot,
                                         'xpo_due_date' => $updatedata->xpo_due_date,
                                         'xpo_vend' => $updatedata->xpo_vend,
                                         'xpo_status' => 'Closed',
                                         'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                         'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                 ]);
                         }
 
                     }else{
                         DB::table('xpod_dets')
                             ->where('xpod_nbr','=',$data->xpo_nbr)
                             ->where('xpod_line','=',$data->xpo_line)
                             ->update([
                                     'xpod_qty_rcvd' => $totalqtyafter,
                             ]);
 
                         $updatedata = DB::table('xpod_dets')
                                         ->join('xpo_mstrs','xpo_nbr','=','xpod_nbr')
                                         ->where('xpod_nbr','=',$data->xpo_nbr)
                                         ->where('xpod_line','=',$data->xpo_line)
                                         ->first();
                                         
                         if(!is_null($updatedata)){
                             DB::table('xpo_hist')
                                 ->insert([
                                         'xpo_domain' => $updatedata->xpo_domain,
                                         'xpo_nbr' => $updatedata->xpo_nbr,
                                         'xpo_line' => $updatedata->xpod_line,
                                         'xpo_part' => $updatedata->xpod_part,
                                         'xpo_desc' => $updatedata->xpod_desc,
                                         'xpo_um' => $updatedata->xpod_um,
                                         'xpo_qty_ord' => $updatedata->xpod_qty_ord,
                                         'xpo_qty_rcvd' => $totalqtyafter,
                                         'xpo_qty_open' => $updatedata->xpod_qty_open,
                                         'xpo_qty_prom' => $updatedata->xpod_qty_prom,
                                         'xpo_price' => $updatedata->xpod_price,
                                         'xpo_loc' => $updatedata->xpod_loc,
                                         'xpo_lot' => $updatedata->xpod_lot,
                                         'xpo_due_date' => $updatedata->xpo_due_date,
                                         'xpo_vend' => $updatedata->xpo_vend,
                                         'xpo_status' => 'Open', // Statusnya masi buka blom closed
                                         'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                         'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                                 ]);
                         }
                     }            
                 }
    
 
                 // Hapus Table PO_receipt
                 DB::table('xpo_receipt')
                         ->where('xpo_user','=',Session::get('userid'))
                         ->delete();
 
                 // Update Table SJ --> Closed
                 DB::table('xsj_mstr')
                         //->where('xsj_id','=',$header->xpo_sj_id)
                         ->where('xsj_sj','=',$header->xpo_sj_id)
                         ->update([
                                 'xsj_status' => 'Closed' // SJ Sudah selesai
                         ]);
 
                 $date = Carbon::now('ASIA/JAKARTA')->format('ymd');
                 // session()->flash("updated","Data is successfully updated to QAD");
                 alert()->success('Success','Data is succesfully Updated to QAD');
                 
                 return redirect()->route('poreceipt');  
                 //return redirect()->back()->with(['updated'=>'Data Berhasil Diupdate ke QAD']);
 
                 //return view('/po/poreceipt',['date'=>$date]);
 
             }else{
                 // Error balikin menu awal
                 return redirect()->back()->with(['error'=>$note]);
             }
 
         }
    }

    // PO Receipt
    public function receiptupdate(Request $req){
        //dd($req->all());

        $domain = $req->input('m_domain');
        $ponbr = $req->input('m_ponbr');
        $sj = $req->input('m_sj');
        $line = $req->input('m_line');
        $itemcode = $req->input('m_itemcode');
        $qtyord = $req->input('m_qtyord');
        $itemdesc = $req->input('m_itemdesc');
        $qtyship = $req->input('m_qtyship');

        $qtyopen = $req->input('m_qtyopen'); //---
        $qtyrec = $req->input('m_qtyrec'); //---
        //$boflg = $req->input('boflg'); //---

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
        $new_qtyrec = $qtyrec + $old_data->xpod_qty_rcvd;
        

        // Validasi WSA
        $wsa = DB::table('wsas')
                ->first();

        $qxUrl          = $wsa->wsas_url;  /*services/wsdl*/
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';
        $timeout        = 0;

        $domain         = $wsa->wsas_domain;

        $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                         '<Body>'.
                         '<porcp xmlns="'.$wsa->wsas_path.'">'.
                         '<inpdomain>'.$domain.'</inpdomain>'.
                         '<innbr>'.$ponbr.'</innbr>'.
                         '<inline>'.$line.'</inline>'.
                         '</porcp>'.
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
        
        $xmlResp->registerXPathNamespace('ns1', $wsa->wsas_path);
        
        //$qdocResult[] =  $xmlResp->xpath('//ns1:t_rcvd');
        //dd($qdocResult[0]);
        
        $qdocResult = '';
        foreach($xmlResp->xpath('//ns1:t_rcvd') as $data) {
            $qdocResult = (string) $xmlResp->xpath('//ns1:t_rcvd')[0];             
        }
        // cari qty rcvd di web 
        $qtyrcvd_web = DB::table('xpod_dets')
                        ->where('xpod_dets.xpod_nbr','=',$ponbr)
                        ->where('xpod_dets.xpod_line','=',$line)
                        ->where('xpod_dets.xpod_domain','=',$domain)
                        ->first();

        if($qdocResult == $qtyrcvd_web->xpod_qty_rcvd){
            // validasi klo sesuai lanjut
            // Update Data Ke DB
            // qty open > 0 data masih muncul
            if($new_qtyopen > 0){
                
                // POD Detail
                DB::table('xpod_dets')
                    ->where('xpod_nbr', $ponbr)
                    ->where('xpod_line', $line)
                    ->update([
                            'xpod_qty_open' => $new_qtyopen,
                            'xpod_qty_rcvd' => $new_qtyrec,
                            //'xpod_cancel' => $boflg,
                            'xpod_um' => $um,
                            'xpod_site' => $site,
                            'xpod_loc' => $loc,
                            'xpod_lot' => $lot,
                            'xpod_ref' => $ref,
                            'xpod_eff_date' => $effdate,
                            'xpod_ship_date' => $shipdate,
                    ]);

                // SJ Mstr
                DB::table('xsj_mstr')
                    ->where('xsj_po_nbr', $ponbr)
                    ->where('xsj_line', $line)
                    ->update([
                            'xsj_qty_open' => $new_qtyopen
                            //'xsj_qty_ship' => $new_qtyrec
                    ]);
            
                //$query = "xsj_mstr.xsj_id like '%".$sj."%' AND xsj_mstr.xsj_status = 'Created' ";

                $query = "xsj_mstr.xsj_sj like '%".$sj."%' AND xsj_mstr.xsj_status = 'Created' ";


                $users=DB::table("xsj_mstr")
                                ->join("xpo_mstrs",'xpo_mstrs.xpo_nbr','=','xsj_mstr.xsj_po_nbr')
                                ->join("xpod_dets", function($join){
                                    $join->on('xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
                                         ->on('xsj_mstr.xsj_line','=','xpod_dets.xpod_line');
                                })
                                ->whereRaw($query)
                                ->get();  

                $date = Carbon::now('ASIA/JAKARTA')->format('ymd');
                return view('/po/poreceipt', compact('users','date'));


            }else{
                
                DB::table('xpod_dets')
                    ->where('xpod_nbr', $ponbr)
                    ->where('xpod_line', $line)
                    ->update([
                            //'xpod_qty_open' => $new_qtyopen,
                            'xpod_qty_open' => '0',
                            'xpod_qty_rcvd' => $new_qtyrec,
                            //'xpod_cancel' => $boflg,
                            'xpod_um' => $um,
                            'xpod_site' => $site,
                            'xpod_loc' => $loc,
                            'xpod_lot' => $lot,
                            'xpod_ref' => $ref,
                            'xpod_eff_date' => $effdate,
                            'xpod_ship_date' => $shipdate,
                            'xpod_status' => 'Closed',
                    ]);

                DB::table('xpo_mstrs')
                    ->where('xpo_nbr', $ponbr)
                    ->update([
                            'xpo_status' => 'Closed',
                    ]);    

                DB::table('xsj_mstr')
                    ->where('xsj_po_nbr', $ponbr)
                    ->where('xsj_line', $line)
                    ->update([
                            'xsj_status' => 'Closed',
                    ]);  
            }



            // Disini Kirim Data Ke QAD, Lewat Textfile pake .Bat kirim ke Server QAD
            $content = '';

            //$file_effdate = date('d/m/y',$new_effdate);
            //$file_shipdate = date('d/m/y',$new_shipdate);
            $file_effdate = date('m/d/y',$new_effdate); 
            $file_shipdate = date('m/d/y',$new_shipdate);

            $content .= '"'.$ponbr.'"'
                        .PHP_EOL.'"'.$sj.'"'.' '.'-'.' '.$file_effdate.' '.'"no"'.' '.'"no"'.' '.'"no"'.' '.'"no"'.' '.$file_shipdate
                        .PHP_EOL.'"'.$line.'"'
                        .PHP_EOL.'"'.$qtyrec.'"'.' '.'-'.' '.'"no"'.' '.'"'.$um.'"'.' '.'-'.' '.'-'.' '.'"'.$site.'"'.' '.'"'.$loc.'"'.' '.'"'.$lot.'"'.' '.'"'.$ref.'"'.' '.'-'.' '    .'-'.' '.'"no"'.' '.'"no"'.' '.'"no"'
                        .PHP_EOL.'.'
                        .PHP_EOL.'.';

            File::put('cim/xxcimporcp.cim',$content); 

            exec("start cmd /c cimporcpt.bat");

            // session()->flash("updated","PO Receipt is Successful");
            alert()->success('Success','PO Receipt is Successful');
                  
            return back();
        }else{
            // validasi error , qty rcvd web != qad
            
            // session()->flash("error","Qty Received Web is different from QAD, Refresh PO / Contact your Admin");
            alert()->error('Error','Qty Received Web is different from QAD, Refresh PO / Contact your Admin');

            return back();

        }
    }

}
