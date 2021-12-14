<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class TrHistController extends Controller
{
    public function thistindex()
    {
        $thistinput = DB::table('xtransaction')
                        ->get();
 
        //dd($pastduepo, $mytime);
       //return view('inventory/expitem',['expitem'=>$expitem]);
       
       // $pastdue = xpod_dets::sortable()->paginate(10);       
       return view('po/thistinput',['thistinput' => $thistinput]);
    }

    
    public function addtrhist()
    {
        return view('po/addtrhist');
    }
 
    public function trproses(Request $request)
    {


            $supplier = $request->input('xtr_type');
            $part = $request->input('xtr_code');

            $checkdata = DB::table('xtransaction')
                        ->where('xtr_type','=',$supplier)
                        ->where('xtr_code','=',$part)
                        ->first();

                        // dd($checkdata);
            if($checkdata){
            // Found
            // session()->flash("error","Transaction Type Already Exists");
            alert()->error('Error','Transaction Type Already Exists');
                  
            return back();
        }else{



     // dd($request->all());
        $this->validate($request,[
           'xtr_type' => 'required',
           'xtr_code' => 'required'
        ]);


        DB::table('xtransaction')
        ->insert([
          'xtr_type' => $request->xtr_type,
          'xtr_code' => $request->xtr_code
        ]);


                // session()->flash("added","Transaction Type Successfully Added !");
                alert()->success('Success','Transaction Type Succesfully Added');
        return back()->withInput();
}


    }



    public function deletehist(Request $req){
        // dd($req->all());
        db::table('xtransaction')
            ->where('xtr_id','=',$req->temp_id)
            ->delete();

        // session()->flash("deleted","Transaction Type Successfully Deleted !");
        alert()->success('Success','Transaction Type Successfully Deleted');
        return back();
    }    


// ========================================================================


}