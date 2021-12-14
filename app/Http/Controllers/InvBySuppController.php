<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
 
class InvBySuppController extends Controller
{
    public function index()
    {
      if(Session::get('supp_code') != null)  //jika login sebagai supplier
      {
        $invbysupp = DB::table('xsuppinv_mstr')
                           ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
                           ->join('xinv_mstr','xinv_mstr.xinv_part', '=', 'xsuppinv_mstr.xitem_nbr')
                           ->join('users','users.supp_id', '=', 'xsuppinv_mstr.xsupp')
                           ->where("xsuppinv_mstr.xsupp","=",Session::get('supp_code'))
                           // ->distinct()
                           ->paginate(10);


      }else{ 

        $invbysupp = DB::table('xsuppinv_mstr')
                          ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
                          ->join('xinv_mstr','xinv_mstr.xinv_part', '=', 'xsuppinv_mstr.xitem_nbr')
                           ->join('users','users.supp_id', '=', 'xsuppinv_mstr.xsupp')
                           ->paginate(10);
      }

        $itemmstr = DB::table('xitem_mstr')
                           ->get();                         
   
        $supp = DB::table('xalert_mstrs')
                        ->get();

       return view('setting/invbysupp',['invbysupp' => $invbysupp, 'itemmstr' => $itemmstr, 'supp' => $supp]);
    }
    
    public function supplsearch(Request $request)
    {
        if($request->ajax())
        {

          if(Session::get('supp_code') != null) 
          {
          
          $item_search = $request->item_search;

        
          $invbysupp = DB::table('xsuppinv_mstr')
          ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
                              ->join('users','users.supp_id', '=', 'xsuppinv_mstr.xsupp')

            ->join('xinv_mstr','xinv_mstr.xinv_part', '=', 'xsuppinv_mstr.xitem_nbr')
            ->where('xitem_nbr','like',"%".$item_search."%")
            ->where("xsuppinv_mstr.xsupp","=",Session::get('supp_code'))
          
            ->paginate(10);

          }else{
            $item_search = $request->item_search;

        
          $invbysupp = DB::table('xsuppinv_mstr')
            ->join('xinv_mstr','xinv_mstr.xinv_part', '=', 'xsuppinv_mstr.xitem_nbr')
            ->where('xitem_nbr','like',"%".$item_search."%")

            ->join('xitem_mstr','xitem_mstr.xitem_part', '=', 'xsuppinv_mstr.xitem_nbr')
                              ->join('users','users.supp_id', '=', 'xsuppinv_mstr.xsupp')

            // ->where("xsuppinv_mstr.xsupp","=",Session::get('supp_code'))
          
            ->paginate(10);
          }

          return view('setting/tbinvbysupp',['invbysupp' => $invbysupp]);
        }
    }

    public function delete(Request $req)
    {
        DB::table('xsuppinv_mstr')
            ->where('xitem_nbr','=',$req->temp_id)
            ->delete();

        session()->flash("deleted","User Successfully Deleted !");
        alert()->success('Success','User Succesfully Deleted');
        return back();
    }


}