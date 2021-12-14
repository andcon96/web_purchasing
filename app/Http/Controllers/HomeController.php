<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
use Auth;

use Carbon\Carbon;
use GuzzleHttp\Client;
// use GuzzleHttp\Message\RequestGuzzle;

class HomeController extends Controller
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

        // $i = true;
        // $s = 0;

        // while($i == 'true'){
        //     $s++;
        // }


        // $client = new Client();
        // $res = $client->request('POST', 'http://127.0.0.1:8002/api/article', [
        //     'form_params' => [
        //         'title' => 'Create title Web lain',
        //         "body" => "Create body Web Lain"
        //     ]
        // ]);

        // echo $res->getStatusCode();
        // // 200 Success 201 Created

        // echo $res->getBody();

        // $object = json_decode($res->getBody());
        
        // $i = 0;

        // foreach($object->data as $obj){
        //     $i++;
        // }

        // dd($object,$i,$object->data);

        return view("home", ["users" => $users] );
    }
}
