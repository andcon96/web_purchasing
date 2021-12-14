<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Log;

class loadPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Payment TR_Hist and TR_Sum';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // HTTP Header WSA
	private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Open CSV File n Read
        /* .Bat OLD
            $file = fopen(public_path('pay.csv'),"r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                $num = count($filedata );

                for ($c=0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
            fclose($file);

            $validate = DB::table('transaksi_sum')
                            ->whereRaw('CAST(updated_at as Date) = "'.Carbon::now()->toDateString().'"')
                            ->count();

            // 1 hari 1x load csv
            if($validate == 0){

                // Save Data to DB
                foreach($importData_arr as $importData){
                    $datahist = DB::table('transaksi_hist')
                                    ->where('tr_nbr','=',$importData[4])
                                    ->where('item_code','=',$importData[5])
                                    ->first();
                    

                    // Save qty pay ke contract mstr
                    $data = DB::table('contract_mstrs')
                                ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                                ->where('cust_code','=',$importData[7])
                                //->where('item_code','=',$importData[5])
                                ->where('brand_code','=',$importData[8])
                                ->where(function($query) use ($importData,$datahist){
                                    $query->where('start_date',"<=",$datahist->date_trans);
                                    $query->where('end_date','>=',$datahist->date_trans);
                                })
                                ->first();

                    // Hanya update data tr_hist & tr_sum klo ada contract yg berlaku buat customer itu.
                    if(!is_null($data)){
                        // ada data
                        DB::table('contract_mstrs')
                            ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                            ->where('cust_code','=',$importData[7])
                            //->where('item_code','=',$importData[5])
                            ->where('brand_code','=',$importData[8])
                            ->where(function($query) use ($importData,$datahist){
                                    $query->where('start_date',"<=",$datahist->date_trans);
                                    $query->where('end_date','>=',$datahist->date_trans);
                            })
                            ->update([
                                    'qty_paid' => $data->qty_paid + $importData[6],
                            ]);

                        if($datahist){
                            //$this->output->write("Ada isi",false); Hanya update yang sudah ada no rf di ship
                        
                            $data = DB::table('transaksi_sum')
                                        ->where('item_code','=',$importData[5])
                                        ->where('cust_code','=',$importData[7])
                                        ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                                        ->first();

                        
                            DB::table('transaksi_hist')
                                    ->where('tr_nbr','=',$importData[4])
                                    ->where('item_code','=',$importData[5])
                                    ->update([
                                        'qty_paid' => $datahist->qty_paid + $importData[6], // Decimal jadiin integer
                                        'total_paid' => ( $datahist->qty_paid + $importData[6] ) / $datahist->qty * $datahist->total,
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);

                                    
                            DB::table('transaksi_sum')
                                ->where('item_code','=',$importData[5])
                                ->where('cust_code','=',$importData[7])
                                ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                                ->update([
                                    'qty_paid' => $data->qty_paid + $importData[6], // Decimal jadiin integer
                                    'total_paid' => ( $data->qty_paid + $importData[6] ) / $data->qty * $data->total,
                                    'updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                    
                        }    
                    }

                }
                
            }
        */

        // WSA
        // Validasi WSA --> Gantiin .bat
        $wsa = DB::table('wsas')
                        ->first();

        if(!$wsa){
            session()->flash('error', 'Please register WSA in web');
            return back();
        }


		$qxUrl          = $wsa->wsas_url;
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
        $timeout        = 0;
        
        $domain         = $wsa->wsas_domain;
        $code           = 'pt_promo'; // Brand Code

		// ** Edit here
		$qdocRequest =      '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                            '<SGI_load_pay xmlns="urn:iris.co.id:wsatest">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '</SGI_load_pay>'.
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

        $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
        //dd($qdocResultx,$qdocResponse,$dataloop);
        if ($qdocResultx == 'true')  {
            $validate = DB::table('transaksi_sum')
                        ->whereRaw('CAST(pay_updated_at as Date) = "'.Carbon::now()->toDateString().'"')
                        ->count();

            if($validate == 0){

                // Save Data to DB
                foreach($dataloop as $data){
                    $datahist = DB::table('transaksi_hist')
                                    ->where('tr_nbr','=',$data->t_idhnbr)
                                    ->where('item_code','=',$data->t_idhpart)
                                    ->first();
                    

                    // Save qty pay ke contract mstr
                    $contract = DB::table('contract_mstrs')
                                ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                                ->where('cust_code','=',$data->t_debtorcode)
                                //->where('item_code','=',$importData[5])
                                ->where('brand_code','=',$data->t_brand)
                                ->where(function($query) use ($data,$datahist){
                                    $query->where('start_date',"<=",$datahist->date_trans);
                                    $query->where('end_date','>=',$datahist->date_trans);
                                })
                                ->first();
                    
                    // Hanya update data tr_hist & tr_sum klo ada contract yg berlaku buat customer itu.
                    if(!is_null($contract)){
                        // ada data
                        DB::table('contract_mstrs')
                            ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                            ->where('cust_code','=',$data->t_debtorcode)
                            //->where('item_code','=',$importData[5])
                            ->where('brand_code','=',$data->t_brand)
                            ->where(function($query) use ($data,$datahist){
                                    $query->where('start_date',"<=",$datahist->date_trans);
                                    $query->where('end_date','>=',$datahist->date_trans);
                            })
                            ->update([
                                    'qty_paid' => $contract->qty_paid + $data->t_idhqtyinv,
                            ]);
                   

                        if($datahist){
                            //$this->output->write("Ada isi",false); Hanya update yang sudah ada no rf di ship
                        
                            $trsum = DB::table('transaksi_sum')
                                        ->where('item_code','=',$data->t_idhpart)
                                        ->where('cust_code','=',$data->t_debtorcode)
                                        ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                                        ->first();

                        
                            DB::table('transaksi_hist')
                                    ->where('tr_nbr','=',$data->t_idhnbr)
                                    ->where('item_code','=',$data->t_idhpart)
                                    ->update([
                                        'qty_paid' => $datahist->qty_paid + $data->t_idhqtyinv, // Decimal jadiin integer
                                        'total_paid' => ( $datahist->qty_paid + $data->t_idhqtyinv ) / $datahist->qty * $datahist->total,
                                        'pay_updated_at' => Carbon::now()->toDateTimeString()
                                    ]);

                                    
                            DB::table('transaksi_sum')
                                ->where('item_code','=',$data->t_idhpart)
                                ->where('cust_code','=',$data->t_debtorcode)
                                ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                                ->update([
                                    'qty_paid' => $trsum->qty_paid + $data->t_idhqtyinv, // Decimal jadiin integer
                                    'total_paid' => ( $trsum->qty_paid + $data->t_idhqtyinv ) / $trsum->qty * $trsum->total,
                                    'pay_updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                    
                        }    
                    }
                }
                
            }else{
                Log::channel('shippay')->info('Data Payment '.Carbon::now()->toDateString().' sudah diload');
                session()->flash("error", "Error Payment Hari ini sudah diload");
                return back();
            }
        }else{
            Log::channel('shippay')->info('Error WSA Payment returns false Tanggal : '.Carbon::now()->toDateString());
            session()->flash("error", "WSA Payment return False");
            return back();
        }

    }
}
