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

class EmailRfqSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $note1, $note2, $note3, $note4,
            $note5, $note6, $note7, $note8, $note9,
            $arrayemail, $com_name, $com_email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($note1,$note2,$note3,$note4,$note5,$note6,$note7,$note8,$note9,$arrayemail,$com_name,$com_email)
    {
        //
        $this->note1 = $note1;
        $this->note2 = $note2;
        $this->note3 = $note3;
        $this->note4 = $note4;
        $this->note5 = $note5;
        $this->note6 = $note6;
        $this->note7 = $note7;
        $this->note8 = $note8;
        $this->note9 = $note9;
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
        $arrayemail = $this->arrayemail;
        $com_name = $this->com_name;
        $com_email = $this->com_email;

        Mail::send('email.emailrfq', 
            ['pesan' => 'There is a new RFQ awaiting your response',
                        'note1' => $this->note1,
                        'note2' => $this->note2,
                        'note3' => $this->note3,
                        'note4' => $this->note4,
                        'note5' => $this->note5,
                        'note6' => $this->note6,
                        'note7' => $this->note7,
                        'note8' => $this->note8,
                        'note9' => $this->note9],
            function ($message) use ($arrayemail,$com_name,$com_email)
        {
            $message->subject('PhD - Request for Quotation - '.$com_name);
            $message->from($com_email); // Email Admin Fix
            $message->to($arrayemail);
        });
    }
}
