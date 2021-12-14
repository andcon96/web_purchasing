<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;
use Auth;

// use App\Expired;

use App\Exports\ExpiredExport;
use App\Imports\expItemImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Storage;

//sorting
use Kyslik\ColumnSortable\Sortable;

class PODasboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $id = Auth::id();

        // $users = DB::table("users")
        //             ->where("users.id",$id)
        //             ->get();

// =========================Data pada chart 1-7 Days==============================

        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 0')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 8')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
                        // ->whereRaw('datediff(curdate(),xpo_due_date) > 0') //yang ditampilkan hanya past due in days dengan data lebih dari nol
                        ->paginate(10);

         // $temp_pastduepo = DB::table('xpo_mstrs')
         //           ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
         //           // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend') 
         //           ->where('xpod_qty_open', '>', '0')  
         //           ->where('xpod_status', '!=', 'Closed')
         //           ->whereRaw('datediff(curdate(),xpo_due_date) < 0') 
         //           // ->whereRaw('datediff(curdate(),xpo_due_date) > 0') //yang ditampilkan hanya past due in days dengan lebih dari nol
         //           ->first();

        // dd($temp_pastduepo->xpo_due_date);
       //return view('inventory/expitem',['expitem'=>$expitem]);
       
       // $pastdue = xpod_dets::sortable()->paginate(10);       
       return view('po/pastduepo',['pastduepo' => $pastduepo]);

    }

// search
  public function pastduesearch(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search1 = $request->po_search1;
        // $po_search2 = $request->po_search2;
  
       
        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_nbr','like',"%".$po_search1."%")
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 0')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 8')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
          ->paginate(10);

        
        // $id = DB::table('xpo_mstrs')
        //                 ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
        //                 ->where('xpo_nbr','like',"%".$po_search2."%")
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
        //                 ->where('xpod_status', '!=', 'Closed')
        //                 ->where('xpod_qty_open', '>', '0') 
        //   ->paginate(10);

        // dd($pastduepo);               
       
        return view('po/tbpastduepo',['pastduepo' => $pastduepo]);

      }
  }




// =========================Data pada chart 8-30 Days==============================
    public function indexpastdue2()
    {

        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
                        ->paginate(10);
   
       return view('po/pastduepo2',['pastduepo' => $pastduepo]);

    }


  // search
  public function pastduesearch2(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search2 = $request->po_search2;
        // $po_search2 = $request->po_search2;
  
       
        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_nbr','like',"%".$po_search2."%")
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
          ->paginate(10);

        
        // $id = DB::table('xpo_mstrs')
        //                 ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
        //                 ->where('xpo_nbr','like',"%".$po_search2."%")
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
        //                 ->where('xpod_status', '!=', 'Closed')
        //                 ->where('xpod_qty_open', '>', '0') 
        //   ->paginate(10);

        // dd($pastduepo);               
       
        return view('po/tbpastduepo',['pastduepo' => $pastduepo]);

      }
  }

// =========================Data pada chart > 30 Days==============================
    public function indexpastdue3()
    {

        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 30')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
                        ->paginate(10);

  
       return view('po/pastduepo3',['pastduepo' => $pastduepo]);

    }







    // search
  public function pastduesearch3(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search3 = $request->po_search3;
        // $po_search2 = $request->po_search2;
  
       
        $pastduepo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_nbr','like',"%".$po_search3."%")
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 30')
                        ->where('xpod_status', '!=', 'Closed')
                        // ->where('xpod_qty_open', '>', '0') 
          ->paginate(10);

        
        // $id = DB::table('xpo_mstrs')
        //                 ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
        //                 ->where('xpo_nbr','like',"%".$po_search2."%")
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
        //                 ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
        //                 ->where('xpod_status', '!=', 'Closed')
        //                 ->where('xpod_qty_open', '>', '0') 
        //   ->paginate(10);

        // dd($pastduepo);               
       
        return view('po/tbpastduepo',['pastduepo' => $pastduepo]);

      }
  }

// =================================End of Past due===========================================



// =================================Open PO===========================================

    public function indexopenpo()
    {

        $openpo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->where('xpod_qty_open', '!=', '0')
                         ->where('xpo_status', '!=', 'Closed')

                        // ->whereRaw('datediff(curdate(),xpod_due_date) > 0') //baru sampai sini, untuk yang datannya tdk minus belum bisa ditampilkan
                        ->paginate(10);




        //dd($openpo, $mytime);
       //return view('inventory/expitem',['expitem'=>$expitem]);
       
       // $pastdue = xpod_dets::sortable()->paginate(10);       
       return view('po/openpo',['openpo' => $openpo]);

    }


