<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Hashing\BcryptHasher;

use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //session(['url.intended' => url()->previous()]);
        //$this->redirectTo = session()->get('url.intended');
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'username';
    }

    protected function authenticated(Request $request)
    {
        // Ambil Username Session
        $username = $request->input('username');
        $id = Auth::id();
        
        // Set Session Username & ID
        Session::put('userid', $id);
        $request->session()->put('username', $username);
        
        // Konek ke role buat Menu Access & supp_code
        $users = DB::table("users")
                    ->join("xxrole_mstrs",'xxrole_mstrs.xxrole_role','=','users.role_type')
                    ->where("users.id",$id)
                    ->first();

        // set Session Flag buat Menu Access
        if($users == ''){
            Auth::logout();
            return redirect()->back()->with(['error'=>'Pastikan Role User sudah dibuat, Silahkan kontak Admin']);
        }else{
            Session::put('menu_flag', $users->xxrole_flag);
            Session::put('supp_code', $users->supp_id);
            Session::put('user_role', $users->role);
            Session::put('domain', $users->domain);
            Session::put('department', $users->department);
            Session::put('name', $users->name);
            
            Session::put('username',$users->username);
            
            //Log::channel('userLogin')->info('User '.$username.' Berhasil Login');
            
            if(session()->get('url.now') != null){
                // buat redirect ke prev url klo ada.
                return redirect(session()->get('url.now'));
            }
        }
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $previous_session = Auth::User()->session_id;
        if ($previous_session) {
            \Session::getHandler()->destroy($previous_session);
        }

        Auth::user()->session_id = \Session::getId();
        
        Auth::user()->save();
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $data = DB::table('users')
                    ->where('username','=',$request->username)
                    ->get();
     
        if(count($data) == 0){
            return redirect()->back()->with(['error'=>'Username salah / tidak terdaftar']);
        }

        $hasher = app('hash');

        $users = DB::table("users")
                    ->select('id','password')
                    ->where("users.username",$request->username)
                    ->first();

        if(!$hasher->check($request->password,$users->password))
        {   
            return redirect()->back()->with(['error'=>'Password salah']);
        }
    }

}
