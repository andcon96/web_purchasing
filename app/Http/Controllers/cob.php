<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class AgingfgController extends Controller
{
    public function agingfg(){
    	$data = DB::table("xxaging_fg")
		    	->orderBy('af_marketing', 'asc')
		    	->orderBy('af_part', 'asc')
		    	->get();

		$dataDis = DB::table("xxaging_fg")
    			->select('af_marketing')
    			->distinct()
		    	->orderBy('af_marketing', 'asc')
		    	->get();

		 $dataCost = DB::table("xxaging_fg")
    			->select('af_costset')
    			->distinct()
		    	->orderBy('af_costset', 'asc')
		    	->get();   

    	return view('/aging/agingfg', ['data' => $data, 'dataDis' => $dataDis, 'dataCost' => $dataCost]);
          
    }
    
        public function tes(Request $request){
           
       Schema::create('temp_table', function($table)
                {
                    $table->string('xpart');
                    $table->string('xdesc');
                    $table->temporary();
                });    
           
              $qxUrl			= 'http://192.168.20.17:9399/wsasim/services';	/*services/wsdl*/
		$qxReceiver		= '';
		$qxSuppRes		= 'false';
		$qxScopeTrx		= '';
		$qdocName		= '';
		$qdocVersion	= '';
		$dsName			= '';
		$timeout 		= 0;
      $nodeJson 	= 'oJSON';
      // menangkap data pencarian
		$cari = $request->cari;      
      $qdocRequest = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                     '<Body>'.
                     '<itm xmlns="http://ws.iris.co.id/wsasim">'.
                     '<inpdomain>10usa</inpdomain>'.
                     '<inpart>01010</inpart>'.
                     '</itm>'.
                     '</Body>'.
                     '</Envelope>';
                      
             
    $curlOptions = array(CURLOPT_URL => $qxUrl,
								 CURLOPT_CONNECTTIMEOUT => $timeout,		// in seconds, 0 = unlimited / wait indefinitely.
								 CURLOPT_TIMEOUT => $timeout + 120,	// The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
								 CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
								 CURLOPT_POSTFIELDS	=> preg_replace("/\s+/", " ", $qdocRequest),
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
				$qdocResponse = curl_exec($curl);			// sending qdocRequest here, the result is qdocResponse.
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
            			
			$xmlResp = simplexml_load_string($qdocResponse);
         
			$xmlResp->registerXPathNamespace('ns1','http://ws.iris.co.id/wsasim');
         $json = $xmlResp->xpath('//ns1:t_desc');
         $datax1 = array(
                             'xpart'=>$xmlResp->xpath('//ns1:t_desc'),
                             'xdesc'=>$xmlResp->xpath('//ns1:t_desc'),
                            );
         //$aryJson = json_decode($json[0], true);
         //$jsonResult= json_encode($aryJson);
            }
		
      
   

			
			//echo $xmlResp->asXML();
			

			
           //foreach($xmlResp as $data){
			  foreach($json as $data) {               
            
            dd($data);
            
               
            } 
             DB::table('temp_table')->insert($datax1);  
             
            $abc = DB::table("temp_table")
		    	->get();
            dd($abc);
            Schema::drop('temp_table');
            
           	return view('/aging/tes', ['datax1' => $abc]); 

    }
    
    	private function httpHeader($req) {
         return array('Content-type: text/xml;charset="utf-8"',
               'Accept: text/xml',
               'Cache-Control: no-cache',
               'Pragma: no-cache', 
			   
			   
               'SOAPAction: ""',		// jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
               'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
         }
		 

    public function parAgingfg(Request $request){
    	$data = DB::table("xxaging_fg")
    			->where('af_costset', $request->category)
		    	->orderBy('af_marketing', 'asc')
		    	->orderBy('af_part', 'asc')
		    	->get();

		$dataDis = DB::table("xxaging_fg")
    			->select('af_marketing')
    			->distinct()
    			->where('af_costset', $request->category)
		    	->orderBy('af_marketing', 'asc')
		    	->get();

		 $dataCost = DB::table("xxaging_fg")
    			->select('af_costset')
    			->distinct()
		    	->orderBy('af_costset', 'asc')
		    	->get();   

    	return view('/aging/agingfg', ['data' => $data, 'dataDis' => $dataDis, 'dataCost' => $dataCost]);
		//dd($data);
    }
}
