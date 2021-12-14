<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class SjmtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function browse()
    {
        if (Session::get('supp_code') != null) {
            $sj = DB::table('xsj_mstr')->where("xsj_mstr.xsj_supp", "=", Session::get('supp_code'))
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(10);
        } else {
            $sj = DB::table('xsj_mstr')
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(10);
        }

        return view('/sj/sjmtbrw', ['sjmt' => $sj]);
    }

    public function index()
    {
        if (Session::get('supp_code') != null) {
            $sj = DB::table('xsj_mstr')
                ->where("xsj_mstr.xsj_supp", "=", Session::get('supp_code'))
                ->where('xsj_status', "Created")
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(10);
        } else {
            $sj = DB::table('xsj_mstr')
                ->where('xsj_status', "Created")
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(10);
        }

        return view('/sj/sjmt', ['sjmt' => $sj]);
    } /*public*/

    /*=====save sjcrtdet=======*/
    public function crt(Request $req)
    {
        $sj   = $req->input('sj');
        $id   = $req->input('id');
        $nbr  = $req->input('nbr');
        $conf = "confirmed";

        $sjmt = DB::table('xsj_mstr')->where('xsj_sj', $sj)
            ->join("xpod_dets", function ($join) {
                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
            })->get();

        if (Session::get('supp_code') != null) {
            $country_list = DB::table('xpo_mstrs')
                ->where("xpo_mstrs.xpo_vend", "=", Session::get('supp_code'))
                ->where('xpo_status', $conf)->get();
        } else {
            $country_list = DB::table('xpo_mstrs')
                ->where('xpo_status', $conf)->get();
        }

        return view('sj/sjcrt', compact('sjmt', 'country_list', 'id', 'nbr', 'sj'));
    }

    /*========Display sjcrtdet ========*/
    public function cari(Request $req)
    {
        $id = $req->input('id');
        $sj = $req->input('sj');
        $nbr =  $req->input('xpod_nbr');
        $conf = "confirmed";
        $shp = "0";

        $data1 = array(
            'xpod_qty_shipx' => $shp,
        );

        DB::table('xpod_dets')->where('xpod_nbr', $nbr)
            ->update($data1);

        $poddet = DB::table('xpod_dets')
            ->where('xpod_nbr', $nbr)
            ->where('xpod_status', $conf)
            ->join('xpo_mstrs', 'xpod_nbr', '=', 'xpo_nbr')->get();

        return view('sj/sjcrtdet', compact('poddet', 'id', 'nbr', 'sj'));
    }

    public function searchbrw(Request $req){

        if ($req->ajax()) {
            $shippingid = $req->shippingid;

            // dd($req->all());



            if ($shippingid == null) {
                    if (Session::get('supp_code') != null) {
                        $sj = DB::table('xsj_mstr')->where("xsj_mstr.xsj_supp", "=", Session::get('supp_code'))
                            ->join("xpod_dets", function ($join) {
                                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                            })
                            ->paginate(10);
                    } else {
                        $sj = DB::table('xsj_mstr')
                            ->join("xpod_dets", function ($join) {
                                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                            })
                            ->paginate(10);
                    }


                    return view('/sj/tablesjmtbrw', ['sjmt' => $sj]);

            }
                // echo $query;

            try {


                // echo $query;
                $sj = DB::table("xsj_mstr")
                    ->join("xpod_dets", function ($join) {
                        $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                            ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                    })
                    ->whereraw("xsj_mstr.xsj_id like '" . $shippingid . "%'")
                    ->paginate(10);

                // dd($datas);
                return view('/sj/tablesjmtbrw', ['sjmt' => $sj]);
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


    public function search(Request $req)
    {
        //    $id = $req->input('id');

        // $sj=DB::table("xsj_mstr")        
        //     ->join("xpod_dets", function($join){
        //     $join->on('xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
        //            ->on('xsj_mstr.xsj_line','=','xpod_dets.xpod_line');										 
        //     })       
        //     ->whereraw("xsj_mstr.xsj_id like '".$id."%'")         
        //     ->paginate(10);   

        //           return view('/sj/sjmt',['sjmt'=>$sj]);   

        if ($req->ajax()) {
            $shippingid = $req->shippingid;

            // dd($req->all());



            if ($shippingid == null) {
                if (Session::get('supp_code') != null) {
                    $sj = DB::table('xsj_mstr')
                        ->where("xsj_mstr.xsj_supp", "=", Session::get('supp_code'))
                        ->where('xsj_status', "Created")
                        ->join("xpod_dets", function ($join) {
                            $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                                ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                        })
                        ->paginate(10);
                } else {
                    $sj = DB::table('xsj_mstr')
                        ->where('xsj_status', "Created")
                        ->join("xpod_dets", function ($join) {
                            $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                                ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                        })
                        ->paginate(10);
                }
                // echo $query;
                return view('/sj/tablesjmt', ['sjmt' => $sj]);
            }


            try {


                // echo $query;
                $sj = DB::table("xsj_mstr")
                    ->join("xpod_dets", function ($join) {
                        $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                            ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                    })
                    ->whereraw("xsj_mstr.xsj_id like '" . $shippingid . "%'")
                    ->paginate(10);

                // dd($datas);
                return view('/sj/tablesjmt', ['sjmt' => $sj]);
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




    private function httpHeader($req)
    {
        return array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',


            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req))
        );
    }

    public function edit(Request $req)
    {
        $sj = $req->input('sj');
        $id = $req->input('id');
        $nbr =  $req->input('nbr');
        $line = $req->input('line');
        $lot = $req->input('lot');
        $sta = "Created";


        $sj1 = DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)
            ->where('xsj_status', $sta)->where('xsj_lot', $lot)
            ->join("xpod_dets", function ($join) {
                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
            })
            ->get();

        return view('sj/sjmtedt', ['sjmt' => $sj1]);
    }


    /*====simpan data Surat Jalan====*/
    public function save(Request $req)
    {
        $sj = $req->input('t_sj');
        $id = $req->input('t_id');
        $nbr =  $req->input('t_nbr');
        $supp =  $req->input('t_supp');
        $line = $req->input('t_line');
        $due = $req->input('t_due');
        $part = $req->input('t_part');
        $desc = $req->input('t_desc');
        $ord = $req->input('t_ord');
        $opn = $req->input('t_opn');
        $shipx = $req->input('t_shipx');
        $shipx1 = $req->input('t_shipx1');
        $loc = $req->input('t_loc');
        $lot = $req->input('t_lot');
        $ref = $req->input('t_ref');
        $ship = $req->input('t_ship');
        $conf = "Created";
        $open = $opn - $shipx;
        $qtyship = $ship + $shipx;
        $open_pod =  $ord - $qtyship;
        $open_pod1 =  $ord + $shipx1 - $qtyship;
        $qtyship1 = $ship - $shipx1 + $shipx;

        if ($shipx < 1) {
            //    session()->flash("updated","ERROR:QTY Less From Zero!");
            alert()->error('Error', 'Qty Less than Zero');
            return redirect()->back();
        }

        $runnbr = DB::table('xsj_ctrl')->get();

        foreach ($runnbr as $run) {
            $pref = $run->xsj_pref;
            $runbr = str_pad($run->xsj_run + 1, 3, "0", STR_PAD_LEFT);
            $bln1 = $run->xsj_per;
        }

        $bln = date("m");
        if ($sj == null) {

            if ($bln == $bln1) {
                $sj = $pref . date("ym") . ($runbr);
                DB::table('xsj_ctrl')->update(['xsj_run' => $runbr]);
            } else {
                $runbr = "001";
                $sj = $pref . date("ym") . ($runbr);
                DB::table('xsj_ctrl')->update(['xsj_run' => $runbr, 'xsj_per' => $bln]);
            }
        }

        $data1 = array(
            'xsj_sj' => $sj,
            'xsj_id' => $id,
            'xsj_po_nbr' => $nbr,
            'xsj_supp' => $supp,
            'xsj_part' => $part,
            'xsj_line' => $line,
            'xsj_desc' => $desc,
            'xsj_qty_ord' => $ord,
            'xsj_qty_open' => $open_pod,
            'xsj_qty_ship' => $shipx,
            'xsj_loc' => $loc,
            'xsj_lot' => $lot,
            'xsj_ref' => $ref,
            'xsj_status' => $conf,
        );

        $data2 = array(
            'xpod_qty_open' => $open_pod,
            'xpod_qty_ship' => $qtyship,
            'xpod_qty_shipx' => $shipx,
        );

        $data3 = array(
            'xpod_qty_open' => $open_pod1,
            'xpod_qty_ship' => $qtyship1,
            'xpod_qty_shipx' => $shipx,
        );



        if ($qtyship > $ord) {
            // session()->flash("updated","ERROR : ship qty passes Order Qty!");
            alert()->error('Error', 'Ship Qty Passes Order Qty');
        } else {
            // session()->flash("updated","Successfully Confirmed");
            alert()->success('Success', 'Successfully Confirmed');
            DB::table('xpod_dets')->where('xpod_nbr', $nbr)->where('xpod_line', $line)
                ->update($data2);

            $checksj = DB::table('xsj_mstr')
                ->where('xsj_sj', $sj)
                ->where('xsj_id', $id)
                ->where('xsj_line', $line)
                ->where('xsj_po_nbr', $nbr)
                ->where('xsj_lot', $lot)
                ->first();

            if ($checksj) {
                DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)
                    ->where('xsj_lot', $lot)->update($data1);
                DB::table('xpod_dets')->where('xpod_nbr', $nbr)->where('xpod_line', $line)
                    ->update($data3);
            } else {
                DB::table('xsj_mstr')->insert($data1);
            }
        }
        $poddet = DB::table('xpod_dets')
            ->where('xpod_nbr', $nbr)
            ->where('xpod_status', 'confirmed')
            ->join('xpo_mstrs', 'xpod_nbr', '=', 'xpo_nbr')->get();

        return view('sj/sjcrtdet', compact('poddet', 'id', 'nbr', 'sj'));
    }


    /*===Delete Record SJ=====*/
    public function deledt(Request $req)
    {
        $id   = $req->input('delete_id');
        $sj  = $req->input('t_sj');
        $nbr  = $req->input('t_nbr');
        $line = $req->input('t_line');
        $lot  = $req->input('t_lot');
        $opn  = $req->input('t_opn');
        $shp  = $req->input('t_shp');
        $qshp = $req->input('t_qship');
        $supp = $req->input('t_supp');
        $conf = "Confirmed";
        $qtyship  = $shp - $qshp;
        $open_pod = $opn + $qshp;

        //dd($req->all());

        $data2 = array(
            'xpod_qty_open' => $open_pod,
            'xpod_qty_ship' => $qtyship,
        );

        DB::table('xpod_dets')->where('xpod_nbr', $nbr)->where('xpod_line', $line)
            ->update($data2);

        DB::table('xsj_mstr')
            ->where('xsj_sj', $sj)
            ->where('xsj_id', $id)
            ->where('xsj_supp', $supp)
            ->where('xsj_po_nbr', $nbr)
            ->where('xsj_line', $line)
            ->where('xsj_lot', $lot)
            ->delete();

        if (Session::get('supp_code') != null) {
            $country_list = DB::table('xpo_mstrs')
                ->where("xpo_mstrs.xpo_vend", "=", Session::get('supp_code'))
                ->where('xpo_status', $conf)->get();
        } else {
            $country_list = DB::table('xpo_mstrs')
                ->where('xpo_status', $conf)->get();
        }

        $sjmt = DB::table('xsj_mstr')
            ->where('xsj_supp', $supp)
            ->where('xsj_sj', $sj)
            ->where('xsj_id', $id)
            ->join("xpod_dets", function ($join) {
                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
            })
            ->get();

        return view('sj/sjcrt', compact('sjmt', 'country_list', 'id', 'nbr', 'sj'));
    }

    public function delete(Request $req)
    {
        $id   = $req->input('delete_id');
        $sj   = $req->input('sj');
        $nbr  = $req->input('t_nbr');
        $line = $req->input('t_line');
        $lot  = $req->input('t_lot');
        $opn  = $req->input('t_opn');
        $shp  = $req->input('t_shp');
        $qshp = $req->input('t_qship');
        $supp = $req->input('t_supp');
        $qtyship  = $shp - $qshp;
        $open_pod = $opn + $qshp;

        //dd($req->all());

        $data2 = array(
            'xpod_qty_open' => $open_pod,
            'xpod_qty_ship' => $qtyship,
        );
        DB::table('xpod_dets')->where('xpod_nbr', $nbr)->where('xpod_line', $line)
            ->update($data2);


        DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)->where('xsj_supp', $supp)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)->where('xsj_lot', $lot)->delete();
        return redirect()->back();
    }

    public function upd(Request $req)
    {
        $sj = $req->input('sj');
        $id = $req->input('id');
        $nbr =  $req->input('nbr');
        $line = $req->input('line');
        $due = $req->input('due');
        $ord = $req->input('ord');
        $opn = $req->input('opn');
        $ship = $req->input('ship');
        $ship1 = $req->input('ship1');
        $loc = $req->input('loc');
        $lot = $req->input('lot');
        $ref = $req->input('ref');
        $conf = "Confirmed";
        $open = $opn + $ship1 - $ship;

        $dataship = $req->input('qship1');

        $qtyshp = $dataship - $ship1 + $ship;

        $data1 = array(
            'xsj_qty_ship' => $ship,
            'xsj_qty_open' => $open,
            'xsj_loc' => $loc,
            'xsj_lot' => $lot,
            'xsj_ref' => $ref,

        );

        $data2 = array(
            'xpod_qty_open' => $open,
            'xpod_qty_ship' => $qtyshp,
        );

        DB::table('xpod_dets')->where('xpod_nbr', $nbr)->where('xpod_line', $line)
            ->update($data2);

        DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)
            ->where('xsj_lot', $lot)->update($data1);
        if (Session::get('supp_code') != null) {

            $country_list = DB::table('xpo_mstrs')->where("xpo_mstrs.xpo_vend", "=", Session::get('supp_code'))
                ->where('xpo_status', $conf)->get();
        } else {

            $country_list = DB::table('xpo_mstrs')
                ->where('xpo_status', $conf)->get();
        }
        $sjmt = DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)
            ->join("xpod_dets", function ($join) {
                $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                    ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
            })->get();
        return view('sj/sjcrt', compact('sjmt', 'country_list', 'id', 'nbr', 'sj'));
    }

    public function cancel(Request $req)
    {
        $id = $req->input('id');
        $sj = $req->input('sj');
        $sjmt = DB::table('xsj_mstr')->where('xsj_sj', $sj)->where('xsj_id', $id)->get();

        foreach ($sjmt as $show) {
            $poddet = DB::table('xpod_dets')->where('xpod_dets.xpod_nbr', '=', $show->xsj_po_nbr)
                ->where('xpod_dets.xpod_line', '=', $show->xsj_line)->first();

            if ($poddet) {
                $data1 = array(
                    'xpod_qty_ship' => $poddet->xpod_qty_ship  - $show->xsj_qty_ship,
                    'xpod_qty_open' => $poddet->xpod_qty_open  + $show->xsj_qty_ship,
                );

                DB::table('xpod_dets')->where('xpod_dets.xpod_nbr', '=', $show->xsj_po_nbr)
                    ->where('xpod_dets.xpod_line', '=', $show->xsj_line)
                    ->update($data1);

                DB::table('xsj_mstr')->where('xsj_sj', $sj)->delete();
            }
        }

        if (Session::get('supp_code') != null) {

            $sj = DB::table('xsj_mstr')->where("xsj_mstr.xsj_supp", "=", Session::get('supp_code'))
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(15);
        } else {
            $sj = DB::table('xsj_mstr')
                ->join("xpod_dets", function ($join) {
                    $join->on('xsj_mstr.xsj_po_nbr', '=', 'xpod_dets.xpod_nbr')
                        ->on('xsj_mstr.xsj_line', '=', 'xpod_dets.xpod_line');
                })
                ->paginate(15);
        }

        return view('/sj/sjmt', ['sjmt' => $sj]);
    }

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
