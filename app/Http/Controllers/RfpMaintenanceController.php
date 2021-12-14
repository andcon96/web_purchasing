<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;
use App;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Block\Element\Document;
use App\Jobs\EmailtoApprover;
use App\Jobs\EmailRFP;
use App\Services\CheckBudgetRFP;
use App\Services\CheckBudgetService;


class RfpMaintenanceController extends Controller
{
    // public function index(){
    // 	return view('/setting/rfpapprove');
    // }

    //tambah 28/12/2020
    public function getumitem(Request $req)
    {
        if ($req->ajax()) {
            $data = DB::table('xitemreq_mstr')
                ->where('xitemreq_part', '=', $req->item)
                ->first();

            //  return response($data->xitemreq_um);

            return response()->json([
                'item_price' => $data->xitemreq_price,
                'item_um'  => $data->xitemreq_um,
            ], 200);
        }
    }

    //view untuk rfp data maintenance
    public function viewinput()
    {
        //disini code

        try {
            // $date = Carbon::now()->format('ymd');
            if (Session::get('user_role') == 'Admin') {
                $alert = DB::table('xrfq_mstrs')
                    ->first();

                $date = Carbon::now()->format('ymd');

                $supp = DB::table('xalert_mstrs')
                    ->join('users', 'xalert_supp', '=', 'supp_id')
                    ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                    ->get();

                $site = DB::table('xsite_mstr')
                    ->get();

                $item = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                //    dd($item);


                $item2 = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                $bid = DB::table('xrfp_mstrs')
                    // ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                    // ->selectRaw('*, xrfp_mstrs.created_by as "rfp_createby",
                    //                 MAX(due_date) as "duedate_mstrs"')
                    //->groupBy('xrfp_enduser')
                    ->orderBy('xrfp_nbr', 'DESC')
                    ->where('status', '=', 'New Request')
                    ->paginate(10);
                //dd($bid);
                $dept = Session::get('department');
                // alert()->info('Info', ' created. EXCEED BUDGET, approval required');

                return view('rfp.rfpinput', compact('alert', 'date', 'supp', 'site', 'item', 'dept', 'bid', 'item2'));
            } else {
                $alert = DB::table('xrfq_mstrs')
                    ->first();

                $date = Carbon::now()->format('ymd');

                $supp = DB::table('xalert_mstrs')
                    ->join('users', 'xalert_supp', '=', 'supp_id')
                    ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                    ->get();

                $site = DB::table('xsite_mstr')
                    ->get();

                $item = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                // dd($item);

                $item2 = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                $bid = DB::table('xrfp_mstrs')
                    // ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                    // ->selectRaw('*, xrfp_mstrs.created_by as "rfp_createby",
                    //                 MAX(due_date) as "duedate_mstrs"')
                    //->groupBy('xrfp_enduser')
                    ->orderBy('xrfp_nbr', 'DESC')
                    ->where('xrfp_dept', '=', Session::get('department'))
                    ->where('status', '=', 'New Request')
                    ->paginate(10);

                $dept = Session::get('department');

                // alert()->info('Info', ' created. EXCEED BUDGET, approval required');


                return view('rfp.rfpinput', compact('alert', 'date', 'supp', 'site', 'item', 'dept', 'bid', 'item2'));
            }
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }


    //fungsi untuk create rfp di rfp data maintenance
    public function insertrfp(Request $req)
    {
        // dd($req->all());

        $prefix = DB::table('xrfq_mstrs')
            ->first();

        $rfp_nbr = $prefix->xrfq_rfp_prefix . $prefix->xrfq_rfp_nbr;
        $supp = $req->input('supp');
        $enduser = $req->input('enduser');
        $shipto = $req->input('shipto');
        $site = $req->input('site');
        $dept = $req->input('dept');
        $emailflag = $req->input('kirimnotif');


        // $itemno = $req->input('itemno');
        // $itemflg = $req->input('itemflg');

        // $needdate = $req->input('needdate');
        // $duedate = $req->input('duedate');

        // $qtyorder = $req->input('qtyorder');



        if ($req->itemno == null) {
            // return redirect()->back()->with('error', 'Item Cannot be Blank');
            alert()->error('Error', 'Item Cannot be Blank');
            return back();
        } else {

            $flg = '';
            $x = 0;
            foreach ($req->itemno as $itemno) {

                if (str_contains($flg, $itemno)) {
                    // return redirect()->back()->with('error', 'Item Cannot be Duplicate');
                    alert()->error('Error', 'Item Cannot be Duplicate');
                    return back();
                } elseif (strtotime($req->duedate[$x]) < strtotime($req->needdate[$x])) {
                    // return redirect()->back()->with('error', 'Due Date cannot be less than Need Date');
                    alert()->error('Error', 'Due Date cannot be less than Need Date');
                    return back();
                }

                $x++;
                $flg .= $itemno;
            }


            // dd($data);

            // try{

            //table tampung untuk grouping
            Schema::create('temp_group', function ($table) {
                $table->string('rfp_nbr_tmp');
                $table->string('item_tmp');
                $table->string('priceitem_tmp');
                $table->string('prodline_tmp');
                $table->string('acc_tmp');
                $table->string('subacc_tmp');
                $table->string('cc_tmp');
                $table->temporary();
            });


            //table header

            $data = array(
                'xrfp_nbr' => $rfp_nbr,
                'xrfp_enduser' => $enduser,
                'xrfp_supp' => $supp,
                'xrfp_shipto' => $shipto,
                'xrfp_site' => $site,
                'xrfp_dept' => $dept,
                'created_by' => Session::get('username'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'update_at' => Carbon::now()->toDateTimeString(),
                'status' => 'New Request',
                'xrfp_sendmail' => $emailflag,
            );

            DB::table('xrfp_mstrs')->insert($data);



            //table detail
            $date = "";
            if (count($req->itemno) >= 0) {
                foreach ($req->itemno as $item => $v) { //masukin ke temp table untuk grouping check budget

                    //tambahan tommy 30/10/2020
                    $dataum = DB::table('xitemreq_mstr')
                        ->where('xitemreq_part', '=', $req->itemno[$item])
                        ->first();


                    $pricetotalitem = $req->qtyorder[$item] * $req->price[$item];

                    DB::table('temp_group')
                        ->insert([
                            'rfp_nbr_tmp' => $rfp_nbr,
                            'item_tmp' => $req->itemno[$item],
                            'priceitem_tmp' => $pricetotalitem,
                            'prodline_tmp' => $dataum->xitemreq_prod_line,
                            'acc_tmp' => $dataum->acc,
                            'subacc_tmp' => $dataum->subacc,
                            // 'cc_tmp' => $dataum->costcenter,
                            'cc_tmp' => $req->dept, // Update 09-02-2020 AC
                        ]);



                    $data2 = array(
                        'rfp_nbr' => $rfp_nbr,
                        'itemcode' => $req->itemno[$item],
                        'need_date' => $req->needdate[$item],
                        'due_date' => $req->duedate[$item],
                        'qty_order' => $req->qtyorder[$item],
                        'um' => $req->um[$item],
                        'price' => $req->price[$item],
                        'created_by' => Session::get('username'),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'update_at' => Carbon::now()->toDateTimeString(),
                        'dets_flag' => 'Open',
                    );

                    DB::table('xrfp_dets')->insert($data2);

                    if ($date == '') {
                        $date = $req->duedate[$item];
                    } elseif (strtotime($date) < strtotime($req->duedate[$item])) {
                        $date = $req->duedate[$item];
                    }
                }
                //update

                $checktmp = DB::table('temp_group')
                    ->groupBy('acc_tmp')
                    ->groupBy('subacc_tmp')
                    ->groupBy('cc_tmp')
                    ->selectRaw('acc_tmp, subacc_tmp, cc_tmp, sum(priceitem_tmp) as total')
                    ->get();

                // $rfpnewreq = DB::table('xrfp_mstrs')
                //     ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                //     ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
                //     ->where('status', '=', 'New Request')
                //     ->where('xitemreq_part', '=',  $req->itemno[$item])
                //     ->selectRaw('xitemreq_part, ')
                //     ->get();

                // dd($rfpnewreq);

                $budgetService = new CheckBudgetService();
                $budgetService2 = new CheckBudgetRFP();
                // dd($budgetService);

                // $budgetoday1 = $budgetService->loadWSA();
                $budgetoday = $budgetService->loadWSA();
                $budgetyesterday = $budgetService2->loadRFP();

                // dd($checktmp,$budgetoday,$budgetyesterday,$req->all());

                $arrayvalidate = [];
                foreach ($checktmp as $check) {


                    // dump($budgetoday);

                    if ($budgetoday) {
                        //jika sudah ada po, pr, rfp yang terbentuk hari ini maka cek data hari ini
                        // dump('cek hari ini');
                        // dump($check->acc_tmp,$check->subacc_tmp,$check->cc_tmp);


                        foreach ($budgetoday as $b1) {
                            $totused_budgettoday = 0;
                            if ($check->acc_tmp == $b1->gl && $check->subacc_tmp == $b1->subacc && $check->cc_tmp == $b1->cc) {
                                // dump('ada');
                                //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                // dd('kena');

                                $totused_budgettoday = $check->total + $b1->used_budget;

                                if ($b1->total_budget < $totused_budgettoday) {


                                    array_push($arrayvalidate, 'budget');

                                    // dump('kena today');

                                } else {

                                    array_push($arrayvalidate, 'nonbudget');

                                    // dump('tidak kena today');
                                }

                                // dump($tot_budgettoday);
                            } else {
                                // dump('tidak ada');
                                $totused_budgetyesterday = 0;
                                //masuk sini jika acc, subacc, dan cc tidak ada di hari ini, lebih lengkap karena baca semua transaksi h-1
                                // dump($budgetyesterday);

                                foreach ($budgetyesterday as $b3) {
                                    if ($check->acc_tmp == $b3->gl && $check->subacc_tmp == $b3->subacc && $check->cc_tmp == $b3->cc) {
                                        //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                        // dd('kena');

                                        $totused_budgetyesterday = $check->total + $b3->used_budget;

                                        if ($b3->total_budget < $totused_budgetyesterday) {

                                            array_push($arrayvalidate, 'budget');

                                            // dump('kena yesterday');

                                        } else {

                                            array_push($arrayvalidate, 'nonbudget');
                                            // 
                                            // dump('tidak kena yesterday');
                                        }

                                        // dump($tot_budgettoday);
                                    }
                                }
                            }
                        }
                    } else {
                        //jika belum ada po, pr, dan rfp yang terbentuk pada hari ini. cek po, pr kemarin
                        // dump('cek kemaren');
                        $totused_budgetyesterday = 0;

                        foreach ($budgetyesterday as $b2) {
                            if ($check->acc_tmp == $b2->gl && $check->subacc_tmp == $b2->subacc && $check->cc_tmp == $b2->cc) {
                                //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                // dump($b2,$check->total);

                                $totused_budgetyesterday = $check->total + $b2->used_budget;

                                // dump($b2->total_budget,$totused_budgetyesterday);
                                if ($b2->total_budget < $totused_budgetyesterday) {

                                    array_push($arrayvalidate, 'budget');

                                    // dump('kena');

                                } else {

                                    array_push($arrayvalidate, 'nonbudget');

                                    // dump('tidak kena');
                                }

                                // dump($tot_budgettoday);
                            }
                        }
                    }
                }

                // dd($date);
                DB::table('xrfp_mstrs')
                    ->where('xrfp_nbr', '=', $rfp_nbr)
                    ->update([
                        'xrfp_duedate' => $date,
                    ]);
            }

            // dd($checktmp, $arrayvalidate);

            //rfphist
            $datahist = DB::table('xrfp_mstrs')
                ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                ->where('rfp_nbr', '=', $rfp_nbr)
                ->get();

            // dd($datahist);
            $flg = 1;
            foreach ($datahist as $datahist) {
                DB::table('xrfp_hist')
                    ->insert([
                        'rfp_hist_nbr' => $datahist->rfp_nbr,
                        'rfp_hist_supp' => $datahist->xrfp_supp,
                        'rfp_dept' => $datahist->xrfp_dept,
                        'rfp_hist_enduser' => $datahist->xrfp_enduser,
                        'rfp_hist_site' => $datahist->xrfp_site,
                        'rfp_hist_shipto' => $datahist->xrfp_shipto,
                        'rfp_duedate_mstr' => $datahist->xrfp_duedate,
                        'rfp_create_by' => Session::get('username'),
                        'rfp_create_at' => Carbon::now()->toDateTimeString(),
                        'rfp_status' => $datahist->status,
                        'line' => $flg,
                        'itemcode_hist' => $datahist->itemcode,
                        'need_date_dets' => $datahist->need_date,
                        'due_date_dets' => $datahist->due_date,
                        'qty_order_hist' => $datahist->qty_order,
                        'price_hist' => $datahist->price,
                    ]);
                $flg++;
            }

            $jumlaharray = count($arrayvalidate);
            $kenabudget = "";
            for ($i = 0; $i < $jumlaharray; $i++) {

                if ($arrayvalidate[$i] == "budget") {
                    $kenabudget = "Y";
                }
            }



            // dd($kenabudget);

            if ($kenabudget == 'Y') {

                $apprbudget = DB::table('approver_budget')
                    ->first();

                // dd($apprbudget);

                DB::table('xrfp_app_trans')
                    ->insert([
                        'xrfp_app_nbr' => $rfp_nbr,
                        'xrfp_app_approver' => $apprbudget->approver_budget,
                        'xrfp_app_order' => 1,
                        'xrfp_app_status' => '0',
                        'xrfp_app_alt_approver' => $apprbudget->alt_approver_budget,
                        'create_at' => Carbon::now()->toDateTimeString()
                    ]);


                $emailbudget1 = DB::table('approver_budget')
                    ->join('users', 'approver_budget.approver_budget', '=', 'users.id')
                    ->selectRaw('email, approver_budget')
                    ->first();

                $emailbudget2 = DB::table('approver_budget')
                    ->join('users', 'approver_budget.alt_approver_budget', '=', 'users.id')
                    ->selectRaw('email, alt_approver_budget')
                    ->first();

                $listemail2 = $emailbudget1->email . ',' . $emailbudget2->email;


                $array_email2 = explode(',', $listemail2);
                // dd($array_email2);

                $rfpmstrs2 = DB::table('xrfp_mstrs')
                    ->where('xrfp_nbr', '=', $rfp_nbr)
                    ->first();

                $company2 = DB::table('com_mstr')
                    ->first();

                $rfp_duedate = $rfpmstrs2->xrfp_duedate;
                $created_by = $rfpmstrs2->created_by;
                $rfp_dept = $rfpmstrs2->xrfp_dept;

                // dd('sebelum email');
                EmailRFP::dispatch($rfp_nbr, $rfp_duedate, $created_by, $rfp_dept, '', $company2, $emailbudget1, $emailbudget2, $array_email2, '3');
                // dd('setelah jobs');

                $user = App\User::where('id', '=', $emailbudget1->approver_budget)->first(); // user siapa yang terima notif (lewat id)
                $useralt = App\User::where('id', '=', $emailbudget2->alt_approver_budget)->first();

                $details = [
                    'body' => 'There is a new RFP exceed budget awaiting your response',
                    'url' => 'rfpapproval',
                    'nbr' => $rfp_nbr,
                    'note' => 'Please click to delete.'
                ]; // isi data yang dioper


                $user->notify(new \App\Notifications\eventNotification($details));
                $useralt->notify(new \App\Notifications\eventNotification($details));
            } else {


                $listapprover = DB::table('xrfp_control')
                    ->whereRaw('xorder > 0')
                    ->where('rfp_department', '=', Session::get('department'))
                    ->orderBy('xorder', 'ASC')
                    ->get();

                // dd($listapprover);
                $i = 1;

                foreach ($listapprover as $listapprover) {

                    DB::table('xrfp_app_trans')
                        ->insert([
                            'xrfp_app_nbr' => $rfp_nbr,
                            'xrfp_app_approver' => $listapprover->xrfp_approver,
                            'xrfp_app_order' => $i,
                            'xrfp_app_status' => '0',
                            'xrfp_app_alt_approver' => $listapprover->xrfp_alt_app,
                            'create_at' => Carbon::now()->toDateTimeString()
                        ]);

                    $i++;
                }

                // $data3 = array(
                //     'xrfp_app_nbr' => $rfp_nbr,
                //     'xrfp_app_approver' => $newapp->xrfp_approver,
                //     'xrfp_app_alt_approver' => $newapp->xrfp_alt_app,
                //     'xrfp_app_order' => $newapp->xorder,
                //     'xrfp_app_status' => '0',
                //     'create_at' => Carbon::now()->toDateTimeString()
                // );

                // DB::table('xrfp_app_trans')->insert($data3);


                if ($emailflag == 'Y') {
                    // kirim email ke approver pertama
                    $emailreq1 = DB::table('xrfp_mstrs')
                        ->join('xrfp_control', 'xrfp_control.rfp_department', '=', 'xrfp_mstrs.xrfp_dept')
                        ->join('users', 'xrfp_control.xrfp_approver', '=', 'users.id')
                        ->where('xrfp_mstrs.xrfp_nbr', '=', $rfp_nbr)
                        ->selectRaw('email, xrfp_approver')
                        ->orderBy('xrfp_control.xorder', 'ASC')
                        //->get();
                        ->first();

                    $emailreq2 = DB::table('xrfp_mstrs')
                        ->join('xrfp_control', 'xrfp_control.rfp_department', '=', 'xrfp_mstrs.xrfp_dept')
                        ->join('users', 'xrfp_control.xrfp_alt_app', '=', 'users.id')
                        ->where('xrfp_mstrs.xrfp_nbr', '=', $rfp_nbr)
                        ->selectRaw('email, xrfp_alt_app')
                        ->orderBy('xrfp_control.xorder', 'ASC')
                        //->get();
                        ->first();
                    // $item = DB::table('xitemreq_mstr')
                    //         ->where('xitemreq_mstr.xitemreq_part',$req->itemno)
                    //         ->first();
                    $listemail = $emailreq1->email . ',' . $emailreq2->email;

                    $array_email = explode(',', $listemail);
                    //dd($array_email);

                    $rfpmstrs = DB::table('xrfp_mstrs')
                        // ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                        ->where('xrfp_nbr', '=', $rfp_nbr)
                        ->first();

                    // dd($emailreq);

                    // if(count($emailreq) != 0){
                    //     $email = '';
                    //     foreach($emailreq as $emailreq){
                    //         $email .= $emailreq->email.',';
                    //     }

                    //     $email = substr($email, 0, strlen($email) - 1);

                    //     $array_email = explode(',', $email);    


                    $company = DB::table('com_mstr')
                        ->first();

                    // Kirim Email Notif Ke approver
                    // Mail::send('emailrfp', 
                    //         [
                    //             'pesan' => 'There is a new RFP awaiting your response',
                    //             'note1' => $rfp_nbr,
                    //             'note2' => $rfpmstrs->xrfp_duedate,
                    //             'note3' => $rfpmstrs->created_by,
                    //             'note4' => $rfpmstrs->xrfp_dept],
                    //             // 'note3' => $rfpmstrs->xrfp_duedate,
                    //             // 'note4' => $rfpmstrs->created_by,
                    //             // 'note5' => $rfpmstrs->xrfp_dept],
                    //             function ($message) use ($array_email,$company)
                    //         {
                    //             $message->subject('PhD - RFP Approval Task - '.$company->com_name);
                    //             $message->from($company->com_email); // Email Admin Fix
                    //             $message->to($array_email);
                    //         });



                    $rfp_duedate = $rfpmstrs->xrfp_duedate;
                    $created_by = $rfpmstrs->created_by;
                    $rfp_dept = $rfpmstrs->xrfp_dept;


                    EmailRFP::dispatch($rfp_nbr, $rfp_duedate, $created_by, $rfp_dept, '', $company, $emailreq1, $emailreq2, $array_email, '4');

                    $user = App\User::where('id', '=', $emailreq1->xrfp_approver)->first(); // user siapa yang terima notif (lewat id)
                    $useralt = App\User::where('id', '=', $emailreq2->xrfp_alt_app)->first();

                    $details = [
                        'body' => 'There is a new RFP awaiting your response',
                        'url' => 'rfpapproval',
                        'nbr' => $rfp_nbr,
                        'note' => 'Please click to delete.'
                    ]; // isi data yang dioper


                    $user->notify(new \App\Notifications\eventNotification($details));
                    $useralt->notify(new \App\Notifications\eventNotification($details));
                }


                // }
            }

            //tempat untuk table history rfp

            // }catch(\InvalidArgumentException $ex){
            //     return back()->withError($ex->getMessage())->withInput();
            //     //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            // }catch(\Exception $ex){
            //     return back()->withError($ex->getMessage())->withInput();
            //     //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            // }catch(\Error $ex){
            //     return back()->withError($ex->getMessage())->withInput();
            //     //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            // }


            $no_rfp = Str::substr($rfp_nbr, strlen($rfp_nbr) - 6, 6);
            $int_no_rfp =  (int)$no_rfp + 1;

            // dd($int_no_rfp);

            if ($int_no_rfp < 10) {
                $string_rfp_nbr = strval("00000" . $int_no_rfp);
            } else if ($int_no_rfp < 100 & $int_no_rfp >= 10) {
                $string_rfp_nbr = strval("0000" . $int_no_rfp);
            } else if ($int_no_rfp < 1000 & $int_no_rfp >= 100) {
                $string_rfp_nbr = strval("000" . $int_no_rfp);
            } else if ($int_no_rfp < 10000 & $int_no_rfp >= 1000) {
                $string_rfp_nbr = strval("00" . $int_no_rfp);
            } else if ($int_no_rfp < 100000 & $int_no_rfp >= 10000) {
                $string_rfp_nbr  = strval("0" . $int_no_rfp);
            } else {
                $string_rfp_nbr = strval($int_no_rfp);
            }

            try {
                DB::table('xrfq_mstrs')
                    ->update([
                        'xrfq_rfp_nbr' => $string_rfp_nbr,
                    ]);
            } catch (\InvalidArgumentException $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            } catch (\Exception $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            } catch (\Error $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }

            // return redirect()->back()->with('updated', 'RFP No. : '.$rfp_nbr.' is created');
            if ($kenabudget == 'Y') {

                alert()->info('WARNING', 'RFP No. : ' . $rfp_nbr . ' created. EXCEED BUDGET, approval required');
                return back();
            } else {
                alert()->success('Success', 'RFP No. : ' . $rfp_nbr . ' is created');
                return back();
            }
        }
    }


    // START RFP APPROVAL CONTROLER
    public function controlindex(Request $req)
    {
        if ($req->ajax()) {
            $users = DB::table('xdepartment')
                ->leftJoin('xrfp_control', 'xrfp_control.rfp_department', '=', 'xdepartment.xdept')
                ->selectRaw('*, xdepartment.id as "deptid"')
                ->groupBy('xdept')
                ->paginate(10);

            $names = DB::table('users')
                ->distinct()
                ->select('id', 'name', 'role_type')
                ->where('role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            $names1 = DB::table('users')
                ->distinct()
                ->select('id', 'name', 'role_type')
                ->where('role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            return view('/Setting/tablerfp', compact('users', 'names', 'names1'));
        } else {
            $users = DB::table('xdepartment')
                ->leftJoin('xrfp_control', 'xrfp_control.rfp_department', '=', 'xdepartment.xdept')
                ->selectRaw('*, xdepartment.id as "deptid"')
                ->groupBy('xdept')
                ->paginate(10);

            $names = DB::table('users')
                ->distinct()
                ->select('id', 'name', 'role_type')
                ->where('role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            $names1 = DB::table('users')
                ->distinct()
                ->select('id', 'name', 'role_type')
                ->where('role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            return view('/setting/rfpapprove', compact('users', 'names', 'names1'));
        }
    }

    public function controlcreatenew(Request $req)
    {
    }

    public function editcontrol(Request $req)
    {
        //dd($req->all());
        $deptname = $req->dept_name;
        $deptname = substr($deptname, 0, strpos($deptname, ' '));

        $flg = 0;
        $listapprover = '';
        $order = '';

        if (is_null($req->suppname)) {
        } else {
            foreach ($req->suppname as $data) {


                $flg += 1;
            }

            for ($x = 0; $x < $flg; $x++) {
                if ($req->suppname[$x] == $req->altname[$x]) {
                    // session()->flash('error', 'Approver and Alternate Cannot be The Same');
                    alert()->error('Error', 'Approver and Alternate Cannot be The Same');
                    return back();
                }

                if (strpos($listapprover, $req->suppname[$x]) !== false) {
                    // session()->flash('error', 'Approver cannot be the same');
                    alert()->error('Error', 'Approver cannot be the same');
                    return back();
                }
                if ($order == $req->order[$x]) {
                    alert()->error('Error', 'Order cannot be the same');
                    // return redirect()->back()->with('error', 'Order cannot be the same');
                }

                $order .= $req->order[$x];
                $listapprover .= $req->suppname[$x];
            }
        }

        try {

            DB::table('xrfp_control')
                ->where('xrfp_control.rfp_department', '=', $deptname)
                ->delete();

            if (is_null($req->suppname)) {
                // session()->flash("updated", "RFP Control successfully deleted for departmend : ".$deptname);
                alert()->success('Success', 'RFP Control succesfully deleted for departemen : ' . $deptname);
                return back();
            } else {

                if (count($req->suppname) >= 0) {
                    foreach ($req->suppname as $item => $v) {
                        // dd($item);
                        // dd($req->suppname);

                        $data2 = array(
                            'xrfp_approver' => $req->suppname[$item],
                            'rfp_department' => $deptname,
                            'xrfp_alt_app' => $req->altname[$item],
                            'xorder' => $req->order[$item],
                        );

                        DB::table('xrfp_control')->insert($data2);
                    }
                }
            }
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }

        // session()->flash('updated', 'RFP control successfully created for department : ' .$deptname);
        alert()->success('Success', 'RFP Control succesfully created for departemen : ' . $deptname);
        return back();
    }

    public function searchcontrol(Request $req)
    {
        // dd($req->all());
        if ($req->ajax()) {
            $output = "";
            $flg = 0;

            $users = DB::table('xrfp_control')
                ->join('users', 'xrfp_control.xrfp_approver',  '=', 'users.id')
                ->where('xrfp_control.rfp_department', '=', $req->search)
                ->get();

            $newdata = DB::table('users')
                ->where('users.role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            $data1 = DB::table('users')
                ->where('users.role', '=', 'Purchasing')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

            // dd($users->all());

            if ($users) {
                foreach ($users as $key => $users) {
                    $output .= "<tr>" .

                        "<td>
                            <select id='suppname[]' class='form-control suppname' name='suppname[]' required autofocus>";
                    foreach ($newdata as $data) :
                        if ($users->xrfp_approver == $data->id) :
                            $output .= '<option value=' . $data->id . ' Selected>' . $data->name . ' - ' . $data->role_type . '</option>';
                        else :
                            $output .= '<option value=' . $data->id . ' >' . $data->name . ' - ' . $data->role_type . '</option>';
                        endif;
                    endforeach;
                    $output .= "</select>
                        </td>" .

                        "<td>
                            <select id='altname[]' class='form-control altname' name='altname[]' required autofocus>";
                    foreach ($data1 as $new) :
                        if ($users->xrfp_alt_app == $new->id) :
                            $output .= '<option value=' . $new->id . ' Selected>' . $new->name . ' - ' . $new->role_type . '</option>';
                        else :
                            $output .= '<option value=' . $new->id . ' >' . $new->name . ' - ' . $new->role_type . '</option>';
                        endif;
                    endforeach;

                    $output .= "</select>
                        </td>" .

                        "<td> 
                            <input type='number' class='form-control order' min='1' Autocomplete='Off' id='order[]' name='order[]' style='height:38px' 
                            value='" . $users->xorder . "' required autofocus autocomplete='off'/>
                        </td>" .

                        "<td data-title='Action'><input type='button' class='ibtnDel btn btn-danger' value='delete'></td>" .

                        "</tr>";

                    $flg = $flg + 1;
                }

                return Response($output);
            }
        }
    }

    public function searchshipto(Request $req)
    {
        if ($req->ajax()) {

            $data = DB::table('xsite_mstr')
                ->where('xsite_site', '=', $req->search)
                ->get();

            $array = json_decode(json_encode($data), true);

            return response()->json($array);
        }
    }

    // public function searchitemdesc(Request $req){
    //     if($req->ajax()){

    //         $data = DB::table('xitemreq_mstr')
    //                 ->where('xitemreq_part', '=', $req->search)
    //                 ->get();

    //         $array = json_decode(json_encode($data), true);

    //         return response()->json($array);
    //     }
    // }

    //update rfp data maintenance
    public function updaterfpmaint(Request $req)
    {
        // dd($req->all());
        $u_rfpnumber = $req->input('u_rfpnumber');
        // $supp = $req->input('u_supp');
        // $enduser = $req->input('u_enduser');
        // $site = $req->input('u_site');
        // $shipto = $req->input('u_shipto');
        // $dept = $req->input('u_dept');
        // $status = $req->input('rfp_status');
        // $mstrduedate = $req->input('rfpmstrs_duedate');
        $itemno = $req->itemno;

        // dd($req->all());

        if ($itemno == null) {
            // return redirect()->back()->with('error', 'Item Cannot be Blank');
            alert()->error('Error', 'Item Cannot be Blank');
            return back();
        } else {

            $flg = '';
            $x = 0;
            foreach ($itemno as $itemno) {

                if (str_contains($flg, $itemno)) {
                    // return redirect()->back()->with('error', 'Item Cannot be Duplicate');
                    alert()->error('Error', 'Item Cannot be Duplicate');
                    return back();
                }
                if (str_contains($flg, $itemno)) {
                    // return redirect()->back()->with('error', 'Item Cannot be Duplicate');
                    alert()->error('Error', 'Item Cannot be Duplicate');
                    return back();
                } elseif (strtotime($req->duedate[$x]) < strtotime($req->needdate[$x])) {
                    // return redirect()->back()->with('error', 'Due Date cannot be less than Need Date');
                    alert()->error('Error', 'Due Date cannot be less than Need Date');
                    return back();
                }
                $x++;
                $flg .= $itemno;
            }
        }

        // try {

        Schema::create('temp_table', function ($table) {
            $table->string('rfp_nbr_tmp');
            $table->string('item_tmp');
            $table->string('needdate_tmp');
            $table->string('duedate_tmp');
            $table->string('qtyorder_tmp');
            $table->string('price_tmp');
            $table->string('created_by_tmp');
            $table->string('created_at_tmp');
            $table->string('update_at_tmp');
            $table->temporary();
        });

        if (count($req->itemno) >= 0) {
            foreach ($req->itemno as $item => $v) {
                // dd($req->itemno[$item]);


                $thistemp = array(
                    'rfp_nbr_tmp' => $u_rfpnumber,
                    'item_tmp' => $req->itemno[$item],
                    'needdate_tmp' => $req->needdate[$item],
                    'duedate_tmp' => $req->duedate[$item],
                    'qtyorder_tmp' => $req->qtyorder[$item],
                    'price_tmp' => $req->price[$item],
                    'created_by_tmp' => Session::get('username'),
                    'created_at_tmp' => now()->toDateTimeString(),
                    'update_at_tmp' => now()->toDateTimeString(),
                );

                // dd('ini jalan'); 


                DB::table('temp_table')->insert($thistemp);
            }
        }

        $tempdata = DB::table('temp_table')
            ->where('rfp_nbr_tmp', '=', $u_rfpnumber)
            ->get();

        // dd($olddata->itemcode);           
        // dd($tempdata);

        foreach ($tempdata as $tempdata) {
            $olddata = DB::table('xrfp_dets')
                ->where('xrfp_dets.rfp_nbr', '=', $u_rfpnumber)
                ->get();
            foreach ($olddata as $olddata) {
                if ($tempdata->item_tmp == $olddata->itemcode && $tempdata->needdate_tmp == $olddata->need_date && $tempdata->duedate_tmp == $olddata->due_date && $tempdata->qtyorder_tmp == $olddata->qty_order && $tempdata->price_tmp == $olddata->price) {

                    // dd('rwdadada');
                    DB::table('temp_table')
                        ->where('item_tmp', '=', $tempdata->item_tmp)
                        ->delete();
                }
                // $line++;
            }
        }

        $temp1 = DB::table('temp_table')
            // ->where('rfp_nbr_tmp', '=', $u_rfpnumber)
            ->get();

        // dd($temp1);
        $line = 1;
        $date1 = "";
        foreach ($temp1 as $temp1) {

            if ($date1 == '') {
                $date1 = $temp1->duedate_tmp;
            } elseif (strtotime($date1) < strtotime($temp1->duedate_tmp)) {
                $date1 = $temp1->duedate_tmp;
            }
            $line++;

            DB::table('xrfp_hist')
                ->insert([
                    'rfp_hist_nbr' => $u_rfpnumber,
                    'rfp_hist_supp' => $req->u_supp,
                    'rfp_dept' => $req->u_dept,
                    'rfp_hist_enduser' => $req->u_enduser,
                    'rfp_hist_site' => $req->u_site,
                    'rfp_hist_shipto' => $req->u_shipto,
                    'rfp_duedate_mstr' => $date1,
                    'rfp_create_by' =>  Session::get('username'),
                    'rfp_create_at' => now()->toDateTimeString(),
                    'rfp_status' => $req->rfp_status,
                    'line' => $line,
                    'itemcode_hist' => $temp1->item_tmp,
                    'need_date_dets' => $temp1->needdate_tmp,
                    'due_date_dets' => $temp1->duedate_tmp,
                    'qty_order_hist' => $temp1->qtyorder_tmp,
                    'price_hist' => $temp1->price_tmp,
                ]);

            //bingung
            // if($date1 == ''){
            //     $date1 = $temp1->duedate_tmp;
            // }elseif(strtotime($date1) < strtotime($temp1->duedate_tmp)){
            //     $date1 = $temp1->duedate_tmp;
            // }
            // $line++;

            DB::table('xrfp_dets')
                ->where('rfp_nbr', '=', $temp1->rfp_nbr_tmp)
                ->where('itemcode', '=', $temp1->item_tmp)
                ->where('need_date', '=', $temp1->needdate_tmp)
                ->where('due_date', '=', $temp1->duedate_tmp)
                ->where('qty_order', '=', $temp1->qtyorder_tmp)
                ->where('price', '=', $temp1->price_tmp)
                ->delete();

            //bingung
            // DB::table('xrfp_hist')
            //     ->where('rfp_hist_nbr', '=', $u_rfpnumber)
            //     ->update([
            //         'rfp_duedate_mstr' => $date1
            //     ]);
        }




        DB::table('xrfp_dets')
            ->where('rfp_nbr', '=', $u_rfpnumber)
            ->delete();

        Schema::create('temp_group', function ($table) {
            $table->string('rfp_nbr_tmp');
            $table->string('item_tmp');
            $table->string('priceitem_tmp');
            $table->string('prodline_tmp');
            $table->string('acc_tmp');
            $table->string('subacc_tmp');
            $table->string('cc_tmp');
            $table->temporary();
        });

        if (is_null($req->itemno)) {
            // session()->flash('updated', 'no item at RFP');
            alert()->error('Error', 'no item at RFP');
            return back();
        } else {
            $date = "";
            if (count($req->itemno) >= 0) {
                foreach ($req->itemno as $item => $v) {

                    //tambahan tommy 30/10/2020
                    $dataum = DB::table('xitemreq_mstr')
                        ->where('xitemreq_part', '=', $req->itemno[$item])
                        ->first();

                    $pricetotalitem = $req->qtyorder[$item] * $req->price[$item];


                    DB::table('temp_group')
                        ->insert([
                            'rfp_nbr_tmp' => $u_rfpnumber,
                            'item_tmp' => $req->itemno[$item],
                            'priceitem_tmp' => $pricetotalitem,
                            'prodline_tmp' => $dataum->xitemreq_prod_line,
                            'acc_tmp' => $dataum->acc,
                            'subacc_tmp' => $dataum->subacc,
                            'cc_tmp' => $dataum->costcenter,
                        ]);

                    $datadets = array(
                        'rfp_nbr' => $u_rfpnumber,
                        'itemcode' => $req->itemno[$item],
                        'need_date' => $req->needdate[$item],
                        'due_date' => $req->duedate[$item],
                        'qty_order' => $req->qtyorder[$item],
                        'um' => $dataum->xitemreq_um, //tambahan tommy 30/10/2020
                        'price' => $req->price[$item],
                        'created_by' => Session::get('username'),
                        'created_at' => now()->toDateTimeString(),
                        'update_at' => now()->toDateTimeString(),
                        'dets_flag' => 'Open'
                    );

                    DB::table('xrfp_dets')
                        ->insert($datadets);

                    if ($date == '') {
                        $date = $req->duedate[$item];
                    } elseif (strtotime($date) < strtotime($req->duedate[$item])) {
                        $date = $req->duedate[$item];
                    }
                }

                $checktmp = DB::table('temp_group')
                    ->groupBy('acc_tmp')
                    ->groupBy('subacc_tmp')
                    ->groupBy('cc_tmp')
                    ->selectRaw('acc_tmp, subacc_tmp, cc_tmp, sum(priceitem_tmp) as total')
                    ->get();

                $budgetService = new CheckBudgetService();
                $budgetService2 = new CheckBudgetRFP();

                $budgetoday = $budgetService->loadWSA();
                $budgetyesterday = $budgetService2->loadRFP();

                // dd($budgetoday,$budgetyesterday);

                $arrayvalidate = [];

                foreach ($checktmp as $check) {


                    // dd($budgetoday);

                    if ($budgetoday) {
                        //jika sudah ada po, pr, rfp yang terbentuk hari ini maka cek data hari ini
                        // dump('cek hari ini');
                        // dump($check->acc_tmp,$check->subacc_tmp,$check->cc_tmp);


                        foreach ($budgetoday as $b1) {
                            $totused_budgettoday = 0;
                            if ($check->acc_tmp == $b1->gl && $check->subacc_tmp == $b1->subacc && $check->cc_tmp == $b1->cc) {
                                // dump('ada');
                                //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                // dd('kena');

                                $totused_budgettoday = $check->total + $b1->used_budget;

                                if ($b1->total_budget < $totused_budgettoday) {


                                    array_push($arrayvalidate, 'budget');

                                    // dump('kena today');

                                } else {

                                    array_push($arrayvalidate, 'nonbudget');

                                    // dump('tidak kena today');
                                }

                                // dump($tot_budgettoday);
                            } else {
                                // dump('tidak ada');
                                $totused_budgetyesterday = 0;
                                //masuk sini jika acc, subacc, dan cc tidak ada di hari ini, lebih lengkap karena baca semua transaksi h-1
                                // dump($budgetyesterday);

                                foreach ($budgetyesterday as $b3) {
                                    if ($check->acc_tmp == $b3->gl && $check->subacc_tmp == $b3->subacc && $check->cc_tmp == $b3->cc) {
                                        //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                        // dd('kena');

                                        $totused_budgetyesterday = $check->total + $b3->used_budget;

                                        if ($b3->total_budget < $totused_budgetyesterday) {

                                            array_push($arrayvalidate, 'budget');

                                            // dump('kena yesterday');

                                        } else {

                                            array_push($arrayvalidate, 'nonbudget');
                                            // 
                                            // dump('tidak kena yesterday');
                                        }

                                        // dump($tot_budgettoday);
                                    }
                                }
                            }
                        }
                    } else {
                        //jika belum ada po, pr, dan rfp yang terbentuk pada hari ini. cek po, pr kemarin
                        // dump('cek kemaren');
                        $totused_budgetyesterday = 0;

                        foreach ($budgetyesterday as $b2) {
                            if ($check->acc_tmp == $b2->gl && $check->subacc_tmp == $b2->subacc && $check->cc_tmp == $b2->cc) {
                                //masuk sini jika acc, subacc, dan cc nya ada di hari ini
                                // dd('kena');

                                $totused_budgetyesterday = $check->total + $b2->used_budget;

                                if ($b2->total_budget < $totused_budgetyesterday) {

                                    array_push($arrayvalidate, 'budget');

                                    // dump('kena');

                                } else {

                                    array_push($arrayvalidate, 'nonbudget');

                                    // dump('tidak kena');
                                }

                                // dump($tot_budgettoday);
                            }
                        }
                    }
                }


                DB::table('xrfp_mstrs')
                    ->where('xrfp_nbr', '=', $u_rfpnumber)
                    ->update([
                        'xrfp_duedate' => $date,
                    ]);
            }

            // dd($arrayvalidate);

            $jumlaharray = count($arrayvalidate);
            $kenabudget = "";
            for ($i = 0; $i < $jumlaharray; $i++) {

                if ($arrayvalidate[$i] == "budget") {
                    $kenabudget = "Y";
                }
            }

            // dd($kenabudget);

            if ($kenabudget == "Y") {

                /* jika sebelumnya tidak kena budget namun dilakukan pengeditan RFP
                yang menyebabkan RFP menjadi kena budget maka data di xrfp_app_trans harus dihapus
                kemudian digantikan dengan approver dan alt approver budget. */

                $deleteapp = DB::table('xrfp_app_trans')
                    ->where('xrfp_app_nbr', '=', $u_rfpnumber)
                    ->delete();

                $apprbudget = DB::table('approver_budget')
                    ->first();

                // dd($apprbudget);

                DB::table('xrfp_app_trans')
                    ->insert([
                        'xrfp_app_nbr' => $u_rfpnumber,
                        'xrfp_app_approver' => $apprbudget->approver_budget,
                        'xrfp_app_order' => 1,
                        'xrfp_app_status' => '0',
                        'xrfp_app_alt_approver' => $apprbudget->alt_approver_budget,
                        'create_at' => Carbon::now()->toDateTimeString()
                    ]);


                $emailbudget1 = DB::table('approver_budget')
                    ->join('users', 'approver_budget.approver_budget', '=', 'users.id')
                    ->selectRaw('email, approver_budget')
                    ->first();

                $emailbudget2 = DB::table('approver_budget')
                    ->join('users', 'approver_budget.alt_approver_budget', '=', 'users.id')
                    ->selectRaw('email, alt_approver_budget')
                    ->first();

                $listemail2 = $emailbudget1->email . ',' . $emailbudget2->email;


                $array_email2 = explode(',', $listemail2);
                // dd($array_email2);

                $rfpmstrs2 = DB::table('xrfp_mstrs')
                    ->where('xrfp_nbr', '=', $u_rfpnumber)
                    ->first();

                $company2 = DB::table('com_mstr')
                    ->first();

                $rfp_duedate = $rfpmstrs2->xrfp_duedate;
                $created_by = $rfpmstrs2->created_by;
                $rfp_dept = $rfpmstrs2->xrfp_dept;

                // dd('sebelum email');
                EmailRFP::dispatch($u_rfpnumber, $rfp_duedate, $created_by, $rfp_dept, '', $company2, $emailbudget1, $emailbudget2, $array_email2, '3');
                // dd('setelah jobs');

                $user = App\User::where('id', '=', $emailbudget1->approver_budget)->first(); // user siapa yang terima notif (lewat id)
                $useralt = App\User::where('id', '=', $emailbudget2->alt_approver_budget)->first();

                $details = [
                    'body' => 'There is a new RFP exceed budget awaiting your response',
                    'url' => 'rfpapproval',
                    'nbr' => $u_rfpnumber,
                    'note' => 'Please click to delete.'
                ]; // isi data yang dioper


                $user->notify(new \App\Notifications\eventNotification($details));
                $useralt->notify(new \App\Notifications\eventNotification($details));
            } else {
                /* jika pengeditan RFP tidak menyebabkan kelebihan budget maka dilakukan update 
                RFP secara normal */

                $resetappstatus = DB::table('xrfp_app_trans')
                    ->where('xrfp_app_nbr', '=', $u_rfpnumber)
                    ->delete();

                $listapprover = DB::table('xrfp_control')
                    ->whereRaw('xorder > 0')
                    ->where('rfp_department', '=', Session::get('department'))
                    ->orderBy('xorder', 'ASC')
                    ->get();

                // dd($listapprover);
                $i = 1;

                foreach ($listapprover as $listapprover) {

                    DB::table('xrfp_app_trans')
                        ->insert([
                            'xrfp_app_nbr' => $u_rfpnumber,
                            'xrfp_app_approver' => $listapprover->xrfp_approver,
                            'xrfp_app_order' => $i,
                            'xrfp_app_status' => '0',
                            'xrfp_app_alt_approver' => $listapprover->xrfp_alt_app,
                            'create_at' => Carbon::now()->toDateTimeString()
                        ]);

                    $i++;
                }

                // foreach ($resetappstatus as $reset) {
                //     DB::table('xrfp_app_trans')
                //         ->update([
                //             'xrfp_app_status' => '0', //dikembalikan status sehingga harus di approve kembali dr approver pertama lagi
                //             'xrfp_app_reason' => ''
                //         ]);
                // }

                $emailreq1 = DB::table('xrfp_mstrs')
                    ->join('xrfp_control', 'xrfp_control.rfp_department', '=', 'xrfp_mstrs.xrfp_dept')
                    ->join('users', 'xrfp_control.xrfp_approver', '=', 'users.id')
                    ->where('xrfp_mstrs.xrfp_nbr', '=', $u_rfpnumber)
                    ->selectRaw('email, xrfp_approver')
                    ->orderBy('xrfp_control.xorder', 'ASC')
                    //->get();
                    ->first();

                $emailreq2 = DB::table('xrfp_mstrs')
                    ->join('xrfp_control', 'xrfp_control.rfp_department', '=', 'xrfp_mstrs.xrfp_dept')
                    ->join('users', 'xrfp_control.xrfp_alt_app', '=', 'users.id')
                    ->where('xrfp_mstrs.xrfp_nbr', '=', $u_rfpnumber)
                    ->selectRaw('email, xrfp_alt_app')
                    ->orderBy('xrfp_control.xorder', 'ASC')
                    //->get();
                    ->first();

                $listemail = $emailreq1->email . ',' . $emailreq2->email;

                $array_email = explode(',', $listemail);

                $rfpmstrs = DB::table('xrfp_mstrs')
                    ->where('xrfp_nbr', '=', $u_rfpnumber)
                    ->first();


                $company = DB::table('com_mstr')
                    ->first();

                $tmp_rfpduedate = $rfpmstrs->xrfp_duedate;
                $tmp_createdby = $rfpmstrs->created_by;
                $tmp_dept = $rfpmstrs->xrfp_dept;

                $tmp_emailreq1 = $emailreq1->xrfp_approver;
                $tmp_emailreq2 = $emailreq2->xrfp_alt_app;

                EmailtoApprover::dispatch($u_rfpnumber, $tmp_rfpduedate, $tmp_createdby, $tmp_dept, $array_email, $company, $tmp_emailreq1, $tmp_emailreq2);
            }





            //dd($emailreq->email);
            // Kirim Email Notif Ke approver
            // Mail::send('email.emailrfp', 
            //                     [
            //                         'pesan' => 'There are updates on following RFP. Approval is needed, Please check.',
            //                         'note1' => $u_rfpnumber,
            //                         'note2' => $rfpmstrs->xrfp_duedate,
            //                         'note3' => $rfpmstrs->created_by,
            //                         'note4' => $rfpmstrs->xrfp_dept],
            //                         // 'note3' => $rfpmstrs->xrfp_duedate,
            //                         // 'note4' => $rfpmstrs->created_by,
            //                         // 'note5' => $rfpmstrs->xrfp_dept],
            //                         function ($message) use ($array_email,$company)
            //                     {
            //                         $message->subject('PhD - RFP Approval Task - '.$company->com_name);
            //                         $message->from($company->com_email); // Email Admin Fix
            //                         $message->to($array_email);
            //                     });

            // $user = App\User::where('id','=', $emailreq1->xrfp_approver)->first(); // user siapa yang terima notif (lewat id)
            // $useralt = App\User::where('id','=', $emailreq2->xrfp_alt_app)->first();

            // $details = [
            //         'body' => 'There are updates on following RFP',
            //         'url' => 'rfpapproval',
            //         'nbr' => $u_rfpnumber,
            //         'note' => 'Approval is needed, Please check'
            // ]; // isi data yang dioper


            // $user->notify(new \App\Notifications\eventNotification($details));
            // $useralt->notify(new \App\Notifications\eventNotification($details));
        }

        Schema::drop('temp_table');
        Schema::drop('temp_group');

        // return redirect()->back()->with('updated', 'Data has successfully updated');
        if ($kenabudget == 'Y') {

            alert()->info('WARNING', 'RFP No. : ' . $u_rfpnumber . ' updated. EXCEED BUDGET, approval required');
            return back();
        } else {

            alert()->success('Success', 'RFP No. : ' . $u_rfpnumber . ' successfully updated');
            return back();
        }


        // } catch (\InvalidArgumentException $ex) {
        //     return back()->withError($ex->getMessage())->withInput();
        //     //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        // } catch (\Exception $ex) {
        //     return back()->withError($ex->getMessage())->withInput();
        //     //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        // } catch (\Error $ex) {
        //     return back()->withError($ex->getMessage())->withInput();
        //     //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        // }
    }

    //edit rfp data maintenance
    public function editrfpmaint(Request $req)
    {

        if ($req->ajax()) {
            $flg = 0;
            $output = '';
            if ($req->search != "") {
                $data = DB::table('xrfp_dets')
                    ->join('xrfp_mstrs', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                    ->join('xitemreq_mstr', 'xitemreq_mstr.xitemreq_part', '=', 'xrfp_dets.itemcode')
                    ->where('xrfp_mstrs.xrfp_nbr', '=', $req->search)
                    ->get();

                if ($data) {

                    foreach ($data as $data) {
                        $output .= "<tr>" .

                            "<td>
                            <input type='hidden' id='itemno[]' name='itemno[]' value = '" . $data->itemcode . "' style='width:300px'>
                            " . $data->itemcode . " -- " . $data->xitemreq_desc . "
                        </td>" .

                            "<td>
                            <input type='date' min='" . Carbon::now()->format('Y-m-d') . "' class='form-control form-control-sm needdate' autocomplete='off' name='needdate[]' id='needdate[]'  style='height:37px' value='" . $data->need_date . "' required>
                        </td>" .

                            "<td>
                            <input type='date' min='" . $data->need_date . "' class='form-control form-control-sm duedate' autocomplete='off' name='duedate[]' id='duedate[]' style='height:37px' value='" . $data->due_date . "' required>
                        </td>" .

                            "<td>
                            <input type='number' min='0' step='0.01' class='form-control form-control-sm qtyorder' autocomplete='off' name='qtyorder[]' id='qtyorder[]' style='height:37px' value='" . $data->qty_order . "' required>
                        </td>" .
                            //tambahan 28/12/2020
                            "<td>
                            <input type='text' class='form-control form-control-sm um' autocomplete='off' name='um[]' id='um[]' style='height:37px' value='" . $data->xitemreq_um . "' required readonly>
                        </td>" .

                            "<td>
                            <input type='text' class='form-control form-control-sm price' autocomplete='off' name='price[]' id='price[]' style='height:37px; width:100%;' value='" . $data->price . "' required>
                        </td>" .

                            "<td><input type='button' class='ibtnDel btn btn-danger' value='delete'></td>" .

                            "</tr>";

                        // $flg = $flg + 1;
                    }

                    return Response($output);
                }
            }
        }
    }

    //ketika melakukan close rfp di rfp data maintenance
    public function cancelrfp(Request $req)
    {
        // $rfpnbr = $req->d_rfpnbr;

        DB::table('xrfp_mstrs')
            ->where('xrfp_mstrs.xrfp_nbr', '=', $req->d_rfpnumber)
            ->update([
                'status' => 'Close'
            ]);

        DB::table('xrfp_dets')
            ->where('xrfp_dets.rfp_nbr', '=', $req->d_rfpnumber)
            ->where('dets_flag', '!=', 'New Request')
            ->update([
                'dets_flag' => 'Close'
            ]);

        $datahist = DB::table('xrfp_mstrs')
            ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
            ->where('rfp_nbr', '=', $req->d_rfpnumber)
            ->get();
        $flg = 1;
        foreach ($datahist as $datahist) {
            DB::table('xrfp_hist')
                ->insert([
                    'rfp_hist_nbr' => $datahist->rfp_nbr,
                    'rfp_hist_supp' => $datahist->xrfp_supp,
                    'rfp_dept' => $datahist->xrfp_dept,
                    'rfp_hist_enduser' => $datahist->xrfp_enduser,
                    'rfp_hist_site' => $datahist->xrfp_site,
                    'rfp_hist_shipto' => $datahist->xrfp_shipto,
                    'rfp_duedate_mstr' => $datahist->xrfp_duedate,
                    'rfp_create_by' => $datahist->created_by,
                    'rfp_create_at' => Carbon::now()->toDateTimeString(),
                    'rfp_status' => 'Close',
                    'line' => $flg,
                    'itemcode_hist' => $datahist->itemcode,
                    'need_date_dets' => $datahist->need_date,
                    'due_date_dets' => $datahist->due_date,
                    'qty_order_hist' => $datahist->qty_order,
                ]);

            $flg++;
        }

        // DB::table('xrfp_app_trans')
        //     ->where('xrfp_app_nbr', '=', $req->d_rfpnumber)
        //     ->delete();

        // session()->flash("updated", "RFP No. : ".$req->d_rfpnumber." Is Closed");
        alert()->success('Success', 'RFP No. : ' . $req->d_rfpnumber . ' is Closed');
        return back();
    }

    public function searchroute(Request $req)
    {
        if ($req->ajax()) {
            $data = DB::select('select approver1.xrfp_app_approver, approver1.name, approver1.xrfp_app_alt_approver, users.name as  "nama", approver1.xrfp_app_user, approver1.xrfp_app_reason, approver1.create_at, approver1.xrfp_app_status,approver1.xrfp_app_nbr,approver1.xrfp_app_order
            from
            (select users.id, xrfp_app_trans.xrfp_app_approver, users.name,xrfp_app_trans.xrfp_app_alt_approver, xrfp_app_trans.xrfp_app_user,xrfp_app_trans.xrfp_app_reason,xrfp_app_trans.create_at,xrfp_app_trans.xrfp_app_status, xrfp_app_trans.xrfp_app_nbr,xrfp_app_trans.xrfp_app_order
                                 from xrfp_app_trans 
                                 join users 
                                 on xrfp_app_trans.xrfp_app_approver = users.id)approver1
                                 JOIN
                                 users on users.id = approver1.xrfp_app_alt_approver
                                 where approver1.xrfp_app_status <= 3
                                 AND approver1.xrfp_app_nbr = "' . $req->search . '"
                                 UNION
                                select approver.xrfp_app_approver, approver.name, approver.xrfp_app_alt_approver, users.name as  "nama", approver.xrfp_app_user, approver.xrfp_app_reason, approver.create_at , approver.xrfp_app_status,approver.xrfp_app_nbr,approver.xrfp_app_order
            from
            (select users.id, xrfp_app_hist.xrfp_app_approver, users.name,xrfp_app_hist.xrfp_app_alt_approver,xrfp_app_hist.xrfp_app_user,xrfp_app_hist.xrfp_app_reason,xrfp_app_hist.create_at,xrfp_app_hist.xrfp_app_status, xrfp_app_hist.xrfp_app_nbr,xrfp_app_hist.xrfp_app_order
                                 from xrfp_app_hist 
                                 join users 
                                 on xrfp_app_hist.xrfp_app_approver = users.id)approver
                                 JOIN
                                 users on users.id = approver.xrfp_app_alt_approver
                                 where approver.xrfp_app_status <= 3
                                 AND approver.xrfp_app_nbr = "' . $req->search . '"
                                 ');

            $output = '';

            if (count($data) != 0) {
                foreach ($data as $data) {

                    $output .= "<tr>" .

                        "<td>"
                        . $data->xrfp_app_order .
                        "</td>" .

                        "<td>"
                        . $data->name .
                        "</td>" .

                        "<td>"
                        . $data->nama .
                        "</td>" .

                        "<td>"
                        . $data->xrfp_app_reason .
                        "</td>" .

                        "<td>";
                    if ($data->xrfp_app_status == '3') :
                        $output .= 'History';
                    elseif ($data->xrfp_app_status == '2') :
                        $output .= 'Rejected';
                    elseif ($data->xrfp_app_status == '1') :
                        $output .= 'Approved';
                    elseif ($data->xrfp_app_status == '0') :
                        $output .= 'On Status';
                    endif;
                    $output .= "</td>" .

                        "<td>" .
                        $data->create_at .
                        "</td>" .

                        "</tr>";
                }
                return Response($output);
            }
        }
    }

    //fungsi searching pada rfp data maintenance
    public function rfpinputsearch(Request $req)
    {
        //dd($req->all());
        if ($req->ajax()) {
            $rfpnbr = $req->rfp;
            $supp = $req->supp;
            $status = $req->status;
            $requestby = $req->requestby;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            // dd($req->all());

            if (Session::get('user_role') == 'Admin') {

                if ($rfpnbr == null && $supp == null && $status == null && $requestby == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        // ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('status', '=', 'New Request')
                        ->paginate(10);
                    // echo $query;
                    return view('rfp.tablerfpinput', ['bid' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    if ($status != null) {
                        $query .= " and status = '" . $status . "'";
                    }
                    if ($requestby != null) {
                        $query .= " and created_by = '" . $requestby . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        // ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('status', '=', 'New Request')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('rfp.tablerfpinput', ['bid' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            } elseif (Session::get('user_role') == 'Purchasing') {

                if ($rfpnbr == null && $supp == null && $status == null && $requestby == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('status', '=', 'New Request')
                        ->paginate(10);
                    // echo $query;
                    return view('rfp.tablerfpinput', ['bid' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    if ($status != null) {
                        $query .= " and status = '" . $status . "'";
                    }
                    if ($requestby != null) {
                        $query .= " and created_by = '" . $requestby . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('status', '=', 'New Request')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('rfp.tablerfpinput', ['bid' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            }
        }
    }

    public function viewrfpapp(Request $req)
    {

        try {

            if (Session::get('user_role') == 'Admin') {

                $alert = DB::table('xrfq_mstrs')
                    ->first();

                $date = Carbon::now()->format('ymd');

                $supp = DB::table('xalert_mstrs')
                    ->join('users', 'xalert_supp', '=', 'supp_id')
                    ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                    ->get();

                $site = DB::table('xsite_mstr')
                    ->get();

                $item = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                $item2 = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();


                $bid = DB::table('xrfp_mstrs')
                    ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                    ->where('xrfp_app_status', '=', '0')
                    ->where('status', '!=', 'Close')
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('xrfp_nbr', 'DESC')
                    ->orderBy('xrfp_app_order', 'ASC')
                    ->groupBy('xrfp_nbr')
                    ->paginate(10);
                // dd($bid);

                $dept = Session::get('department');

                return view('rfp.rfpapproval', compact('alert', 'date', 'supp', 'site', 'item', 'dept', 'bid', 'item2'));
            } elseif (Session::get('user_role') == 'Purchasing') {

                $alert = DB::table('xrfq_mstrs')
                    ->first();

                $date = Carbon::now()->format('ymd');

                $supp = DB::table('xalert_mstrs')
                    ->join('users', 'xalert_supp', '=', 'supp_id')
                    ->selectRaw('DISTINCT(xalert_supp) as xalert_supp, xalert_nama')
                    ->get();

                $site = DB::table('xsite_mstr')
                    ->get();

                $item = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();

                $item2 = DB::table('xitemreq_mstr')
                    ->distinct()
                    ->select('xitemreq_part', 'xitemreq_desc', 'xitemreq_um')
                    ->get();


                $bid = DB::table('xrfp_mstrs')
                    ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                    ->where('xrfp_app_status', '=', '0')
                    ->where('status', '!=', 'Close')
                    ->where('xrfp_app_approver', '=', Session::get('userid'))
                    ->orWhere('xrfp_app_alt_approver', '=', Session::get('userid'))
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('xrfp_app_order', 'ASC')
                    ->groupBy('xrfp_nbr')
                    ->paginate(10);
                // dd($bid);

                $dept = Session::get('department');

                return view('rfp.rfpapproval', compact('alert', 'date', 'supp', 'site', 'item', 'dept', 'bid', 'item2'));
            }
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }

    public function rfpappsearch(Request $req)
    {
        if ($req->ajax()) {
            $rfpnbr = $req->rfp;
            $supp = $req->supp;
            // $status = $req->status;
            $enduser = $req->enduser;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            // dd($req->all());

            if (Session::get('user_role') == 'Admin') {

                if ($rfpnbr == null && $supp == null && $enduser == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        //->where('xrfp_dept', '=', Session::get('department'))
                        ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                        ->where('xrfp_app_status', '=', '0')
                        ->where('status', '!=', 'Close')
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('xrfp_nbr', 'DESC')
                        ->orderBy('xrfp_app_order', 'ASC')
                        ->groupBy('xrfp_nbr')
                        // ->where('status', '=', 'New Request')
                        ->paginate(10);
                    // echo $query;
                    return view('/rfp/loadapp', ['bid' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    if ($enduser != null) {
                        $query .= " and xrfp_enduser = '" . $enduser . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                        ->where('xrfp_app_status', '=', '0')
                        ->where('status', '!=', 'Close')
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('xrfp_nbr', 'DESC')
                        ->orderBy('xrfp_app_order', 'ASC')
                        ->groupBy('xrfp_nbr')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('/rfp/loadapp', ['bid' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            } elseif (Session::get('user_role') == 'Purchasing') {

                if ($rfpnbr == null && $supp == null && $enduser == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                        ->where('xrfp_app_status', '=', '0')
                        ->where('status', '!=', 'Close')
                        ->where('xrfp_app_approver', '=', Session::get('userid'))
                        ->orWhere('xrfp_app_alt_approver', '=', Session::get('userid'))
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('xrfp_nbr', 'DESC')
                        ->orderBy('xrfp_app_order', 'ASC')
                        ->groupBy('xrfp_nbr')
                        ->paginate(10);
                    // echo $query;
                    return view('/rfp/loadapp', ['bid' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    // if($status != null){
                    //     $query .= " and status = '".$status."'";
                    // }
                    if ($enduser != null) {
                        $query .= " and xrfp_enduser = '" . $enduser . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                        ->where('xrfp_app_status', '=', '0')
                        ->where('status', '!=', 'Close')
                        ->where('xrfp_app_approver', '=', Session::get('userid'))
                        ->orWhere('xrfp_app_alt_approver', '=', Session::get('userid'))
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('xrfp_nbr', 'DESC')
                        ->orderBy('xrfp_app_order', 'ASC')
                        ->groupBy('xrfp_nbr')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('/rfp/loadapp', ['bid' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            }
        }
    }

    /* Moved
    public function approverfp(Request $req){
        switch($req->input('action')){
            case 'reject' :
                $rfpnbr = $req->rfpnumber;
                $apporder = $req->apporder;

                $users = DB::table('users')
                            ->where('users.department' , '=', Session::get('department'))
                            ->where('users.id', '=', Session::get('userid'))
                            ->first();

                DB::table('xrfp_app_trans')
                    ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                    ->where('xrfp_app_order', '=', $apporder)
                    ->where('xrfp_app_status', '=', '0')
                    ->update([
                        'xrfp_app_status' => '2', //0 new, 1 approved, 2 reject
                        'create_at' => Carbon::now()->toDateTimeString(),
                        'xrfp_app_reason' => $req->e_reason,
                        'xrfp_app_user' => $users->name
                    ]);

                //data transaksi pindah ke history

                $data = DB::table('xrfp_app_trans')
                            ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                            ->where('xrfp_app_status', '!=', '0')
                            ->get();

                foreach($data as $data){
                    $inputthis = array(
                        'xrfp_app_nbr' => $data->xrfp_app_nbr,
                        'xrfp_app_approver' => $data->xrfp_app_approver,
                        'xrfp_app_alt_approver'=> $data->xrfp_app_alt_approver,
                        'xrfp_app_user' => $data->xrfp_app_user,
                        'xrfp_app_order' => $data->xrfp_app_order,
                        'xrfp_app_reason' => $data->xrfp_app_reason,
                        'xrfp_app_status' => $data->xrfp_app_status,
                        'create_at' => $data->create_at
                    );

                    DB::table('xrfp_app_hist')->insert($inputthis);
                }

                DB::table('xrfp_app_trans')
                        ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                        ->delete();

                DB::table('xrfp_mstrs')
                    ->where('xrfp_mstrs.xrfp_nbr', '=', $req->rfpnumber)
                    ->update([
                        'status' => 'Rejected' 
                    ]);

                $rfpmstrs = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $req->rfpnumber)
                            ->get();
                $line = 1;            
                foreach ($rfpmstrs as $mstr) {

                    $inputreject = array(
                        'rfp_hist_nbr' => $mstr->xrfp_nbr,
                        'rfp_hist_supp' => $mstr->xrfp_supp,
                        'rfp_hist_enduser' => $mstr->xrfp_enduser,
                        'rfp_hist_site' => $mstr->xrfp_site,
                        'rfp_hist_shipto' => $mstr->xrfp_shipto,
                        'rfp_dept' => $mstr->xrfp_dept,
                        'rfp_duedate_mstr' => $mstr->xrfp_duedate,
                        'rfp_create_by' => $mstr->created_by,
                        'rfp_create_at' => Carbon::now()->toDateTimeString(),
                        'rfp_status' => $mstr->status,
                        'line' => $line,
                        'itemcode_hist' => $mstr->itemcode,
                        'need_date_dets' => $mstr->need_date,
                        'due_date_dets' => $mstr->due_date,
                        'qty_order_hist' => $mstr->qty_order

                    );

                    DB::table('xrfp_hist')->insert($inputreject);
                    
                    $line ++;
                }

					
				// DB::table('xrfp_hist')
                //         ->where('rfp_hist_nbr', '=', $req->rfpnumber)
                //         ->update([
                //               'rfp_status' => 'Rejected'
                //         ]);

                //email ke yg request ngasih tau rfp nya di reject

                $datarfp = DB::table('xrfp_mstrs')
                            ->where('xrfp_nbr', '=', $req->rfpnumber)
                            ->first();

                $emailreject = DB::table('users')
                            ->join('xrfp_mstrs', 'xrfp_mstrs.created_by', '=', 'users.username')
                            ->where('status', '=', 'Rejected')
                            ->where('xrfp_nbr', '=', $rfpnbr)
                            // ->where('xrfp_sendmail', '=', 'Y')
                            ->select('email', 'username')
                            // ->select('email')
                            ->first();

                // dd($emailreject);
            
                $company = DB::table('com_mstr')
                                ->first();
            
                Mail::send('emailrfpapproval',
                        [   'pesan' => 'Following Request for Purchasing has been rejected :',
                            'note1' => $rfpnbr,
                            'note2' => $datarfp->xrfp_duedate,
                            'note3' => $datarfp->created_by,
                            'note4' => $datarfp->xrfp_dept,
                            'note5' => 'Please Check.'],
                function ($message) use ($rfpnbr, $emailreject, $company)
                {
                    $message->subject('Notifikasi : Request for Purchasing Approval Rejected - '.$company->com_name);
                    $message->from($company->com_email);
                    $message->to($emailreject->email);
                });

                $user = App\User::where('username','=', $emailreject->username)->first(); // user siapa yang terima notif (lewat id)
                          
                $details = [
                    'body' => 'Following Request for Purchasing has been rejected',
                    'url' => 'inputrfp',
                    'nbr' => $rfpnbr,
                    'note' => 'Please check'
                ]; // isi data yang dioper
                                            
                                        
                $user->notify(new \App\Notifications\eventNotification($details));

                // session()->flash("updated", "RFP Number : ".$req->rfpnumber." is Rejected");
                alert()->success('Success','RFP Number : '.$req->rfpnumber.' is Rejected');
                return redirect()->route('rfpapproval');
            break;

            case 'confirm' :

                $rfpnbr = $req->rfpnumber;
                $convert = $req->convertto;

                $users = DB::table('users')
                            ->where('users.department' , '=', Session::get('department'))
                            ->where('users.id', '=', Session::get('userid'))
                            ->first();

                $rfpapprove = DB::table('xrfp_app_trans')
                                ->where('xrfp_app_nbr', '=', $rfpnbr)
                                ->where('xrfp_app_status', '=', '0')
                                ->where('xrfp_app_order', '=', $req->apporder)
                                ->update([
                                    'xrfp_app_status' => '1',
                                    'xrfp_app_reason' => $req->e_reason,
                                    'xrfp_app_user' => $users->name,
                                    'create_at' => Carbon::now()->toDateTimeString()
                                ]);

                $nextapprover = DB::table('xrfp_app_trans')
                                    ->join('users', 'users.id', '=', 'xrfp_app_trans.xrfp_app_approver')
                                    ->where('xrfp_app_nbr', '=', $rfpnbr)
                                    ->where('xrfp_app_status', '=', '0')
                                    ->select('users.id', 'name', 'email', 'xrfp_app_nbr', 'xrfp_app_order', 'xrfp_app_approver')
                                    ->orderBy('xrfp_app_order')
                                    ->first();
                $nextaltapprover = DB::table('xrfp_app_trans')
                                    ->join('users', 'users.id', '=', 'xrfp_app_trans.xrfp_app_alt_approver')
                                    ->where('xrfp_app_nbr', '=', $rfpnbr)
                                    ->where('xrfp_app_status', '=', '0')
                                    ->select('users.id', 'name', 'email', 'xrfp_app_nbr', 'xrfp_app_order', 'xrfp_app_alt_approver')
                                    ->orderBy('xrfp_app_order')
                                    ->first();
                // dd($req->all());
                if($nextapprover == null){
                            
                    $data = DB::table('xrfp_app_trans')
                            ->where('xrfp_app_nbr', '=', $rfpnbr)
                            ->get();

                    foreach($data as $data){
                        $inputdata = array(
                            'xrfp_app_nbr' => $data->xrfp_app_nbr,
                            'xrfp_app_approver' => $data->xrfp_app_approver,
                            'xrfp_app_alt_approver' => $data->xrfp_app_alt_approver,
                            'xrfp_app_user' =>$data->xrfp_app_user,
                            'xrfp_app_order' => $data->xrfp_app_order,
                            'create_at' => $data->create_at,
                            'xrfp_app_status' => $data->xrfp_app_status,
                            'xrfp_app_reason' => $data->xrfp_app_reason,
                        );

                        DB::table('xrfp_app_hist')->insert($inputdata);
                    }

                    DB::table('xrfp_app_trans')
                        ->where('xrfp_app_nbr', '=', $req->rfpnumber)
                        ->delete();
                        
                    if($req->convertto == 'PP'){

                        $data = DB::table('xrfp_mstrs')
                                ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                                ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->get();

                        DB::table('xpurplan_mstrs')
                                ->insert([
                                    'rf_number' => $req->rfpnumber,
                                    'supp_code' => $req->supp,
                                    'due_date' => $req->duedate,
                                    'site' => $req->site,
                                    // 'propose_date' => $req->m_prodate,
                                    'rf_from' => '2', // 1 = RFQ , 2 RFP
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                        $flg = 1;
                        foreach($data as $data){
                            DB::table('xpurplan_dets')
                                    ->insert([
                                        'rf_number' =>$data->xrfp_nbr,
                                        'supp_code' => $data->xrfp_supp,
                                        'line' => $flg, // pasti cuma 1 line
                                        'item_code' => $data->itemcode,
                                        'qty_req' => $data->qty_order,
                                        // 'qty_pro' => $req->m_proqty,
                                        // 'qty_pur' => $req->purqty,
                                        // 'price' => $req->proprice,
                                        'due_date' => $data->due_date,
                                        // 'propose_date' => $req->m_prodate,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                    ]);

                            $flg++;
                        }

                        DB::table('xrfp_mstrs')
                        ->where('xrfp_nbr', '=', $rfpnbr)
                        ->update([
                           'status' => 'Approved'
                        ]);
						
						
                        // session()->flash("updated","Purchase Plan is successfully Created");
                        alert()->success('Success','Purchase Plan is successfully Created');
                          
                        return redirect()->route('rfpapproval');

                    }else if($req->convertto == 'RFQ'){
                        // dd('test');
                        $data = DB::table('xrfp_mstrs')
                                ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
                                ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->get();

                        $prefix = DB::table('xrfq_mstrs')
                                    ->first();

                       
                        // dd($data);

                        foreach($data as $data){
                            $prefix = DB::table('xrfq_mstrs')
                                    ->first();
                            $rfq_nbr = $prefix->xrfq_prefix.$prefix->xrfq_nbr;
                            DB::table('xbid_mstr') 
                                ->insert([
                                    'xbid_id' => $rfq_nbr,
                                    'xbid_site' => $data->xrfp_site,
                                    'xbid_part' => $data->itemcode,
                                    'xbid_qty_req' => $data->qty_order,
                                    'xbid_due_date' => $data->xrfp_duedate,
                                    'xbid_flag' => '0'
                                ]);

                            DB::table('xbid_det')
                                ->insert([
                                    'xbid_id' => $rfq_nbr,
                                    'xbid_qty' => $data->qty_order,
                                    'xbid_date' => $data->due_date,
                                    'xbid_supp' => $data->xrfp_supp,
                                    'xbid_apprv' => 'Yes',
                                    'xbid_flag' => '0'
                                ]);

                            $new_rfq_nbr = Str::substr($rfq_nbr, strlen($rfq_nbr) - 6, 6);
                            $int_rfq_nbr = (int)$new_rfq_nbr + 1;
    
                                if($int_rfq_nbr < 10 ){
                                    $string_rfq_nbr = strval("00000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 100 & $int_rfq_nbr >= 10){
                                    $string_rfq_nbr = strval("0000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 1000 & $int_rfq_nbr >= 100){
                                    $string_rfq_nbr = strval("000".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 10000 & $int_rfq_nbr >= 1000){
                                    $string_rfq_nbr = strval("00".$int_rfq_nbr);
                                }else if($int_rfq_nbr < 100000 & $int_rfq_nbr >= 10000){
                                    $string_rfq_nbr = strval("0".$int_rfq_nbr);
                                }else{
                                    $string_rfq_nbr = strval($int_rfq_nbr);
                                }
        
                                DB::table('xrfq_mstrs')
                                ->update([
                                        'xrfq_nbr' => $string_rfq_nbr,
                                ]);

                                // UPDATE 13112020
                                 DB::table("xrfp_mstrs")
                                    ->join("xrfp_dets","xrfp_mstrs.xrfp_nbr","=","xrfp_dets.rfp_nbr")
                                    ->where('xrfp_nbr','=',$rfpnbr)
                                    ->where('xrfp_dets.itemcode','=',$data->itemcode)
                                    ->update([
                                        'xrfp_no_po' => $rfq_nbr
                                    ]);

                        }

                        DB::table('xrfp_mstrs')
                        ->where('xrfp_nbr', '=', $rfpnbr)
                        ->update([
                            'status' => 'Close'
                        ]);
						
						//tommy 30/10/2020 
                        DB::table('xrfp_dets')
                            ->where('rfp_nbr', '=', $rfpnbr)
                            ->update([
                                'dets_flag' => 'Close'
                            ]);

                        
                        $rfpmstrs2 = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->get();
                        $line = 1;            
                        foreach ($rfpmstrs2 as $mstr2) {

                            $inputreject2 = array(
                                'rfp_hist_nbr' => $mstr2->xrfp_nbr,
                                'rfp_hist_supp' => $mstr2->xrfp_supp,
                                'rfp_hist_enduser' => $mstr2->xrfp_enduser,
                                'rfp_hist_site' => $mstr2->xrfp_site,
                                'rfp_hist_shipto' => $mstr2->xrfp_shipto,
                                'rfp_dept' => $mstr2->xrfp_dept,
                                'rfp_duedate_mstr' => $mstr2->xrfp_duedate,
                                'rfp_create_by' => $mstr2->created_by,
                                'rfp_create_at' => Carbon::now()->toDateTimeString(),
                                'rfp_status' => $mstr2->status,
                                'line' => $line,
                                'itemcode_hist' => $mstr2->itemcode,
                                'need_date_dets' => $mstr2->need_date,
                                'due_date_dets' => $mstr2->due_date,
                                'qty_order_hist' => $mstr2->qty_order,
                                'nbr_convert' => $mstr2->xrfp_no_po

                            );

                            DB::table('xrfp_hist')->insert($inputreject2);
                            
                            $line ++;
                        }

                        // session()->flash("updated", "RFQ No.".$rfq_nbr." is Successfully Created");
                        alert()->success('Success','RFQ No. '.$rfq_nbr.' is Successfully Created');
                        return redirect()->route('rfpapproval');

                    }else if($req->convertto == 'PR'){
                        //ditambahkan tommy
                        //menanti template stanley
                        //dd($req->all());   
                        $dataheader = DB::table('xrfp_mstrs')
                                       ->where('xrfp_nbr', '=', $rfpnbr)
                                       ->first();
                                       
                                       

                        $getpr = DB::table('xrfq_mstrs')
                              ->first();

                        $new_no_pr = $getpr->xrfq_pr_prefix.$getpr->xrfq_pr_nbr;

                        $new_due_date = strtotime($dataheader->xrfp_duedate);
                        $cimduedate = date('m/d/y',$new_due_date);

                        
                        //header RFP to PR
                        $content = '';
                        $content .= '"'.$new_no_pr.'"'.PHP_EOL;
                        $content .= '"'.$dataheader->xrfp_supp.'"'.PHP_EOL;
                        $content .= '"'.$dataheader->xrfp_site.'"'.PHP_EOL;
                        $content .= $cimduedate.' - - - "'.$dataheader->xrfp_enduser.'" '.$rfpnbr.' "" "'.$dataheader->xrfp_dept.'" - '.$dataheader->xrfp_site.' - - - - - - - - - yes'.PHP_EOL;
                        $content .= '"'.$req->e_remarks.'"'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '.'.PHP_EOL;
                        $content .= '-'.PHP_EOL;
                        
                        $dataline = DB::table('xrfp_dets')
                                    ->where('rfp_nbr', '=', $rfpnbr)
                                    ->get();
                           
                        $line = 1;
                        foreach($dataline as $dataline){
                           $old_needdate = $dataline->need_date;
                           $new_needdate =  strtotime($old_needdate);
                           $new_dataline_needdate = date('m/d/y', $new_needdate);
                           
                           
                           $content .= '"'.$line.'"'.PHP_EOL;
                           $content .= '"'.$dataheader->xrfp_site.'"'.PHP_EOL;
                           $content .= '"'.$dataline->itemcode.'"'.PHP_EOL;
                           $content .= '-'.PHP_EOL;
                           $content .= '- -'.PHP_EOL;
                           $content .= $dataline->qty_order.' - '.PHP_EOL;
                           $content .= '- -'.PHP_EOL;
                           $content .= $new_dataline_needdate.' - - - - - - - - - - - - no'.PHP_EOL;
                           $content .= '.'.PHP_EOL;
                           $content .= '"S"'.PHP_EOL;
                           $content .= '.'.PHP_EOL;
                           
                           $line += 1;
                        }

                        File::put('cim/xxcimpr.cim',$content);
                        
                        exec("start cmd /c cimrfppr.bat");

                        $new_pr_nbr = Str::substr($new_no_pr, strlen($new_no_pr) - 6, 6);
                        $int_pr_nbr = (int)$new_pr_nbr + 1;

                        //dd($new_rfq_nbr);
                        
                        if($int_pr_nbr < 10 ){
                            $string_pr_nbr = strval("00000".$int_pr_nbr);
                        }else if($int_pr_nbr < 100 & $int_pr_nbr >= 10){
                            $string_pr_nbr = strval("0000".$int_pr_nbr);
                        }else if($int_pr_nbr < 1000 & $int_pr_nbr >= 100){
                            $string_pr_nbr = strval("000".$int_pr_nbr);
                        }else if($int_pr_nbr < 10000 & $int_pr_nbr >= 1000){
                            $string_pr_nbr = strval("00".$int_pr_nbr);
                        }else if($int_pr_nbr < 100000 & $int_pr_nbr >= 10000){
                            $string_pr_nbr = strval("0".$int_pr_nbr);
                        }else{
                            $string_pr_nbr = strval($int_pr_nbr);
                        }

                        // update next po nbr
                        DB::table('xrfq_mstrs')
                            ->update([
                                'xrfq_pr_nbr' => $string_pr_nbr
                            ]);

                        DB::table('xrfp_mstrs')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->update([
                                'status' => 'Close'
                            ]);
                            
                        DB::table("xrfp_mstrs")
                           ->join("xrfp_dets","xrfp_mstrs.xrfp_nbr","=","xrfp_dets.rfp_nbr")
                           ->where('xrfp_nbr','=',$rfpnbr)
                           ->update([
                                 'xrfp_no_po' => $new_no_pr
                           ]);
                            
                        
                        
                        $rfpmstrs2 = DB::table('xrfp_mstrs')
                            ->join('xrfp_dets', 'xrfp_dets.rfp_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
                            ->where('xrfp_nbr' ,'=', $rfpnbr)
                            ->get();
                        $line = 1;
                           $inputprhist2=array();
                        foreach ($rfpmstrs2 as $mstr2) {

                            $inputprhist2 = array(
                                'rfp_hist_nbr' => $mstr2->xrfp_nbr,
                                'rfp_hist_supp' => $mstr2->xrfp_supp,
                                'rfp_hist_enduser' => $mstr2->xrfp_enduser,
                                'rfp_hist_site' => $mstr2->xrfp_site,
                                'rfp_hist_shipto' => $mstr2->xrfp_shipto,
                                'rfp_dept' => $mstr2->xrfp_dept,
                                'rfp_duedate_mstr' => $mstr2->xrfp_duedate,
                                'rfp_create_by' => $mstr2->created_by,
                                'rfp_create_at' => Carbon::now()->toDateTimeString(),
                                'rfp_status' => $mstr2->status,
                                'line' => $line,
                                'itemcode_hist' => $mstr2->itemcode,
                                'need_date_dets' => $mstr2->need_date,
                                'due_date_dets' => $mstr2->due_date,
                                'qty_order_hist' => $mstr2->qty_order,
                                'nbr_convert' => $mstr2->xrfp_no_po

                            );

                            DB::table('xrfp_hist')->insert($inputprhist2);
                            
                            $line ++;
                        }
                        
                        // session()->flash("updated","PR is successfully Created, PR No. : ".$new_no_pr);
                        alert()->success('Success','PR is sucessfully Created, PR No. '.$new_no_pr);
                        return redirect()->route('rfpapproval');
                    
                    }

                    session()->flash('updated', "RFP Number : ".$req->rfpnumber." is Approved");
                    return redirect()->route('rfpapproval');

                }else{
                    //jika masih ada next approver
                    $listemail = $nextapprover->email.','.$nextaltapprover->email;

                    $array_email = explode(',', $listemail);
					//dd($array_email);
                    $rfpdata2 = DB::table('xrfp_mstrs')
                                ->where('xrfp_nbr', '=', $rfpnbr)
                                ->first();

                    $company = DB::table('com_mstr')
                                ->first();

                    Mail::send('emailrfpapproval',
                        [
                            'pesan' => 'There is a RFP awaiting for approval',
                            'note1' => $rfpnbr,
                            'note2' => $rfpdata2->xrfp_duedate,
                            'note3' => $rfpdata2->created_by,
                            'note4' => $rfpdata2->xrfp_dept,
                            'note5' => 'Please Check.'],
                            function($message) use ($rfpnbr, $array_email, $company)
                        {
                            $message->subject('PhD - RFP Approval Task -'.$company->com_name);
                            $message->from($company->com_email);
                            $message->to($array_email);
                        });

                    // ditambahkan 03/11/2020
                    $user = App\User::where('id','=', $nextapprover->xrfp_app_approver)->first(); // user siapa yang terima notif (lewat id)
                    $useralt = App\User::where('id','=', $nextaltapprover->xrfp_app_alt_approver)->first(); 

                    $details = [
                        'body' => 'There is a RFP awaiting for approval',
                        'url' => 'rfpapproval',
                        'nbr' => $rfpnbr,
                        'note' => 'Please check'
                    ]; // isi data yang dioper
                                                    
                                                
                    $user->notify(new \App\Notifications\eventNotification($details));
                    $useralt->notify(new \App\Notifications\eventNotification($details));

                    // session()->flash('updated', "RFP Number : ".$req->rfpnumber." is Approved");
                    alert()->success('Success','RFP Number : '.$req->rfpnumber.' is Approved');
                    return redirect()->route('rfpapproval');
                }
            break;

            case 'close':
                return redirect()->route('rfpapproval');
            break;
        }
    }*/

    public function detailrfpapp(Request $req, $id)
    {
        // dd($req->all());
        $data = DB::table('xrfp_mstrs')
            ->join('xrfp_dets', 'xrfp_mstrs.xrfp_nbr', '=', 'xrfp_dets.rfp_nbr')
            ->join('xitemreq_mstr', 'xrfp_dets.itemcode', '=', 'xitemreq_mstr.xitemreq_part')
            ->where('xrfp_nbr', '=', $id)
            ->get();

        $norfp = DB::table('xrfp_mstrs')
            ->where('xrfp_nbr', '=', $id)
            ->first();

        $approver = DB::table('xrfp_mstrs')
            ->join('xrfp_app_trans', 'xrfp_app_trans.xrfp_app_nbr', '=', 'xrfp_mstrs.xrfp_nbr')
            ->where('xrfp_mstrs.xrfp_nbr', '=', $id)
            ->where('xrfp_app_status', '=', '0')
            ->orderBy('xrfp_app_order', 'ASC')
            ->first();

        $nextapprover = DB::table('xrfp_app_trans')
            ->join('users', 'users.id', '=', 'xrfp_app_trans.xrfp_app_approver')
            ->where('xrfp_app_nbr', '=', $id)
            ->where('xrfp_app_status', '=', '0')
            ->orderBy('xrfp_app_order')
            ->count();

        //  dd($nextapprover);

        return view('rfp.detailrfpapp', ['data' => $data, 'norfp' => $norfp, 'approver' => $approver, 'nextapprover' => $nextapprover]);
    }

    public function searchpo(Request $req)
    {
        // dd($req->all());
        // dd($req->search);
        if ($req->ajax()) {
            // dd($req->all());
            $itemcode = $req->search;
            $datapo = DB::table('xpo_mstrs')
                ->join('xpod_dets', 'xpod_dets.xpod_nbr', '=', 'xpo_mstrs.xpo_nbr')
                ->where('xpod_part', '=', $itemcode)
                ->where(function ($query) {
                    $query->where('xpo_status', '=', 'UnConfirm')
                        ->orWhere('xpo_status', '=', 'Approved');
                })
                ->get();


            // dd($datapo);
            $output = '';

            if (count($datapo) != 0) {
                foreach ($datapo as $datapo) {

                    $output .= "<tr>" .

                        "<td>"
                        . $datapo->xpo_nbr .
                        "</td>" .

                        "<td>"
                        . $datapo->xpo_vend .
                        "</td>" .

                        "<td>"
                        . $datapo->xpod_qty_ord .
                        "</td>" .

                        "<td>"
                        . $datapo->xpo_due_date .
                        "</td>" .

                        "</tr>";
                }
                return Response($output);
            }
        }
    }

    public function histsearch(Request $req)
    {
        // dd($req->all());
        $rfpnbr = $req->rfp;
        $supp = $req->supp;
        $status = $req->status;
        $requestby = $req->enduser;
        $datefrom = $req->datefrom;
        $dateto = $req->dateto;

        // dd($req->all());

        if ($rfpnbr == null && $supp == null && $status == null && $requestby == null && $datefrom == null && $dateto == null) {
            $datas = DB::table('xrfp_hist')
                // ->groupBy('rfp_hist_nbr')
                ->orderBy('rfp_create_at', 'DESC')
                ->paginate(10);
            // echo $query;
            return view('rfp.loadhistrfp', ['histrfpdata' => $datas]);
        }

        if ($req->datefrom == null) {
            $datefrom = '2000-01-01';
        }

        if ($req->dateto == null) {
            $dateto = '3000-12-31';
        }


        try {
            $query = "due_date_dets BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

            if ($rfpnbr != null) {
                $query .= " and rfp_hist_nbr = '" . $rfpnbr . "'";
            }
            if ($supp != null) {
                $query .= " and rfp_hist_supp = '" . $supp . "'";
            }
            if ($status != null) {
                $query .= " and rfp_status = '" . $status . "'";
            }
            if ($requestby != null) {
                $query .= " and rfp_create_by = '" . $requestby . "'";
            }

            // dd($query);
            // echo $query;
            $datas = DB::table('xrfp_hist')
                ->whereRaw($query)
                // ->groupBy('rfp_hist_nbr')
                ->orderBy('rfp_create_at', 'DESC')
                ->paginate(10);

            // dd($datas);
            return view('rfp.loadhistrfp', ['histrfpdata' => $datas]);
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }


    public function viewhist()
    {
        try {
            $histrfpdata = DB::table('xrfp_hist')
                //->groupBy('rfp_hist_nbr')
                ->orderBy('rfp_create_at', 'DESC')
                ->paginate(10);

            return view('rfp.viewrfphist', ['histrfpdata' => $histrfpdata]);
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }

    public function searchdets(Request $req)
    {
        if ($req->ajax()) {
            $data = DB::table('xrfp_hist')
                ->join('xitemreq_mstr', 'xrfp_hist.itemcode_hist', '=', 'xitemreq_mstr.xitemreq_part')
                ->where('rfp_hist_nbr', '=', $req->search)
                ->orderBy('rfp_create_at', 'DESC')
                ->get();

            // dd($data);

            $output = '';

            if (count($data) != 0) {
                foreach ($data as $data) {

                    $output .= "<tr>" .

                        "<td>"
                        . $data->itemcode_hist .
                        "</td>" .

                        "<td>"
                        . $data->need_date_dets .
                        "</td>" .

                        "<td>"
                        . $data->due_date_dets .
                        "</td>" .

                        "<td>"
                        . $data->qty_order_hist .
                        "</td>" .

                        "<td>"
                        . $data->xitemreq_um .
                        "</td>" .

                        "<td>" .
                        $data->rfp_create_at .
                        "</td>" .

                        "</tr>";
                }
                return Response($output);
            }
        }
    }

    public function viewapphist(Request $req)
    {
        try {

            $test = DB::select('select approver1.id, approver1.xrfp_app_approver, approver1.name, approver1.xrfp_app_alt_approver, users.name as  "nama", approver1.xrfp_app_user, approver1.xrfp_app_reason, approver1.create_at, approver1.xrfp_app_status,approver1.xrfp_app_nbr,approver1.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
            from
            (select users.id, xrfp_app_trans.xrfp_app_approver, users.name,xrfp_app_trans.xrfp_app_alt_approver, xrfp_app_trans.xrfp_app_user,xrfp_app_trans.xrfp_app_reason,xrfp_app_trans.create_at,xrfp_app_trans.xrfp_app_status, xrfp_app_trans.xrfp_app_nbr,xrfp_app_trans.xrfp_app_order
                                 from xrfp_app_trans 
                                 join users 
                                 on xrfp_app_trans.xrfp_app_approver = users.id)approver1
                                 JOIN
                                 users on users.id = approver1.xrfp_app_alt_approver
                                 JOIN
                                 xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver1.xrfp_app_nbr								 
                                 UNION
                                select approver.id, approver.xrfp_app_approver, approver.name, approver.xrfp_app_alt_approver, users.name as  "nama", approver.xrfp_app_user, approver.xrfp_app_reason, approver.create_at , approver.xrfp_app_status,approver.xrfp_app_nbr,approver.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
            from
            (select users.id, xrfp_app_hist.xrfp_app_approver, users.name, xrfp_app_hist.xrfp_app_alt_approver,xrfp_app_hist.xrfp_app_user,xrfp_app_hist.xrfp_app_reason,xrfp_app_hist.create_at,xrfp_app_hist.xrfp_app_status, xrfp_app_hist.xrfp_app_nbr,xrfp_app_hist.xrfp_app_order
                                 from xrfp_app_hist 
                                 join users 
                                 on xrfp_app_hist.xrfp_app_approver = users.id)approver
                                 JOIN
                                 users on users.id = approver.xrfp_app_alt_approver
                                 JOIN
                                 xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver.xrfp_app_nbr
                                 ORDER BY rfpnbr,xrfp_app_order ASC
                                 ');

            return view('rfp.rfpauditapp', ['test' => $test]);
        } catch (\InvalidArgumentException $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
        } catch (\Exception $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
        } catch (\Error $ex) {
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }

    public function apphistsearch(Request $req)
    {
        if ($req->ajax()) {
            $rfpnbr = $req->rfp;
            // $supp = $req->supp;
            // $status = $req->status;
            // $requestby = $req->requestby;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            // dd($req->all());

            if ($rfpnbr == null &&  $datefrom == null && $dateto == null) {
                $test = DB::select('select approver1.xrfp_app_approver, approver1.name, approver1.xrfp_app_alt_approver, users.name as  "nama", approver1.xrfp_app_user, approver1.xrfp_app_reason, approver1.create_at, approver1.xrfp_app_status,approver1.xrfp_app_nbr,approver1.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
                from
               (select users.id, xrfp_app_trans.xrfp_app_approver, users.name,xrfp_app_trans.xrfp_app_alt_approver, xrfp_app_trans.xrfp_app_user,xrfp_app_trans.xrfp_app_reason,xrfp_app_trans.create_at,xrfp_app_trans.xrfp_app_status, xrfp_app_trans.xrfp_app_nbr,xrfp_app_trans.xrfp_app_order
                                from xrfp_app_trans 
                                join users 
                                on xrfp_app_trans.xrfp_app_approver = users.id)approver1
                                JOIN
                                users on users.id = approver1.xrfp_app_alt_approver
                                JOIN
                                xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver1.xrfp_app_nbr
                                UNION
                               select approver.xrfp_app_approver, approver.name, approver.xrfp_app_alt_approver, users.name as  "nama", approver.xrfp_app_user, approver.xrfp_app_reason, approver.create_at , approver.xrfp_app_status,approver.xrfp_app_nbr,approver.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
               from
               (select users.id, xrfp_app_hist.xrfp_app_approver, users.name, xrfp_app_hist.xrfp_app_alt_approver,xrfp_app_hist.xrfp_app_user,xrfp_app_hist.xrfp_app_reason,xrfp_app_hist.create_at,xrfp_app_hist.xrfp_app_status, xrfp_app_hist.xrfp_app_nbr,xrfp_app_hist.xrfp_app_order
                                from xrfp_app_hist 
                                join users 
                                on xrfp_app_hist.xrfp_app_approver = users.id)approver
                                JOIN
                                users on users.id = approver.xrfp_app_alt_approver
                                JOIN
                                xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver.xrfp_app_nbr
                                ORDER BY rfpnbr,xrfp_app_order ASC, create_at
                                ');
                // echo $query;
                return view('/rfp/loadapphist', ['test' => $test]);
            }

            if ($req->datefrom == null) {
                $datefrom = '2000-01-01';
            }

            if ($req->dateto == null) {
                $dateto = '3000-12-31';
            }


            try {
                $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";

                if ($rfpnbr != null) {
                    $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                }


                // dd($query);
                // echo $query;
                $test = DB::select('select approver1.xrfp_app_approver, approver1.name, approver1.xrfp_app_alt_approver, users.name as  "nama", approver1.xrfp_app_user, approver1.xrfp_app_reason, approver1.create_at, approver1.xrfp_app_status,approver1.xrfp_app_nbr,approver1.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
                 from
                (select users.id, xrfp_app_trans.xrfp_app_approver, users.name,xrfp_app_trans.xrfp_app_alt_approver, xrfp_app_trans.xrfp_app_user,xrfp_app_trans.xrfp_app_reason,xrfp_app_trans.create_at,xrfp_app_trans.xrfp_app_status, xrfp_app_trans.xrfp_app_nbr,xrfp_app_trans.xrfp_app_order
                                 from xrfp_app_trans 
                                 join users 
                                 on xrfp_app_trans.xrfp_app_approver = users.id)approver1
                                 JOIN
                                 users on users.id = approver1.xrfp_app_alt_approver
                                 JOIN
                                 xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver1.xrfp_app_nbr
                                 where ' . $query . '
                                 UNION
                                select approver.xrfp_app_approver, approver.name, approver.xrfp_app_alt_approver, users.name as  "nama", approver.xrfp_app_user, approver.xrfp_app_reason, approver.create_at , approver.xrfp_app_status,approver.xrfp_app_nbr,approver.xrfp_app_order, xrfp_mstrs.xrfp_nbr as "rfpnbr", xrfp_mstrs.created_by as "requestby", xrfp_mstrs.xrfp_duedate as "rfpduedate"
                from
                (select users.id, xrfp_app_hist.xrfp_app_approver, users.name, xrfp_app_hist.xrfp_app_alt_approver,xrfp_app_hist.xrfp_app_user,xrfp_app_hist.xrfp_app_reason,xrfp_app_hist.create_at,xrfp_app_hist.xrfp_app_status, xrfp_app_hist.xrfp_app_nbr,xrfp_app_hist.xrfp_app_order
                                 from xrfp_app_hist 
                                 join users 
                                 on xrfp_app_hist.xrfp_app_approver = users.id)approver
                                 JOIN
                                 users on users.id = approver.xrfp_app_alt_approver
                                 JOIN
                                 xrfp_mstrs on xrfp_mstrs.xrfp_nbr = approver.xrfp_app_nbr
                                 where ' . $query . '
                                 ORDER BY rfpnbr,xrfp_app_order ASC, create_at
                                 ');

                // dd($datas);
                return view('/rfp/loadapphist', ['test' => $test]);
            } catch (\InvalidArgumentException $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
            } catch (\Exception $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
            } catch (\Error $ex) {
                return back()->withError($ex->getMessage())->withInput();
                //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
            }
        }
    }

    public function viewutil(Request $req)
    {

        if (Session::get('user_role') == 'Admin') {
            $data = DB::table('xrfp_mstrs')
                ->orderBy('xrfp_nbr', 'ASC')
                ->where('status', '=', 'Rejected')
                ->paginate(10);


            return view('rfp.rfpappreset', ['data' => $data]);
        } elseif (Session::get('user_role') == 'Purchasing') {
            $data = DB::table('xrfp_mstrs')
                ->orderBy('xrfp_nbr', 'ASC')
                ->where('xrfp_dept', '=', Session::get('department'))
                ->where('created_by', '=', Session::get('username'))
                ->where('status', '=', 'Rejected')
                ->paginate(10);


            return view('rfp.rfpappreset', ['data' => $data]);
        }
    }

    public function utilrfpsearch(Request $req)
    {
        //dd($req->all());
        if ($req->ajax()) {
            $rfpnbr = $req->rfp;
            $supp = $req->supp;
            $enduser = $req->enduser;
            $datefrom = $req->datefrom;
            $dateto = $req->dateto;

            // dd($req->all());

            if (Session::get('user_role') == 'Admin') {

                if ($rfpnbr == null && $supp == null && $enduser == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        ->where('status', '=', 'Rejected')
                        ->paginate(10);
                    // echo $query;
                    return view('/rfp/tablerfpreset', ['data' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";
                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    if ($enduser != null) {
                        $query .= " and xrfp_enduser = '" . $enduser . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        ->where('status', '=', 'Rejected')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('/rfp/tablerfpreset', ['data' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            } elseif (Session::get('user_role') == 'Purchasing') {
                if ($rfpnbr == null && $supp == null && $enduser == null && $datefrom == null && $dateto == null) {
                    $datas = DB::table('xrfp_mstrs')
                        ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('created_by', '=', Session::get('username'))
                        ->where('status', '=', 'Rejected')
                        ->paginate(10);
                    // echo $query;
                    return view('/rfp/tablerfpreset', ['data' => $datas]);
                }

                if ($req->datefrom == null) {
                    $datefrom = '2000-01-01';
                }

                if ($req->dateto == null) {
                    $dateto = '3000-12-31';
                }


                try {
                    $query = "xrfp_duedate BETWEEN '" . $datefrom . "' and '" . $dateto . "' ";
                    if ($rfpnbr != null) {
                        $query .= " and xrfp_nbr = '" . $rfpnbr . "'";
                    }
                    if ($supp != null) {
                        $query .= " and xrfp_supp = '" . $supp . "'";
                    }
                    if ($enduser != null) {
                        $query .= " and xrfp_enduser = '" . $enduser . "'";
                    }

                    // echo $query;
                    $datas = DB::table('xrfp_mstrs')
                        ->where('xrfp_dept', '=', Session::get('department'))
                        ->where('created_by', '=', Session::get('username'))
                        ->where('status', '=', 'Rejected')
                        ->whereRaw($query)
                        ->paginate(10);

                    // dd($datas);
                    return view('/rfp/tablerfpreset', ['data' => $datas]);
                } catch (\InvalidArgumentException $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                } catch (\Exception $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                } catch (\Error $ex) {
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
            }
        }
    }

    public function resetrfpapp(Request $req)
    {
        // dd($req->all());

        DB::table('xrfp_mstrs')
            ->where('xrfp_nbr', '=', $req->t_rfpnbr)
            ->update([
                'status' => 'New Request'
            ]);


        $listapprover = DB::table('xrfp_control')
            ->join('xrfp_mstrs', 'xrfp_mstrs.xrfp_dept', '=', 'xrfp_control.rfp_department')
            ->where('xrfp_mstrs.xrfp_nbr', '=', $req->t_rfpnbr)
            ->whereRaw('xorder > 0')
            // ->where('rfp_department', '=', Session::get('department'))
            ->orderBy('xorder', 'ASC')
            ->get();
        // dd($listapprover);
        $i = 1;

        foreach ($listapprover as $listapprover) {

            DB::table('xrfp_app_trans')
                ->insert([
                    'xrfp_app_nbr' => $req->t_rfpnbr,
                    'xrfp_app_approver' => $listapprover->xrfp_approver,
                    'xrfp_app_order' => $i,
                    'xrfp_app_status' => '0',
                    'xrfp_app_alt_approver' => $listapprover->xrfp_alt_app,
                    'create_at' => Carbon::now()->toDateTimeString()
                ]);

            $i++;
        }

        // session()->flash('updated', "RFP Number : ".$req->t_rfpnbr." status reset");
        alert()->success('Success', 'RFP Number : ' . $req->t_rfpnbr . ' status reset');
        return back();
    }
}
