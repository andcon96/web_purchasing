<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SiteController extends Controller
{
    public function view()
    {
	  $data = DB::table('xsite_mstr')					
					->get();
	  $dom = DB::table('xdomain_mstr')					
					->get();					
						
       return view('/setting/sitemt',['sitemt'=>$data],['domain'=>$dom]);    
    }
	public function create(Request $req)
	{
		$dom  = $req->input('dom');	
		$site = $req->input('site');
		$desc = $req->input('desc');
		$act  = $req->input('act');
		
		if(is_null($act)){
			$act1 = "No";
		}
		else {
			$act1 = "True";
		}			
		
		
		$data1 = array(
						'xsite_domain'=>$dom,
						'xsite_site'=>$site,
						'xsite_desc'=>$desc,
						'xsite_act'=>$act1,                           
					);               
					
		DB::table('xsite_mstr')->insert($data1);

		$data = DB::table('xsite_mstr')					
						->get();
		$dom = DB::table('xdomain_mstr')					
						->get();					
			
		alert()->success('Success','Site Successfully Created');			
       	return back();
					
	
    }
	
	public function update(Request $req)
	{
		$dom  = $req->input('e_dom');	
		$site = $req->input('e_site');
		$desc = $req->input('e_desc');
		$act  = $req->input('e_act');
			
			
		if(is_null($act))
		{
		
		$act1 = "false";
		}
		else {
		$act1 = "true";
		}	
		
			$data1 = array(
						'xsite_desc'=>$desc,
						'xsite_act'=>$act1,                           
					);   

		DB::table('xsite_mstr')->where('xsite_site',$site)->update($data1);	
		
		$data = DB::table('xsite_mstr')					
						->get();
		$dom = DB::table('xdomain_mstr')					
						->get();					
		
		alert()->success('Success','Site Successfully Updated');
		return back();       				
	}
	
	public function hapus(Request $req)
	{
		$dom  = $req->input('temp_dom');	
	    $site = $req->input('temp_site');
		
		DB::table('xsite_mstr')->where('xsite_domain', $dom)->where('xsite_site', $site)->delete();   
		alert()->success('Success','Site Successfully Deleted');    
        return redirect()->back();
	}
}
