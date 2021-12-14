<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\suppconf;
use Carbon\Carbon;

class PodedtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $nbr = $request->nbr;
        $line = $request->line;  
           
        
        $poed = DB::table('xpod_dets')->where('xpod_nbr', $nbr )->where('xpod_line', $line)->get();
        return view('po/podedt',['podedt'=>$poed]);  
    }


     public function insert(Request $req)
    {     
    
        $domain = $req->input('domain');
        $nbr =  $req->input('nbr');
        $line = $req->input('line');  
        $part = $req->input('part');
        $qty = $req->input('qty');
        $due = $req->input('due');
		$ord = $req->input('ord');
        $tot = $ord - $qty;
		$date = Carbon::now()->format('yy-m-d');
		$dat1 = Carbon::parse($due)->format('Y-m-d');
		
 $this->validate($req,[
           'qty' => 'required|numeric'
           
        ]);
        
    if ($tot == 0){
               
             $conf = "Confirm";
        }
        else {
           $conf = "Waiting";
        }     		
if ($dat1 < $date){
	// session()->flash("updated","ERROR :Prom date Less From today!");
    alert()->error('Error','Prom date Less from today');
    return redirect()->back();
}

if($qty < 0) {       
        //    session()->flash("updated","ERROR:QTY Less From Zero!");
           alert()->flash('Success','Qty Less than Zero');
           return redirect()->back();
       }
	 
        $data1 = array('xpod_domain'=>$domain,
                    'xpod_nbr'=>$nbr,
                    'xpod_line'=>$line,      
                    'xpod_qty_prom'=>$qty, 
                    'xpod_prom_date'=>$due,
                    'xpod_status'=>$conf,
                    'xpod_status1'=>$conf,                    
                );
                
                
        
         DB::table('xpod_dets')->where('xpod_nbr',$nbr )->where('xpod_line', $line)->update($data1);
         
         
         
         
         
         $pod1 = DB::table('xpod_dets')->where('xpod_nbr', $nbr  )->get();       
    	   return view('po/poddet',['poddet'=>$pod1],['car'=>$nbr]); 
       
        
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
