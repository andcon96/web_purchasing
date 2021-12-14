<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Facade\FlareClient\Stacktrace\File as StacktraceFile;
use File;
use Illuminate\Support\Facades\Schema;

class PurchasePlanController extends Controller
{
    public function purplanbrowse(){ //25 Januari 2021
        
        $datas = DB::table('xpurplan_mstrs')
                    ->join('xpurplan_dets', 'xpurplan_dets.rf_number', '=', 'xpurplan_mstrs.rf_number')
                    ->join('xalert_mstrs', 'xalert_mstrs.xalert_supp', '=', 'xpurplan_mstrs.supp_code')
                    ->join('xitemreq_mstr', 'xitemreq_mstr.xitemreq_part', '=', 'xpurplan_dets.item_code')
                    ->orderBy('xpurplan_dets.id', 'ASC')
                    ->paginate(15);
        
        return view('purplan.purplanbrowse', ['data' => $datas]);
    }
    
    public function viewppbrowse(Request $req){
        $datas = DB::table('xpurplan_mstrs')
                    ->join('xpurplan_dets', 'xpurplan_dets.rf_number', '=', 'xpurplan_mstrs.rf_number')
                    ->join('xalert_mstrs', 'xalert_mstrs.xalert_supp', '=', 'xpurplan_mstrs.supp_code')
                    ->join('xitemreq_mstr', 'xitemreq_mstr.xitemreq_part', '=', 'xpurplan_dets.item_code')
                    ->orderBy('xpurplan_dets.id', 'ASC')
                    ->paginate(15);
                    
        if($req->ajax()){
                return view('purplan.table-ppbrowse', ['data' => $datas]);
        }
        
        return view('purplan.purplanbrowse', ['data' => $datas]);
    }
    
    public function ppbrowsesearch(Request $req){ //25 Januari 2021
        
        if($req->ajax()){
            $rfnumber = $req->value;
            $suppcode = $req->code;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;
            $status = $req->status;
            
            if($rfnumber == null and $suppcode == null and $datefrom == null and $dateto == null and $status == null){
                $datas = DB::table('xpurplan_mstrs')
                    ->join('xpurplan_dets', 'xpurplan_dets.rf_number', '=', 'xpurplan_mstrs.rf_number')
                    ->join('xalert_mstrs', 'xalert_mstrs.xalert_supp', '=', 'xpurplan_mstrs.supp_code')
                    ->join('xitemreq_mstr', 'xitemreq_mstr.xitemreq_part', '=', 'xpurplan_dets.item_code')
                    ->orderBy('xpurplan_dets.id', 'ASC')
                    ->paginate(15);
                    
                return view('purplan.table-ppbrowse',['data' => $datas]);
            }else{
                if($datefrom == null){
                    $datefrom = '2000-01-01';
                }else{
                    $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                    // ubah ke int
                    $new_date_from = strtotime($new_format_date_from);

                    // ubah ke format date
                    $datefrom = date('Y-m-d',$new_date_from);
                }
                if($dateto == null){
                    $dateto = '3000-01-01';
                }else{
                    // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                    $new_format_date_to = str_replace('/', '-', $req->dateto); 

                    // ubah ke int
                    $new_date_to = strtotime($new_format_date_to);

                    // ubah ke format date
                    $dateto = date('Y-m-d',$new_date_to);
                }

                $query = 'xpurplan_dets.due_date >= "'.$datefrom.'" AND xpurplan_dets.due_date <= "'.$dateto.'" ';

                if($rfnumber != null){
                    $query .= "AND xpurplan_mstrs.rf_number = '".$rfnumber."'";
                }
                if($suppcode != null){
                    $query .= "AND xpurplan_mstrs.supp_code = '".$suppcode."'";
                }
                if($status != null){
                    $query .= "AND xpurplan_dets.status = '".$status."'"; 
                }

                $datas = DB::table('xpurplan_mstrs')
                    ->join('xpurplan_dets', 'xpurplan_dets.rf_number', '=', 'xpurplan_mstrs.rf_number')
                    ->join('xalert_mstrs', 'xalert_mstrs.xalert_supp', '=', 'xpurplan_mstrs.supp_code')
                    ->join('xitemreq_mstr', 'xitemreq_mstr.xitemreq_part', '=', 'xpurplan_dets.item_code')
                    ->whereRaw($query)
                    ->orderBy('xpurplan_dets.id', 'ASC')
                    ->paginate(15);
                
                return view('purplan.table-ppbrowse',['data' => $datas]);
                

            }
        }
    }
    
    public function index(){
    	$data = DB::table('xpurplan_mstrs')
    				->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_mstrs.supp_code')
    				->where('status','=','New')
    				->get();
    	return view('purplan.purplan-view',['data' => $data]);
    }

