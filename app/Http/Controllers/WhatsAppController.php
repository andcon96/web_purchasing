<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Jobs\SendWaJobs;

class WhatsAppController extends Controller
{
    //
    public function sendwa(Request $req){
        // dd($req->all());

        $sendmail = (new SendWaJobs($req->nowa,$req->isiwa))->delay(Carbon::now()->addSeconds(3));
        dispatch($sendmail);

        alert()->success('Success','WA Message Succesfully Sent');
        return back();
    }
}
