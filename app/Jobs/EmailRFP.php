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
use App;

class EmailRFP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $rfpnumber;
    protected $rfp_duedate;
    protected $created_by;
    protected $rfp_dept;
    protected $emailreject;
    protected $company;
    protected $rfp_apr;
    protected $rfp_altapr;
    protected $array_email;
    protected $parameter;

    public function __construct($rfpnumber,$rfp_duedate,$created_by,$rfp_dept,$emailreject,$company,$rfp_apr,$rfp_altapr,$array_email,$parameter)
    {
        //
        $this->rfpnumber = $rfpnumber;
        $this->rfp_duedate = $rfp_duedate;
        $this->created_by = $created_by;
        $this->rfp_dept = $rfp_dept;
        $this->emailreject = $emailreject;
        $this->company = $company;
        $this->rfp_apr = $rfp_apr;
        $this->rfp_altapr = $rfp_altapr;
        $this->array_email = $array_email;
        $this->parameter = $parameter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // dd('dispatch');
        //
        $rfpnbr = $this->rfpnumber;
        $rfpduedate = $this->rfp_duedate;
        $created_by = $this->created_by;
        $rfp_dept = $this->rfp_dept;
        $array_email = $this->array_email;
        $company = $this->company;

        $rfp_apr = $this->rfp_apr;
        $rfp_altapr = $this->rfp_altapr;

        $emailreject = $this->emailreject;

        $paramater = $this->parameter;

        if($paramater == '2'){
            Mail::send('email.emailrfpapproval',
                        [
                            'pesan' => 'There is a RFP awaiting for approval',
                            'note1' => $rfpnbr,
                            'note2' => $rfpduedate,
                            'note3' => $created_by,
                            'note4' => $rfp_dept,
                            'note5' => 'Please Check.'],
                            function($message) use ($rfpnbr, $array_email, $company)
                        {
                            $message->subject('PhD - RFP Approval Task -'.$company->com_name);
                            $message->from($company->com_email);
                            $message->to($array_email);
                        });

                    // ditambahkan 03/11/2020
                    $user = App\User::where('id','=', $rfp_apr)->first(); // user siapa yang terima notif (lewat id)
                    $useralt = App\User::where('id','=', $rfp_altapr)->first(); 

                    $details = [
                        'body' => 'There is a RFP awaiting for approval',
                        'url' => 'rfpapproval',
                        'nbr' => $rfpnbr,
                        'note' => 'Please check'
                    ]; // isi data yang dioper
                                                    
                                                
                    $user->notify(new \App\Notifications\eventNotification($details));
                    $useralt->notify(new \App\Notifications\eventNotification($details));
        }

        if($paramater == '1'){
            // dd('parameter 1');
            Mail::send('email.emailrfpapproval',
                        [   'pesan' => 'Following Request for Purchasing has been rejected :',
                            'note1' => $rfpnbr,
                            'note2' => $rfpduedate,
                            'note3' => $created_by,
                            'note4' => $rfp_dept,
                            'note5' => 'Please Check.'],
                function ($message) use ($rfpnbr, $emailreject, $company)
                {
                    $message->subject('Notifikasi : Request for Purchasing Approval Rejected - '.$company->com_name);
                    $message->from($company->com_email);
                    $message->to($emailreject->email);
                });

                $user = App\User::where('username','=', $emailreject->username)->first(); // user siapa yang terima notif (lewat id)
                          
                $details = [
                    'body' => 'Following Request for Purchasing has been rejected',
                    'url' => 'inputrfp',
                    'nbr' => $rfpnbr,
                    'note' => 'Please check'
                ]; // isi data yang dioper
                                            
                                        
                $user->notify(new \App\Notifications\eventNotification($details));
        }

        if($paramater == '3' ){
                // dd('email param ke 3');
                // Kirim Email Notif Ke approver
                Mail::send('email.emailrfp', 
                        [
                            'pesan' => 'There is a new RFP exceed budget awaiting your response',
                            'note1' => $rfpnbr,
                            'note2' => $rfpduedate,
                            'note3' => $created_by,
                            'note4' => $rfp_dept],
                            // 'note3' => $rfpmstrs->xrfp_duedate,
                            // 'note4' => $rfpmstrs->created_by,
                            // 'note5' => $rfpmstrs->xrfp_dept],
                            function ($message) use ($array_email,$company)
                        {
                            $message->subject('PhD - RFP Approval Task - '.$company->com_name);
                            $message->from($company->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });

        }

        if($paramater == '4'){
            // dd('email param ke 4');
            Mail::send('email.emailrfp', 
            [
                'pesan' => 'There is a new RFP awaiting your response',
                'note1' => $rfpnbr,
                'note2' => $rfpduedate,
                'note3' => $created_by,
                'note4' => $rfp_dept],
                // 'note3' => $rfpmstrs->xrfp_duedate,
                // 'note4' => $rfpmstrs->created_by,
                // 'note5' => $rfpmstrs->xrfp_dept],
                function ($message) use ($array_email,$company)
            {
                $message->subject('PhD - RFP Approval Task - '.$company->com_name);
                $message->from($company->com_email); // Email Admin Fix
                $message->to($array_email);
            });

        }


        
    }
}