    public function purplansearch(Request $req){
        if($req->ajax()){
            $rfnumber = $req->value;
            $suppcode = $req->code;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            if($rfnumber == null and $suppcode == null and $datefrom == null and $dateto == null){
                $data = DB::table('xpurplan_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_mstrs.supp_code')
                        ->where('status','=','New')
                        ->get();
                return view('purplan.table-view',['data' => $data]);
            }else{
                if($datefrom == null){
                    $datefrom = '2000-01-01';
                }else{
                    $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                    // ubah ke int
                    $new_date_from = strtotime($new_format_date_from);

                    // ubah ke format date
                    $datefrom = date('Y-m-d',$new_date_from);
                }
                if($dateto == null){
                    $dateto = '3000-01-01';
                }else{
                    // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                    $new_format_date_to = str_replace('/', '-', $req->dateto); 

                    // ubah ke int
                    $new_date_to = strtotime($new_format_date_to);

                    // ubah ke format date
                    $dateto = date('Y-m-d',$new_date_to);
                }

                $query = 'due_date >= "'.$datefrom.'" AND due_date <= "'.$dateto.'" ';

                if($rfnumber != null){
                    $query .= "AND rf_number = '".$rfnumber."'";
                }
                if($suppcode != null){
                    $query .= "AND supp_code = '".$suppcode."'";
                }

                $data = DB::table('xpurplan_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_mstrs.supp_code')
                        ->where('status','=','New')
                        ->whereRaw($query)
                        ->get();
                
                return view('purplan.table-view',['data' => $data]);
                

            }
        }
    }

    public function viewdetails(Request $req){
    // 	dd($req->all());

    	if($req->data == ''){
    		// session()->flash('error','Please Select at least 1 data');
            alert()->error('Error','Please Select at least 1 data');
    		return back();
    	}else{

    		DB::table('xpurplan_temp')
    				->where('username','=',Session::get('username'))
    				->delete();
			$validate = '';
            foreach($req->data as $data){

            	$detail = DB::table("xpurplan_mstrs")
            				->join('xpurplan_dets','xpurplan_mstrs.rf_number','=','xpurplan_dets.rf_number')
            				->join('xitemreq_mstr','xitemreq_part','=','xpurplan_dets.item_code')
            				->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_dets.supp_code')
            				->where('xpurplan_dets.rf_number','=',$data)
							->where('xpurplan_dets.status', '!=','Close')
                            ->get();
                

                
                foreach($detail as $show){

                    if($show->supp_code != $validate && $show->supp_code != '' && $validate != ''){
                        return redirect()->back()->with('error', 'Supplier cannot be the same and blank');
                    }
                    $validate = $show->supp_code;
                }


            	foreach($detail as $detail){
                    // dd($detail);
            		DB::table('xpurplan_temp')
            				->insert([
            					"rf_number" => $detail->rf_number,
            					"supp_code" => $detail->supp_code,
            					"line" => $detail->line,
            					"item_code" => $detail->item_code,
            					"qty_req" => $detail->qty_req,
            					"qty_pro" => $detail->qty_pro,
                                "qty_pur" => $detail->qty_pur,
                                "site" => $detail->site,
            					"price" => $detail->price,
            					"due_date" => $detail->due_date,
            					"propose_date" => $detail->propose_date,
            					"purchase_date" => $detail->purchase_date,
            					"username" => Session::get('username'),
            				]);
            	}
            }

            //return view('purplan.purplandet-view',['data' => $users]);
            return redirect()->route('viewdetailtmp');
    	}
    }

    public function viewdetailtmp(){
        // get data 
        $users = DB::table('xpurplan_temp')
                    ->join('xitemreq_mstr','xitemreq_part','=','xpurplan_temp.item_code')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_temp.supp_code')
                    ->where('username','=',Session::get('username'))
                    ->orderBy('rf_number','ASC')
                    ->orderBy('line','ASC')
                    ->get();

        return view('purplan.purplandet-view',['data' => $users]);
    }

    public function deletetemp(Request $req){
        if($req->ajax()){
            DB::table('xpurplan_temp')
                    ->where('id','=',$req->id)
                    ->where('rf_number','=',$req->rfnumber)
                    ->delete();

            $data = DB::table('xpurplan_temp')
                    ->join('xitemreq_mstr','xitemreq_part','=','xpurplan_temp.item_code')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_temp.supp_code')
                    ->where('xpurplan_temp.username','=',Session::get('username'))
                    ->orderBy('rf_number','ASC')
                    ->orderBy('line','ASC')
                    ->get();

            return view('purplan.tabledet-view',['data' => $data]);
        }
    }

    public function edittemp(Request $req){
        if($req->ajax()){
            // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
            $new_format_pur_date = str_replace('/', '-', $req->purdate); 

            // ubah ke int
            $new_pur_date = strtotime($new_format_pur_date);

            // ubah ke format date
            $formatted_date = date('Y-m-d',$new_pur_date);


            DB::table('xpurplan_temp')
                    ->where('id','=',$req->id)
                    ->where('rf_number','=',$req->rfnumber)
                    ->update([
                        'purchase_date' => $formatted_date,
                        'qty_pur' => $req->qtypur,
                        'price' => $req->price,
                    ]);

            $data = DB::table('xpurplan_temp')
                    ->join('xitemreq_mstr','xitemreq_part','=','xpurplan_temp.item_code')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpurplan_temp.supp_code')
                    ->where('xpurplan_temp.username','=',Session::get('username'))
                    ->orderBy('rf_number','ASC')
                    ->orderBy('line','ASC')
                    ->get();

            return view('purplan.tabledet-view',['data' => $data]);
        }
    }

    /* Moved
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
    }*/
    
}