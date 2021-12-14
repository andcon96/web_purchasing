<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;

class PoconfController extends Controller
{
   
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 public function menu(Request $req)
    {
	   
    if(Session::get('supp_code') != null)
      { 
       $po1 = DB::table('xpo_mstrs')->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))->where('xpo_status', 'Approved')->count();
       $po2 = DB::table('xpo_mstrs')->where('xpo_mstrs.xpo_vend','=',Session::get('supp_code'))->where('xpo_status','Confirm')->count();
      }
      else {
       $po1 = DB::table('xpo_mstrs')->where('xpo_status', 'Approved')->count();
       $po2 = DB::table('xpo_mstrs')->where('xpo_status','Confirm')->count();
      }
      
      return view('/po/suppmenu',['poc'=>$po1,'pod'=>$po2]);
    }
	
    public function index(Request $req)
    {
		 if($req->ajax()){
            if(Session::get('supp_code') != null){
                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10); 

                return view('/po/tablepo',['users'=>$users]);

            }else{
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->orderBy('xpo_mstrs.xpo_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10); 

                return view('/po/tablepo',['users'=>$users]);
            }

        }else{
            if(Session::get('supp_code') != null)
            {
                // Login Sbg Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->where("xpo_mstrs.xpo_vend","=",Session::get('supp_code'))
                ->orderBy('xpod_dets.xpod_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10);  

                return view('/po/pobrowse',['users'=>$users]);
            }else{
                // Login Sbg non Supplier
                $users = DB::table("xpo_mstrs")
                ->join("xpod_dets",'xpod_dets.xpod_nbr','=','xpo_mstrs.xpo_nbr')
                ->orderBy('xpod_dets.xpod_nbr')
                ->orderBy('xpod_dets.xpod_part','ASC')
                ->paginate(10);  

                return view('/po/pobrowse',['users'=>$users]);
            }

        }
    }
	
	 public function pdf(Request $request)
    {
     $nbr = $request->nbr; 
      $pdf1 = DB::table('xpo_mstrs')->where('xpo_nbr',$nbr )
      ->join('xalert_mstrs', 'xalert_mstrs.xalert_supp', '=', 'xpo_mstrs.xpo_vend')
      ->get();
      
      $pdf2 = DB::table('xpod_dets')->where('xpod_nbr',$nbr )->get();
      $invamt1 = DB::table('xpod_dets')->where('xpod_nbr',$nbr )->selectraw('sum(xpod_qty_ord*xpod_price ) as "total"')->first();
      $ppn = $invamt1->total * 0.1;
      $grand =  $invamt1->total + $ppn; 
    
    $pdf = PDF::loadview('po/popdf',compact('pdf1','pdf2','invamt1','ppn','grand'));
      return $pdf->stream();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
    }
    
    public function cari(Request $request)
	{
      
         }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
