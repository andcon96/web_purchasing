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
use App\Jobs\SendEmailJob;
use App\Jobs\EmailRfqReject;
use App\Jobs\EmailRfqSend;

class RfqMaintenanceController extends Controller
{
    // Menu Purchasing
    public function index()
    {   
        $alert = DB::table('xrfq_mstrs')
                        ->first();


    	return view('/setting/rfqmaint',['alert'=>$alert]);
    }

    public function update(Request $req)
    {        

        $prefix = $req->input('prefix');
    	$curnumber = $req->input('curnumber');

        $poallowed = $req->input('cbpoallowed');
        $prallowed = $req->input('cbprallowed');

        $prefixpo = $req->input('prefix_po');
        $prefixpr = $req->input('prefix_pr');
        $nbrpo = $req->input('ponbr');
    	$nbrpr = $req->input('prnbr');

        $data = DB::Table('xrfq_mstrs')
                    ->count();


        if($data == 0){
            DB::table('xrfq_mstrs')
                ->insert([
                        'xrfq_prefix' => $prefix,
                        'xrfq_nbr' => $curnumber,
                        'xrfq_po' => $poallowed,
                        'xrfq_pr' => $prallowed,
                        'xrfq_pr_prefix' => $prefixpr,
                        'xrfq_po_prefix' => $prefixpo,
                        'xrfq_po_nbr' => $nbrpo,
                        'xrfq_pr_nbr' => $nbrpr,
                        'xrfq_rfp_prefix' => $req->prefix_rfp,
                        'xrfq_rfp_nbr' => $req->rfpnbr,
                ]);

            // session()->flash("updated","Data is Successfully Updated !");
            alert()->success('Success','Data is Succesfully Updated');
                  
            return back();
        }else{
            if($req->get('cbpoallowed') == null){
                $poallowed = "No";
            }else {
                $poallowed = $req->input('cbpoallowed');    
            }

            if($req->get('cbprallowed') == null){
                $prallowed = "No";
            }else {
                $prallowed = $req->input('cbprallowed');    
            }

            DB::table('xrfq_mstrs')
                ->update([
                        'xrfq_prefix' => $prefix,
                        'xrfq_nbr' => $curnumber,
                        'xrfq_po' => $poallowed,
                        'xrfq_pr' => $prallowed,
                        'xrfq_pr_prefix' => $prefixpr,
                        'xrfq_po_prefix' => $prefixpo,
                        'xrfq_po_nbr' => $nbrpo,
                        'xrfq_pr_nbr' => $nbrpr,
                        'xrfq_rfp_prefix' => $req->prefix_rfp,
                        'xrfq_rfp_nbr' => $req->rfpnbr,
                ]);

            // session()->flash("updated","Data is Successfully Updated !");
            alert()->success('Success','Data is Succesfully Updated');
                  
            return back();
        }	
    }

