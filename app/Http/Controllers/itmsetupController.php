<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class itmsetupController extends Controller
{

  private function httpHeader($req)
  {
    return array(
      'Content-type: text/xml;charset="utf-8"',
      'Accept: text/xml',
      'Cache-Control: no-cache',
      'Pragma: no-cache',
      'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
      'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req))
    );
  }
  public function menu()
  {
    return view('setting/itemsetmenu');
  }

  public function index()
  {
    $itm = DB::table('xitm_ctrl')->get();
    return view('setting/itmsetup', compact('itm'));
  }

  public function itmmstr(Request $req)
  {

    
    $itm = DB::table('xitem_mstr')->paginate(10);

    if($req->ajax()){
      $part   = $req->input('part');
      $prod   = $req->input('prod');
      $type   = $req->input('type');

      if($part == "" && $prod == "" && $type == ""){
        

      }else{

        $query = "xitem_mstr.xitem_part like '%" . $part . "%'";
  
        if ($prod != null) {
          $query .= " AND xitem_mstr.xitem_prod_line like '%" . $prod . "%'";
        }
        if ($type != null) {
          $query .= " AND xitem_mstr.xitem_type like '%" . $type . "%'";
        }
    
        $itm = DB::table("xitem_mstr")
          ->whereRaw($query)
          ->paginate(10);

      }
  
      return view('setting/table-itmmstr', compact('itm'));
    }else{
      return view('setting/itmmstr', compact('itm'));
    }

    
  }

  public function itmrfqmstr(Request $req)
  {


    $itm = DB::table('xitemreq_mstr')->paginate(10);

    if ($req->ajax()) {

      $part   = $req->input('part');
      $prod   = $req->input('prod');
      $type   = $req->input('type');

      if ($part == "" && $prod == "" && $type == "") {
        // dd('masuk');
      } else {

        $query = "xitemreq_mstr.xitemreq_part like '%" . $part . "%'";

        if ($prod != null) {
          $query .= " AND xitemreq_mstr.xitemreq_prod_line like '%" . $prod . "%'";
        }
        if ($type != null) {
          $query .= " AND xitemreq_mstr.xitemreq_type like '%" . $type . "%'";
        }

        $itm = DB::table("xitemreq_mstr")
          ->whereRaw($query)
          ->paginate(10);
      }

      return view('setting/table-itmrfqmstr', compact('itm'));
    } else {

      return view('setting/itmrfqmstr', compact('itm'));
    }
  }

  public function itmmstrcari(Request $req)
  {
    $part   = $req->input('part');
    $prod   = $req->input('prod');
    $type   = $req->input('type');

    $query = "xitem_mstr.xitem_part like '%" . $part . "%'";

    if ($prod != null) {
      $query .= " AND xitem_mstr.xitem_prod_line like '%" . $prod . "%'";
    }
    if ($type != null) {
      $query .= " AND xitem_mstr.xitem_type like '%" . $type . "%'";
    }

    $itm = DB::table("xitem_mstr")
      ->whereRaw($query)
      ->paginate(10);

    return view('setting/itmmstr', compact('itm'));
  }

  // public function itmrfqmstrcari(Request $req)
  // {

  //     $part   = $req->input('part');	
  //     $prod   = $req->input('prod');
  //     $type   = $req->input('type');

  //     $query = "xitemreq_mstr.xitemreq_part like '%".$part."%'";

  //     if($prod != null){
  //           $query .= " AND xitemreq_mstr.xitemreq_prod_line like '%".$prod."%'";
  //     }
  //     if($type != null){
  //           $query .= " AND xitemreq_mstr.xitemreq_type like '%".$type."%'";
  //     }

  //     $itm=DB::table("xitemreq_mstr")                           
  //              ->whereRaw($query)
  //              ->paginate(10);   

  //     return view('setting/itmrfqmstr',compact('itm'));
  // }

  public function itmmstrupd(Request $req)
  {
    $sfty       = $req->input('sfty');
    $sftyemail1 = $req->input('sftyemail1');
    $part    = $req->input('part');
    $days1   = $req->input('alertdays1');
    $days2   = $req->input('alertdays2');
    $days3   = $req->input('alertdays3');
    $email1  = $req->input('alertemail1');
    $email2  = $req->input('alertemail2');
    $email3  = $req->input('alertemail3');

    if (is_null($email1)) {
      $email1 = "";
    }
    if (is_null($email2)) {
      $email2 = "";
    }
    if (is_null($email3)) {
      $email3 = "";
    }
    if (is_null($sftyemail1)) {
      $sftyemail1 = "";
    }


    $data1 = array(
      'xitem_part' => $part,
      'xitem_sfty' => $sfty,
      'xitem_sfty_email' => $sftyemail1,
      'xitem_day1' => $days1,
      'xitem_day_email1' => $email1,
      'xitem_day2' => $days2,
      'xitem_day_email2' => $email2,
      'xitem_day3' => $days3,
      'xitem_day_email3' => $email3,
    );

    DB::table('xitem_mstr')
      ->where('xitem_part', $part)
      ->update($data1);

    $itm = DB::table('xitem_mstr')->paginate(10);
    return view('setting/itmmstr', compact('itm'));
  }

  public function itmmstredt(Request $req)
  {
    $part = $req->input('part');
    $itm = DB::table('xitem_mstr')->where('xitem_part', $part)->get();
    return view('setting/itmmstredt', compact('itm'));
  }

  public function rfqindex()
  {
    $itm = DB::table('xitmreq_ctrl')->get();
    return view('setting/itmreq', compact('itm'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $req)
  {
    return view('setting/itmcrt');
  }

  public function reqcreate(Request $req)
  {
    return view('setting/itmreqcrt');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function save(Request $req)
  {
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');


    if (is_null($promo)) {
      $promo = "";
    }
    if (is_null($type)) {
      $type = "";
    }
    if (is_null($grp)) {
      $grp = "";
    }
    if (is_null($line)) {
      $line = "";
    }
    if (is_null($dsgn)) {
      $dsgn = "";
    }


    $data1 = array(
      'xitm_part' => $part,
      'xitm_type' => $type,
      'xitm_design' => $dsgn,
      'xitm_promo' => $promo,
      'xitm_group' => $grp,
      'xitm_prod_line' => $line,
    );

    DB::table('xitm_ctrl')->insert($data1);

    $itm = DB::table('xitm_ctrl')->get();
    //return view('setting/itmsetup',compact('itm'));
    return redirect()->route('itmsetup');
  }

  public function reqsave(Request $req)
  {
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');


    if (is_null($promo)) {
      $promo = "";
    }
    if (is_null($part)) {
      $part = "";
    }
    if (is_null($type)) {
      $type = "";
    }
    if (is_null($grp)) {
      $grp = "";
    }
    if (is_null($line)) {
      $line = "";
    }
    if (is_null($dsgn)) {
      $dsgn = "";
    }

    $data1 = array(
      'xitmreq_part' => $part,
      'xitmreq_type' => $type,
      'xitmreq_design' => $dsgn,
      'xitmreq_promo' => $promo,
      'xitmreq_group' => $grp,
      'xitmreq_prod_line' => $line,
    );

    DB::table('xitmreq_ctrl')->insert($data1);

    $itm = DB::table('xitmreq_ctrl')->get();
    //return view('setting/itmreq',compact('itm'));
    // session()->flash('updated','Data Successfully Loaded');
    alert()->success('Success', 'Data Succesfully Loaded');
    return redirect()->route('itemrfqset');
  }

  public function upd(Request $req)
  {
    $id      = $req->input('id');
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');

    if (is_null($promo)) {
      $promo = "";
    }
    if (is_null($part)) {
      $part = "";
    }
    if (is_null($type)) {
      $type = "";
    }
    if (is_null($grp)) {
      $grp = "";
    }
    if (is_null($line)) {
      $line = "";
    }
    if (is_null($dsgn)) {
      $dsgn = "";
    }

    $data1 = array(
      'xitm_part' => $part,
      'xitm_type' => $type,
      'xitm_design' => $dsgn,
      'xitm_promo' => $promo,
      'xitm_group' => $grp,
      'xitm_prod_line' => $line,
    );

    DB::table('xitm_ctrl')
      ->where('xitm_id', $id)
      ->update($data1);

    $itm = DB::table('xitm_ctrl')->get();
    // return view('setting/itmsetup',compact('itm'));

    return redirect()->route('itmsetup');
  }

  public function rfqupd(Request $req)
  {
    $id      = $req->input('id');
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');

    if (is_null($promo)) {
      $promo = "";
    }
    if (is_null($part)) {
      $part = "";
    }
    if (is_null($type)) {
      $type = "";
    }
    if (is_null($grp)) {
      $grp = "";
    }
    if (is_null($line)) {
      $line = "";
    }
    if (is_null($dsgn)) {
      $dsgn = "";
    }

    $data1 = array(
      'xitmreq_part' => $part,
      'xitmreq_type' => $type,
      'xitmreq_design' => $dsgn,
      'xitmreq_promo' => $promo,
      'xitmreq_group' => $grp,
      'xitmreq_prod_line' => $line,
    );

    DB::table('xitmreq_ctrl')
      ->where('xitmreq_id', $id)
      ->update($data1);

    $itm = DB::table('xitmreq_ctrl')->get();
    //return view('setting/itmreq',compact('itm'));
    return redirect()->route('itemrfqset');
  }
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $req)
  {
    $id      = $req->input('id');
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');



    $itm = DB::table('xitm_ctrl')
      ->where('xitm_id', $id)
      ->get();

    return view('setting/itmedt', compact('itm'));
  }

  public function rfqedit(Request $req)
  {
    $id      = $req->input('id');
    $part    = $req->input('part');
    $type    = $req->input('type');
    $dsgn    = $req->input('dsgn');
    $promo   = $req->input('promo');
    $grp     = $req->input('grp');
    $line    = $req->input('line');



    $itm = DB::table('xitmreq_ctrl')
      ->where('xitmreq_id', $id)
      ->get();

    return view('setting/itmreqedt', compact('itm'));
  }

  public function hapus(Request $req)
  {

    $id = $req->input('delete_id');

    DB::table('xitm_ctrl')->where('xitm_id', $id)->delete();
    return redirect()->back();
  }

  public function reqhapus(Request $req)
  {

    $id = $req->input('delete_id');

    DB::table('xitmreq_ctrl')->where('xitmreq_id', $id)->delete();

    alert()->success('Success', 'Data Succesfully Deleted');
    return redirect()->back();
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  // public function loaditm1(){
  //     DB::table('xitemreq_mstr')->delete();
  //     exec("start cmd /c itmmstr.bat");	

  //     $file = fopen(public_path('itm.csv'),"r");

  //     $importData_arr = array();
  //       $i = 0;

  //       while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
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
  //       }
  //       fclose($file);

  //      $itm = DB::table('xitmreq_ctrl')->first();          
  //      if(is_null($itm))
  //      {
  //          foreach($importData_arr as $importData){   
  //                     DB::table('xitemreq_mstr')->updateOrInsert(
  //                            ['xitemreq_domain' => $importData[0], 'xitemreq_part' => $importData[1] ],
  //                            ['xitemreq_desc' => $importData[2], 
  //                             'xitemreq_um' => $importData[3],
  //                             'xitemreq_prod_line' => $importData[4],
  //                             'xitemreq_type' => $importData[5],
  //                             'xitemreq_group' => $importData[6],
  //                             'xitemreq_pm' => $importData[7],
  //                             'xitemreq_sfty_stk' => $importData[8],     
  //                             'xitemreq_promo' => $importData[9], 
  //                             'xitemreq_dsgn' => $importData[10], 
  //                         ]);
  //          }
  //      }
  //      else
  //      {

  //                        // Insert to MySQL database
  //              foreach($importData_arr as $importData){    
  //                  $checkitm = DB::table('xitmreq_ctrl')
  //                         ->where('xitmreq_ctrl.xitmreq_part','=',$importData[1])
  //                         ->first();
  //                  if ($checkitm) {
  //                      DB::table('xitemreq_mstr')->updateOrInsert(
  //                                  ['xitemreq_domain' => $importData[0], 'xitemreq_part' => $importData[1] ],
  //                                  ['xitemreq_desc' => $importData[2], 
  //                                   'xitemreq_um' => $importData[3],
  //                                   'xitemreq_prod_line' => $importData[4],
  //                                   'xitemreq_type' => $importData[5],
  //                                   'xitemreq_group' => $importData[6],
  //                                   'xitemreq_pm' => $importData[7],
  //                                   'xitemreq_sfty_stk' => $importData[8],     
  //                                   'xitemreq_promo' => $importData[9], 
  //                                   'xitemreq_dsgn' => $importData[10], 
  //                               ]);                          
  //                  }
  //                  else
  //                  {   
  //                        $checkdb = DB::table('xitmreq_ctrl')
  //                               ->where('xitmreq_ctrl.xitmreq_prod_line','=',$importData[4])
  //                               ->where('xitmreq_ctrl.xitmreq_type','=',$importData[5])
  //                               ->where('xitmreq_ctrl.xitmreq_group','=',$importData[6])
  //                               ->where('xitmreq_ctrl.xitmreq_design','=',$importData[10])
  //                               ->where('xitmreq_ctrl.xitmreq_promo','=',$importData[9])
  //                               ->first();
  //                        if ($checkdb) {
  //                              DB::table('xitemreq_mstr')->updateOrInsert(
  //                                  ['xitemreq_domain' => $importData[0], 'xitemreq_part' => $importData[1] ],
  //                                  ['xitemreq_desc' => $importData[2], 
  //                                   'xitemreq_um' => $importData[3],
  //                                   'xitemreq_prod_line' => $importData[4],
  //                                   'xitemreq_type' => $importData[5],
  //                                   'xitemreq_group' => $importData[6],
  //                                   'xitemreq_pm' => $importData[7],
  //                                   'xitemreq_sfty_stk' => $importData[8],     
  //                                   'xitemreq_promo' => $importData[9], 
  //                                   'xitemreq_dsgn' => $importData[10], 
  //                               ]);
  //                        }
  //                  }
  //               }
  //      } 

  //     return back();

  // }

  public function loaditm()
  {
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

    $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">' .
      '<Body>' .
      '<supp_item_mstr xmlns="' . $wsa->wsas_path . '">' .
      '<inpdomain>' . $domain . '</inpdomain>' .
      '</supp_item_mstr>' .
      '</Body>' .
      '</Envelope>';

    $curlOptions = array(
      CURLOPT_URL => $qxUrl,
      CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
      CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
      CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
      CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false
    );

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

      foreach (curl_getinfo($curl) as $key => $value) {
        if (gettype($value) != 'array') {
          if (!$first) $getInfo .= ", ";
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

    if ($qdocResult == true) {
      $itm = DB::table('xitmreq_ctrl')
        ->whereRaw('xitmreq_part = "" and xitmreq_type = "" 
                                  and xitmreq_promo = "" 
                                  and xitmreq_group = "" 
                                  and xitmreq_design = ""
                                  and xitmreq_prod_line =""')
        ->first();
      // dd($itm,$dataloop);

      if (!is_null($itm)) {
        foreach ($dataloop as $data) {
          DB::table('xitemreq_mstr')
            ->updateOrInsert(
              [
                'xitemreq_domain' => $data->t_domain,
                'xitemreq_part' => $data->t_part
              ],
              [
                'xitemreq_desc' => $data->t_desc,
                'xitemreq_um' => $data->t_um,
                'xitemreq_prod_line' => $data->t_prod_line,
                'xitemreq_type' => $data->t_part_type,
                'xitemreq_group' => $data->t_group,
                'xitemreq_pm' => $data->t_pm_code,
                'xitemreq_sfty_stk' => $data->t_sfty_stk,
                'xitemreq_promo' => $data->t_promo,
                'xitemreq_dsgn' => $data->t_dsgn_grp,
                'xitemreq_price' => $data->t_price,
                'acc' => $data->t_acc,
                'subacc' => $data->t_subacc,
                'costcenter' => $data->t_cc,
              ]
            );
        }
      } else {
        $i = 0;
        foreach ($dataloop as $data) {
          $checkitm = DB::table('xitmreq_ctrl')
            ->where('xitmreq_ctrl.xitmreq_part', '=', $data->t_part)
            ->first();
          if ($checkitm) {
            DB::table('xitemreq_mstr')
              ->updateOrInsert(
                [
                  'xitemreq_domain' => $data->t_domain,
                  'xitemreq_part' => $data->t_part
                ],
                [
                  'xitemreq_desc' => $data->t_desc,
                  'xitemreq_um' => $data->t_um,
                  'xitemreq_prod_line' => $data->t_prod_line,
                  'xitemreq_type' => $data->t_part_type,
                  'xitemreq_group' => $data->t_group,
                  'xitemreq_pm' => $data->t_pm_code,
                  'xitemreq_sfty_stk' => $data->t_sfty_stk,
                  'xitemreq_promo' => $data->t_promo,
                  'xitemreq_dsgn' => $data->t_dsgn_grp,
                  'xitemreq_price' => $data->t_price,
                  'acc' => $data->t_acc,
                  'subacc' => $data->t_subacc,
                  'costcenter' => $data->t_cc,
                ]
              );
          } else {
            $checkdb = DB::table('xitmreq_ctrl')
              ->where('xitmreq_ctrl.xitmreq_prod_line', '=', $data->t_prod_line)
              ->where('xitmreq_ctrl.xitmreq_type', '=', $data->t_part_type)
              ->where('xitmreq_ctrl.xitmreq_group', '=', $data->t_group)
              ->where('xitmreq_ctrl.xitmreq_design', '=', $data->t_dsgn_grp)
              ->where('xitmreq_ctrl.xitmreq_promo', '=', $data->t_promo)
              ->first();

            if ($checkdb) {
              DB::table('xitemreq_mstr')
                ->updateOrInsert(
                  [
                    'xitemreq_domain' => $data->t_domain,
                    'xitemreq_part' => $data->t_part
                  ],
                  [
                    'xitemreq_desc' => $data->t_desc,
                    'xitemreq_um' => $data->t_um,
                    'xitemreq_prod_line' => $data->t_prod_line,
                    'xitemreq_type' => $data->t_part_type,
                    'xitemreq_group' => $data->t_group,
                    'xitemreq_pm' => $data->t_pm_code,
                    'xitemreq_sfty_stk' => $data->t_sfty_stk,
                    'xitemreq_promo' => $data->t_promo,
                    'xitemreq_dsgn' => $data->t_dsgn_grp,
                    'xitemreq_price' => $data->t_price,
                    'acc' => $data->t_acc,
                    'subacc' => $data->t_subacc,
                    'costcenter' => $data->t_cc,
                  ]
                );
            }
          }
          $i += 1;
        }
      }

      alert()->success('Success', 'Load Data Successfull');
      return back();
    } else {
      alert()->error('Error', 'Load Data Failed, WSA Error');
      return back();
    }
  }
}
