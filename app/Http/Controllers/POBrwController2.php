<?php
// DATA UNTUK CHART DASHBOARD
// FL
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
use Auth;

use Carbon\Carbon;

class POBrwController2 extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $id = Auth::id();

        $users = DB::table("users")
                    ->where("users.id",$id)
                    ->get();

        $expitem1 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->where('xpo_app_flg', '=', '1')
                        ->get();

        $expitem2 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->where('xpo_app_flg', '=', '2')
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
                        ->get();

        $expitem3 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total, xpo_crt_date'),DB::raw("SUM(xpo_total) as poamt"))
                        ->where('xpo_app_flg', '=', '2')
                        //->where(Carbon::parse('xpo_crt_date')->diffInDays() , '>', '3') 
                        ->whereRaw('datediff(curdate(),xpo_crt_date) > 3')
                        ->whereRaw('datediff(curdate(),xpo_crt_date) < 7')
                        ->get();

        $expitem4 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total, xpo_crt_date'),DB::raw("SUM(xpo_total) as poamt"))
                        ->where('xpo_app_flg', '=', '2')
                        //->where(Carbon::parse('xpo_crt_date')->diffInDays() , '>', '3') 
                        ->whereRaw('datediff(curdate(),xpo_crt_date) > 7')
                        // ->whereRaw('datediff(curdate(),xpo_crt_date) < 7')
                        ->get();


// ===================================================================================================

       $item1 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total, xpo_due_date'))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 0')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 8')
                        ->where('xpod_qty_open', '>', '0') 
                        ->where('xpod_status', '!=', 'Closed')
                        ->where('xpod_qty_open', '>', '0') 
                        ->get();

        $item2 = DB::table("xpo_mstrs")
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->select(DB::raw('count(*) as total, xpo_due_date'), DB::raw('count(*) as xpo_nbr'))
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 7')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 31')
                        ->where('xpod_status', '!=', 'Closed')
                        ->where('xpod_qty_open', '>', '0') 
                        
                        ->get();


        $item3 = DB::table("xpo_mstrs")
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->select(DB::raw('count(*) as total, xpo_due_date'))
                        ->where('xpod_status', '!=', 'Closed')
                        //->where(Carbon::parse('xpo_crt_date')->diffInDays() , '>', '3') 
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 30')
                        ->where('xpod_status', '!=', 'Closed')
                        ->where('xpod_qty_open', '>', '0') 
                        ->get();


// ===================================================================================================

        

        $potot1 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(30)->subMonth()->month)
                        ->get();

        $potot2 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->whereMonth('xpo_ord_date', '=', Carbon::now()->subMonth()->month)
                        // ->whereDate('xpo_ord_date', '<=', Carbon::now()->addDays(-30))
                        // ->whereDate('xpo_ord_date', '>', Carbon::now()->addDays(-60))
                        ->get();

        $potot3 = DB::table("xpo_mstrs")
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->whereMonth('xpo_ord_date', '=', Carbon::now()->addDays(-30)->subMonth()->month)
                        ->get();


 // =================================Un PO By Supp===========================================


        $unpobysupp = DB::table('xpo_mstrs')
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->where('xpo_status', '=', 'Unconfirm')
                        // ->whereRaw('datediff(curdate(),xpod_due_date) > 0')
                        // ->whereDate('xpod_due_date', date('Y-m-d'))
                        ->get();


        $pastduepo = DB::table('xpo_mstrs')
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpod_qty_open', '>', '0')  
                        ->where('xpod_status', '!=', 'Closed')
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 0') 
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        // ->whereRaw('datediff(curdate(),xpo_due_date) > 0') //yang ditampilkan hanya past due in days dengan data lebih dari nol
                        ->get();
     
   
        $upcomingpo = DB::table('xpo_mstrs')
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->where('xpod_qty_open', '>', '0')  
                        ->where('xpod_status', '!=', 'Closed')
                        ->whereRaw('datediff(curdate(),xpo_due_date) < 0') 
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        // ->whereRaw('datediff(curdate(),xpo_due_date) > 0') //yang ditampilkan hanya past due in days dengan data lebih dari nol
                        ->get();


        $openporcv = DB::table('xpo_mstrs')
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->whereRaw('datediff(curdate(),xpo_due_date) > 0')
                        ->get();


        $openpo = DB::table('xpo_mstrs')
                        ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
                        ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
                        // ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_vend')   
                        ->where('xpod_qty_open', '!=', '0')
                        ->where('xpo_status', '!=', 'Closed')
                        // ->whereRaw('datediff(curdate(),xpod_due_date) > 0') //baru sampai sini, untuk yang datannya tdk minus belum bisa ditampilkan
                        ->get();




        // $openrfq = DB::table('xpo_mstrs')
        //                 ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpo_total) as poamt"))
        //                 ->join('xpod_dets','xpo_mstrs.xpo_nbr', '=', 'xpod_dets.xpod_nbr')
        //                 ->join('xbid_det','xpo_mstrs.xpo_nbr', '=', 'xbid_det.xbid_no_po')
        //                 ->where('xbid_det.xbid_flag', '!=', '2')
        //                 ->where('xbid_det.xbid_flag', '!=', '3')
        //                 ->where('xbid_det.xbid_flag', '!=', '4')
        //                 ->get();


        $openrfq = DB::table('xbid_mstr')
                        ->select(DB::raw('count(*) as total'))
                        ->join('xbid_det','xbid_mstr.xbid_id', '=', 'xbid_det.xbid_id')
                        ->join('xitemreq_mstr','xbid_mstr.xbid_part', '=', 'xitemreq_mstr.xitemreq_part')
                        ->where('xbid_det.xbid_flag', '!=', '2')
                        ->where('xbid_det.xbid_flag', '!=', '3')
                        ->where('xbid_det.xbid_flag', '!=', '4')
                        ->get();


        // $revenueMonth = Callback::whereMonth('xpo_ord_date', '=', Carbon::now()->subMonth()->month);     

        //dd(Carbon::parse('xpo_crt_date')->diffInDays());

        //\Carbon\Carbon::parse($pdue->xpod_due_date)->diffInDays()

        // $expitem3 = DB::table("xpod_dets")
        //                 ->select(DB::raw('count(*) as total'),DB::raw("SUM(xpod_date) as expamt"))
        //                 ->whereDate('xpod_due_date', '<=', Carbon::now()->addDays(90))
        //                 ->whereDate('xpod_due_date', '>', Carbon::now()->addDays(60))
        //                 ->get();


        
        // dd($expitem1[0]->total);
        //dd(Carbon::now(),Carbon::now()->addDays(30),Carbon::now()->addDays(60),Carbon::now()->addDays(90));
        //dd($expitem1);

        return view("po/dash2", ["users" => $users, "expitem1" => $expitem1, "expitem2" => $expitem2, "expitem3" => $expitem3, "expitem4" => $expitem4, "item1" => $item1, "item2" => $item2, "item3" => $item3, "potot1" => $potot1, "potot2" => $potot2, "potot3" => $potot3, "unpobysupp" => $unpobysupp,  "pastduepo" => $pastduepo, "upcomingpo" => $upcomingpo, "openpo" => $openpo, "openrfq" => $openrfq]  );
    }
}

// , "revenueMonth" => $revenueMonth