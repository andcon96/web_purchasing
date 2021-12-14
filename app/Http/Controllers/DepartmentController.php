<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(){
        $dept = DB::table('xdepartment')
                    ->get();
                    
        return view('/setting/deptmaint', ['depts'=>$dept]);
    }

    public function updatedept(Request $req){
        DB::table('xdepartment')
            ->where('id', '=', $req->e_id)
            ->update([
                'xdept' => $req->e_xdept,
                'xdept_desc' => $req->e_xdept_desc,
            ]);


        // session()->flash("updated", "Department Successfully Updated !");;
        alert()->success('Success','Department Successfully Updated');

        return back();
    }

    public function update(Request $req){

        $this->validate($req,[
            'xdept' => 'required|unique:xdepartment',
            'xdept_desc' => 'required|unique:xdepartment'
        ],[
            'xdept.unique' => 'Department Name Already Exists',
            'xdept_desc.unique' => 'Department Description Already Exists',
        ]);

        $deptname = $req->input('xdept');
        $deptdesc = $req->input('xdept_desc');

        $data = array('xdept'=>($deptname),
                    'xdept_desc'=>($deptdesc),
        );

        DB::table('xdepartment')->insert($data);

        // session()->flash("updated", "Department Successfully Created !");;
        alert()->success('Success','Department Successfully Created');
        return back();

    }

    public function deletedept(Request $req){
        // dd($req->all());
        DB::table('xdepartment')
            ->where('id', '=', $req->temp_id)
            ->delete();

        // session()->flash("deleted", "Department Successfully Deleted !");;
        alert()->success('Success','User Successfully Deleted');
        return back();
    }
}
