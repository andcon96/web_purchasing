<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
//use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;


class SendEmailController extends Controller
{
	public function index(Request $request)
	{
		$nbr = $request->nbr;  
	    return view('po/formmail',compact('nbr'));
	}

    public function send(Request $request)
    {
    	try{
            
            //dd($id);
            $data = 'KANBAN1';
            $line = '1';
    		// buat email 
	        Mail::send('email.emailConf', ['url' => url("/confirmpo/".$data."/".$line)], function ($message) use ($request)
            {
                //$message->subject($request->judul);
                $message->subject('Notifikasi Web Support IMI');
                $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                $message->to('ray@ptimi.co.id');
            });

	        return back()->with('alert-success','Berhasil Kirim Email');
	    }catch(\InvalidArgumentException $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        }catch(\Exception $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        }catch(\Error $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }  	
    }

    public function confirm(Request $req, $ponbr, $line){
        $date = Carbon::now()->format('Y-m-d');
        $total = DB::table('xpod_dets')
                        ->select('xpod_tot_conf')
                        ->where('xpod_dets.xpod_nbr','=',$ponbr)
                        ->first();
        $newtotal = $total->xpod_tot_conf + 1;
        
        DB::table('xpod_dets')
            ->where('xpod_nbr', $ponbr)
            ->where('xpod_line', $line)
            ->update([
                    'xpod_last_conf' => $date,
                    'xpod_tot_conf' => $newtotal,
            ]);

        return redirect('/home')->with('updated','PO Successfully Confirmed');  

    }
}
