<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use PDF;
use App;

class EmailPoApproval implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $note1, $note2, $note3, $note4, $note5,
            $arrayemail, $com_name, $com_email, $pesan;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pesan,$note1,$note2,$note3,$note4,$note5,$arrayemail,$com_name,$com_email)
    {
        //
        $this->pesan = $pesan;
        $this->note1 = $note1;
        $this->note2 = $note2;
        $this->note3 = $note3;
        $this->note4 = $note4;
        $this->note5 = $note5;
        $this->arrayemail = $arrayemail;
        $this->com_name = $com_name;
        $this->com_email = $com_email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $arrayemail = $this->arrayemail;
        $com_name = $this->com_name;
        $com_email = $this->com_email;

        Mail::send('email.emailapproval', 
            ['pesan' => $this->pesan,
                'note1' => $this->note1,
                'note2' => $this->note2,
                'note3' => $this->note3,
                'note4' => $this->note4,
                'note5' => $this->note5], 
            function ($message) use ($arrayemail,$com_name,$com_email)
        {
            $message->subject('PhD - Purchase Order Approval Task - '.$com_name);
            $message->from($com_email); // Email Admin Fix
            $message->to($arrayemail);
        });
    }
}
