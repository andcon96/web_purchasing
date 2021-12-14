<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class AlertMaintController extends Controller
{
    //
    public function index(Request $req)
    {    
        if($req->ajax()){
            $alert = DB::table('xalert_mstrs')
                                ->paginate(10);    

            $users = DB::table('xalert_mstrs')
                                ->get();  
            return view('/setting/tablesupplier', compact('alert','users'));
        }else{
            $alert = DB::table('xalert_mstrs')
                                ->paginate(10);    

            $users = DB::table('xalert_mstrs')
                                ->get();  
            return view('/setting/alertmaint', compact('alert','users'));
        }
       
    }

    public function update(Request $req)
    {
        // dd($req->all());
        $id = $req->input('edit_id');
        $supplier = $req->input('supname');
        $active = $req->input('active');
        $poapprove = $req->input('poapprove');

        $day1 = $req->input('alertdays1');
        $day2 = $req->input('alertdays2');
        $day3 = $req->input('alertdays3');
        $day4 = $req->input('alertdays4');
        $day5 = $req->input('alertdays5');

        $email1 = $req->input('alertemail1');
        $email2 = $req->input('alertemail2');
        $email3 = $req->input('alertemail3');
        $email4 = $req->input('alertemail4');
        $email5 = $req->input('alertemail5');  

        $idleday = $req->input('idledays');
        $idleemail = $req->input('idleemail');

        $puremail = $req->input('emailpur');
        $phone = $req->input('phone');

        if($phone){
            if(!str_starts_with($phone,'+628')){
                alert()->error('Error','Phone Number Must Start with +628...');
              
                return back();
            }
        }

        DB::table('xalert_mstrs')
            ->where('xalert_id', $id)
            ->update([
                    'xalert_day1' => $day1,
                    'xalert_day2' => $day2,
                    'xalert_day3' => $day3,
                    'xalert_day4' => $day4,
                    'xalert_day5' => $day5,
                    'xalert_email1' => $email1,
                    'xalert_email2' => $email2,
                    'xalert_email3' => $email3,
                    'xalert_email4' => $email4,
                    'xalert_email5' => $email5,
                    'xalert_idle_days' => $idleday,
                    'xalert_idle_emails' =>$idleemail,
                    'xalert_not_pur' =>$puremail,
                    'xalert_po_app'=>$poapprove,
                    'xalert_active'=>$active,
                    'xalert_phone'=>$phone
            ]);
            
        // session()->flash("updated","Supplier Alert Successfully Updated !");;
        alert()->success('Success','Supplier Alert Succesfully Updated');
              
        return back();
    }

    public function createnew(Request $req)
    {    
        //dd($req->all()); 
        $supplier = $req->input('supname');
        $active = $req->input('active');
        $approved = $req->input('poapprove');


        $day1 = $req->input('alertdays1');
        $day2 = $req->input('alertdays2');
        $day3 = $req->input('alertdays3');
        $day4 = $req->input('alertdays4');
        $day5 = $req->input('alertdays5');

        $email1 = $req->input('alertemail1');
        $email2 = $req->input('alertemail2');
        $email3 = $req->input('alertemail3');
        $email4 = $req->input('alertemail4');
        $email5 = $req->input('alertemail5');  

        $idleday = $req->input('idledays');
        $idleemail = $req->input('idleemail');

        $puremail = $req->input('emailpur');

        $data1 = array(
                    'xalert_supp'=>$supplier,
                    'xalert_active'=>$active,
                    'xalert_po_app'=>$approved,
                    'xalert_day1'=>$day1,
                    'xalert_day2'=>$day2,      
                    'xalert_day3'=>$day3,
                    'xalert_day4'=>$day4,    
                    'xalert_day5'=>$day5,    
                    'xalert_email1'=>$email1,
                    'xalert_email2'=>$email2,
                    'xalert_email3'=>$email3,
                    'xalert_email4'=>$email4,
                    'xalert_email5'=>$email5,    
                    'xalert_idle_days'=>$idleday,
                    'xalert_idle_emails'=>$idleemail,
                    'xalert_not_pur'=>$puremail,               
                );
        
        DB::table('xalert_mstrs')->insert($data1);
        // session()->flash("updated","Supplier Alert Successfully Created !");;
        alert()->success('Success','Supplier Alert Succesfully Created');
              
        return back();
        //dd($req->all());
    }

    public function search(Request $req)
    {
        if($req->ajax()){

            $output="";

            $jabatan=DB::table("xalert_mstrs")->where("xalert_id",$req->search)
                                 ->get();
            
            $array = json_decode(json_encode($jabatan), true);

            return response()->json($array);

        }
    }
    
    
    //ditambahkan 03/11/2020
    public function notifread(Request $req){
        auth()->user()
        ->unreadNotifications
        ->when($req->input('id'), function ($query) use ($req) {
            return $query->where('id', $req->input('id'));
        })
        ->markAsRead();
         
        // DB::table('notifications')->where('id', '=', $req->input('id'))->delete();

        return response()->noContent();
        
    }
    
    public function notifreadall(Request $req){
       
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        //DB::table('notifications')->where('id', '=', $req->input('id'))->delete();
      
      return response()->noContent();
    }

}
