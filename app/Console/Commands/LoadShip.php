<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Log;

class loadShip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:ship';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Ship untuk TR_hist dan TR_Sum';

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
        // .Bat OLD
        /*
            $file = fopen(public_path('ship.csv'),"r");

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

            $validate = DB::table('transaksi_hist')
                            ->whereRaw('CAST(created_at as Date) = "'.Carbon::now()->toDateString().'"')
                            ->count();
            
            if($validate == 0){
                // belum pernah load hari itu.

                // Save Data to DB
                foreach($importData_arr as $importData){
                    // Save qty ship ke contract mstr
                    $data = DB::table('contract_mstrs')
                                ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                                ->where('cust_code','=',$importData[1])
                                //->where('item_code','=',$importData[3])
                                ->where('brand_code','=',$importData[7])
                                ->where(function($query) use ($importData){
                                    $query->where('start_date',"<=",$importData[6]);
                                    $query->where('end_date','>=',$importData[6]);
                                })
                                ->first();
                    //dd($data);
                    if(!is_null($data)){
                        // ada data kehubung kontrak
                        DB::table('contract_mstrs')
                            ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                            ->where('cust_code','=',$importData[1])
                            //->where('item_code','=',$importData[3])
                            ->where('brand_code','=',$importData[7])
                            ->where(function($query) use ($importData){
                                $query->where('start_date',"<=",$importData[6]);
                                $query->where('end_date','>=',$importData[6]);
                            })
                            ->update([
                                    'qty_ship' => $data->qty_ship + $importData[5],
                            ]);

                        DB::table('transaksi_hist')
                                ->insert([
                                    'tr_nbr' => $importData[0],
                                    'item_code' => $importData[3],
                                    'cust_code' => $importData[1],
                                    'date_trans' => $importData[6],
                                    'qty' => $importData[5], // Decimal jadiin integer
                                    'total' => $importData[4] * $importData[5], // Decimal jadiin integer
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                    'remark' => $data->contract_id,
                                    'brand_code' => $importData[7]
                                ]);
                        
                        $datatr = DB::table('transaksi_sum')
                                    ->where('item_code','=',$importData[3])
                                    ->where('cust_code','=',$importData[1])
                                    ->whereRaw('year(date_trans) = year("'.$importData[6].'") and month(date_trans) = month("'.$importData[6].'")')
                                    ->first();

                        if(is_null($datatr)){
                            // tidak ada data, insert baru
                            DB::table('transaksi_sum')
                                    //->where('item_code','=',$importData[3])
                                    //->where('cust_code','=',$importData[1])
                                    // ->whereRaw('year(date_trans) = year('.$importData[6].') and month(date_trans) = month('.$importData[6].')')
                                    ->insert([
                                        'item_code' => $importData[3],
                                        'cust_code' => $importData[1],
                                        'brand_code' => $importData[7],
                                        'date_trans' => $importData[6],
                                        'qty' => $importData[5], // Decimal jadiin integer
                                        'total' => $importData[4] * $importData[5], // Decimal jadiin integer
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);
                        }else{
                            // ada data update

                            DB::table('transaksi_sum')
                                    ->where('item_code','=',$importData[3])
                                    ->where('cust_code','=',$importData[1])
                                    ->whereRaw('year(date_trans) = year("'.$importData[6].'") and month(date_trans) = month("'.$importData[6].'")')
                                    ->update([
                                        'qty' => $datatr->qty + $importData[5], // Decimal jadiin integer
                                        'total' => $datatr->total + $importData[4] * $importData[5], // Decimal jadiin integer
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
                            '<SGI_load_ship xmlns="urn:iris.co.id:wsatest">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '</SGI_load_ship>'.
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
            $validate = DB::table('transaksi_hist')
                        ->whereRaw('CAST(created_at as Date) = "'.Carbon::now()->toDateString().'"')
                        ->count();

            if($validate == 0){
                // belum pernah load hari itu.
    
                // Save Data to DB
                foreach($dataloop as $data){
                    // Save qty ship ke contract mstr
                    $contract = DB::table('contract_mstrs')
                                ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                                ->where('cust_code','=',$data->t_socust)
                                //->where('item_code','=',$importData[3])
                                ->where('brand_code','=',$data->t_brand)
                                ->where(function($query) use ($data){
                                    $query->where('start_date',"<=",$data->t_vdate);
                                    $query->where('end_date','>=',$data->t_vdate);
                                })
                                ->first();
                    //dd($contract,$data);
                    if(!is_null($contract)){
                        // ada data kehubung kontrak
                        DB::table('contract_mstrs')
                            ->join('contract_dets','contract_mstrs.id','=','contract_dets.contract_id')
                            ->where('cust_code','=',$data->t_socust)
                            //->where('item_code','=',$importData[3])
                            ->where('brand_code','=',$data->t_brand)
                            ->where(function($query) use ($data){
                                $query->where('start_date',"<=",$data->t_vdate);
                                $query->where('end_date','>=',$data->t_vdate);
                            })
                            ->update([
                                    'qty_ship' => $contract->qty_ship + $data->t_qty,
                            ]);
    
                        DB::table('transaksi_hist')
                                ->insert([
                                    'tr_nbr' => $data->t_trnbr,
                                    'item_code' => $data->t_trpart,
                                    'cust_code' => $data->t_socust,
                                    'date_trans' => $data->t_vdate,
                                    'qty' => $data->t_qty, // Decimal jadiin integer
                                    'total' => $data->t_trprice * $data->t_qty, // Decimal jadiin integer
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                    'remark' => $contract->contract_id,
                                    'brand_code' => $data->t_brand
                                ]);
                        
                        $datatr = DB::table('transaksi_sum')
                                    ->where('item_code','=',$data->t_trpart)
                                    ->where('cust_code','=',$data->t_socust)
                                    ->whereRaw('year(date_trans) = year("'.$data->t_vdate.'") and month(date_trans) = month("'.$data->t_vdate.'")')
                                    ->first();
    
                        if(is_null($datatr)){
                            // tidak ada data, insert baru
                            DB::table('transaksi_sum')
                                    ->insert([
                                        'item_code' => $data->t_trpart,
                                        'cust_code' => $data->t_socust,
                                        'brand_code' => $data->t_brand,
                                        'date_trans' => $data->t_vdate,
                                        'qty' => $data->t_qty, // Decimal jadiin integer
                                        'total' => $data->t_qty * $data->t_trprice, // Decimal jadiin integer
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);
                        }else{
                            // ada data update
    
                            DB::table('transaksi_sum')
                                    ->where('item_code','=',$data->t_trpart)
                                    ->where('cust_code','=',$data->t_socust)
                                    ->whereRaw('year(date_trans) = year("'.$data->t_vdate.'") and month(date_trans) = month("'.$data->t_vdate.'")')
                                    ->update([
                                        'qty' => $datatr->qty + $data->t_qty, // Decimal jadiin integer
                                        'total' => $datatr->total + $data->t_qty * $data->t_trprice, // Decimal jadiin integer
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);
                        }
                    }
    
                }

            }else{
                Log::channel('shippay')->info('Data Shipment '.Carbon::now()->toDateString().' sudah diload');
                session()->flash("error", "Error Shipment Hari ini sudah diload");
                return back();
            }
            
        }else{
            Log::channel('shippay')->info('Error WSA returns false Tanggal : '.Carbon::now()->toDateString());
            session()->flash("error", "WSA Shipment return False");
            return back();
        }


        

    }
}
