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


class EmailtoApprover implements ShouldQueue
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
    protected $department;
    protected $tampungemail;
    protected $company;
    protected $emailreq1;
    protected $emailreq2;

    public function __construct($rfpnumber,$rfp_duedate,$created_by,$department,$tampungemail,$company,$emailreq1,$emailreq2)
    {
        //
        $this->rfpnumber = $rfpnumber;
        $this->rfp_duedate = $rfp_duedate;
        $this->created_by = $created_by;
        $this->department = $department;
        $this->tampungemail = $tampungemail;
        $this->company = $company;
        $this->emailreq1 = $emailreq1;
        $this->emailreq2 = $emailreq2;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $rfpnumber = $this->rfpnumber;
        $rfp_duedate = $this->rfp_duedate;
        $created_by = $this->created_by;
        $department = $this->department;
        $tampungemail = $this->tampungemail;
        $company = $this->company;

        $emailreq1 = $this->emailreq1;
        $emailreq2 = $this->emailreq2;

        // dd($rfpnumber,$rfp_duedate,$created_by,$department,$tampungemail,$company);

        Mail::send('email.emailrfp', 
            [
                'pesan' => 'There are updates on following RFP. Approval is needed, Please check.',
                'note1' => $rfpnumber,
                'note2' => $rfp_duedate,
                'note3' => $created_by,
                'note4' => $department],
                // 'note3' => $rfpmstrs->xrfp_duedate,
                // 'note4' => $rfpmstrs->created_by,
                // 'note5' => $rfpmstrs->xrfp_dept],
                function ($message) use ($tampungemail,$company)
            {
                $message->subject('PhD - RFP Approval Task - '.$company->com_name);
                $message->from($company->com_email); // Email Admin Fix
                $message->to($tampungemail);
            });

        $user = App\User::where('id','=', $emailreq1)->first(); // user siapa yang terima notif (lewat id)
        $useralt = App\User::where('id','=', $emailreq2)->first();
    
        $details = [
                'body' => 'There are updates on following RFP',
                'url' => 'rfpapproval',
                'nbr' => $rfpnumber,
                'note' => 'Approval is needed, Please check'
        ]; // isi data yang dioper
                                                        
                                                    
        $user->notify(new \App\Notifications\eventNotification($details));
        $useralt->notify(new \App\Notifications\eventNotification($details));

    }
}
