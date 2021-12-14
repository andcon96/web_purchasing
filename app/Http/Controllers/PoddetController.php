<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\suppconf;
use Session;

class PoddetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
	
	 public function send(Request $request)
    {

        $sup = session::get("supp_code");

        $id = session::get("userid");
        

        $data = db::table("xalert_mstrs")->
            where("xalert_mstrs.xalert_supp","=",$sup)->
            first();
        $idn = db::table("users")->
            where("users.id","=",$id)->
            first();       
			
	    $com = DB::table('com_mstr')
                          ->first();		
 
      $note1="";
      $note2="";
      $car = $request->input('nbr'); 
      $poddet = DB::table('xpod_dets')->where('xpod_nbr', $car  )->get(); 
      $po = DB::table('xpo_mstrs')->where('xpo_nbr', $car )->get();
		
         Mail::send('email.email', ['pesan' => $request->pesan,'note1'=>$note1,'note2'=>$note2], function ($message) use ($request,$com,$data)
	        {
	            $message->subject($request->judul);
	            $message->from($com->com_email);
	            $message->to($data->xalert_not_pur);
	        }); 
     
        //  session()->flash("updated","Email has been sent");
         alert()->success('Success','Email has been sent');

         return view('po/poddet',compact('poddet','car','po')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       	
        $car = $request->cari;
        $carix = $request->submit;
        $confx = "Approved";
        $confz = "";                       
        $poddet = DB::table('xpod_dets')->where('xpod_nbr', $car )->get();
        $po = DB::table('xpo_mstrs')->where('xpo_nbr', $car )->get();
        return view('po/poddet',compact('poddet','car','po'));        
    }

	public function email(Request $request)
    {     
 	
        $car = $request->cari;
        $carix = $request->submit;
        $confx = "UnCOnfirm";
        $confz = "";                       
        $poddet = DB::table('xpod_dets')->where('xpod_nbr', $car )->get();
        $po = DB::table('xpo_mstrs')->where('xpo_nbr', $car )->get();       
        $com = DB::table('com_mstr')
                          ->first();
		
			Mail::send('email.email', 
            ['pesan' => '',
            'note1' => $data->xpo_nbr,
            'note2' => ''], 
            function ($message) use ($data,$com)
                {
                    $message->subject('Web Support IMI Notification');
                    $message->from($com->com_email); // Email Admin Fix
                    $message->to($data->email);
                });
									
        return view('po/poddet',compact('poddet','car','po'));        
    }
	
	
     public function insert(Request $req)
    {         
		$nbr = $req->input('nbr');   
        $car = $req->input('nbr');          
        $conf = "Approved";        
        $supp = $req->input('supp');
        $data1 = array('xpo_status'=>$conf,
                    
                );
				
				$data2 = array('xpod_status'=>$conf,
                    
                );
   
         DB::table('xpo_mstrs')->where('xpo_nbr',$nbr )->update($data1);
		  DB::table('xpod_dets')->where('xpod_nbr',$nbr )->update($data2);
        //  session()->flash("updated","PO Number Successfully UnConfirm");
         alert()->success('Success','PO Number Succesfully Unconfirm');

         $poddet = DB::table('xpod_dets')->where('xpod_nbr', $nbr  )->get(); 
         $po = DB::table('xpo_mstrs')->where('xpo_nbr', $car )->get();
         $pod1 = DB::table('xpod_dets')->where('xpod_nbr', $nbr  )->get();  
         $eto = DB::table('xalert_mstrs')
                            ->where('xalert_supp','=',$supp)
                            ->where('xalert_active','=','Yes')
                            ->first();       
            $com = DB::table('com_mstr')
                              ->first();

           foreach ($po as $show){                  
           Mail::send('email.emailapproval', 
            ['pesan' => 'A Purchase Order has been unconfirmed by : '.$supp,
             'note1' => $nbr,
             'note2' => $show->xpo_ord_date,
             'note3' => $show->xpo_due_date, 
             'note4' => number_format($show->xpo_total,2),
             'note5' => 'Please check.'],
             function ($message) use ($eto,$supp,$nbr,$show,$com)
             {
              $message->subject('PhD - Purchase Order Confirmation -'.$eto->xalert_nama);
              $message->from($com->com_email); // Email Admin Fix
              $message->to($eto->xalert_not_pur);
           });   
           }      
    	   return view('po/poddet',compact('poddet','car','po'));  
    } 
    
     public function confirmall(Request $req)
    {         
       
        $nbr = $req->input('nbr');        
        $conf = $req->input('conf');        

        $data1 = array(
                    'xpod_nbr'=>$nbr,                     
                    'xpod_status'=>$conf,
                    'xpod_status1'=>$conf,                    
                );
   
         DB::table('xpod_dets')->where('xpod_nbr',$nbr )
							   ->where('xpod_status',"unconfirm")
							   ->update($data1);
         
         
         $pod1 = DB::table('xpod_dets')->where('xpod_nbr', $nbr  )->get();       
    	   return view('po/poddet',['poddet'=>$pod1],['car'=>$nbr]); 
       
        
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
      public function insertall(Request $req)
    {         
        
        $car = $req->input('nbr');
        $nbr = $req->input('nbr');
        $conf = $req->input('conf');                
        $conf1 = "";
        $conf2 = "Approved"; 
        $supp = $req->input('supp');
        $data1 = array('xpod_status'=>$conf, 
                );               
                
        $data2 = array(
                    'xpo_status'=>$conf, 
                );
                 
        $data3 = array(
               'xpo_status'=>$conf2, 
        );

	  $eto = DB::table('xalert_mstrs')
                            ->where('xalert_supp','=',$supp)
                            ->where('xalert_active','=','Yes')
                            ->first();
							
         DB::table('xpo_mstrs')->where('xpo_nbr',$nbr )->update($data2);  
         DB::table('xpod_dets')->where('xpod_nbr',$nbr )->update($data1);  		 
		//    session()->flash("updated","PO Number Successfully Confirm");
        alert()->success('Success','PO Number Succesfully Confirm');


         $po = DB::table('xpo_mstrs')->where('xpo_nbr', $car )->get();
         $poddet = DB::table('xpod_dets')->where('xpod_nbr', $nbr  )->get();        
            $com = DB::table('com_mstr')
                              ->first(); 
          foreach ($po as $show){ 
			  Mail::send('email.emailapproval', 
            ['pesan' => 'A Purchase Order has been confirmed by : '.$supp,
             'note1' => $nbr,
             'note2' => $show->xpo_ord_date,
             'note3' => $show->xpo_due_date, 
             'note4' => number_format($show->xpo_total,2),
             'note5' => 'Please check.'],
             function ($message) use ($eto,$nbr,$show,$supp,$com)
             {
              $message->subject('Phd - Purchase Order Confirmation -'.$eto->xalert_nama);
              
              $message->from($com->com_email); // Email Admin Fix
              $message->to($eto->xalert_not_pur);
           });	
          }                
    	   return view('po/poddet',compact('poddet','car','po'));  
        
    } 
     
     
    public function show($id)
    {
        //
    }
    
     public function cari(Request $request)
	{
     $nbr = $request->input('nbr');
     $item  = $request->item;
     $line  = $request->line; 
	   $pobr1 = DB::table('xpod_dets')
	 ->where('xpod_nbr','like',"%".$nbr."%")      
      ->where('xpod_part','like',"%".$item."%")
      ->where('xpod_line','like',"%".$line."%")
		->paginate(10);

    		// mengirim data pegawai ke view index
		return view('po/poddet',['poddet' => $pobr1],['car'=>$nbr]);
     	
    
   }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