    public function viewinput(Request $req)
    {

        try{
            $alert = DB::table('xrfq_mstrs')
                        ->first();

            $date = Carbon::now()->format('ymd');

            $part = DB::table('xitemreq_mstr')
                            ->distinct()
                            ->select('xitemreq_part','xitemreq_desc')
                            ->get();

            $users = DB::table('xalert_mstrs')
                            ->join('users','xalert_supp','=','supp_id')
                            ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                            ->get();

            $supplier = DB::table('xalert_mstrs')
                            ->join('users','xalert_supp','=','supp_id')
                            ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                            ->get();

            $bid = DB::table("xbid_mstr")
                    ->leftjoin("xitemreq_mstr",'xitemreq_mstr.xitemreq_part','=','xbid_part')
                    ->where("xbid_mstr.xbid_flag",'<=', '1')
                    ->orderby('xbid_mstr.xbid_id','Desc')
                    ->paginate(10);  

            $site = DB::table('xsite_mstr')
                    ->get();
            
            $item = DB::table("xitemreq_mstr")
                    ->get();
                    

            if($req->ajax()){
                return view('/rfq/loadpurch', compact('alert','date','part','users','bid','supplier','item','site'));
            }
            
            return view('/rfq/rfqinput', compact('alert','date','part','users','bid','supplier','item','site'));

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

    public function suppsearch(Request $req)
    {
        if($req->ajax())
        {
        $output=""; 
        $flg = 0;

        if($req->search != ""){
            $users=DB::table("xsurel_mstrs")
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xsurel_mstrs.xsurel_supp')
                                ->where("xsurel_part","LIKE",$req->search."%")
                                ->get();

            if($users)
                {
                foreach ($users as $key => $users) {
                
                $output.= "<tr>".

                "<td>
                    <input type='hidden' id= 'suppname[]' name='suppname[]' class='form-control form-control-sm' 
                        value="."'".$users->xalert_supp."'"." readonly>".$users->xalert_supp.' -   '.$users->xalert_nama."</input>
                </td>".

                "
                    <input type='hidden' name='suppflg[".$flg."]' value='Yes'/>
                    <td data-title='Action'><input type='button' class='ibtnDel btn btn-md bt-action'  value='Delete'></td>
                ".


                "</tr>";

                $flg = $flg + 1;
                }
            return Response($output);
                } 
            }
        
        }
    }

    public function insertpurch(Request $req)
    {
        // dd($req->all());

        $prefix = DB::table('xrfq_mstrs')
                    ->first();
        $date = Carbon::now()->format('ymd');
     
        //$rfq_nbr = $req->input('rfqnumber');
        $rfq_nbr = $prefix->xrfq_prefix.$prefix->xrfq_nbr;


        $item_desc = '';
        if($req->itempart == null){
            $item_part = $req->input('m_itempart');
            $item_desc = $req->input('m_itemdesc');
        }else{
            $item_part = $req->input('itempart');
        }
        

        $qty_req = $req->input('qtyreq');
        $due_date = $req->input('duedate');
        $remarks = $req->input('remarks');
        $price_min = $req->input('pricemin');
        $price_max = $req->input('pricemax');
        $start_date = $req->input('startdate');
        $site = $req->input('site');
        $um = $req->input('um');

        $suppname = $req->input('suppname');
        $suppflg = $req->input('suppflg');      

        if($suppname == null){
            alert()->error('Error','Supplier Cannot be Blank');
            return back();
            // return redirect()->back()->with('error','Supplier Cannot be Blank'); 
        }else{

            $flg = '';
            foreach($suppname as $suppname){

                if(str_contains($flg,$suppname)){
                    alert()->error('Error','Supplier Cannot be Duplicate');
                    return back();
                    // return redirect()->back()->with('error','Supplier Cannot be Duplicate'); 
                }
                
                $flg .= $suppname;
            }

            if((int)$price_min > (int)$price_max){
                alert()->error('Error','Price Min Lebih Besar dari Price Max');
                return back();
                // return redirect()->back()->with('error','Price Min Lebih Besar dari Price Max');     
            }

            // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
            $new_format_start_date = str_replace('/', '-', $start_date); 
            $new_format_due_date = str_replace('/', '-', $due_date);

            // ubah ke int
            $new_start_date = strtotime($new_format_start_date);
            $new_due_date = strtotime($new_format_due_date);

            // ubah ke format date
            $formatted_date = date('Y-m-d',$new_start_date);
            $formatted_due_date = date('Y-m-d',$new_due_date);

            // Buat Nama File Unik di Public

            $savepath = "";
            $filename = "";

            if($req->hasFile('file')){
                $dataTime = date('Ymd_His');
                $file = $req->file('file');
                $filename = $dataTime . '-' .$file->getClientOriginalName();

                // Simpan File Upload pada Public
                $savepath = public_path('/upload/');
                $file->move($savepath, $filename);
            }


            $data = array(
                'xbid_id'=>$rfq_nbr,
                'xbid_part'=>$item_part,
                'xbid_qty_req'=>$qty_req,
                'xbid_due_date'=>$formatted_due_date,
                'xbid_start_date'=>$formatted_date,
                'xbid_remarks'=>$remarks,
                'xbid_price_min'=>$price_min,
                'xbid_price_max'=>$price_max,
                'xbid_attch'=>$savepath.$filename,
                'xbid_site'=>$site,
                'xbid_um'=>$um,
                'xbid_desc'=>$item_desc,
            );
             
            
            try{
                // table header
                DB::table('xbid_mstr')->insert($data);
                // table detail
                if(count($req->suppname) >= 0){
                    foreach($req->suppname as $item=>$v){
                        
                        $data2=array(
                            'xbid_id'=>$rfq_nbr,
                            'xbid_qty'=>$qty_req,
                            'xbid_date'=>$formatted_due_date,
                            'xbid_supp'=>$req->suppname[$item],
                            'xbid_apprv'=>$req->suppflg[$item],
                        );                
                            DB::table('xbid_det')->insert($data2);

                    }
                }

                //table history
                if(count($req->suppname) >= 0){
                    foreach($req->suppname as $item=>$v){
                        
                        $data3=array(
                            'xbid_nbr'=>$rfq_nbr,
                            'xbid_qty_req'=>$qty_req,
                            'xbid_due_date'=>$formatted_due_date,
                            'xbid_start_date'=>$formatted_date,
                            'xbid_part'=>$item_part,
                            'xbid_attch'=>$savepath.$filename,
                            'xbid_price_min'=>$price_min,
                            'xbid_price_max'=>$price_max,
                            'xbid_remarks'=>$remarks,
                            'xbid_supp'=>$req->suppname[$item],
                            'xbid_apprv'=>$req->suppflg[$item],
                            'xbid_hist_remarks'=>'Supplier Create RFQ',
                            'xbid_site'=>$req->site,
                            'xbid_um'=>$um,
                            'xbid_desc'=>$item_desc,
                        );                
                            DB::table('xbid_hist')->insert($data3);
                    }
                }

                    
                // Buat Nomor RFQ Baru + 1;

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


            alert()->success('Success','RFQ No. : '.$rfq_nbr.' is created');
            return back();
            // return redirect()->back()->with('updated','RFQ No. : '.$rfq_nbr.' is created');     
        }         
    }

    public function updatepurch(Request $req)
    {
        $u_rfqnumber = $req->input('u_rfqnumber');
        $u_qtyreq = $req->input('u_qtyreq');
        $u_duedate = $req->input('u_duedate');
        $u_startdate = $req->input('u_startdate');
        $u_itempart = $req->input('u_itempart');
        $u_pricemin = $req->input('u_pricemin');
        $u_pricemax = $req->input('u_pricemax');
        $u_remarks = $req->input('u_remarks');
        $site = $req->input('rfqsite');
        $suppname = $req->suppname; // 22072020
        $um = $req->input('u_um');


        // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
        $new_format_start_date = str_replace('/', '-', $u_startdate); 
        $new_format_due_date = str_replace('/', '-', $u_duedate);

        // ubah ke int
        $new_start_date = strtotime($new_format_start_date);
        $new_due_date = strtotime($new_format_due_date);

        // ubah ke format date
        $formatted_date = date('Y-m-d',$new_start_date);
        $formatted_due_date = date('Y-m-d',$new_due_date);

        if((int)$u_pricemin > (int)$u_pricemax){
            alert()->error('Error','Price Min Lebih Besar dari Price Max');
            return back();
            // return redirect()->back()->with('error','Price Min Lebih Besar dari Price Max');     
        }

        if($suppname == null){
            alert()->error('Error','Supplier Cannot be Blank');
            return back();
            // return redirect()->back()->with('error','Supplier Cannot be Blank'); 
        }else{

            $flg = '';
            foreach($suppname as $suppname){
                // Cek inputan double ato ga 
                if(str_contains($flg,$suppname)){
                    alert()->error('Error','Supplier Cannot be Duplicate');
                    return back();
                    // return redirect()->back()->with('error','Supplier Cannot be Duplicate'); 
                }
                
                $flg .= $suppname;
            }

        } // 22072020

        // Buat Temp, Hapus yang ga ada di request
        Schema::create('temp_table', function($table)
        {
            $table->string('xbid_nbr');
            $table->string('xbid_supp');
            $table->temporary();
        });
        
        $dataold = DB::table('xbid_det')
                        ->where('xbid_det.xbid_id','=',$u_rfqnumber)
                        ->get();
        
        foreach($dataold as $dataold){
            $coba1=DB::table('temp_table')
                    ->insert([
                        'xbid_nbr' => $dataold->xbid_id,
                        'xbid_supp' => $dataold->xbid_supp  
                    ]);

        }





        try{
            //update table header
            $data = DB::table('xbid_mstr')
                        ->where('xbid_id','=',$u_rfqnumber)
                        ->first();

            $oldqty = $data->xbid_qty_req;
            $oldpricemin = $data->xbid_price_min;
            $oldpricemax = $data->xbid_price_max;

            DB::table('xbid_mstr')
            ->where('xbid_id', $u_rfqnumber)
            ->update([
                    'xbid_price_min' => $u_pricemin,
                    'xbid_price_max' => $u_pricemax,
                    'xbid_qty_req' => $u_qtyreq,
                    'xbid_um' => $um,
            ]);

            // $tabletmp = DB::table('temp_table')->get();
            // delete temp table det -> dapetin yang perlu didelete di master
            if(count($req->suppname) >= 0){
                foreach($req->suppname as $item=>$v){
                    DB::table('temp_table')
                            ->where('xbid_supp','=',$req->suppname[$item])
                            ->delete();
                }
            }

            $tabletmp = DB::table('temp_table')->get();
            foreach($tabletmp as $tabletmp){
                // Create Histroy
                DB::table('xbid_hist')
                        ->insert([
                                'xbid_nbr'=>$u_rfqnumber,
                                'xbid_qty_req'=>$u_qtyreq,
                                'xbid_due_date'=>$formatted_due_date,
                                'xbid_start_date'=>$formatted_date,
                                'xbid_part'=>$u_itempart,
                                'xbid_price_min'=>$u_pricemin,
                                'xbid_price_max'=>$u_pricemax,
                                'xbid_remarks'=>$u_remarks,
                                'xbid_supp'=>$req->suppname[$item],
                                'xbid_apprv'=>'Yes',
                                'xbid_hist_remarks'=>'Purchasing Update RFQ, Delete Supplier',
                                'xbid_site'=>$site,
                                'xbid_um'=>$um,
                        ]);


                // Delete Detail
                DB::table('xbid_det')
                        ->where('xbid_det.xbid_id','=',$tabletmp->xbid_nbr)
                        ->where('xbid_det.xbid_supp','=',$tabletmp->xbid_supp)
                        ->delete();
            }

            // table detail + History
            if(count($req->suppname) >= 0){
                foreach($req->suppname as $item=>$v){

                    $checkdata = DB::table('xbid_det')
                                ->where('xbid_det.xbid_id','=',$u_rfqnumber)
                                ->where('xbid_det.xbid_supp','=',$req->suppname[$item])
                                ->first();

                    if(is_null($checkdata)){
                        $data2=array(
                            'xbid_id'=>$u_rfqnumber,
                            'xbid_qty'=>$u_qtyreq,
                            'xbid_date'=>$formatted_due_date,
                            'xbid_supp'=>$req->suppname[$item],
                            'xbid_apprv'=>'Yes',
                        );                
                            DB::table('xbid_det')->insert($data2);

                        $data3=array(
                                'xbid_nbr'=>$u_rfqnumber,
                                'xbid_qty_req'=>$u_qtyreq,
                                'xbid_due_date'=>$formatted_due_date,
                                'xbid_start_date'=>$formatted_date,
                                'xbid_part'=>$u_itempart,
                                'xbid_price_min'=>$u_pricemin,
                                'xbid_price_max'=>$u_pricemax,
                                'xbid_remarks'=>$u_remarks,
                                'xbid_supp'=>$req->suppname[$item],
                                'xbid_apprv'=>'Yes',
                                'xbid_hist_remarks'=>'Purchasing Update RFQ',
                                'xbid_site'=>$site,
                                'xbid_um'=>$um,
                            );                
                            DB::table('xbid_hist')->insert($data3);
                    }
                }

            }

            // email ga jadi 22072020, History Brubah
            /*
                $email = DB::table('xbid_mstr')
                        ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                        ->where('xbid_mstr.xbid_id','=',$u_rfqnumber)
                        ->get();        

                $listemail = '';

                foreach($email as $email){
                    $listuser = DB::table('users')
                                    ->where('users.supp_id','=',$email->xbid_supp)
                                    ->get();
                    foreach($listuser as $listuser){
                        $listemail .= $listuser->email.',';
                    }
                }

                $new_listemail = substr($listemail, 0, strlen($listemail) - 1);
                
                $array_email = explode(',', $new_listemail); 


                Mail::send('emailrfqedit', 
                    ['pesan' => 'An RFQ has been updated by Purchasing',
                     'note1' => $u_rfqnumber,
                     'note2' => $u_qtyreq,
                     'note3' => $oldqty,
                     'note4' => $u_pricemin,
                     'note5' => $oldpricemin,
                     'note6' => $u_pricemax,
                     'note7' => $oldpricemax],  
                    function ($message) use ($array_email,$oldqty,$oldpricemax,$oldpricemin)
                {
                    $message->subject('Notifikasi Web Support IMI');
                    $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                    $message->to($array_email);
                });
            

            //create table hist
            $data1=array(
            'xbid_nbr'=>$u_rfqnumber,
            'xbid_qty_req'=>$u_qtyreq,
            'xbid_due_date'=>$u_duedate,
            'xbid_start_date'=>$u_startdate,
            'xbid_part'=>$u_itempart,
            'xbid_price_min'=>$u_pricemin,
            'xbid_price_max'=>$u_pricemax,
            'xbid_remarks'=>$u_remarks,
            'xbid_flag' => '0',
            'xbid_hist_remarks'=>'Purchasing Update Bid',
            'xbid_site'=>$site,
            );                
            DB::table('xbid_hist')->insert($data1);
            */


            // drop temp table
            Schema::drop('temp_table');

            alert()->success('Success','Data is successfully updated');
            return back();
            // return redirect()->back()->with('updated','Data has successfully updated');   

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

    public function searchum(Request $req){
        if($req->ajax()){
            $output = '';

            $data = DB::table('item_konversi')
                        ->where('item_code','=',$req->search)
                        ->get();

            $umbase = DB::table('xitemreq_mstr')
            			->where('xitemreq_part','=',$req->search)
            			->first();

            if(count($data) != 0){
                $umawal = DB::table('item_konversi')
                            ->where('item_code','=',$req->search)
                            ->first();

        		if($umbase->xitemreq_um == $umawal->um_1){
        			$output .= '<option value = "'.$umawal->um_1.'" selected="selected">'.$umawal->um_1.'</option>';
        		}else{
        			$output .= '<option value = "'.$umawal->um_1.'">'.$umawal->um_1.'</option>';
        		}
                foreach($data as $data){
                	if($umbase->xitemreq_um == $data->um_2){
                		$output .= '<option value="'.$data->um_2.'" selected="selected">'.$data->um_2.'</option>';
                	}else{
                		$output .= '<option value="'.$data->um_2.'">'.$data->um_2.'</option>';
                	}
                    
                }

            }else{
                $datas = DB::table('item_um')
                        ->get();

                foreach($datas as $datas){
                	if($umbase->xitemreq_um == $datas->um){
                		$output .= '<option value="'.$datas->um.'" selected>'.$datas->um.'</option>';
                	}else{
                		$output .= '<option value="'.$datas->um.'">'.$datas->um.'</option>';
                	}
                    
                }

            }

            return response($output);
            
        }
    }

    public function searchumedit(Request $req){
        if($req->ajax()){
            $output = '';

            $data = DB::table('item_konversi')
                        ->where('item_code','=',$req->search)
                        ->get();

            if(count($data) != 0){
                $umawal = DB::table('item_konversi')
                            ->where('item_code','=',$req->search)
                            ->first();
                    if($umawal->um_1 == $req->um){
                        $output .= '<option value='.$umawal->um_1.' selected>'.$umawal->um_1.'</option>';
                    }else{
                        $output .= '<option value='.$umawal->um_1.'>'.$umawal->um_1.'</option>';
                    }
                foreach($data as $data){
                    if($data->um_2 == $req->um){
                        $output .= '<option value='.$data->um_2.' selected>'.$data->um_2.'</option>';
                    }else{
                        $output .= '<option value='.$data->um_2.'>'.$data->um_2.'</option>';
                    }
                }

            }else{
                $datas = DB::table('item_um')
                        ->get();

                foreach($datas as $datas){
                    if($datas->um == $req->um){
                        $output .= '<option value='.$datas->um.' selected>'.$datas->um.'</option>';
                    }else{
                        $output .= '<option value='.$datas->um.'>'.$datas->um.'</option>';
                    }
                   
                }

            }

            return response($output);
            
        }
    }

    // Approve Supplier
    public function viewinputsupp()
    {
        try{
            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){
                $totalrfq = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->count();

                $totunapprfq = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->where('xbid_mstr.xbid_flag','<=','1')
                            ->count();

                $totnoresp = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->where('xbid_mstr.xbid_flag','=','0')
                            ->count();
                $users = DB::table("xbid_mstr")
                        ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                        ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                        ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                        ->orderBy('xbid_mstr.xbid_id','DESC')
                        ->paginate(10);  

                return view('/rfq/rfqinputsupp',['users'=>$users,'totalrfq'=>$totalrfq,'totunapprfq'=>$totunapprfq,'totnoresp'=>$totnoresp]);
                }else{

                $totalrfq = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->where('xbid_det.xbid_supp','=',Session::get('supp_code'))
                            ->count();

                $totunapprfq = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->where('xbid_det.xbid_supp','=',Session::get('supp_code'))
                            ->where('xbid_mstr.xbid_flag','<=','1')
                            ->count();

                $totnoresp = DB::table('xbid_mstr')
                            ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                            ->where('xbid_det.xbid_supp','=',Session::get('supp_code'))
                            ->where('xbid_mstr.xbid_flag','=','0')
                            ->count();

                $users = DB::table("xbid_mstr")
                        ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                        ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                        ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                        ->where("xbid_det.xbid_supp",'=', Session::get('supp_code'))
                        ->orderBy('xbid_mstr.xbid_id','DESC')
                        ->paginate(10);

                return view('/rfq/rfqinputsupp',['users'=>$users,'totalrfq'=>$totalrfq,'totunapprfq'=>$totunapprfq,'totnoresp'=>$totnoresp]);
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

    public function downloadfile(Request $req, $id)
    {
        $path = DB::table('xbid_mstr')
                ->where('xbid_id', $id)
                ->select('xbid_attch')
                ->first();

        if(is_null($path->xbid_attch)) // <----
        {
            // session()->flash("error","There Is No Attachment to this RFQ Number");
            alert()->error('Error','There is no Attachment to this RFQ Number');
            return back();
        }else{
            try{
                return response()->download($path->xbid_attch);
            }catch(\InvalidArgumentException $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }catch(\Exception $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }catch(\Error $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }
        }
    }

    public function purchbid(Request $req)
    {
        $edit_id = $req->input('edit_id');
        $supplier = $req->input('supplier');
        $rfqsite = $req->input('rfqsite');
        $startdate = $req->input('startdate');

        $rfqnbr = $req->input('rfqnbr');
        $old_itemcode = $req->input('itemcode');
        $itemcode = substr($old_itemcode, 0,strpos($old_itemcode,'-',0) - 1);
        $qtyreq =  str_replace(',', '', $req->input('qtyreq'));
        $duedate = $req->input('duedate');
        $pricemin = str_replace(',', '', $req->input('pricemin'));
        $pricemax = str_replace(',', '', $req->input('pricemax'));

        $old_prodate = $req->input('prodate');
        $proqty = $req->input('qtypro');
        $proprice = $req->input('proprice');
        $remarks = $req->input('remarks');


        // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
        $new_format_pro_date = str_replace('/', '-', $old_prodate); 

        // ubah ke int
        $new_pro_date = strtotime($new_format_pro_date);

        // ubah ke format date
        $prodate = date('Y-m-d',$new_pro_date);

        
         // Buat Nama File Unik di Public

        $savepath = "";
        $filename = "";

        if($req->hasFile('file')){
            $dataTime = date('Ymd_His');
            $file = $req->file('file');
            $filename = $dataTime . '-' .$file->getClientOriginalName();

            // Simpan File Upload pada Public
            $savepath = public_path('/upload_supplier/');
            $file->move($savepath, $filename);
        }
        
        try{
            // update table 
            DB::table('xbid_det')
            ->where('xbid_det_id', $edit_id)
            ->update([
                    'xbid_pro_qty' => $proqty,
                    'xbid_pro_date' => $prodate,
                    'xbid_pro_price' => $proprice,
                    'xbid_pro_remarks' => $remarks,
                    'xbid_pro_attch' => $savepath.$filename,
                    'xbid_user' => Session::get('userid'), // Biar tau user siapa dari supplier itu yang input
                    'xbid_flag' => '1', //0 = dibuat purch, 1 = dibid supplier
            ]);

            //update master flag 1 --> mesti validasi minta user purch update yes
            DB::table('xbid_mstr')
            ->where('xbid_id', $rfqnbr)
            ->update([
                    'xbid_flag' => '1', // Status Closed
            ]);

            //table history
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
                'xbid_pro_remarks' => $remarks,
                'xbid_pro_attch' => $savepath.$filename,
                'xbid_flag' => '1',
                'xbid_hist_remarks'=>'Supplier Input BID',
                'xbid_supp'=>$supplier,
                'xbid_site'=>$rfqsite,
                'xbid_start_date'=>$startdate,
            );                
            DB::table('xbid_hist')->insert($data3);
            
            // Baca Supplier Email di Users
            $emailpur = DB::table('xalert_mstrs')
                    ->join("users",'users.supp_id','=','xalert_mstrs.xalert_supp')
                    ->select('xalert_not_pur','name','xalert_nama','id')
                    ->where('xalert_supp','=',Session::get('supp_code'))
                    ->first();

            $user = App\User::where('id','=', $emailpur->id)->first(); // user siapa yang terima notif (lewat id)
                        
            $details = [
                'body' => 'Supplier : '.$emailpur->xalert_nama.' has made an offer for following RFQ',
                'url' => 'rfq',
                'nbr' => $rfqnbr,
                'note' => 'Please check'
            ]; // isi data yang dioper
                                
                            
            $user->notify(new \App\Notifications\eventNotification($details));

            $item = DB::table('xitemreq_mstr')
                    ->where('xitemreq_mstr.xitemreq_part',$itemcode)
                    ->first();

            $itemdesc = '';

            if($item){
                $itemdesc = $item->xitemreq_desc;
            }else{
                $memo = DB::table('xbid_mstr')
                            ->where('xbid_mstr.xbid_id','=',$rfqnbr)
                            ->first();

                if($memo){
                    $itemdesc = $memo->xbid_desc;
                }
            }

            // Send Email
            $sendmail = (new SendEmailJob($rfqnbr,$itemcode,$itemdesc,$qtyreq,
                        number_format($proqty,2),$duedate,$prodate,$remarks))
                        ->delay(Carbon::now()->addSeconds(3));
            dispatch($sendmail);

            // $com = DB::table('com_mstr')
            //         ->first();

            // if($emailpur){
            //     $array_email = explode(',', $emailpur->xalert_not_pur);

            //     // Kirim Email Notif Ke Purchasing
            //     Mail::send('email.emailrfqbid', 
            //         ['pesan' => 'Supplier : '.$emailpur->xalert_nama.' has made an offer for following RFQ',
            //          'note1' => $rfqnbr,
            //          'note2' => $itemcode,
            //          'note3' => $itemdesc,
            //          'note4' => $qtyreq,
            //          'note5' => number_format($proqty,2),
            //          'note6' => $duedate,
            //          'note7' => $prodate,
            //          'note8' => $remarks], 
            //         function ($message) use ($emailpur,$array_email,$com)
            //     {
            //         $message->subject('PhD - Request for Quotation Feedback - '.$emailpur->xalert_nama);
            //         $message->from($com->com_email); // Email Admin Fix
            //         $message->to($array_email);
            //     });
                
            //     $user = App\User::where('id','=', $emailpur->id)->first(); // user siapa yang terima notif (lewat id)
                          
            //     $details = [
            //         'body' => 'Supplier : '.$emailpur->xalert_nama.' has made an offer for following RFQ',
            //         'url' => 'rfq',
            //         'nbr' => $rfqnbr,
            //         'note' => 'Please check'
            //     ]; // isi data yang dioper
                                    
                                
            //     $user->notify(new \App\Notifications\eventNotification($details));
            // }

            


        // return redirect()->back()->with('updated','Data has successfully updated');    
        alert()->success('Success','Data is successfully updated');
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
    
    public function viewlistsupp()
    {
        try{

            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){
            $users = DB::table("xbid_mstr")
                    ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->orderBy('xbid_mstr.xbid_id','DESC')
                    ->paginate(10);  

            return view('/rfq/loadsupp',['users'=>$users]);
                }else{
            $users = DB::table("xbid_mstr")
                    ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->orderBy('xbid_mstr.xbid_id','DESC')
                    ->where("xbid_det.xbid_supp",'=', Session::get('supp_code'))
                    ->orderBy('xbid_mstr.xbid_id','DESC')
                    ->paginate(10);

            return view('/rfq/loadsupp',['users'=>$users]);
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

    public function searchsupp(Request $req)
    {
        if($req->ajax())
        {
            $rfqnbr = $req->rfq;
            $itemcode = $req->code;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;
            $status = $req->status;

            if($rfqnbr == null && $itemcode == null and $datefrom == null and $dateto == null && $status == null){
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){
                $alert = DB::table("xbid_mstr")
                            ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                            ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                            ->leftjoin('xitemreq_mstr','xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                            ->whereRaw('xbid_det.xbid_id IN (
                                        SELECT MAX(xbid_id)
                                        FROM xbid_det
                                        GROUP BY xbid_supp,xbid_id)  
                                        ')
                            ->orderBy('xbid_mstr.xbid_id','DESC')
                            ->paginate(10);

                return view('/rfq/loadsupp',['users'=>$alert]);
                }else{
                $alert = DB::table("xbid_mstr")
                            ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                            ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                            ->leftjoin('xitemreq_mstr','xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                            ->whereRaw('xbid_det.xbid_id IN (
                                        SELECT MAX(xbid_id)
                                        FROM xbid_det
                                        GROUP BY xbid_supp,xbid_id)  
                                        ')
                            ->where("xbid_det.xbid_supp",'=', Session::get('supp_code'))
                            ->orderBy('xbid_mstr.xbid_id','DESC')
                            ->paginate(10);

                return view('/rfq/loadsupp',['users'=>$alert]);
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
            $query = "xbid_det.xbid_id IN (
                        SELECT MAX(xbid_id)
                        FROM xbid_det
                        GROUP BY xbid_supp,xbid_id)                    
                        AND xbid_det.xbid_date >= '".$datefrom."' 
                        AND xbid_det.xbid_date <= '".$dateto."'";

            if($rfqnbr != null){
                $query .= " AND xbid_det.xbid_id like '".$rfqnbr."%'";
            }
            if($itemcode != null){
                $query .= " AND xbid_mstr.xbid_part like '".$itemcode."%'";
            }
            if($status != null){
                $query .= " AND xbid_det.xbid_flag like '".$status."%'";
            }

            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){
                $alert=DB::table("xbid_mstr")
                                ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_mstr.xbid_part')
                                ->whereRaw($query)
                                ->orderBy('xbid_mstr.xbid_id','DESC')
                                ->paginate(100);   
            
                return view('/rfq/loadsupp',['users'=>$alert]);
            }else{
                $alert=DB::table("xbid_mstr")
                                ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_mstr.xbid_part')
                                ->whereRaw($query)
                                ->where("xbid_det.xbid_supp",'=', Session::get('supp_code'))
                                ->orderBy('xbid_mstr.xbid_id','DESC')
                                ->paginate(100);   
            
                return view('/rfq/loadsupp',['users'=>$alert]);
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

    // Approve Purchasing
    public function viewapprove()
    {
        $openrfq = DB::table('xbid_mstr')
                    ->where('xbid_flag','<=','1')
                    ->count();

        $pastduerfq = DB::table('xbid_mstr')
                    ->where('xbid_due_date','<',Carbon::now()->format('yy-m-d'))
                    ->where('xbid_flag','<=','1')
                    ->count();

        $users = DB::table("xbid_mstr")
                    ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                    ->where("xbid_det.xbid_flag",'=', '1')
                    ->orderBy('xbid_mstr.xbid_id','ASC')
                    ->orderBy('xbid_det.xbid_pro_price','DESC')
                    ->paginate(10);  

        $listpo = DB::table("xpo_mstrs")
                    ->distinct()
                    ->select('xpo_nbr')
                    ->where('xpo_mstrs.xpo_status','=','Created') // Display PO yang blom di Approve
                    ->orWhere('xpo_mstrs.xpo_status','=','Waiting')
                    ->get();

        $listconvert = DB::table('xrfq_mstrs')
                    ->first();


        return view('/rfq/rfqapprove', compact('users','listpo','listconvert','openrfq','pastduerfq'));
    }

    public function rfqsearch(Request $req)
    {
        if($req->ajax())
        {   $rfqnbr = $req->rfq;
            $itemcode = $req->code;

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
            $query = "xbid_det.xbid_flag = 1 AND xbid_det.xbid_pro_date >= '".$datefrom."' 
                        AND xbid_det.xbid_pro_date <= '".$dateto."'";

            if($rfqnbr != null){
                $query .= " AND xbid_mstr.xbid_id like '".$rfqnbr."%'";
            }
            if($itemcode != null){
                $query .= " AND xbid_mstr.xbid_part like '".$itemcode."%'";
            }

            $users=DB::table("xbid_mstr")
                            ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                            ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                            ->leftjoin("xitemreq_mstr",'xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                            ->whereRaw($query)
                            /*->where("xbid_det.xbid_flag","=","1")
                            ->where(function ($query) use ($rfqnbr,$itemcode,$datefrom,$dateto) {
                                $query->where("xbid_mstr.xbid_id","LIKE","%".$rfqnbr."%")
                                    ->orWhere("xbid_mstr.xbid_part","=",$itemcode)
                                    ->orWhereBetween("xbid_det.xbid_pro_date", [$datefrom, $dateto]);
                            })*/

                            //->where("xbid_mstr.xbid_id","LIKE","%".$req->rfq."%")
                            //->orWhere("xbid_mstr.xbid_part","LIKE","%".$req->code."%")
                            //->orWhereBetween("xbid_mstr.xbid_due_date", [$req->datefrom, $req->dateto])
                            ->orderBy('xbid_mstr.xbid_id','ASC')
                            ->orderBy('xbid_det.xbid_pro_price','DESC')
                            ->paginate(10);   

            return view('/rfq/load',['users'=>$users]);

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

    /* Moved
    public function purchupdate(Request $req)
    {
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

    }*/

    public function fetch_data()
    {
       if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){ //admin
            $users = DB::table("xbid_mstr")
                    ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where("xbid_det.xbid_flag",'=', '1')
                    ->orderBy('xbid_mstr.xbid_id')
                    ->paginate(10);  

            //return view('/rfq/rfqapprove', compact('users'));
            return view('/rfq/load',['users'=>$users]);
                }else{
            $users = DB::table("xbid_mstr")
                    ->join("xbid_det",'xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where("xbid_det.xbid_supp",'=', Session::get('supp_code')) //purchasing
                    ->where("xbid_det.xbid_flag",'=', '1')
                    ->orderBy('xbid_mstr.xbid_id')
                    ->paginate(10);  
            return view('/rfq/load',['users'=>$users]);
                }  
    }

    public function downloadfiledet(Request $req, $id, $supp)
    {
        $path = DB::table('xbid_det')
                ->where('xbid_id', $id)
                ->where('xbid_supp',$supp)
                ->select('xbid_pro_attch')
                ->first();

        if(is_null($path->xbid_pro_attch)) // <----
        {
            // session()->flash("error","There Is No Attachment to this RFQ Number");
            alert()->error('Error','There is no Attachment to this RFQ Number');
            return back();
        }else{
            try{
                return response()->download($path->xbid_pro_attch);

            }catch(\InvalidArgumentException $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }catch(\Exception $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }catch(\Error $ex){
                //return back()->withError($ex->getMessage())->withInput();
                return redirect()->back()->with(['error'=>'There Is No Attachment to this RFQ Number']);
            }
        }
    }


    // History 
    public function viewhist(Request $req)
    {
        try{
            if($req->ajax())
            {
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){ //admin
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->paginate(10);

                    return view('/rfq/loadhist',['alert'=>$alert]);
                }else{
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->where("xbid_hist.xbid_supp",'=', Session::get('supp_code'))
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->paginate(10);

                    return view('/rfq/loadhist',['alert'=>$alert]);
                }

            }else{
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){ //admin
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->paginate(10);
                    return view('/rfq/viewhist',['alert'=>$alert]);
                }else{
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->where("xbid_hist.xbid_supp",'=', Session::get('supp_code'))
                                ->paginate(10);
                    return view('/rfq/viewhist',['alert'=>$alert]);
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
    }
    
    public function searchhist(Request $req)
    {
        if($req->ajax())
        {
            $rfqnbr = $req->rfq;
            $itemcode = $req->code;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;
            $statushist = $req->status;
            $suppcode = $req->suppcode;

            if($rfqnbr == null && $itemcode == null and $datefrom == null and $dateto == null && $statushist == null && $suppcode == null){
                
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->paginate(10);

                    return view('/rfq/loadhist',['alert'=>$alert]);
                }else{
                    $alert = DB::table('xbid_hist')
                                ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                                ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                                ->whereRaw('xbid_id IN (
                                            SELECT MAX(xbid_id)
                                            FROM xbid_hist
                                            GROUP BY xbid_supp,xbid_nbr)  
                                            ')
                                ->where("xbid_hist.xbid_supp",'=', Session::get('supp_code'))
                                ->orderBy('xbid_hist.xbid_nbr','DESC')
                                ->paginate(10);

                    return view('/rfq/loadhist',['alert'=>$alert]);
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
            $query = "xbid_id IN (
                        SELECT MAX(xbid_id)
                        FROM xbid_hist
                        GROUP BY xbid_supp,xbid_nbr)                    
                        AND xbid_hist.xbid_due_date >= '".$datefrom."' 
                        AND xbid_hist.xbid_due_date <= '".$dateto."'";

            if($rfqnbr != null){
                $query .= " AND xbid_hist.xbid_nbr like '".$rfqnbr."%'";
            }
            if($itemcode != null){
                $query .= " AND xbid_hist.xbid_part like '".$itemcode."%'";
            }
            if($statushist != null){
                $query .= " AND xbid_hist.xbid_flag = '".$statushist."'";
            }
            if($suppcode != null){
                $query .= " AND xbid_hist.xbid_supp like '".$suppcode."%'";
            }


            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing'){ //admin   
                $alert=DB::table("xbid_hist")
                        ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                        ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                        ->whereRaw($query)
                        ->orderBy('xbid_hist.xbid_nbr','DESC')
                        ->paginate(10);   
            }else{
                $alert=DB::table("xbid_hist")
                        ->join("xalert_mstrs",'xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                        ->leftjoin('xitemreq_mstr','xitemreq_mstr.xitemreq_part','=','xbid_hist.xbid_part')
                        ->where("xbid_hist.xbid_supp",'=', Session::get('userid'))
                        ->whereRaw($query)
                        ->orderBy('xbid_hist.xbid_nbr','DESC')
                        ->paginate(10);   
            }     

            return view('/rfq/loadhist',['alert'=>$alert]);

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


    // update

    public function rfqreject(Request $req){
        if($req->ajax())
        {
            $rfqnbr = $req->rfq;
            $suppid = $req->suppid;

            DB::table('xbid_det')
                ->where('xbid_det.xbid_id','=',$rfqnbr)
                ->where('xbid_det.xbid_supp','=',$suppid)
                ->update([
                    'xbid_det.xbid_flag'=>'3' // 0 Dibuat Purch, 1 Dibid Supplier, 2 Diapprove Purch, 3 Direject Purch 
                ]);

            // Kirim Email Notif Ke Supplier
            $email = DB::table('xbid_det')
                    ->join('xbid_mstr','xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                    ->leftjoin('xitemreq_mstr','xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->join('users','users.supp_id','=','xalert_mstrs.xalert_supp')
                    ->where('xbid_det.xbid_id','=',$rfqnbr)
                    ->where('xbid_det.xbid_supp','=',$suppid)
                    ->get();

            $company = DB::table('com_mstr')
                        ->first();

            if(count($email) != 0){
                $email1 = '';

                foreach($email as $email){
                    $email1 .= $email->email.',';
                }

                $email1 = substr($email1, 0, strlen($email1) - 1);

                $array_email = explode(',', $email1); 

                $sendmail = (new EmailRfqReject(
                                $rfqnbr,
                                $email->xbid_due_date,
                                $email->xbid_part,
                                $email->xitemreq_desc,
                                $array_email,
                                $company->com_name,
                                $company->com_email))
                                ->delay(Carbon::now()->addSeconds(3));
                dispatch($sendmail);


                // Mail::send('email.emailrfqreject', 
                //     ['pesan' => 'An RFQ has been closed by Purchasing',
                //      'note1' => $rfqnbr,
                //      'note2' => $email->xbid_due_date,
                //      'note3' => $email->xbid_part,
                //      'note4' => $email->xitemreq_desc,],
                //     function ($message) use ($rfqnbr,$array_email,$email,$company)
                // {
                //     $message->subject('PhD - Request for Quotation - '.$company->com_name);
                //     $message->from($company->com_email); // Email Admin Fix
                //     $message->to($array_email);
                // });
                
                $user = App\User::where('supp_id','=', $email->xalert_supp)->first(); // user siapa yang terima notif (lewat id)
                          
                $details = [
                    'body' => 'An RFQ has been closed by Purchasing',
                    'url' => 'rfq',
                    'nbr' => $rfqnbr,
                    'note' => 'Please check'
                ]; // isi data yang dioper
                                    
                                
                $user->notify(new \App\Notifications\eventNotification($details));

            }            

            // session()->flash("success","RFQ ".$rfqnbr."Is Closed");
            alert()->success('Success','RFQ '.$rfqnbr.'is Closed');
            return back();
            
        }
    }

    public function cancelrfqpurch(Request $req){
        $rfqnbr = $req->d_rfqnbr;
        
        $data = DB::Table('xbid_det')
                ->join('xbid_mstr','xbid_det.xbid_id','=','xbid_mstr.xbid_id')
                ->leftjoin('xitemreq_mstr','xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                ->where('xbid_det.xbid_id','=',$req->d_rfqnbr)
                ->where('xbid_det.xbid_flag','<','2') // buat new request ato submitted
                ->get();

        foreach($data as $data){
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
                        'xbid_flag' => '4', // Closed by Purchasing
                        'xbid_hist_remarks'=>'RFQ Closed by Purchasing',
                        'xbid_supp'=>$data->xbid_supp,
                        'xbid_site'=>$data->xbid_site,
                        'xbid_start_date'=>$data->xbid_start_date,
                    ]);
                    
                    // Kirim Email Notif Ke Supplier
                    if($req->kirimemail == 'Yes'){
                        $email = DB::table('xbid_det')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                            ->join('users','xalert_mstrs.xalert_supp','=','users.supp_id')
                            ->where('xbid_det.xbid_id','=',$data->xbid_id)
                            ->where('xbid_det.xbid_supp','=',$data->xbid_supp)
                            ->get();

                        $company = DB::table('com_mstr')
                                    ->first();

                        if(count($email) != 0){
                            $email1 = '';

                            foreach($email as $email){
                                $email1 .= $email->email.',';
                            }

                            $email1 = substr($email1, 0, strlen($email1) - 1);

                            $array_email = explode(',', $email1); 

                            $sendmail = (new EmailRfqReject(
                                $rfqnbr,
                                $data->xbid_due_date,
                                $data->xbid_part,
                                $data->xitemreq_desc,
                                $array_email,
                                $company->com_name,
                                $company->com_email))
                                ->delay(Carbon::now()->addSeconds(3));
                            dispatch($sendmail);


                            // Mail::send('email.emailrfqreject', 
                            //     ['pesan' => 'An RFQ has been closed by Purchasing',
                            //      'note1' => $rfqnbr,
                            //      'note2' => $data->xbid_due_date,
                            //      'note3' => $data->xbid_part,
                            //      'note4' => $data->xitemreq_desc],
                            //     function ($message) use ($rfqnbr,$array_email,$data,$company)
                            // {
                            //     $message->subject('PhD - Request for Quotation Closed - '.$company->com_name);
                            //     $message->from($company->com_email); // Email Admin Fix
                            //     $message->to($array_email);
                            // });
                            
                            $user = App\User::where('supp_id','=', $email->xalert_supp)->first(); // user siapa yang terima notif (lewat id)
                          
                            $details = [
                                'body' => 'An RFQ has been closed by Purchasing',
                                'url' => 'rfq',
                                'nbr' => $rfqnbr,
                                'note' => 'Please check'
                            ]; // isi data yang dioper
                                                
                                            
                            $user->notify(new \App\Notifications\eventNotification($details));
                        }
                    }
                    

        }


        DB::table('xbid_mstr')
                ->where('xbid_mstr.xbid_id','=',$req->d_rfqnbr)
                ->update([
                        'xbid_flag' => '2' // 0 Dibuat, 1 Supplier ada input , 2 Closed
                ]);
        DB::Table('xbid_det')
                ->where('xbid_id','=',$req->d_rfqnbr)
                ->where('xbid_flag','!=','2')
                ->update([
                        'xbid_flag' => '4' // 0 Dibuat Purch, 1 Dibid Supplier, 2 Diapprove Purch, 3 Direject Purch, 4 RFQ Closed by purchasing 
                ]);

        // session()->flash("updated","RFQ No.: ".$req->d_rfqnbr." Is Closed");
        alert()->success('Success','RFQ No. '.$req->d_rfqnbr.' is Closed');
        return back();    
    }

    public function searcholdsupp(Request $req){

        //echo $req->search;

        $data = db::table('xbid_det')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where('xbid_det.xbid_id','=',$req->search)
                    ->get();

        $output = "<tr><td colspan='2' style='color:red;'><center>No Data Available</center></td></tr>";

        if(count($data) > 0) {
            $output = '';
            foreach($data as $data){
                $output.= "<tr>".

                "<td>
                    <input type='hidden' name='suppname[]' value ='".$data->xbid_supp."'>
                    ".$data->xbid_supp.' - '.$data->xalert_nama."
                </td>";
                /*
                "<td> 
                    ".$data->xbid_apprv."
                </td>".
                */
                if($data->xbid_send_email == '0'):
                $output .= "<td><input type='button' class='ibtnDel btn btn-md bt-action' value='Delete'></td>";
                else:
                $output .= "<td>Email Sent</td>";
                endif;
                "</tr>";
            }
        }

        return Response($output);
    }

    public function searcholdsuppdel(Request $req){

        //echo $req->search;

        $data = db::table('xbid_det')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where('xbid_det.xbid_id','=',$req->search)
                    ->get();

        $output = "<tr><td colspan='2' style='color:red;'><center>No Data Available</center></td></tr>";

        if(count($data) > 0) {
            $output = '';
            foreach($data as $data){
                $output.= "<tr>".

                "<td>
                    <input type='hidden' name='suppname[]' value ='".$data->xbid_supp."'>
                    ".$data->xbid_supp.' - '.$data->xalert_nama."
                </td>";
                /*
                "<td> 
                    ".$data->xbid_apprv."
                </td>".
                */
                if($data->xbid_send_email == '0'):
                $output .= "<td>Not Sent</td>";
                else:
                $output .= "<td>Email Sent</td>";
                endif;
                "</tr>";
            }
        }

        return Response($output);
    }

    public function addsupplierrfq(Request $req){

        $data = DB::table('xbid_det')
                    ->where('xbid_det.xbid_id',$req->add_rfqnbr)
                    ->where('xbid_det.xbid_send_email','=','0')
                    ->get();

        foreach($data as $data){

            // Kirim Email Notif Ke Supplier
                $email = DB::table('xbid_det')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                        ->join('users','xalert_mstrs.xalert_supp','=','users.supp_id')
                        ->where('xbid_det.xbid_id','=',$data->xbid_id)
                        ->where('xbid_det.xbid_supp','=',$data->xbid_supp)
                        ->get();


                if(count($email) != 0){

                    DB::table('xbid_det')
                        ->where('xbid_det.xbid_id','=',$data->xbid_id)
                        ->where('xbid_det.xbid_supp','=',$data->xbid_supp)
                        ->update([
                                'xbid_det.xbid_send_email' => '1', // 0 Blom Email, 1 Sudah Email
                        ]);


                    $email1 = '';

                    foreach($email as $email){
                        $email1 .= $email->email.',';
                    }

                    $email1 = substr($email1, 0, strlen($email1) - 1);

                    $array_email = explode(',', $email1); 

                    $newdata = db::table('xbid_mstr')
                                ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                                ->leftjoin('xitemreq_mstr','xbid_mstr.xbid_part','=','xitemreq_mstr.xitemreq_part')
                                ->where('xbid_mstr.xbid_id','=',$data->xbid_id)
                                ->first();

                    $company = DB::table('com_mstr')
                                ->first();

                    $sendmail = (new EmailRfqSend(
                        $newdata->xbid_id,
                        $newdata->xitemreq_part,
                        $newdata->xitemreq_desc,
                        $newdata->xbid_start_date,
                        $newdata->xbid_due_date,
                        $newdata->xbid_qty,
                        number_format($newdata->xbid_price_min,2),
                        number_format($newdata->xbid_price_max,2),
                        $newdata->xbid_remarks,
                        $array_email,
                        $company->com_name,
                        $company->com_email))
                        ->delay(Carbon::now()->addSeconds(3));
                    dispatch($sendmail);

                    // Mail::send('email.emailrfq', 
                    //     ['pesan' => 'There is a new RFQ awaiting your response',
                    //                      'note1' => $newdata->xbid_id,
                    //                      'note2' => $newdata->xitemreq_part,
                    //                      'note3' => $newdata->xitemreq_desc,
                    //                      'note4' => $newdata->xbid_start_date,
                    //                      'note5' => $newdata->xbid_due_date,
                    //                      'note6' => $newdata->xbid_qty,
                    //                      'note7' => number_format($newdata->xbid_price_min,2),
                    //                      'note8' => number_format($newdata->xbid_price_max,2),
                    //                      'note9' => $newdata->xbid_remarks],
                    //     function ($message) use ($newdata,$array_email,$company)
                    // {
                    //     $message->subject('PhD - Request for Quotation - '.$company->com_name);
                    //     $message->from($company->com_email); // Email Admin Fix
                    //     $message->to($array_email);
                    // });
                    
                    $user = App\User::where('supp_id','=', $email->xalert_supp)->first(); // user siapa yang terima notif (lewat id)
                          
                    $details = [
                        'body' => 'There is a new RFQ awaiting your response',
                        'url' => 'rfqapprove',
                        'nbr' => $newdata->xbid_id,
                        'note' => 'Please check'
                    ]; // isi data yang dioper
                                                
                                            
                    $user->notify(new \App\Notifications\eventNotification($details));

                }else{
                    // return redirect()->back()->with('error','There is no user with Supplier Code : '.$data->xbid_supp.'');
                    alert()->error('Error','There is no user with Supplier Code : '.$data->xbid_supp.'');
                    return back();
                }

        }
        

        // return redirect()->back()->with('updated','Request is successfully sent');
        alert()->success('Success','Request is succesfully sent');
        return back();
    }

    
    public function polast10search(Request $req){
        if($req->ajax()){
            $data = DB::table('xpo_hist')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                        ->where('xpo_hist.xpo_part','=',$req->search)
                        //->where('xpo_hist.xpo_status','=','Receipted')
                        ->whereRaw('(xpo_hist.xpo_status = "Closed" or xpo_hist.xpo_status = "Open")')
                        ->orderBy('updated_at','DESC')
                        ->groupBy('xpo_hist.xpo_nbr')
                        ->take(10)
                        ->get();

            $output = '';
            if(count($data) > 0){
                foreach($data as $data){
                    $output .= '<tr>'.
                        '<td>'.
                                $data->xpo_nbr.
                        '</td>'.

                        '<td>'.
                                $data->xpo_vend.
                        '</td>'.

                        '<td>'.
                                $data->xalert_nama.
                        '</td>'.

                        '<td>'.
                                number_format($data->xpo_price,2).
                        '</td>'.

                        '<td>'.
                                \Carbon\Carbon::parse($data->updated_at)->format('Y-m-d').
                        '</td>'.

                        '</tr>';
                }
            }else{
                $output .= '<tr><td colspan = "5" style="color:red;text-align:center;"><b>No Data Available</b></td></tr>';
            }
            

            return Response($output);

        }
    }


    public function rfqlast10search(Request $req){
        if($req->ajax()){
            $data = DB::table('xbid_hist')
                        ->join('xalert_mstrs','xbid_hist.xbid_supp','=','xalert_mstrs.xalert_supp')
                        ->where('xbid_hist.xbid_part','=',$req->search)
                        ->where('xbid_hist.xbid_no_po','!=','')
                        ->orderBy('xbid_id','DESC')
                        ->take(10)
                        ->get();
            
            $output = '';
            if(count($data) > 0){
                foreach($data as $data){
                    $output .= '<tr>'.
                        '<td>'.
                                $data->xbid_nbr.
                        '</td>'.

                        '<td>'.
                                $data->xbid_supp.
                        '</td>'.

                        '<td>'.
                                $data->xalert_nama.
                        '</td>'.

                        '<td>'.
                                number_format($data->xbid_pro_price,2).
                        '</td>'.

                        '<td>'.
                                $data->xbid_pur_date.
                        '</td>'.

                        '</tr>';
                }
            }else{
                $output .= '<tr><td colspan = "5" style="color:red;text-align:center;"><b>No Data Available</b></td></tr>';
            }
            

            return Response($output);
        }
    }


    public function rfqinputsearch(Request $req){
        if($req->ajax())
        {   $rfqnbr = $req->rfq;
            $itemcode = $req->code;

            try{
            $query = "";

            if($rfqnbr != null){
                $query .= "xbid_mstr.xbid_id like '".$rfqnbr."%'";
            }
            if($itemcode != null){
                if($rfqnbr != null){
                    $query .= " AND xbid_mstr.xbid_part like '".$itemcode."%'";
                }else{
                    $query .= " xbid_mstr.xbid_part like '".$itemcode."%'";
                }
            }

            if($rfqnbr == null and $itemcode == null){
                $users=DB::table("xbid_mstr")
                        ->leftjoin("xitemreq_mstr",'xitemreq_mstr.xitemreq_part','=','xbid_mstr.xbid_part')
                        ->where("xbid_mstr.xbid_flag",'<=', '1')
                        ->orderby('xbid_mstr.xbid_id','Desc')
                        ->paginate(10);   
            }else{
                $users=DB::table("xbid_mstr")
                        ->leftjoin("xitemreq_mstr",'xitemreq_mstr.xitemreq_part','=','xbid_mstr.xbid_part')
                        ->where("xbid_mstr.xbid_flag",'<=', '1')
                        ->orderby('xbid_mstr.xbid_id','Desc')
                        ->whereRaw($query)
                        ->paginate(10);   
            }

            
        
            return view('/rfq/loadpurch',['bid'=>$users]);

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

    public function top10menu(Request $req){
        $datarfq = DB::table('xbid_hist')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                        ->where('xbid_hist.xbid_no_po','=','123')
                        ->orderBy('xbid_id','DESC')
                        ->take(10)
                        ->get();

        $datapo = DB::table('xpo_hist')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                    ->where('xpo_hist.xpo_status','=','Closed123')
                    ->orderBy('updated_at','DESC')
                    ->take(10)
                    ->get();

        $sel = '';
        
        return view('rfq.rfqlast10',['users'=>$datarfq,'datapo'=>$datapo,'sel'=>$sel]);   
    }

    public function searchtop10menu(Request $req){
        if($req->ajax()){

            $itemcode = $req->item;
            $supplier = $req->supplier;
            $sel = $req->sel;

            if($itemcode == null and $supplier == null){
                $datarfq = DB::table('xbid_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                            ->where('xbid_hist.xbid_no_po','!=','')
                            ->orderBy('xbid_id','DESC')
                            ->take(10)
                            ->get();

                $datapo = DB::table('xpo_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                            //->where('xpo_hist.xpo_status','=','Closed')
                            ->whereRaw('xpo_hist.xpo_status = "Closed" or xpo_hist.xpo_status = "Open"')
                            ->orderBy('id','DESC')
                            ->take(10)
                            ->get();

                return view('rfq.tablelast10',['users'=>$datarfq,'datapo'=>$datapo,'sel'=>$sel]);                          
            }else{

                $queryRFQ = '';
                $queryPO = '';
                if($itemcode != null){
                    $queryRFQ .= " xbid_part like '".$itemcode."%'";
                    $queryPO .= " xpo_part like '".$itemcode."%'";
                }

                if($supplier != null){
                    if($itemcode != null){
                        $queryRFQ .= "AND xbid_supp like '".$supplier."%'";
                        $queryPO .= "AND xpo_vend like '".$supplier."%'";
                    }else{
                        $queryRFQ .= "xbid_supp like '".$supplier."%'";
                        $queryPO .= "xpo_vend like '".$supplier."%'";
                    }
                }
                $datarfq = DB::table('xbid_hist')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                        ->where('xbid_hist.xbid_no_po','!=','""')
                        ->whereRaw($queryRFQ)
                        ->orderBy('xbid_id','DESC')
                        ->take(10)
                        ->get();

                $datapo = DB::table('xpo_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                            //->where('xpo_hist.xpo_status','=','Closed')
                            ->whereRaw('( xpo_hist.xpo_status = "Closed" or xpo_hist.xpo_status = "Open" )')
                            ->whereRaw($queryPO)
                            ->orderBy('updated_at','DESC')
                            ->take(10)
                            ->get();
                
                return view('rfq.tablelast10',['users'=>$datarfq,'datapo'=>$datapo,'sel'=>$sel]);  
            }

        }
    }

    public function searchtop10menupo(Request $req){
        if($req->ajax()){
            $itemcode = $req->item;
            $supplier = $req->supplier;
            $sel = $req->sel;

            if($itemcode == null and $supplier == null){
                $datarfq = DB::table('xbid_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                            ->where('xbid_hist.xbid_no_po','!=','')
                            ->orderBy('xbid_id','DESC')
                            ->take(10)
                            ->get();

                $datapo = DB::table('xpo_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                            //->where('xpo_hist.xpo_status','=','Closed')
                            ->whereRaw('xpo_hist.xpo_status = "Closed" or xpo_hist.xpo_status = "Open"')
                            ->orderBy('id','DESC')
                            ->take(10)
                            ->get();

                return view('rfq.tablelast10po',['users'=>$datarfq,'datapo'=>$datapo,'sel'=>$sel]);                          
            }else{

                $queryPO = '';
                if($itemcode != null){
                    $queryPO .= " xpo_part like '".$itemcode."%'";
                }

                if($supplier != null){
                    if($itemcode != null){
                        $queryPO .= "AND xpo_vend like '".$supplier."%'";
                    }else{
                        $queryPO .= "xpo_vend like '".$supplier."%'";
                    }
                }

                $datarfq = DB::table('xbid_hist')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_hist.xbid_supp')
                        ->where('xbid_hist.xbid_no_po','!=','""')
                        ->orderBy('xbid_id','DESC')
                        ->take(10)
                        ->get();

                $datapo = DB::table('xpo_hist')
                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_hist.xpo_vend')
                            //->where('xpo_hist.xpo_status','=','Closed')
                            ->whereRaw('(xpo_hist.xpo_status = "Closed" or xpo_hist.xpo_status = "Open")')
                            ->whereRaw($queryPO)
                            ->orderBy('updated_at','DESC')
                            ->take(10)
                            ->get();
                
                return view('rfq.tablelast10po',['users'=>$datarfq,'datapo'=>$datapo,'sel'=>$sel]);  

            }

        }
    }

    // 22072020
    public function searchemail(Request $req){

        //echo $req->search;

        $data = db::table('xbid_det')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where('xbid_det.xbid_id','=',$req->search)
                    ->get();

        $output = "<tr><td colspan='2' style='color:red;'><center>No Data Available</center></td></tr>";

        if(count($data) > 0) {
            $output = '';
            foreach($data as $data){
                $output.= "<tr>".

                "<td>
                    <input type='hidden' name='suppname[]' value ='".$data->xbid_supp."'>
                    <input type='hidden' name='flag[]' value ='".$data->xbid_send_email."'>
                    ".$data->xbid_supp.' - '.$data->xalert_nama."
                </td>";
                /*
                "<td> 
                    ".$data->xbid_apprv."
                </td>".
                */
                if($data->xbid_send_email == '0'):
                $output .= "<td>Not Sent</td>";
                else:
                $output .= "<td>Email Sent</td>";
                endif;
                "</tr>";
            }
        }

        return Response($output);
    }


    public function rfqaudit(Request $req){
        $data = DB::table('xbid_hist')
                    ->where('xbid_nbr','=','123123')
                    ->paginate(10);

        return view('rfq.rfqaudit',['data' => $data]);
    }

    public function rfqauditsearch(Request $req){
        if($req->ajax()){
            $nbr = $req->nbr;

            if($nbr == null){
                if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    $users = DB::table('xbid_hist')
                            ->where('xbid_hist.xbid_nbr','=','')
                            ->paginate(10);
                    

                    return view('/rfq/tablerfqaudit',['data'=>$users]);
                }else{
                    $users = DB::table('xbid_hist')
                            ->where('xbid_hist.xbid_nbr','=','')
                            ->paginate(10);
                    

                    return view('/rfq/tablerfqaudit',['data'=>$users]);
                }
            }

            $query = '';

            if($nbr != null){
                 $query .= 'xbid_hist.xbid_nbr LIKE "'.$nbr.'"';
            }

            if(Session::get('user_role') == 'Admin' or Session::get('user_role') == 'Purchasing' ){ //admin
                    
                    $users = DB::table('xbid_hist')
                                ->whereRaw($query)
                                ->paginate(10);
                    

                    return view('/rfq/tablerfqaudit',['data'=>$users]);
            }else{
                
                $users = DB::table('xbid_hist')
                                        ->where("xpo_mstrs.xpo_vend",'=',Session::get('supp_code'))
                                        ->whereRaw($query)
                                        ->paginate(10);
                

                return view('/rfq/tablerfqaudit',['data'=>$users]);
            }
                
        }
    }

    public function searchdetailrfq(Request $req){
        if($req->ajax()){

            $data = db::table('xbid_mstr')
                    ->join('xbid_det','xbid_mstr.xbid_id','=','xbid_det.xbid_id')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xbid_det.xbid_supp')
                    ->where('xbid_det.xbid_id','=',$req->search)
                    ->get();

            $output = "<tr><td colspan='2' style='color:red;'><center>No Data Available</center></td></tr>";

            if(count($data) > 0) {
                $output = '';
                foreach($data as $data){
                    $output.= "<tr>".

                    "<td> 
                        ".$data->xbid_supp."
                    </td>".

                    "<td> 
                        ".$data->xalert_nama."
                    </td>".

                    "<td> 
                        ".number_format($data->xbid_qty_req,2)."
                    </td>".

                    "<td> 
                        ".number_format($data->xbid_pro_qty,2)."
                    </td>".

                    "<td> 
                        ".number_format($data->xbid_pur_qty,2)."
                    </td>".

                    "<td> 
                        ".number_format($data->xbid_pro_price,2)."
                    </td>";

                    if($data->xbid_flag == '0'):
                        $output .= "<td>Open</td>";
                    elseif($data->xbid_flag == '1'):
                        $output .= "<td>Submitted</td>";
                    elseif($data->xbid_flag == '2'):
                        $output .= "<td>Approved</td>";
                    elseif($data->xbid_flag == '3'):
                        $output .= "<td>Closed</td>";    
                    elseif($data->xbid_flag == '4'):
                        $output .= "<td>Closed</td>";  
                    endif;

                    $output .= "<td> 
                                    ".$data->xbid_pur_date."
                                </td>".

                    "<tr>";
                }
            }

            return Response($output);
           


        }
    }

}
    