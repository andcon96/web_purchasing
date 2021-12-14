<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AlertItemController extends Controller
{
    public function index()
    {    
        //return view('/setting/alertmaint');
        $alert = DB::select('select * from xalertitem_mstrs');

        $supp = DB::table('users')
                    ->where('users.role','=','Supplier')
                    ->get();

        //dd($supp);

        return view('/setting/alertitem', compact('alert','supp'));
    }

    public function createnew(Request $req)
    {
        $supplier = $req->input('supname');
        $active = $req->input('active');
        $itemcode = $req->input('itemcode');
        $itemgroup = $req->input('itemgroup');
        $itemtype = $req->input('itemtype');
        $safetystock = $req->input('safetystock');


        $day1 = $req->input('alertdays1');
        $day2 = $req->input('alertdays2');
        $day3 = $req->input('alertdays3');

        $email1 = $req->input('alertemail1');
        $email2 = $req->input('alertemail2');
        $email3 = $req->input('alertemail3');  


        $data1 = array(
                    'xalertitem_supp'=>$supplier,
                    'xalertitem_active'=>$active,
                    'xalertitem_code'=>$itemcode,
                    'xalertitem_group'=>$itemgroup,
                    'xalertitem_type'=>$itemtype,
                    'xalertitem_sfty_stock'=>$safetystock,
                    'xalertitem_day1'=>$day1,
                    'xalertitem_day2'=>$day2,      
                    'xalertitem_day3'=>$day3, 
                    'xalertitem_email1'=>$email1,
                    'xalertitem_email2'=>$email2,
                    'xalertitem_email3'=>$email3,                  
                );
        
        DB::table('xalertitem_mstrs')->insert($data1);

        alert()->success('Success','Supplier Alert Item Succesfully Created');
              
        return back();  
    }

    public function search(Request $req)
    {
        if($req->ajax()){

            $output="";

            $jabatan=DB::table("xalertitem_mstrs")->where("xalertitem_id",$req->search)
                                 ->get();
            
            $array = json_decode(json_encode($jabatan), true);

            return response()->json($array);

        }
    }

    public function update(Request $req)
    {
        $id = $req->input('edit_id');
        $supplier = $req->input('supname');
        $active = $req->input('active');
        $itemcode = $req->input('itemcode');
        $itemgroup = $req->input('itemgroup');
        $itemtype = $req->input('itemtype');
        $safetystock = $req->input('safetystock');


        $day1 = $req->input('alertdays1');
        $day2 = $req->input('alertdays2');
        $day3 = $req->input('alertdays3');

        $email1 = $req->input('alertemail1');
        $email2 = $req->input('alertemail2');
        $email3 = $req->input('alertemail3');  

        DB::table('xalertitem_mstrs')
            ->where('xalertitem_id', $id)
            ->update([
                    'xalertitem_day1' => $day1,
                    'xalertitem_day2' => $day2,
                    'xalertitem_day3' => $day3,
                    'xalertitem_email1' => $email1,
                    'xalertitem_email2' => $email2,
                    'xalertitem_email3' => $email3,
                    'xalertitem_code' => $itemcode,
                    'xalertitem_type' =>$itemtype,
                    'xalertitem_group' =>$itemgroup,
                    'xalertitem_sfty_stock' =>$safetystock,
            ]);

        alert()->success('Success','Supplier Alert Succesfully Updated');
              
        return back();
    }

    public function itemconvmenu(Request $req){
        $data = DB::table('item_konversi')
                    ->paginate(10);
        if($req->ajax()){
            return view('/setting/tableitemconv',compact('data'));
        }        
        
        
        return view('/setting/itemkonv',compact('data'));
    }

    public function ummastermenu(Request $req){
        $datas = DB::table('item_um')
                    ->paginate (10);

        if($req->ajax()){
            return view('/setting/tableummaster',compact('datas'));
        }

        
        return view('/setting/ummaster', compact('datas'));
    }

    public function itemconvsearch(Request $req){
        //dd($req->all());
        if($req->ajax()){
            if($req->itemcode == null){
                $hasil = DB::table('item_konversi')
                    
                    ->paginate(10);
            }else{
            $hasil = DB::table('item_konversi')
                    ->where('item_code', $req->itemcode)
                    ->paginate(10);
            //dd($hasil);
            }
        }

        return view('/setting/tableitemconv',['data' => $hasil]);
    }

    public function ummastersearch(Request $req){

        if($req->ajax()){
            if($req->um == null){
                $hasil = DB::table('item_um')
                    ->paginate(10);
            }else{
                $hasil = DB::table('item_um')
                        ->where('um', $req->um)
                        ->paginate(10);

                
            //dd($hasil);
            }
        }

        return view('/setting/tableummaster',['datas' => $hasil]);
    }

    public function loaditemconv(Request $req){
        // Validasi WSA
        DB::table('item_konversi')->delete();

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
                        '<itemconv xmlns="'.$wsa->wsas_path.'">'.
                        '<inpdomain>'.$domain.'</inpdomain>'.
                        '</itemconv>'.
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

        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

        if($qdocResult == 'true'){

            foreach($dataloop as $dataloop){
                DB::table('item_konversi')
                        ->insert([
                            'item_code' => $dataloop->t_itemcode,
                            'um_1' => $dataloop->t_um1,
                            'um_2' => $dataloop->t_um2,
                            'qty_item' => $dataloop->t_qtyitem,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);
            }
            
            // session()->flash('updated','Load Item Conversion UM Success');;
            alert()->success('Success','Load Item Conversion UM Success');
            return back();
        }else{
            // session()->flash('error','Load Item Conversion UM Failed');;
            alert()->error('Error','Load item Conversion UM Failed');
            return back();
        }
    }

    public function loadum(Request $req){

        DB::table('item_um')->delete();

        $wsa = DB::table('wsas')
                    ->first();

        // Validasi WSA
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
                        '<um_master xmlns="'.$wsa->wsas_path.'">'.
                        '<inpdomain>'.$domain.'</inpdomain>'.
                        '</um_master>'.
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

        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

        if($qdocResult == 'true'){

            foreach($dataloop as $dataloop){
                DB::table('item_um')
                        ->insert([
                            'um' => $dataloop->t_um,
                            'um_desc' => $dataloop->t_cmmt,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);
            }
            
            // session()->flash('updated','Load Item UM Success');;
            alert()->success('Success','Load Item UM Success');
            return back();
        }else{
            // session()->flash('error','Load Item UM Failed');;
            alert()->error('Error','Load Item UM Failed');
            return back();
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
