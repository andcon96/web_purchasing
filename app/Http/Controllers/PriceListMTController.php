<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PriceListMTController extends Controller
{
    public function index()
    {    
        //return view('/setting/alertmaint');
        // $cust = DB::select('select * from xcust_mstr join users on xsurel_mstrs.xsurel_supp = users.id order by xsurel_part');  
        $cust = DB::table('xcust_mstr')
                           // ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xcust_mstr.xitem_code')
                           ->paginate(10);
        //Item Code
        $supp = DB::table('users')
                        ->where('role','LIKE','Supplier')
                        ->distinct()
                        ->get();

        $itemmstr = DB::table('xitem_mstr')
                    // ->where('xitem_part','=','xitem_code')
                    ->get();


        //dd($supp);

        return view('/sales/pricelistmt',['cust'=>$cust,'supp'=>$supp,'itemmstr'=>$itemmstr]);
    }



    public function update(Request $req)
    {
        //dd($req->all()); 
        db::table('xcust_mstr')
            ->where('xcust_mstr.id','=',$req->id)
            ->update([
                'xcust_code' => $req->custcode,
                'xcust_type' => $req->custtype,
            ]);


        // session()->flash("updated","Successfully Updated !");
        alert()->success('Success','Succesfully Updated');
              
        return back();
    }



    public function createnew(Request $req)
    {

        // $date = Carbon::now()->format('ymd');

        $custcode = $req->input('custcode');
        $itemcode = $req->input('itemcode');
        $custtype = $req->input('custtype');
        $startdate = $req->input('xcust_start_date');

        $checkdata = DB::table('xcust_mstr')
                        ->join('xitem_mstr','xitem_mstr.xitem_part','=','xcust_mstr.xitem_code')
                        ->where('xcust_code','=',$custcode)
                        ->where('xitem_code','=',$itemcode)
                        ->where('xcust_type','=',$custtype)
                        ->where('xcust_start_date','=',$startdate)
                        ->first();

                        // dd($checkdata);
        if($checkdata){
            // Found
            // session()->flash("error","Price List Already Exists");
            alert()->error('Error','Price List Already Exists');
                  
            return back();

        }else{
            // Not Found
            $data1 = array(
                'xcust_code'=>$custcode,
                'xitem_code'=>$itemcode,
                'xcust_type'=>$custtype,
                'xcust_start_date'=>$startdate,
            );

        
            DB::table('xcust_mstr')->insert($data1);
            
            // session()->flash("updated","Price List Successfully Created");
            alert()->success('Success','Price List Succesfully Created');
                  
            return back();  
        }
    }

    public function delete(Request $req)
    {
        //dd($req->all());
        DB::table('xcust_mstr')
            ->where('xcust_code','=',$req->delete_id)
            ->delete();
// dd($req->all());
        // session()->flash("updated","Price List Successfully Deleted !");
        alert()->success('Success','Price List Succesfully Deleted');
        return back();
    }
}


