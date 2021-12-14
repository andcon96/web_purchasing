<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CheckBudgetRFP{

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    public function loadRFP(){
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
                <supp_budget_check_rfp xmlns="'.$wsa->wsas_path.'">
                    <inpdomain>'.$domain.'</inpdomain>
                </supp_budget_check_rfp>
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

            // dd($qdocRequest);
            // dd($dataloop1);
            if($qdocResult == 'true'){
                
                $total_detail = 0;
                
                $used_budget = 0;
                $tot_budget  = 0;
                $acc         = "";
                $subacc      = "";
                $cc          = "halo";

                Schema::create('rfp_budgets', function($table)
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
                    if((string)$acc != (string)$data->t_rcpsub || 
                        (string)$subacc != (string)$data->t_subacc || 
                        (string)$cc != (string)$data->t_ccacc){

                        $tot_budget  = $data->t_budget;
                        $used_budget = $data->t_usedbudget;
                        $acc         = $data->t_rcpsub ;
                        $subacc      = $data->t_subacc;
                        $cc          = $data->t_ccacc;

                        $total_detail = 0;
                        $used_budget = $data->t_usedbudget;

                        // dump($budget_rfp, $used_budget, $data->ts_usedbudget);

                    }

                    // dump( $total_detail, $used_budget);

                    DB::table('rfp_budgets')
                            ->updateOrInsert(
                                [
                                    'gl' => $data->t_rcpsub,
                                    'subacc' => $data->t_subacc,
                                    'cc' => $data->ts_cct_ccaccacc,
                                ],
                                [
                                    'used_budget' => $used_budget,
                                    'total_budget' => $data->t_budget,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ]
                            
                            );
                }

                $data = DB::table('rfp_budgets')
                            ->get();

                Schema::drop('rfp_budgets');
                

                return $data;
            }else{
                return false;
            }

    }

}