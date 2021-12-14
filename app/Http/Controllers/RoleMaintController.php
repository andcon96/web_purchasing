<?php

namespace App\Http\Controllers;

use App\XxroleMstr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class RoleMaintController extends Controller
{

    public function index()
    {
        $rolecrt = DB::select('select * from xxrole_mstrs');  
        $domain = DB::table('xdomain_mstr')
                    ->distinct() 
                    ->get();
        $edomain = DB::table('xdomain_mstr')
                    ->distinct() 
                    ->get();

        //return view('/setting/rolecreate',['rolecrt'=>$rolecrt]);
        return view('/setting/rolecreate', compact('rolecrt','domain','edomain'));
    }

    public function updaterole(Request $req)
    {
        //dd($req->all());
        db::table('xxrole_mstrs')
            ->where('xxrole_mstrs.id','=',$req->e_id)
            ->update([
                'xxrole_domain' => $req->e_domain,
                'xxrole_role' => $req->e_role,
                'xxrole_desc' => $req->e_desc,
            ]);

        // session()->flash("updated","Role Information Successfully Updated !");
        alert()->success('Success','Role Information Successfully Updated !');
              
        return back();
    }
    
    public function update(Request $req)
    {     
        //dd($req->all());
        $validator = Validator::make($req->all(),[
            'role' => 'Required|max:15'
        ]);
    
        $domain = $req->input('domain');
        $role = $req->input('role');
        $class = $req->input('class');
        $desc = $req->input('desc');        

        $data1 = array('xxrole_domain'=>$domain,
                    'xxrole_type'=>$role,
                    'xxrole_role'=>$class,
                    'xxrole_desc'=>$desc,                    
                );
        
        DB::table('xxrole_mstrs')->insert($data1);
        // session()->flash("updated","Role Successfully Created !");
        alert()->success('Success','Role Successfully Created');
              
        return back();
    }

    public function destroy(Request $request)
    {
        DB::table('xxrole_mstrs')->where('id','=',$request->temp_id)->delete();

        // session()->flash("deleted","Role Successfully Deleted !");
        alert()->success('Success','Role Successfully Deleted');
        return back();
    }


}
