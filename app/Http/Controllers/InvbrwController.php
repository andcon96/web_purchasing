<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvbrwController extends Controller
{

  private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
          'Accept: text/xml',
          'Cache-Control: no-cache',
          'Pragma: no-cache',
          'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
          'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
  }

  public function index()
  {
    $data = DB::table('users')
        //->join('xpo_mstrs','users.id','=','xpo_mstrs.xpo_vend')
        ->get();
    $podedt = DB::table('xpo_mstrs')
        ->selectraw("count(*)")->get();
  
        
    //dd($podedt);
    return view('/inv/locbrw', compact('data','podedt'));
    //return view('/inv/locbrw',['podedt'=>$poed]);    
  }
    
  public function bstock()
  {

    $bstock = DB::table('xinv_mstr')->where('xinv_ss','y')
              ->join('xitem_mstr', 'xinv_mstr.xinv_part', '=', 'xitem_mstr.xitem_part')
              ->paginate(10);
  
    $bstock1 = DB::table('xinv_mstr')->where('xinv_ss_pct','y')
              ->join('xitem_mstr', 'xinv_mstr.xinv_part', '=', 'xitem_mstr.xitem_part')
              ->paginate(10);
        
    //dd($podedt);
    return view('/inv/bstock', compact('bstock','bstock1'));
    //return view('/inv/locbrw',['podedt'=>$poed]);    
  }
    
  // public function loadinv1(){
    // 	  DB::table('xinv_mstr')->delete();
    //     DB::table('xinvd_det')->delete();
        
    //   exec("start cmd /c inv.bat");	
    //    $file = fopen(public_path('inv.csv'),"r");

    //       $importData_arr = array();
    //         $i = 0;

    //         while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
    //            $num = count($filedata );
              
    //            // Skip first row (Remove below comment if you want to skip the first row)
    //            /*if($i == 0){
    //               $i++;
    //               continue; 
    //            }*/
    //            for ($c=0; $c < $num; $c++) {
    //               $importData_arr[$i][] = $filedata [$c];
    //            }
    //            $i++;
    //         }
    //         fclose($file);
              
      
    //       // Insert to MySQL database
    //     foreach($importData_arr as $importData){  
      
    // 			$checkdb = DB::table('xsite_mstr')
    //                             ->where('xsite_mstr.xsite_site','=',$importData[3])
    //                             ->first();
                  
    // 			$pc1 = DB::table('xitem_mstr')
    //                             ->where('xitem_mstr.xitem_part','=',$importData[1])
    //                             ->first();	
    //       $com = DB::table('com_mstr')
    //                             ->first();			
    // 			if ($pc1) {
    // 			$pct = $importData[2] + ($importData[2] * $pc1->xitem_sfty) / 100;
    // 				if($importData[4] > $importData[2] and $importData[4] < $pct) {
    // 				  $ss = "Y";
    //                       Mail::send('emailexp', 
    //                         ['pesan' => 'There is an item that going to expired',
    //                         'note1' => $importData[1],
    //                         'note2' => $importData[4],
    //                         'note3' => $importData[3],
    //                         'note4' => $importData[2],                  
    //                         'note7' => 'Please check'], 
    //                         function ($message) use ($show,$com)
    //                         {
    //                             $message->subject('Web Support IMI Notification');
    //                             $message->from($com->com_email); // Email Admin Fix
    //                             $message->to($show->xitem_sfty_email);
    //                         });          
                
    // 				}
    // 				else {
    // 					$ss = "N";
    // 				}	
                    
    // 			}	
    //           if ($checkdb) {
    //                    $checkitm = DB::table('xitem_mstr')
    //                           ->where('xitem_mstr.xitem_part','=',$importData[1])
    //                           ->first();
    //                    if ($checkitm) {       
    //                          DB::table('xinv_mstr')->updateOrInsert(
    //                              ['xinv_domain' => $importData[0], 'xinv_part' => $importData[1],'xinv_site' => $importData[3] ],
    //                              ['xinv_sft_stock' => $importData[2],                                 
    //                               'xinv_qty_oh' => $importData[4],
    //                               'xinv_qty_ord' => $importData[5],
    //                               'xinv_qty_req' => $importData[6],
    //                               'xinv_ss' => $importData[7],
    //                               'xinv_ss_pct' => $ss,                 
    //                           ]);
    //                    }
    //           }  
    //     }
      
    //     $file = fopen(public_path('invdet.csv'),"r");

    //       $importData_arrx = array();
    //         $i = 0;

    //         while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
    //            $num = count($filedata );
              
    //            // Skip first row (Remove below comment if you want to skip the first row)
    //            /*if($i == 0){
    //               $i++;
    //               continue; 
    //            }*/
    //            for ($c=0; $c < $num; $c++) {
    //               $importData_arrx[$i][] = $filedata [$c];
    //            }
    //            $i++;
    //         }
    //         fclose($file);
              
    //       // Insert to MySQL database
    //       $site = DB::table('xsite_mstr')->get(); 
          
    //           foreach($importData_arrx as $importData){  
          
    //           $checkdb = DB::table('xsite_mstr')
    //                               ->where('xsite_mstr.xsite_site','=',$importData[1])
    //                               ->first();
                    
    //               $importData11 = str_replace(",", "." , $importData[11]);		
    //               if ($checkdb) {
    //                   $checkitm = DB::table('xitem_mstr')
    //                               ->where('xitem_mstr.xitem_part','=',$importData[2])
    //                               ->first();
    //                       if ($checkitm) {    
    //                           DB::table('xinvd_det')->updateOrInsert(
    //                               ['xinvd_domain' => $importData[0], 'xinvd_site' => $importData[1], 'xinvd_part' => $importData[2], 'xinvd_loc' => $importData[3], 'xinvd_lot' => $importData[4] ],
    //                               ['xinvd_ref' => $importData[5],
    //                               'xinvd_qty_oh' => $importData[6],
    //                               'xinvd_qty_all' => $importData[7],
    //                               'xinvd_expire' => $importData[8],
    //                               'xinvd_ed' => $importData[9],  
    //                               'xinvd_days' => $importData[10],
    //                               'xinvd_amt' => $importData11,				 
    //                           ]);
    //                       }
    //               }  
              
    //           /*============alert3============*/
    //           $data = DB::table('xitem_mstr')                           
    //                               ->where('xitem_mstr.xitem_part','=',$importData[2])
    //                               ->where('xitem_mstr.xitem_day1','=',$importData[10])
    //                               ->where('xitem_mstr.xitem_day_email1','!=', "")
    //                               ->get();       
    //           $com = DB::table('com_mstr')
    //                             ->first();
                            
    //           if(count($data) != 0){
    //               foreach ($data as $show){                                                             
    //                             Mail::send('emailexp', 
    //                             ['pesan' => 'There is an item that going to expired',
    //                             'note1' => $importData[2],
    //                             'note2' => $importData[6],
    //                             'note3' => $importData[3],
    //                             'note4' => $importData[4],
    //                             'note5' => $importData[5],
    //                             'note6' => $importData[10].' Days',
    //                             'note7' => 'Please check'], 
    //                             function ($message) use ($show,$com)
    //                             {
    //                                 $message->subject('Web Support IMI Notification');
    //                                 $message->from($com->com_email); // Email Admin Fix
    //                                 $message->to($show->xitem_day_email1);
    //                             });                    
    //                 }              
    //               }
                    
    //               //dd('123');
                  
    //               $data2 = DB::table('xitem_mstr')
    //                               ->where('xitem_mstr.xitem_part','=',$importData[2])
    //                               ->where('xitem_mstr.xitem_day2','=',$importData[10])
    //                               ->where('xitem_mstr.xitem_day_email2','!=', "")
    //                               ->get();
                
    //           if(count($data2) != 0){
              
    //               foreach ($data2 as $show){                                                               
    //                             Mail::send('emailexp', 
    //                             ['pesan' => 'There is an item that going to expired',
    //                             'note1' => $importData[2],
    //                             'note2' => $importData[6],
    //                             'note3' => $importData[3],
    //                             'note4' => $importData[4],
    //                             'note5' => $importData[5],
    //                             'note6' => $importData[10].' Days',
    //                             'note7' => 'Please check'], 
    //                             function ($message) use ($show,$com)
    //                             {
    //                                 $message->subject('Web Support IMI Notification');
    //                                 $message->from($com->com_email); // Email Admin Fix
    //                                 $message->to($show->xitem_day_email2);
    //                             });
                          
    //                 }              
    //               }
                
    //               $data3 = DB::table('xitem_mstr')
    //                               ->where('xitem_mstr.xitem_part','=',$importData[2])
    //                               ->where('xitem_mstr.xitem_day3','=',$importData[10])
    //                               ->where('xitem_mstr.xitem_day_email3','!=', "")
    //                               ->get();
                                  
    //           if(count($data3) != 0){
    //               foreach ($data3 as $show){                                                               
    //                             Mail::send('email', 
    //                             ['pesan' => 'There is an item that going to expired',
    //                             'note1' => $importData[2],
    //                             'note2' => $importData[6],
    //                             'note3' => $importData[3],
    //                             'note4' => $importData[4],
    //                             'note5' => $importData[5],
    //                             'note6' => $importData[10].' Days',
    //                             'note7' => 'Please check'], 
    //                             function ($message) use ($show,$com)
    //                             {
    //                                 $message->subject('Web Support IMI Notification');
    //                                 $message->from($com->com_email); // Email Admin Fix
    //                                 $message->to($show->xitem_day_email3);
    //                             });
                          
    //                 }              
    //               }
                  
    //           }   
              

        
    //           return view('/home');
	// } Old .bat 

  public function loadinv(){
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
                      '<supp_inv_mstr xmlns="'.$wsa->wsas_path.'">'.
                      '<inpdomain>'.$domain.'</inpdomain>'.
                      '</supp_inv_mstr>'.
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
            DB::table('xinv_mstr')->delete();

            foreach($dataloop as $dataloop){
                $checkdb = DB::table('xsite_mstr')
                                  ->where('xsite_mstr.xsite_site','=',$dataloop->t_site)
                                  ->first();
                    
                $pc1 = DB::table('xitem_mstr')
                                      ->where('xitem_mstr.xitem_part','=',$dataloop->t_part)
                                      ->first();	
                $com = DB::table('com_mstr')
                                      ->first();			
                if ($pc1) {
                  $pct = $dataloop->t_sfty_stk + ($dataloop->t_sfty_stk * $pc1->xitem_sfty) / 100;
                  if($dataloop->t_qty_oh > $dataloop->t_sfty_stk and $dataloop->t_qty_oh < $pct) {
                    $ss = "Y";
                                Mail::send('email.emailexp', 
                                  ['pesan' => 'There is an item that going to expired',
                                  'note1' => $dataloop->t_part,
                                  'note2' => $dataloop->t_qty_oh,
                                  'note3' => $dataloop->t_site,
                                  'note4' => $dataloop->t_sfty_stk,                  
                                  'note7' => 'Please check'], 
                                  function ($message) use ($pc1,$com,$dataloop)
                                  {
                                      $message->subject('Web Support IMI Notification');
                                      $message->from($com->com_email); // Email Admin Fix
                                      $message->to($pc1->xitem_sfty_email);
                                  });          
                      
                  }
                  else {
                    $ss = "N";
                  }	
                          
                }	

                if ($checkdb) {
                          $checkitm = DB::table('xitem_mstr')
                                ->where('xitem_mstr.xitem_part','=',$dataloop->t_part)
                                ->first();
                          if ($checkitm) {       
                                DB::table('xinv_mstr')->updateOrInsert(
                                    ['xinv_domain' => $dataloop->t_domain, 'xinv_part' => $dataloop->t_part,'xinv_site' => $dataloop->t_site ],
                                    ['xinv_sft_stock' => $dataloop->t_sfty_stk,                                 
                                    'xinv_qty_oh' => $dataloop->t_qty_oh,
                                    'xinv_qty_ord' => $dataloop->t_qty_ord,
                                    'xinv_qty_req' => $dataloop->t_qty_req,
                                    'xinv_ss' => $dataloop->t_stkles,
                                    'xinv_ss_pct' => $ss,                 
                                ]);
                          }
                }  
            }

      }else{
        alert()->error('Error','Wsa Return False');
        return back();
      }
      

      // Detail

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
                      '<supp_inv_det xmlns="'.$wsa->wsas_path.'">'.
                      '<inpdomain>'.$domain.'</inpdomain>'.
                      '</supp_inv_det>'.
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

        foreach($dataloop as $datadet){
          $checkdb = DB::table('xsite_mstr')
                ->where('xsite_mstr.xsite_site','=',$datadet->t_site)
                ->first();

              $importData11 = str_replace(",", "." , $datadet->t_amt);		
              if ($checkdb) {
                $checkitm = DB::table('xitem_mstr')
                  ->where('xitem_mstr.xitem_part','=',$datadet->t_part)
                  ->first();
                if ($checkitm) {    
                  DB::table('xinvd_det')->updateOrInsert(
                      ['xinvd_domain' => $datadet->t_domain, 'xinvd_site' => $datadet->t_site, 'xinvd_part' => $datadet->t_part, 'xinvd_loc' => $datadet->t_loc, 'xinvd_lot' => $datadet->t_lot],
                      ['xinvd_ref' => $datadet->t_ref,
                      'xinvd_qty_oh' => $datadet->t_qty_oh,
                      'xinvd_qty_all' => $datadet->t_qty_all,
                      'xinvd_expire' => $datadet->t_exp,
                      'xinvd_ed' => $datadet->t_ket,  
                      'xinvd_days' => $datadet->t_hit,
                      'xinvd_amt' => $importData11,				 
                    ]);
                }
              }  
          
          $data = DB::table('xitem_mstr')                           
              ->where('xitem_mstr.xitem_part','=',$datadet->t_part)
              ->where('xitem_mstr.xitem_day1','=',$datadet->t_hit)
              ->where('xitem_mstr.xitem_day_email1','!=', "")
              ->get();  

          
          if(count($data) != 0){
            foreach ($data as $show){                                                             
                          Mail::send('email.emailexp', 
                          ['pesan' => 'There is an item that going to expired',
                          'note1' => $datadet->t_part,
                          'note2' => $datadet->t_qty_oh,
                          'note3' => $datadet->t_loc,
                          'note4' => $datadet->t_lot,
                          'note5' => $datadet->t_ref,
                          'note6' => $datadet->t_hit.' Days',
                          'note7' => 'Please check'], 
                          function ($message) use ($show,$com,$datadet)
                          {
                              $message->subject('Web Support IMI Notification');
                              $message->from($com->com_email); // Email Admin Fix
                              $message->to($show->xitem_day_email1);
                          });                    
            }              
          }

          $data2 = DB::table('xitem_mstr')
              ->where('xitem_mstr.xitem_part','=',$datadet->t_part)
              ->where('xitem_mstr.xitem_day2','=',$datadet->t_hit)
              ->where('xitem_mstr.xitem_day_email2','!=', "")
              ->get();
              
          if(count($data2) != 0){
              foreach ($data2 as $show){                                                               
                            Mail::send('email.emailexp', 
                            ['pesan' => 'There is an item that going to expired',
                            'note1' => $datadet->t_part,
                            'note2' => $datadet->t_qty_oh,
                            'note3' => $datadet->t_loc,
                            'note4' => $datadet->t_lot,
                            'note5' => $datadet->t_ref,
                            'note6' => $datadet->t_hit.' Days',
                            'note7' => 'Please check'], 
                            function ($message) use ($show,$com,$datadet)
                            {
                                $message->subject('Web Support IMI Notification');
                                $message->from($com->com_email); // Email Admin Fix
                                $message->to($show->xitem_day_email2);
                            });
                      
              }              
          }

          $data3 = DB::table('xitem_mstr')
              ->where('xitem_mstr.xitem_part','=',$datadet->t_part)
              ->where('xitem_mstr.xitem_day3','=',$datadet->t_hit)
              ->where('xitem_mstr.xitem_day_email3','!=', "")
              ->get();
                                
            if(count($data3) != 0){
                foreach ($data3 as $show){                                                               
                    Mail::send('email.email', 
                    ['pesan' => 'There is an item that going to expired',
                    'note1' => $datadet->t_part,
                    'note2' => $datadet->t_qty_oh,
                    'note3' => $datadet->t_loc,
                    'note4' => $datadet->t_lot,
                    'note5' => $datadet->t_ref,
                    'note6' => $datadet->t_hit.' Days',
                    'note7' => 'Please check'], 
                    function ($message) use ($show,$com)
                    {
                        $message->subject('Web Support IMI Notification');
                        $message->from($com->com_email); // Email Admin Fix
                        $message->to($show->xitem_day_email3);
                    });   
                }              
            }

        }

      }else{
        alert()->error('Error','Wsa Return False');
        return back();
      }


      alert()->success('Success','Data Inventory Updated');
      return back();

  }

  public function loaditm(){
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
                        '<supp_item_mstr xmlns="'.$wsa->wsas_path.'">'.
                        '<inpdomain>'.$domain.'</inpdomain>'.
                        '</supp_item_mstr>'.
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

        if($qdocResult == true){
          $itm = DB::table('xitm_ctrl')
                      ->whereRaw('xitm_part = "" and xitm_type = "" 
                                  and xitm_promo = "" 
                                  and xitm_group = "" 
                                  and xitm_design = ""
                                  and xitm_prod_line =""')
                      ->first();      

          if(!is_null($itm))
          { 
              foreach($dataloop as $data){
                DB::table('xitem_mstr')
                      ->updateOrInsert([
                          'xitem_domain' => $data->t_domain,
                          'xitem_part' => $data->t_part],
                          ['xitem_desc' => $data->t_desc,
                          'xitem_um' => $data->t_um,
                          'xitem_prod_line' => $data->t_prod_line,
                          'xitem_type' => $data->t_part_type,
                          'xitem_group' => $data->t_group,
                          'xitem_pm' => $data->t_pm_code,
                          'xitem_sfty_stk' => $data->t_sfty_stk,
                          'xitem_promo' => $data->t_promo,
                          'xitem_dsgn' => $data->t_dsgn_grp,
                      ]);
              }
          }
          else
          {
            $i = 0;
            foreach($dataloop as $data){    
                $checkitm = DB::table('xitm_ctrl')
                        ->where('xitm_ctrl.xitm_part','=',$data->t_part)
                        ->first();
                if ($checkitm) {
                    DB::table('xitem_mstr')
                            ->updateOrInsert([
                              'xitem_domain' => $data->t_domain,
                              'xitem_part' => $data->t_part],
                              ['xitem_desc' => $data->t_desc,
                              'xitem_um' => $data->t_um,
                              'xitem_prod_line' => $data->t_prod_line,
                              'xitem_type' => $data->t_part_type,
                              'xitem_group' => $data->t_group,
                              'xitem_pm' => $data->t_pm_code,
                              'xitem_sfty_stk' => $data->t_sfty_stk,
                              'xitem_promo' => $data->t_promo,
                              'xitem_dsgn' => $data->t_dsgn_grp,
                          ]);                    
                }
                else
                {   
                      $checkdb = DB::table('xitm_ctrl')
                              ->where('xitm_ctrl.xitm_prod_line','=',$data->t_prod_line)
                              ->where('xitm_ctrl.xitm_type','=',$data->t_part_type)
                              ->where('xitm_ctrl.xitm_group','=',$data->t_group)
                              ->where('xitm_ctrl.xitm_design','=',$data->t_dsgn_grp)
                              ->where('xitm_ctrl.xitm_promo','=',$data->t_promo)
                              ->first();
                      if ($checkdb) {
                              DB::table('xitem_mstr')
                                    ->updateOrInsert([
                                      'xitem_domain' => $data->t_domain,
                                      'xitem_part' => $data->t_part],
                                      ['xitem_desc' => $data->t_desc,
                                      'xitem_um' => $data->t_um,
                                      'xitem_prod_line' => $data->t_prod_line,
                                      'xitem_type' => $data->t_part_type,
                                      'xitem_group' => $data->t_group,
                                      'xitem_pm' => $data->t_pm_code,
                                      'xitem_sfty_stk' => $data->t_sfty_stk,
                                      'xitem_promo' => $data->t_promo,
                                      'xitem_dsgn' => $data->t_dsgn_grp,
                                  ]);  
                      }
                }
                $i += 1;
            }
          } 

          alert()->success('Success','Load Data Successfull');
          return back();
        }else{
          alert()->error('Error','Load Data Failed, WSA Error');
          return back();
        }

  }
	
  // public function loaditm1(){
    //   	DB::table('xitem_mstr')->delete();
    //     exec("start cmd /c itmmstr.bat");	
      
    //     $file = fopen(public_path('itm.csv'),"r");

    //         $importData_arr = array();
    //           $i = 0;

    //           while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
    //              $num = count($filedata );
                
    //              // Skip first row (Remove below comment if you want to skip the first row)
    //              /*if($i == 0){
    //                 $i++;
    //                 continue; 
    //              }*/
    //              for ($c=0; $c < $num; $c++) {
    //                 $importData_arr[$i][] = $filedata [$c];
    //              }
    //              $i++;
    //           }
    //           fclose($file);
              
    //          $itm = DB::table('xitm_ctrl')->whereRaw('xitm_part = "" and xitm_type = "" 
    //                                               and xitm_promo = "" 
    //                                               and xitm_group = "" 
    //                                               and xitm_design = ""
    //                                               and xitm_prod_line =""')->first();      
                                          
    //          if(!is_null($itm))
    //          { 
                
    //              foreach($importData_arr as $importData){   
    //                         DB::table('xitem_mstr')->updateOrInsert(
    //                                ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
    //                                ['xitem_desc' => $importData[2], 
    //                                 'xitem_um' => $importData[3],
    //                                 'xitem_prod_line' => $importData[4],
    //                                 'xitem_type' => $importData[5],
    //                                 'xitem_group' => $importData[6],
    //                                 'xitem_pm' => $importData[7],
    //                                 'xitem_sfty_stk' => $importData[8],     
    //                                 'xitem_promo' => $importData[9], 
    //                                 'xitem_dsgn' => $importData[10], 
    //                             ]);
    //              }
    //          }
    //          else
    //          {
              
    //                            // Insert to MySQL database
    //                  foreach($importData_arr as $importData){    
    //                      $checkitm = DB::table('xitm_ctrl')
    //                             ->where('xitm_ctrl.xitm_part','=',$importData[1])
    //                             ->first();
    //                      if ($checkitm) {
    //                          DB::table('xitem_mstr')->updateOrInsert(
    //                                      ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
    //                                      ['xitem_desc' => $importData[2], 
    //                                       'xitem_um' => $importData[3],
    //                                       'xitem_prod_line' => $importData[4],
    //                                       'xitem_type' => $importData[5],
    //                                       'xitem_group' => $importData[6],
    //                                       'xitem_pm' => $importData[7],
    //                                       'xitem_sfty_stk' => $importData[8],     
    //                                       'xitem_promo' => $importData[9], 
    //                                       'xitem_dsgn' => $importData[10], 
    //                                   ]);                          
    //                      }
    //                      else
    //                      {   
    //                            $checkdb = DB::table('xitm_ctrl')
    //                                   ->where('xitm_ctrl.xitm_prod_line','=',$importData[4])
    //                                   ->where('xitm_ctrl.xitm_type','=',$importData[5])
    //                                   ->where('xitm_ctrl.xitm_group','=',$importData[6])
    //                                   ->where('xitm_ctrl.xitm_design','=',$importData[10])
    //                                   ->where('xitm_ctrl.xitm_promo','=',$importData[9])
    //                                   ->first();
    //                            if ($checkdb) {
    //                                  DB::table('xitem_mstr')->updateOrInsert(
    //                                      ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
    //                                      ['xitem_desc' => $importData[2], 
    //                                       'xitem_um' => $importData[3],
    //                                       'xitem_prod_line' => $importData[4],
    //                                       'xitem_type' => $importData[5],
    //                                       'xitem_group' => $importData[6],
    //                                       'xitem_pm' => $importData[7],
    //                                       'xitem_sfty_stk' => $importData[8],     
    //                                       'xitem_promo' => $importData[9], 
    //                                       'xitem_dsgn' => $importData[10], 
    //                                   ]);
    //                            }
    //                      }
    //                   }
    //          } 

    //    return back();
    
  // } Old Load .BAT
	
	public function itmbrw()
    {
	 
      $data = DB::table('xitem_mstr')->paginate(10);
						  
      return view('/inv/itmbrw',['item'=>$data]);    
    }
	
	public function dash()
    {
	  $invdx2 = DB::table('xinvd_det')->where('xinvd_ed', '30')->count(); 
	  $invdx1 = DB::table('xinvd_det')->where('xinvd_ed', '0')->count();
	  $invdx3 = DB::table('xinvd_det')->where('xinvd_ed', '90')->count();
	  $invdx4 = DB::table('xinvd_det')->where('xinvd_ed', '180')->count();
     
     $sft1 = DB::table('xinv_mstr')->where('xinv_ss','y')->count(); 
	  $sft2 = DB::table('xinv_mstr')->where('xinv_ss_pct','y')->count();

	
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();
 
	  
	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
    

      $invbrm1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbrm2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbrm3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbrm4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count();
     
	   $invamtx1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
        
                 						  
      return view('/inv/dash',compact('invbr1','invbr2','invbr3','invbr4','invbrm1','invbrm2','invbrm3','invbrm4',
	  'invamt1','invamt2','invamt3','invamt4','invamtx1','invamtx2','invamtx3','invamtx4',
	  'invdx2','invdx1','invdx3','invdx4','sft1','sft2'));   
    } 
    
    public function noinv()
    {
	  $invdx2 = DB::table('xinvd_det')->where('xinvd_ed', '30')->count(); 
	  $invdx1 = DB::table('xinvd_det')->where('xinvd_ed', '0')->count();
	  $invdx3 = DB::table('xinvd_det')->where('xinvd_ed', '90')->count();
	  $invdx4 = DB::table('xinvd_det')->where('xinvd_ed', '180')->count();
     
     $sft1 = DB::table('xinv_mstr')->where('xinv_ss','y')->count(); 
	  $sft2 = DB::table('xinv_mstr')->where('xinv_ss_pct','y')->count();

	
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();

	  
	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
    

      $invbrm1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbrm2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbrm3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbrm4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count();   
	
	   $invamtx1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamtx4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
 
    // dd($invbr1,$invbr2,$invbr3,$invbr4);     
                 						  
      return view('/inv/noinv',compact('invbr1','invbr2','invbr3','invbr4','invbrm1','invbrm2','invbrm3','invbrm4',
	  'invamt1','invamt2','invamt3','invamt4','invamtx1','invamtx2','invamtx3','invamtx4',
	  'invdx2','invdx1','invdx3','invdx4','sft1','sft2'));   
    } 
    
	public function purdet()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();
      $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'p')->paginate(10);               

	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
  
      return view('inv/purdet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
	
	 public function purdet1()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();
  
      $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->paginate(10);                    
      $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
   
	 return view('inv/purdet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
    
         public function purdet2()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();
      $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->paginate(10);                    
      $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();

	 return view('inv/purdet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
    
         public function purdet3()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();
 
      $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->paginate(10);                    
      $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
    
	 return view('inv/purdet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
    
         public function purdet4()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->count();

      $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->paginate(10);                    
      $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'p')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
 
	 return view('inv/purdet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
     }
    
     
	public function mandet()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count();
       $invbr = DB::table('xtrhist')->where('xtrhist_pm', 'm')->paginate(10);               

	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();
   
      return view('inv/mandet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
	public function mandet1()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count(); 
      $invbr  = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->paginate(10);               

	  $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();

      return view('inv/mandet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
	
	public function mandet2()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count(); 
      $invbr  = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->paginate(10);               

	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();

      return view('inv/mandet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
	
	public function mandet3()
    {
      $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count(); 
      $invbr  = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '180')->paginate(10);               

	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();

      return view('inv/mandet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
    }
	
	public function mandet4()
    {
     $invbr1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->count();
      $invbr2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->count();  
      $invbr3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->count();  
      $invbr4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->count(); 
      $invbr  = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '365')->paginate(10);               

	   $invamt1 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket1', '30')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt2 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket2', '90')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt3 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket3', '180')->selectraw('sum(xtrhist_amt) as "total"')->first();
      $invamt4 = DB::table('xtrhist')->where('xtrhist_pm', 'm')->where('xtrhist_ket4', '365')->selectraw('sum(xtrhist_amt) as "total"')->first();

      return view('inv/mandet',compact('invbr1','invbr2','invbr3','invbr4','invbr','invamt1','invamt2','invamt3','invamt4'));    
  }
	
	public function expitm()
    {
		$invd1 = DB::table('xinvd_det')->where('xinvd_ed', '0')
		        ->join('xitem_mstr', 'xinvd_det.xinvd_part', '=', 'xitem_mstr.xitem_part')
			      ->select('xinvd_det.*', 'xitem_mstr.*')
			      ->paginate(10);
            
	    $invdx1 = DB::table('xinvd_det')->where('xinvd_ed', '0')->count();

			   
	    $invd2 = DB::table('xinvd_det')->where('xinvd_ed', '30')
		       ->join('xitem_mstr', 'xinvd_det.xinvd_part', '=', 'xitem_mstr.xitem_part')
			   ->select('xinvd_det.*', 'xitem_mstr.*')
			   ->paginate(10);
		$invdx2 = DB::table('xinvd_det')->where('xinvd_ed', '30')->count();
		
        $invd3 = DB::table('xinvd_det')->where('xinvd_ed', '90')
		       ->join('xitem_mstr', 'xinvd_det.xinvd_part', '=', 'xitem_mstr.xitem_part')
			   ->select('xinvd_det.*', 'xitem_mstr.*')
			   ->paginate(10);	
		$invdx3 = DB::table('xinvd_det')->where('xinvd_ed', '90')->count();
	    $invd4 = DB::table('xinvd_det')->where('xinvd_ed', '180')
		       ->join('xitem_mstr', 'xinvd_det.xinvd_part', '=', 'xitem_mstr.xitem_part')
			   ->select('xinvd_det.*', 'xitem_mstr.*')
			   ->paginate(10);
		$invdx4 = DB::table('xinvd_det')->where('xinvd_ed', '180')->count();

		return view('inv/expitm',compact('invd1','invdx1','invd2','invdx2','invd3','invd4','invdx3','invdx4'));
	}	
   
  // public function store(Request $request)
    // {
    //     exec("start cmd /c trhist.bat");
    //       // Open CSV File n Read
    //     $file = fopen(public_path('trhist.csv'),"r");

    //     $importData_arr = array();
    //       $i = 0;
    //       while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
    //           $num = count($filedata );
              
              
    //           for ($c=0; $c < $num; $c++) {
    //             $importData_arr[$i][] = $filedata [$c];
    //           }
    //           $i++;
    //       }
    //       fclose($file);
    //       //dd($importData_arr);
    //       foreach($importData_arr as $importData){    
    //       $importData5 = str_replace(",", "." , $importData[5]);           
    //       $importData8 = str_replace(",", "." , $importData[8]);      
                  
    //         DB::table('xtrhist')->updateOrInsert(
    //             ['xtrhist_domain' => $importData[0], 'xtrhist_part' => $importData[1] ],
    //             ['xtrhist_desc' => $importData[2], 
    //               'xtrhist_um' => $importData[3],
    //               'xtrhist_pm' => $importData[4],
    //               'xtrhist_qty_oh' => $importData5,
    //               'xtrhist_last_date' =>$importData[6],
    //               'xtrhist_days' =>$importData[7],
    //               'xtrhist_amt' => $importData8,  
    //               'xtrhist_ket1' => $importData[9],  
    //               'xtrhist_ket2' => $importData[10],  
    //               'xtrhist_ket3' => $importData[11],  
    //               'xtrhist_ket4' => $importData[12],  
    //               'xtrhist_type' => $importData[13],  
    //           ]);

    //     }  
        
        

    //   return view('/home'); 
    
  // }

  public function store(Request $request){
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
                      <supp_trhist xmlns="'.$wsa->wsas_path.'">
                      <inpdomain>'.$domain.'</inpdomain>
                      </supp_trhist>
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

      $dataloop    = $xmlResp->xpath('//ns1:tempRow');
      $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

      if($qdocResult == 'true'){

        foreach($dataloop as $dataloop){     
                    
              DB::table('xtrhist')->updateOrInsert(
                  ['xtrhist_domain' => $dataloop->t_domain, 'xtrhist_part' => $dataloop->t_part],
                  ['xtrhist_desc' => $dataloop->t_desc, 
                    'xtrhist_um' => $dataloop->t_um,
                    'xtrhist_pm' => $dataloop->t_pm_code,
                    'xtrhist_qty_oh' => $dataloop->t_qty,
                    'xtrhist_last_date' =>$dataloop->t_lvt_date,
                    'xtrhist_days' =>$dataloop->t_hit,
                    'xtrhist_amt' => $dataloop->t_amt,  
                    'xtrhist_ket1' => $dataloop->t_ket1,  
                    'xtrhist_ket2' => $dataloop->t_ket2,  
                    'xtrhist_ket3' => $dataloop->t_ket3,  
                    'xtrhist_ket4' => $dataloop->t_ket4,  
                    'xtrhist_type' => $dataloop->t_tr_type,  
                ]);

        }

        alert()->success('Data Loaded');
        return view('/home'); 

      }else{
        alert()->error('Error','Wsa Return False');
        return back();
      }
  }
}
