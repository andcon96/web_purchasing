<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurelMaintenanceController extends Controller
{
    public function index()
    {    
        //return view('/setting/alertmaint');
        $alert = DB::select('select * from xsurel_mstrs join xalert_mstrs on xsurel_mstrs.xsurel_supp = xalert_mstrs.xalert_supp join xitemreq_mstr on xsurel_part = xitemreq_part order by xsurel_part');  
        //Item Code
        $supp = DB::table('xalert_mstrs')
                        ->join('users','xalert_supp','=','supp_id')
                        ->groupBy('xalert_supp')
                        ->get();

        $item = DB::table('xitemreq_mstr')
                        ->get();
        //dd($supp);

        return view('/setting/supprel',['alert'=>$alert,'supp'=>$supp,'item'=>$item]);
    }

    public function update(Request $req)
    {
        //dd($req->all());
        $id = $req->input('edit_id');
        $supplier = $req->input('itemsupp');
        $part = $req->input('itemcode');

        DB::table('xsurel_mstrs')
            ->where('xsurel_id', $id)
            ->update([
                    'xsurel_part' => $part,
                    'xsurel_supp' => $supplier,
            ]);

        // session()->flash("updated","Supplier Relation Successfully Updated !");
        alert()->success('Success','Supplier Relation Successfully Updated');
              
        return back();
    }

    public function createnew(Request $req)
    {
        //dd($req->all());

        $supplier = $req->input('c_itemsupp');
        $part = $req->input('c_itemcode');

        $checkdata = DB::table('xsurel_mstrs')
                        ->where('xsurel_supp','=',$supplier)
                        ->where('xsurel_part','=',$part)
                        ->first();
        //dd($checkdata);

        if($checkdata){
            // Found
            // session()->flash("error","Supplier Relation Already Exists");
            alert()->error('Error','Supplier Relation Already Exists');
                  
            return back();
        }else{
            // Not Found
            $data1 = array(
                    'xsurel_supp'=>$supplier,
                    'xsurel_part'=>$part,               
                );
        
            DB::table('xsurel_mstrs')->insert($data1);
            
            // session()->flash("updated","Supplier Relation Successfully Created");
            alert()->success('Success','Supplier Relation Successfully Created');
                  
            return back();   
        }
    }

    public function delete(Request $req)
    {
        //dd($req->all());
        DB::table('xsurel_mstrs')
            ->where('xsurel_id','=',$req->delete_id)
            ->delete();

        // session()->flash("updated","Supplier Relation Successfully Deleted !");
        alert()->success('Success','Supplier Relation Successfully Updated !');
        return back();
    }

    // public function loadsupplier(Request $req){
        //     //dd('123');
        //     // Jalanin Supplier Mstr 
        //     exec("start cmd /c suppmstr.bat");

        //     $file = fopen(public_path('suppmstr.csv'),"r");

        //     $importData_arr = array();
        //       $i = 0;

        //     while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
        //          $num = count($filedata );
                
        //          // Skip first row (Remove below comment if you want to skip the first row)
        //          /*if($i == 0){
        //             $i++;
        //             continue; 
        //          }*/
        //          for ($c=0; $c < $num; $c++) {
        //             $importData_arr[$i][] = $filedata [$c];
        //          }
        //          $i++;
        //     }
        //     fclose($file);

        //     //dd($importData_arr);

        //     foreach($importData_arr as $importData){
        //         DB::table('xalert_mstrs')->updateOrInsert(
        //             ['xalert_supp' => $importData[0]],
        //             ['xalert_nama' => $importData[1], 
        //             'xalert_alamat' => $importData[2], 
        //          ]);

        //     }


        //     // Session()->flash('updated','Supplier Successfully Uploaded');
        //     alert()->success('Success','Supplier Successfully Uploaded');
        //     return back();
    // }

    public function loadsupplier(Request $req){
        // Validasi WSA
        // DB::table('xalert_mstrs')->delete();

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

        $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                        '<Body>'.
                        '<supp_supp_mstr xmlns="'.$wsa->wsas_path.'">'.
                        '<inpdomain>'.$domain.'</inpdomain>'.
                        '</supp_supp_mstr>'.
                        '</Body>'.
                        '</Envelope>';

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
        // dd($qdocResponse);
        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];


        if($qdocResult == 'true'){

            foreach($dataloop as $dataloop){

                DB::table('xalert_mstrs')
                            ->updateOrInsert(
                                ['xalert_supp' => $dataloop->t_suppcode],
                                ['xalert_nama' => $dataloop->t_suppname,
                                'xalert_alamat' => $dataloop->t_address,
                            ]);
            }
            
            // session()->flash('updated','Load Item Conversion UM Success');;
            alert()->success('Success','Load Supplier Success');
            return back();
        }else{
            // session()->flash('error','Load Item Conversion UM Failed');;
            alert()->error('Error','Load Supplier UM Failed');
            return back();
        }
    }

    public function suppmstrsearch(Request $req){
        if($req->ajax()){

            if($req->suppcode == null){
                $data = DB::table('xalert_mstrs')
                        ->paginate(10);
                return view('/setting/tablesupplier',['alert'=>$data]);
            }


            $data = DB::table('xalert_mstrs')
                        ->whereRaw('xalert_mstrs.xalert_supp like "'.$req->suppcode.'%"')
                        ->paginate(100);
            return view('/setting/tablesupplier',['alert'=>$data]);
        }
    }

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

}


