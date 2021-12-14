<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class WSAController extends Controller
{
    //
    public function index(){
        $data = DB::table('wsas')->first();

        return view('setting.wsas',['data' => $data]);
    }

    public function wsaupdate(Request $req){
        if($req->qxenable == 1){
            if($req->qxurl == '' || $req->qxpath == ''){
                alert()->success('error','Mohon isi URL & Path Qxtend');
                return back();
            }
        }

        DB::table('wsas')
                ->update([
                    'wsas_domain' => $req->domain,
                    'wsas_url' => $req->wsaurl,
                    'wsas_path' => $req->wsapath,
                    'qx_enable' => $req->qxenable,
                    'qx_url' => $req->qxurl,
                    'qx_path' => $req->qxpath,
                ]);

        alert()->success('Success','Data Succesfully Updated');
        return back();
        
    }
}
