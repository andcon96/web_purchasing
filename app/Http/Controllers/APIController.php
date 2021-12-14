<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Schema;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class APIController extends Controller
{
    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['username' => request('username'), 'password' => request('password')])){
            $user = Auth::user();
            // dd($user);
            $success['token'] =  $user->createToken('nApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function getuser(){
        return User::all();
    }

    public function createrfq(Request $req){
        if(Auth::attempt(['username' => request('username'), 'password' => request('password')])){
            $prefix = DB::table('xrfq_mstrs')
                    ->first();

            $date = Carbon::now()->format('ymd');
        
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
                // return redirect()->back()->with('error','Supplier Cannot be Blank'); 
                return response()->json(['error'=>'Supplier Cannot be Blank'], 401);
            }else{

                $flg = '';
                foreach($suppname as $suppname){

                    if(str_contains($flg,$suppname)){
                        // return redirect()->back()->with('error','Supplier Cannot be Duplicate'); 
                        return response()->json(['error'=>'Supplier Cannot be Duplicate'], 401);
                    }
                    
                    $flg .= $suppname;
                }

                if((int)$price_min > (int)$price_max){
                    // return redirect()->back()->with('error','Price Min Lebih Besar dari Price Max');   
                    return response()->json(['error'=>'Price Min Bigger than Price Max'], 401);  
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

                DB::beginTransaction();
                
                try{
                    // table header
                    DB::table('xbid_mstr')->insert($data);
                    // table detail
                    $i = 0;
                    if(count($req->suppname) >= 0){
                        foreach($req->suppname as $item=>$v){
                            $data2=array(
                                'xbid_id'=>$rfq_nbr,
                                'xbid_qty'=>$qty_req,
                                'xbid_date'=>$formatted_due_date,
                                'xbid_supp'=>$req->suppname[$i],
                                'xbid_apprv'=>$req->suppflg[$i],
                            );              
                                DB::table('xbid_det')->insert($data2);
                                $i++;
                        }
                    }

                    //table history
                    $h = 0;
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
                                'xbid_supp'=>$req->suppname[$h],
                                'xbid_apprv'=>$req->suppflg[$h],
                                'xbid_hist_remarks'=>'Supplier Create RFQ',
                                'xbid_site'=>$req->site,
                                'xbid_um'=>$um,
                                'xbid_desc'=>$item_desc,
                            );                
                                DB::table('xbid_hist')->insert($data3);
                                $h++;
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


                    DB::commit();

                }catch(\InvalidArgumentException $ex){
                    // return back()->withError($ex->getMessage())->withInput();
                    return response()->json(['error'=>$ex->getMessage()], 401);
                    DB::rollback();
                    dd('123');
                }catch(\Exception $ex){
                    // return back()->withError($ex->getMessage())->withInput();
                    return response()->json(['error'=>$ex->getMessage()], 401);
                    DB::rollback();
                    dd('1233');
                }catch(\Error $ex){
                    // return back()->withError($ex->getMessage())->withInput();
                    return response()->json(['error'=>$ex->getMessage()], 401);
                    DB::rollback();
                    dd('1233');
                }
            }   
                
            $user = Auth::user();
            // $success['token'] =  $user->createToken('nApp')->accessToken;
            return response()->json(['success' => 'updated','RFQ No. : '.$rfq_nbr.' is created'], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
}
