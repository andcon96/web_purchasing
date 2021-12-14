<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SjmtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $sj = DB::table('xsj_mstr')->paginate(5);
    //   dd($sj);                    
      return view('/sj/sjmt',['sjmt'=>$sj]);
     
    }

    public function crt(Request $req)
    {
     $id = $req->input('id');
     $supp = $req->input('supp');
     
     $sj1 = DB::table('xsj_mstr')->where('xsj_id', $id)->get();       
     $po = DB::table('xpo_mstr')->where('xpo_vend',$supp)->get();               
     return view('/sj/sjcrt',['sjmt'=>$sj1],['pomt'=>$po]);                  
    }
    
    
     public function line(Request $request)
        {
     $select = $request->get('select');
     $value = $request->get('value');
     $dependent = $request->get('dependent');
     $data = DB::table('xpod_det')
       ->where($select, $value)
       ->groupBy($dependent)
       ->get();
       dd($data);
     $output = '<option value="">Select '.ucfirst($dependent).'</option>';
     foreach($data as $row)
     {
      $output .= '<option value="'.$row->$dependent.'">'.$row->$dependent.'</option>';
     }
     echo $output;
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
     
      public function cari(Request $req)
    {     
        $id = $req->input('id');
        $nbr =  $req->input('nbr');
        $line = $req->input('line');  
                  
        $pod1 = DB::table('xpod_det')->where('xpod_nbr', $nbr)->where('xpod_line', $line)->get();       
        return view('sj/sjcrtdet',['poddet'=>$pod1],['idx'=>$id]); 

    } 
    
    
    public function search(Request $req)
    {
        
        if($req->ajax()){
            $shippingid = $req->shippingid;
            
            // dd($req->all());

            
                
                if($shippingid == null){
                    $datas = DB::table('xsj_mstr')
                                ->paginate(5);
                    // echo $query;
                    return view('/sj/tablesjmt',['sjmt'=>$datas]);
                }
    
    
                try{
                   
    
                    // echo $query;
                    $datas = DB::table('xsj_mstr')
                            ->where('xsj_id','like',"%".$shippingid."%")
                            ->paginate(5);
    
                    // dd($datas);
                     return view('/sj/tablesjmt',['sjmt'=>$datas]);
    
                }catch(\InvalidArgumentException $ex){
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Data file xls yang diupload salah']);
                }catch(\Exception $ex){
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan format sesuai dengan template']);
                }catch(\Error $ex){
                    return back()->withError($ex->getMessage())->withInput();
                    //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
                }
    
        }
          
    //  $id = $req->input('id');
    //  $sj1 = DB::table('xsj_mstr')->where('xsj_id','like',"%".$id."%")->get();       
                    
    //  return view('/sj/sjmt',['sjmt'=>$sj1]);                  
       
        
    } 
    public function edit(Request $req)
    {     
        $id = $req->input('id');
        $nbr =  $req->input('nbr');
        $line = $req->input('line');  
        $lot = $req->input('lot');        
        $pod1 = DB::table('xsj_mstr')->where('xsj_id', $id)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)->where('xsj_lot', $lot)->get();       
        return view('sj/sjmtedt',['sjmt'=>$pod1]); 
       
        
    } 
    
    public function save(Request $req)
    {     
		dd('123');
        $id = $req->input('id');
        $nbr =  $req->input('nbr');
        $supp =  $req->input('supp');
        $line = $req->input('line');  
        $due = $req->input('due');  
        $part = $req->input('part');
        $desc = $req->input('desc');
        $ord = $req->input('ord');
        $opn = $req->input('opn');
        $ship = $req->input('ship');
        $loc = $req->input('loc');
        $lot = $req->input('lot');
        $ref = $req->input('ref');
        $conf = $req->input('conf');     
        $open = $opn - $ship;        
     
        $data1 = array('xsj_id'=>$id,
                    'xsj_po_nbr'=>$nbr,
                    'xsj_supp'=>$supp,
                    'xsj_part'=>$part,      
                    'xsj_line'=>$line, 
                    'xsj_desc'=>$desc,
                    'xsj_qty_ord'=>$ord,
                    'xsj_qty_open'=>$open,
                    'xsj_qty_ship'=>$ship,                  
                    'xsj_loc'=>$loc,
                    'xsj_lot'=>$lot,
                    'xsj_ref'=>$ref,                    
                    'xsj_status'=>$conf, 
                );
                
                
         $data2 = array('xpod_qty_open'=>$open, 
                );
                
     $supp = $req->input('supp');         
     $po = DB::table('xpo_mstr')->where('xpo_vend',$supp)->get();         
        
        DB::table('xpod_det')->where('xpod_nbr',$nbr )->where('xpod_line', $line)->update($data2);
         
        DB::table('xsj_mstr')->insert($data1);
         
         
         $sj = DB::table('xsj_mstr')->where('xsj_id', $id)->get();       
    	   return view('sj/sjcrt',['sjmt'=>$sj],['pomt'=>$po]); 
       
        
    } 
    
      public function upd(Request $req)
    {     
    
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
        $conf = $req->input('conf');        
        $open = $opn + $ship1 - $ship;  
     
        $data1 = array(
                    'xsj_qty_ship'=>$ship,	
                    'xsj_qty_open'=>$open,
                    'xsj_loc'=>$loc,
                    'xsj_lot'=>$lot,
                    'xsj_ref'=>$ref,                    
                    'xsj_status'=>$conf, 
                );
                
                
        DB::table('xsj_mstr')->where('xsj_id', $id)->where('xsj_po_nbr', $nbr)->where('xsj_line', $line)
        ->where('xsj_lot', $lot)->update($data1);
        
        $sj1 = DB::table('xsj_mstr')->where('xsj_id', $id)->get();                          
        return view('/sj/sjcrt',['sjmt'=>$sj1]);                  
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
