<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use PDF;
use GuzzleHttp\Client;

class SendWaJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $nowa, $isiwa;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nowa,$isiwa)
    {
        //
        $this->nowa = $nowa;
        $this->isiwa= $isiwa;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $res = $client->request('POST', 'https://api.wassenger.com/v1/messages', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Token' => '9463c20954b5af7b5d5f9d5fae20117b18200ac9e4d42fd36e43fe265735dd27e672c470c5b90199',
            ],
            'json' => [
                'phone' => $this->nowa,
                'message' => $this->isiwa
            ]
        ]);

        $object = json_decode($res->getBody());

        if($res->getStatusCode() == '201' || $res->getStatusCode() == '200'){
            // Berhasil Kebuat WA
            // log::channel('errorpo')->info('WA Berhasil Terbuat untuk Nomor : '.$this->nowa);
        }else{
            // Gagal kebuat WA
            log::channel('errorpo')->info('WA Gagal Terbuat untuk Nomor : '.$this->nowa);
        }

    }
}