// search
  public function posearch3(Request $request)
  {
      if($request->ajax())
      {
  
   // if(Session::get('supp_code') != null) 
   // {
        
        $po_search3 = $request->po_search3;
        // $posearch = $request->posearch;
  
       
        $openpo = DB::table('xpo_mstrs')
          ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
          ->where('xpo_nbr','like',"%".$po_search3."%")
          ->where('xpod_qty_open', '!=', '0')
          ->where('xpo_status', '!=', 'Closed')
          ->paginate(10);
        }
  
        return view('/po/tbopenpo',['openpo' => $openpo]);
      // }
  }

 // =================================Open PO===========================================





 // =================================Un PO By Supp===========================================
    public function indexunpobysupp()
    {

        $unpobysupp = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->where('xpo_status', '=', 'Unconfirm')
                        // ->whereRaw('datediff(curdate(),xpod_due_date) > 0')
                        // ->whereDate('xpod_due_date', date('Y-m-d'))
                        ->paginate(10);
     
       return view('po/unpobysupp',['unpobysupp' => $unpobysupp]);

    }

// search
  public function unposearch(Request $request)
  {
      if($request->ajax())
      {
        
        $unposrc = $request->unposrc;
  
        $unpobysupp = DB::table('xpo_mstrs')
          ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
          // ->where('xpo_status', '=', 'Approved')
          ->where('xpo_status', '=', 'Unconfirm')
          ->where('xpo_nbr','like',"%".$unposrc."%")    
          ->paginate(10);
      }
  
        return view('/po/tbunpobysupp',['unpobysupp' => $unpobysupp]);
  }

 // =================================Un PO By Supp===========================================



// =====================================Upcomig Due for 7 Days=========================================
public function indexupcomingdue()
    {

        $upcoming = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpod_qty_open', '>', '0')  
                        ->where('xpod_status', '!=', 'Closed')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 0')
                        ->paginate(10);

         $temp_pastduepo = DB::table('xpo_mstrs')
                   ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                   ->where('xpod_qty_open', '>', '0')  
                   ->where('xpod_status', '!=', 'Closed')
                   ->whereRaw('datediff(curdate(),xpo_due_date) < 0')
                   ->first();

        // dd($temp_pastduepo->xpo_due_date);
       //return view('inventory/expitem',['expitem'=>$expitem]);
       
       // $pastdue = xpod_dets::sortable()->paginate(10);       
       return view('po/upcoming',['upcoming' => $upcoming, 'temp_pastduepo' => $temp_pastduepo]);

    }


// search
  public function upcomingsearch(Request $request)
  {
      if($request->ajax())
      {
  
   // if(Session::get('supp_code') != null) 
   // {
        
        $up_search = $request->up_search;
        // $posearch = $request->posearch;
  
       
        $upcoming = DB::table('xpo_mstrs')
                      ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                      ->where('xpo_nbr','like',"%".$up_search."%")
                      ->where('xpod_qty_open', '>', '0')  
                      ->where('xpod_status', '!=', 'Closed')
                      ->whereRaw('datediff(curdate(),xpo_due_date) < 0')
                      ->paginate(10);
        }

      $temp_upcomingdue = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend') 
                        ->where('xpod_qty_open', '>', '0')  
                        ->where('xpod_status', '!=', 'Closed')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 0')
                        ->first();

   
       return view('po/tbupcoming',['upcoming' => $upcoming,'temp_pastduepo' => $temp_upcomingdue]);

      // }
  }
// =====================================Upcomig Due for 7 Days=========================================



// =====================================PO Approval Browse=========================================
    public function indexpoappbrw()
    {

        $poappbrw = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_app_flg', '=', '1')
                        // ->groupBy('xpo_nbr')
                        // ->whereRaw('xpo_mstrs.xpo_nbr IN (SELECT MAX(xpo_nbr)FROM xpo_mstrs GROUP BY xpo_nbr)')
                        ->paginate(10);
 
       return view('po/poappbrw',['poappbrw' => $poappbrw]);

    }

// search
  public function poappsearch(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search = $request->po_search;
       
        $poappbrw = DB::table('xpo_mstrs')
            ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
            ->where('xpo_app_flg', '=', '1')
            // ->groupBy('xpo_nbr')
            ->where('xpo_nbr','like',"%".$po_search."%")
            ->paginate(10);
        }
  
        return view('/po/tbpoappbrw',['poappbrw' => $poappbrw]);
      // }
  }

    
    public function indexpoappbrw2()
    {

        $poappbrw = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_app_flg', '=', '2')
                        ->groupBy('xpo_nbr')
                        ->whereRaw('datediff(curdate(),xpo_crt_date) > 3')
                        ->whereRaw('datediff(curdate(),xpo_crt_date) < 7')
                        ->paginate(10);

       return view('po/poappbrw2',['poappbrw' => $poappbrw]);

    }


  public function poappsearch2(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search2 = $request->po_search2;
       
        $poappbrw = DB::table('xpo_mstrs')
            ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
            ->where('xpo_app_flg', '=', '2')
            // ->groupBy('xpo_nbr')
            ->whereRaw('datediff(curdate(),xpo_crt_date) > 3')
            ->whereRaw('datediff(curdate(),xpo_crt_date) < 7')
            ->where('xpo_nbr','like',"%".$po_search2."%")
            ->paginate(10);
        }
  
        return view('/po/tbpoappbrw',['poappbrw' => $poappbrw]);
  }


