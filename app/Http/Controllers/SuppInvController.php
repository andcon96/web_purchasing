<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
 
class SuppInvController extends Controller
{
    public function index()
    {
        // $suppinv = DB::table('xsuppinv_mstr')
        //                 ->get();

        $suppinv = DB::table('xsuppinv_mstr')
                           ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
                           // ->join('xinv_mstr','xinv_mstr.xinv_part', '=', 'xsuppinv_mstr.xitem_nbr')    
                           // ->join('users','users.supp_id', '=', 'xsuppinv_mstr.xsupp')
                           // ->distinct()
                           ->paginate(10);

        $itemmstr = DB::table('xitemreq_mstr')
                           // ->where('role','LIKE','Supplier')
                           // ->distinct()
                           ->get();                        
   
        $supp = DB::table('xalert_mstrs')
                 ->join('users','xalert_mstrs.xalert_supp', '=', 'users.supp_id')
                        // ->where('role','LIKE','Supplier')
                        // ->distinct()
                        ->get();

       return view('setting/suppinv',['suppinv' => $suppinv, 'itemmstr' => $itemmstr, 'supp' => $supp]);
    }


   public function prosessupp(Request $request)
    {


        $itempart = $request->input('itempart');
        $supp = $request->input('alrtsupp');

        $checkdata = DB::table('xsuppinv_mstr')
                        ->where('xitem_nbr','=',$itempart)
                        ->where('xsupp','=',$supp)
                        ->first();
                        //dd($req->all());
        if($checkdata){
            // Found
            // session()->flash("error","Supplier Relation Already Exists");
            alert()->error('error','Supplier Relation Already Exists');
                  
            return back();
        }else{
            // Not Found
            $data1 = array(
                    'xitem_nbr'=>$itempart,
                    'xsupp'=>$supp,               
                );
        
            DB::table('xsuppinv_mstr')->insert($data1);
            
            // session()->flash("added","Supplier Relation Successfully Created");
            alert()->success('Success','Supplier Relation Successfully Created');
                  
            return back();   
        }
    }

    public function delete(Request $req)
    {
        //dd($req->all());
        DB::table('xsuppinv_mstr')
            ->where('xitem_nbr','=',$req->temp_id)
            ->delete();

        // session()->flash("deleted","Supplier Relation Successfully Deleted !");
        alert()->success('Success','Supplier Relation Successfully Deleted');
        return back();
    }

// baru sampai sini

// ================================fungsi search====================================
  public function supp_search(Request $request)
  {
      if($request->ajax())
      {
        
        $item_search = $request->item_search;
      
  
       
        $suppinv = DB::table('xsuppinv_mstr')
          ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
          ->where('xitem_nbr','like',"%".$item_search."%")
          ->paginate(10);
        }
  
        return view('setting/tbsuppinv',['suppinv' => $suppinv]);

  }
  // ================================fungsi search====================================


}