<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

use GuzzleHttp\Client;

class BarcodeController extends Controller
{
    //
    public function index(){

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.wassenger.com/v1/messages",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"phone\":\"+6281289369262\",\"message\":\"Testing Message 3 -- Halo Andrew CT.\"}",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Token: f90e373759c408527f76d5bfce9dd962ada0a10a144eff036d6ff155dd585f8619df43653dd7c46e"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        dd($err);
        } else {
        echo $response;
        dd($response);
        }

        return view('barcode.index-barcode');
    }
}