public function indexpoappbrw3()
    {

        $poappbrw = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpo_app_flg', '=', '2')
                        // ->groupBy('xpo_nbr')
                        ->whereRaw('datediff(curdate(),xpo_crt_date) > 7')
                        ->paginate(10);
 
       return view('po/poappbrw3',['poappbrw' => $poappbrw]);

    }


  public function poappsearch3(Request $request)
  {
      if($request->ajax())
      {
  
        $po_search3 = $request->po_search3;
       
        $poappbrw = DB::table('xpo_mstrs')
            ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
            ->where('xpo_app_flg', '=', '2')
            // ->groupBy('xpo_nbr')
            ->whereRaw('datediff(curdate(),xpo_crt_date) > 7')
            ->where('xpo_nbr','like',"%".$po_search3."%")
            ->paginate(10);
        }
  
        return view('/po/tbpoappbrw',['poappbrw' => $poappbrw]);
  }

// =====================================PO Approval Browse=========================================






 // =================================Number of Purchase Order===========================================
    public function indexnbrofpo()
    {

        $nbrofpo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->paginate(10);
 
       return view('po/nbrofpo',['nbrofpo' => $nbrofpo]);

    }



    public function indexnbrofpo1()
      {

        $nbrofpo = DB::table('xpo_mstrs')
                    ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(30)->subMonth()->month)
                    ->paginate(10);
 
        return view('po/nbrofpo',['nbrofpo' => $nbrofpo]);

      }

// search
  public function nbrofposearch(Request $request)
  {
      if($request->ajax())
      {
  
        $nbrpo_search = $request->nbrpo_search;
       
        $nbrofpo = DB::table('xpo_mstrs')
          ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
          ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(30)->subMonth()->month)
          ->where('xpo_nbr','like',"%".$nbrpo_search."%")
        
          ->paginate(10);
        }
  
        return view('po/tbnbrofpo',['nbrofpo' => $nbrofpo]);

  }




    public function indexnbrofpo2()
      {

        $nbrofpo = DB::table('xpo_mstrs')
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->whereMonth('xpo_ord_date', '=', Carbon::now()->subMonth()->month)
                        ->paginate(10);
 
        return view('po/nbrofpo2',['nbrofpo' => $nbrofpo]);

    }

  public function nbrofposearch2(Request $request)
  {
      if($request->ajax())
      {
        // dd('123');
  
        $nbrpo_search2 = $request->nbrpo_search2;
       
        $nbrofpo = DB::table('xpo_mstrs')
          ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
          ->whereMonth('xpo_ord_date', '=', Carbon::now()->subMonth()->month)
          ->where('xpo_nbr','like',"%".$nbrpo_search2."%")
          ->paginate(10);
        }
  
        return view('po/tbnbrofpo',['nbrofpo' => $nbrofpo]);
  }


 
    public function indexnbrofpo3()
      {

        $nbrofpo = DB::table('xpo_mstrs')
                    ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(-30)->subMonth()->month)
                    ->paginate(10);
 
        return view('po/nbrofpo3',['nbrofpo' => $nbrofpo]);

    }

  public function nbrofposearch3(Request $request)
  {
      if($request->ajax())
      {
  
        $nbrpo_search3 = $request->nbrpo_search3;
       
        $nbrofpo = DB::table('xpo_mstrs')
          ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
          ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(-30)->subMonth()->month)
          ->where('xpo_nbr','like',"%".$nbrpo_search3."%")
          ->paginate(10);
        }
  
        return view('po/tbnbrofpo',['nbrofpo' => $nbrofpo]);
  }
 // =============================End of Number of Purchase Order=====================================




// ==========================OPEN RFQ=============================
public function indexopenrfq()
    {

        $openrfq = DB::table('xbid_mstr')
                        ->join('xbid_det','xbid_mstr.xbid_id', '=', 'xbid_det.xbid_id')
                        ->join('xitemreq_mstr','xbid_mstr.xbid_part', '=', 'xitemreq_mstr.xitemreq_part')
                        ->where('xbid_det.xbid_flag', '!=', '2')
                        ->where('xbid_det.xbid_flag', '!=', '3')
                        ->where('xbid_det.xbid_flag', '!=', '4')
                        // ->whereMonth('xbid_start_date', '=', Carbon::now()->addDays(-30)->subMonth()->month)
                        ->paginate(10);
 
       return view('po/openrfq',['openrfq' => $openrfq]);

    }
    
// search
  public function rfqsearch2(Request $request)
  {
      if($request->ajax())
      {
  
   // if(Session::get('supp_code') != null) 
   // {
        
        $rfq_search = $request->rfq_search;
        // $posearch = $request->posearch;
  
       
        $openrfq = DB::table('xbid_mstr')
            ->join('xbid_det','xbid_mstr.xbid_id', '=', 'xbid_det.xbid_id')
            ->join('xitemreq_mstr','xbid_mstr.xbid_part', '=', 'xitemreq_mstr.xitemreq_part')
            ->where('xbid_det.xbid_flag', '!=', '2')
            ->where('xbid_det.xbid_flag', '!=', '4')
            ->where('xbid_mstr.xbid_id','like',"%".$rfq_search."%")
            ->paginate(10);
        }
  
        return view('po/tbopenrfq',['openrfq' => $openrfq]);
      // }
  }
}