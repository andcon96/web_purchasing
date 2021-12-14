<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CheckBudgetService{

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    public function loadWSA(){
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
                            <supp_budget_check xmlns="'.$wsa->wsas_path.'">
                            <inpdomain>'.$domain.'</inpdomain>
                            </supp_budget_check>
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

            // dd($dataloop1);
            if($qdocResult == 'true'){
                
                $total_detail = 0;
                
                $used_budget = 0;
                $tot_budget  = 0;
                $acc         = "";
                $subacc      = "";
                $cc          = "halo";

                Schema::create('budgets', function($table)
                {
                    $table->string('gl');
                    $table->string('subacc');
                    $table->string('cc');
                    $table->decimal('used_budget', 15, 2);
                    $table->decimal('total_budget', 15, 2);
                    $table->timestamps();
                    $table->temporary();
                });
                

                // PO & PR Hari ini
                foreach($dataloop1 as $data){
                    // dump($acc);
                    if((string)$acc != (string)$data->ts_rcpsub || 
                        (string)$subacc != (string)$data->ts_subacc || 
                        (string)$cc != (string)$data->ts_ccacc){

                        $tot_budget  = $data->ts_budget;
                        $used_budget = $data->ts_usedbudget;
                        $acc         = $data->ts_rcpsub ;
                        $subacc      = $data->ts_subacc;
                        $cc          = $data->ts_ccacc;

                        $total_detail = 0;
                        $used_budget = $data->ts_usedbudget;

                        $budget_rfp = 0; // Total RFP

                        // RFP New Request
                        $rfp = DB::table('xrfp_mstrs')
                                    ->join('xrfp_dets','xrfp_mstrs.xrfp_nbr','xrfp_dets.rfp_nbr')
                                    ->join('xitemreq_mstr','xitemreq_part','=','xrfp_dets.itemcode')
                                    ->where('status','=','New Request')
                                    ->where('xitemreq_mstr.acc','=',$data->ts_rcpsub)
                                    ->where('xitemreq_mstr.subacc','=',$data->ts_subacc)
                                    ->where('xitemreq_mstr.costcenter','=',$data->ts_ccacc)
                                    ->get();

                        foreach($rfp as $rfp){
                            $tmp = $rfp->qty_order * $rfp->price; // Pake Harga di RFP ato Item Master ??

                            $budget_rfp = $budget_rfp + $tmp;
                        }

                        $used_budget = $used_budget + $budget_rfp;

                        // dump($budget_rfp, $used_budget, $data->ts_usedbudget);

                    }

                    // $total_detail = Value dari PO
                    // $used_budget = Value dari PO + Used Budget H-1

                    if($data->ts_orddate == Carbon::now()->toDateString()){
                        $total_detail = $total_detail + $data->ts_totdet;
                        $used_budget = $used_budget + $data->ts_totdet;
                    }else{
                        // SO kemarin di receipt
                        // dump('4', $data->ts_totdet, $data->ts_totfull);
                        $total_detail = $total_detail + $data->ts_totdet - $data->ts_totfull;
                        $used_budget = $used_budget + $data->ts_totdet - $data->ts_totfull;
                    }


                    DB::table('budgets')
                            ->updateOrInsert(
                                [
                                    'gl' => $data->ts_rcpsub,
                                    'subacc' => $data->ts_subacc,
                                    'cc' => $data->ts_ccacc,
                                ],
                                [
                                    'used_budget' => $used_budget,
                                    'total_budget' => $data->ts_budget,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ]
                            
                            );
                }

                $data = DB::table('budgets')
                            ->get();

                Schema::drop('budgets');
                

                return $data;
            }else{
                return false;
            }

    }


    public function listPO(){
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
                            <supp_budget_check xmlns="'.$wsa->wsas_path.'">
                            <inpdomain>'.$domain.'</inpdomain>
                            </supp_budget_check>
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
                $total_detail = 0;
                
                $used_budget = 0;
                $tot_budget  = 0;
                $acc         = "";
                $subacc      = "";
                $cc          = "halo";
                $value       = "";

                Schema::create('budgets', function($table)
                {
                    $table->string('ponbr');
                    $table->string('gl');
                    $table->string('subacc');
                    $table->string('cc');
                    $table->decimal('used_budget', 15, 2);
                    $table->decimal('total_budget', 15, 2);
                    $table->string('app_budget');
                    $table->timestamps();
                    $table->temporary();
                });

                foreach($dataloop1 as $dataloop){
                    if((string)$acc != (string)$dataloop->ts_rcpsub || 
                        (string)$subacc != (string)$dataloop->ts_subacc || 
                        (string)$cc != (string)$dataloop->ts_ccacc){

                        $tot_budget  = $dataloop->ts_budget;
                        $used_budget = $dataloop->ts_usedbudget;
                        $acc         = $dataloop->ts_rcpsub ;
                        $subacc      = $dataloop->ts_subacc;
                        $cc          = $dataloop->ts_ccacc;

                        $total_detail = 0;
                        $used_budget = $dataloop->ts_usedbudget;

                        $budget_rfp = 0; // Total RFP

                        // RFP New Request
                        $rfp = DB::table('xrfp_mstrs')
                                    ->join('xrfp_dets','xrfp_mstrs.xrfp_nbr','xrfp_dets.rfp_nbr')
                                    ->join('xitemreq_mstr','xitemreq_part','=','xrfp_dets.itemcode')
                                    ->where('status','=','New Request')
                                    ->where('xitemreq_mstr.acc','=',$dataloop->ts_rcpsub)
                                    ->where('xitemreq_mstr.subacc','=',$dataloop->ts_subacc)
                                    ->where('xitemreq_mstr.costcenter','=',$dataloop->ts_ccacc)
                                    ->get();

                        foreach($rfp as $rfp){
                            $tmp = $rfp->qty_order * $rfp->price; // Pake Harga di RFP ato Item Master ??

                            $budget_rfp = $budget_rfp + $tmp;
                        }

                        $used_budget = $used_budget + $budget_rfp;

                        // dump($budget_rfp, $used_budget, $dataloop->ts_usedbudget);

                    }

                    if($dataloop->ts_orddate == Carbon::now()->toDateString()){
                        $total_detail = $total_detail + $dataloop->ts_totdet;
                        $used_budget = $used_budget + $dataloop->ts_totdet;
                    }else{
                        // SO kemarin di receipt
                        // dump('4', $used_budget);
                        $total_detail = $total_detail + $dataloop->ts_totdet - $dataloop->ts_totfull;
                        $used_budget = $used_budget + $dataloop->ts_totdet - $dataloop->ts_totfull;
                    }

                    if($dataloop->ts_budget < $used_budget){
                        $value = "Y"; // Melebihi Budget Masukin Approval Purch
                    }else{
                        $value = "N"; // Tidak butuh Approval Purch
                    }

                    DB::table('budgets')
                            ->insert([
                                'ponbr' => $dataloop->ts_nbr,
                                'gl' => $dataloop->ts_rcpsub,
                                'subacc' => $dataloop->ts_subacc,
                                'cc' => $dataloop->ts_ccacc,
                                'used_budget' => $used_budget,
                                'total_budget' => $dataloop->ts_budget,
                                'app_budget' => $value,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]);

                }


                $dataapp = DB::table('budgets')
                                ->where('app_budget','=','Y')
                                // ->groupBy('ponbr')
                                ->get();

                Schema::drop('budgets');

                return $dataapp;
            }else{
                return false;
            }


            
    }

}