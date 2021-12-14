<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use PDF;
use App\Jobs\SendEmailJob;

class schedContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Progress Contract Monthly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
        $contractall = DB::table('contract_mstrs')
                        ->where('contract_mstrs.status','=','F')
                        ->get();

        foreach($contractall as $data){
        
            $contractprog = DB::table('transaksi_hist')
                                ->where('transaksi_hist.remark','=', $data->id)
                                ->selectRaw('*,sum(qty) as qtytotal, month(date_trans) as "bulan", year(date_trans) as "tahun"')
                                ->groupBy('bulan', 'tahun')
                                ->get();

            $contractcount = $contractprog->count();

            if($contractcount > 0){
                // ada data baru hist baru jalanin create pdf
                $detailcontract = DB::table('transaksi_hist')
                                    ->join('items','transaksi_hist.item_code','=','items.itemcode')
                                    ->join('contract_dets',function($query){
                                        $query->on('transaksi_hist.remark','=','contract_dets.contract_id');
                                        $query->on('transaksi_hist.item_code','=','contract_dets.item_code');
                                    })
                                    ->where('transaksi_hist.remark','=', $data->id)
                                    ->selectRaw('*,sum(qty) as qtytotal, month(date_trans) as "bulan", year(date_trans) as "tahun"')
                                    ->groupBy('transaksi_hist.item_code','bulan','tahun')
                                    ->get();

                $date = DB::table('contract_mstrs')
                                ->join('customers','contract_mstrs.cust_code','=','customers.cust_id')
                                ->join('users','contract_mstrs.pihak_1','=','users.username')
                                ->join('schedulers','contract_mstrs.id','=','schedulers.contract_id')
                                ->where('contract_mstrs.id','=', $data->id)
                                ->selectRaw('*,customers.jabatan as "jabatan_cust",users.jabatan as "jabatan_user", contract_mstrs.id as "contract_id",schedulers.email as "email_cust"')
                                ->first();

                // Masukin data ke pdf dan save ke local                
                $pdf = PDF::loadview('pdf.contract-prog',['pegawai'=>$contractprog,'date'=>$date,'jmlrow'=>$contractcount,'detail'=>$detailcontract])->setPaper('A4','landscape'); 

                $pdf->save(public_path('/pdf/Contract - '.$date->contract_id.' - '.Carbon::now()->toDateString().'.pdf'));

                // Cari dan kirim email ke daftar schedulers
                $email = explode(';', $date->email_cust);
                
                // Kirim Email
                Mail::send('email', 
                    ['pesan' => 'Progress Contract Customer',
                     'note1' => $date->contract_id],
                    function ($message) use ($date,$email)
                    {
                        $message->subject('SGI - Contract Progress');
                        $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                        $message->attach(public_path('/pdf/Contract - '.$date->contract_id.' - '.Carbon::now()->toDateString().'.pdf'));
                        $message->to(array_filter($email));
                    });

            }
        }*/

        $sendmail = (new SendEmailJob())->delay(Carbon::now()->addSeconds(3));
        dispatch($sendmail);

    }
}
