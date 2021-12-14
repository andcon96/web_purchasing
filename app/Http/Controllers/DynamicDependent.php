<?php

//DynamicDepdendent.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class DynamicDependent extends Controller
{
    function index(Request $req)
    {
        //dd($req->all());
        $id = $req->input('id');
        $sj = "";
        $supp = $req->input('supp');
        $conf = "Confirmed";
        $sjmt = DB::table('xsj_mstr')->where('xsj_supp', $supp)->where('xsj_id', $id)
                ->join("xpod_dets", function($join){
                                        $join->on('xsj_mstr.xsj_po_nbr','=','xpod_dets.xpod_nbr')
                                            ->on('xsj_mstr.xsj_line','=','xpod_dets.xpod_line');
                                            
                                    })->get();       
        $id = "";
        $nbr= "";
        if($supp == ''){
                    $country_list = DB::table('xpo_mstrs')
                        //->where('xpo_vend',$supp)
                        ->where('xpo_status',$conf)						
                        ->get();    
        }else{
                    $country_list = DB::table('xpo_mstrs')
                        ->where('xpo_vend',$supp)
                        ->where('xpo_status',$conf)						
                        ->get();
        }

        //dd($po);
           
   
     return view('/sj/sjcrt',compact('sjmt','country_list','id','nbr','sj'));
    }

    function fetch(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');

        $dependent = $request->get('dependent');
        $data = DB::table('xpod_dets')->where('xpod_status','Confirmed')
        ->where($select, $value)
        ->get();
        $output = '<option value="">Select '.ucfirst($dependent).'</option>';
        
        foreach($data as $row)
        {
        $output .= '<option value="'.$row->$dependent.'">'.$row->$dependent.'</option>';
        }
        echo $output;

    }
}

?>