<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $dom = DB::table('xdomain_mstr')->first();    
       
       if(is_null($dom))
         {          
         $dat = "yes";
         }
         else
         {
             $dat = "no";
         }
        
         $dom1 = DB::table('xdomain_mstr')->get();    
        return view('setting/domain',['dom'=>$dom1],['data'=>$dat]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        $dom = $req->input('dom');
        $desc =  $req->input('desc');
        $data1 = array('xdomain_code'=>$dom,
                    'xdomain_desc'=>$desc,
                 );
        $dom = DB::table('xdomain_mstr')->first();         
        if(is_null($dom))
         {          
           DB::table('xdomain_mstr')->insert($data1);
         }
         else
         {
             DB::table('xdomain_mstr')->update($data1);
         }
        
       
        // session()->flash("updated","Domain Successfully Created !");
        alert()->success('Success','Domain Succesfully Created');

    	   $dom1 = DB::table('xdomain_mstr')->get();    
         $dat = "no";
        return view('setting/domain',['dom'=>$dom1],['data'=>$dat]);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
