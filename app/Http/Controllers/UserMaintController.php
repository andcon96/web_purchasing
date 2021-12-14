<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Hashing\BcryptHasher;
use Carbon\Carbon;

class UserMaintController extends Controller
{

    public function index(Request $req)
    {
        $users = DB::table('users')
                        ->leftJoin('xalert_mstrs','users.supp_id','=','xalert_mstrs.xalert_supp')
                        ->orderBy('users.role','ASC')
                        ->paginate(10);
        
        $domain = DB::table('xdomain_mstr')
                    ->distinct() 
                    ->get();
        $supp = DB::table('xalert_mstrs')
                    ->where('xalert_mstrs.xalert_active','=','Yes')
                    ->get();
        $supp2 = DB::table('xalert_mstrs')
                    ->where('xalert_mstrs.xalert_active','=','Yes')
                    ->get();

        // dd($supp);

        $dept = DB::table('xdepartment')
                    ->get();
        
        if($req->ajax()){
            // dd($users);
            $username = $req->username;
            $name = $req->name;

            if($username == "" && $name == ""){
                
            }else{
                // $query = "";

                // if($username != null){
                //     $query .= " and xrfp_nbr = '".$rfpnbr."'";
                // }
                // if($name != null){
                //     $query .= " and xrfp_supp = '".$supp."'";
                // }

                $users = DB::table('users')
                        ->leftJoin('xalert_mstrs','users.supp_id','=','xalert_mstrs.xalert_supp')
                        ->where('username', 'like', '%'.$username.'%')
                        ->where('name', 'like', '%'.$name.'%')
                        ->orderBy('users.role','ASC')
                        ->paginate(10);


            }

            return view('/setting/tableusermaint', compact('users','domain','supp','dept','supp2'));
        }else{
            return view('/setting/usermaint', compact('users','domain','supp','dept','supp2'));
        }

        
    }

    public function searchnamasupp(Request $req){
        if($req->ajax()){
            //echo $req->search;
            $data = DB::table('xalert_mstrs')
                        ->where('xalert_supp','=',$req->search)
                        ->get();

            $array = json_decode(json_encode($data), true);

            return response()->json($array);
        }
    }

     public function show()
    {   
        $users = DB::select('select * from users');
        $domain = DB::table('xdomain_mstr')
                    ->distinct() 
                    ->get();       
        return view('/setting/usermaint',['users'=>$users]);
        //return view('/setting/usermaint');
    }

    public function update(Request $req)
    {     
        //dd($req->all());

        $this->validate($req,[
                'username' => 'required|unique:users',
                'name' => 'required',
                'domain' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|max:20',
                'password_confirmation' => 'required|min:8|max:20|same:password',
            ],[
                'unique' => 'Email Must Be Unique',
            ]);

        $user = DB::table('users')
                    ->where('active','=','yes')
                    ->get();

        if($user->count() >= 13){
            alert()->error('Error','Exceeded Maximum Allowed User');
            return back();
        }

        try{
            $username = $req->input('username');
            $name = $req->input('name');
            $domain = $req->input('domain');
            $email = $req->input('email');
            $password = $req->input('password');  
            $role = $req->input('role');
            $roletype = $req->input('roletype');
            $department = $req->input('deptselect');

            // Validasi ke QAD untuk Supp ID 
            $suppid = $req->input('suppid');
            $suppname = $req->input('suppname');

            $data1 = array('name'=>$name,
                        'username'=>$username,
                        'domain'=>$domain,
                        'email'=>$email,
                        'role'=>$role,
                        'department'=>$department,
                        'role_type'=>$roletype,
                        'supp_id'=>$suppid,
                        'supp_name'=>$suppname,
                        'password'=>Hash::make($password),                  
                    );
            
            DB::table('users')->insert($data1);
            // session()->flash("updated","User Successfully Created !");
            alert()->success('Success','User Successfully Created');

                  
            return back();
        }catch(\InvalidArgumentException $ex){
            return back()->withError($ex->getMessage())->withInput();
            alert()->error('Error',$ex->getMessage());
            //return redirect()->back()->with(['error'=>'Username/Email Sudah Terdaftar']);
        }catch(\Exception $ex){
            return back()->withError($ex->getMessage())->withInput();
            alert()->error('Error',$ex->getMessage());
            //return redirect()->back()->with(['error'=>'Username/Email Sudah Terdaftar']);
        }catch(\Error $ex){
            return back()->withError($ex->getMessage())->withInput();
            alert()->error('Error',$ex->getMessage());
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }
    }

    public function destroy(Request $request)
    {
        dd($request->all());
        $roles = XxroleMstr::findOrFail($request->temp_id);
        $roles->delete();
        // session()->flash("deleted","User Successfully Deleted !");
        alert()->success('Success','User Successfully Deleted');
        return back();
    }

    public function indprof(Request $req)
    {
        $value = $req->session()->get('username');
        $value1 = $req->session()->get('userid');

        $users = DB::table("users")
                    ->where("users.id",$value1)
                    ->first();

        return view('/setting/userprof', compact('users'));
    }

    public function updateprof(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $domain = $request->input('domain');
        $email = $request->input('email');

        //dd($uname,$name,$domain,$email);

        DB::table('users')
            ->where('id', $id)
            ->update(['name' => $name,
                    'domain' => $domain,
                    'email' =>$email
            ]);

        // session()->flash("updated","User Information Successfully Updated !");
        alert()->success('Success','User Information Successfully Updated');
        return back();
    }

    public function indchangepass(Request $req)
    {
        $value = $req->session()->get('username');
        $value1 = $req->session()->get('userid');

        $users = DB::table("users")
                    ->where("users.id",$value1)
                    ->first();

        return view('/setting/changepass', compact('users'));
    }

    public function changepass(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');
        $confpass = $request->input('confpass');
        $oldpass = $request->input('oldpass');

        $hasher = app('hash');

        $users = DB::table("users")
                    ->select('id','password')
                    ->where("users.id",$id)
                    ->first();

        if($hasher->check($oldpass,$users->password))
        {
            if($password != $confpass)
            {
                // session()->flash("error","Password & Confirm Password Berbeda");
                alert()->error('error','Password & Confrim Password does not match');
                return back();
            }else{
                DB::table('users')
                ->where('id', $id)
                ->update(['password' => Hash::make($password)]);

                // session()->flash("updated","Password Successfully Updated !");
                alert()->success('Success','Password Successfully Updated');
                return back();
            }
        }else{
                // session()->flash("error","Old Password is Wrong");
                alert()->success('Success','Old Password is Wrong');
                return back();    
        }  
    }

    public function deleteuser(Request $req){
        //dd($req->all());

        if($req->temp_active == '2'){
            //dd('123');
            DB::table('users')
                ->where('id','=',$req->temp_id)
                ->update([
                    'active' => 'no'
                ]);
        }

        if($req->temp_active == '1'){
            //dd('444');
            $user = DB::table('users')
                    ->where('active','=','yes')
                    ->get();

            if($user->count() >= 13){
                alert()->error('Error','Exceeded Maximum Allowed User');
                return back();
            }

            DB::table('users')
                ->where('id','=',$req->temp_id)
                ->update([
                    'active' => 'yes'
                ]);
        }


        /*db::table('users')
            ->where('id','=',$req->temp_id)
            ->delete();
        */
        // session()->flash("deleted","User Successfully Deleted !");
        alert()->success('Success','User Successfully Updated');
        return back();
    }

    public function updateuser(Request $req){
        // dd($req->all());


        $validasi = DB::table('users')
                    ->where('id','!=',$req->t_id)
                    ->get();

        foreach($validasi as $validasi){
            
            if($validasi->email == $req->email){
                // session()->flash("error","Duplicate email with user : ".$validasi->name."");
                alert()->success('Success','Duplicate email with user '.$validasi->name.'');
                return back();
            }

        }

        db::table('users')
            ->where('id','=',$req->t_id)
            ->update(
                ['username' => $req->d_uname,
                 'name' => $req->name,
                 'role' => $req->role,
                 'domain' => $req->d_domain,
                 'email' => $req->email,
                 'role_type' => $req->roletype,
                 'department' => $req->t_dept,
                 'supp_id' => $req->d_supplier,
                 'supp_name' => $req->d_suppname
        ]);
        
        alert()->success('Success','User Information Successfully Updated');
        // session()->flash("updated","User Information Successfully Updated !");
        // echo url()->previous();
        return back();
    }

    public function adminchangepass(Request $req){
        //dd($req->all());
        $this->validate($req,[
                    'c_password' => 'required|min:8',
                    'password_confirmation' => 'required|min:8|same:c_password',
            ]);


        $id = $req->c_id;
        $password = $req->c_password;
        $confpass = $req->password_confirmation;

        DB::table('users')
            ->where('id', $id)
            ->update(['password' => Hash::make($password)]);

        // session()->flash("updated","Password Successfully Updated");
        alert()->success('Success','Password Successfully Updated');
        return back();
    }

    public function searchoptionuser(Request $req){
        if($req->ajax())
        {
            $search = $req->search;

            $data = DB::table('xxrole_mstrs')
                        ->where('xxrole_type','=',$search)
                        ->get();

            $array = json_decode(json_encode($data), true);

            return response()->json($array);

        }
    }
    

    // public function optionuser(Request $req){
    //     if($req->ajax()){
    //         $search = $req->search;

    //         $data = DB::table('xdepartment')
    //                 ->where('')
    //     }
    // }

    public function mtweb(Request $req){
        $data = DB::table('com_mstr')
                    ->get();
                    
        return view('/setting/mt', compact('data'));
    }

    public function mtwebcreate(Request $req){
        //dd($req->all());    

        $savepath = "";
        $filename = "";

        if($req->hasFile('file')){
            //dd('123');
            $dataTime = date('Ymd_His');
            $file = $req->file('file');
            $filename = $dataTime . '-' .$file->getClientOriginalName();

            // Simpan File Upload pada Public
            $savepath = public_path('company/');
            //dd($savepath,$filename);
            $file->move($savepath, $filename);
        }

            //dd($savepath.$filename);

            $data = 
                array(
                    'com_code'=>$req->comcode,
                    'com_name'=>$req->comname,
                    'com_desc'=>hash::make($req->comdesc), // Tanggal
                    'com_img'=>$savepath.$filename,
                    );

        DB::table('com_mstr')->insert($data);


        // session()->flash("updated","MT Succesfully Created");
        alert()->success('Success','MT Successfully Created');
        return back();
             
    }

    public function newlicensekey(Request $req){
        // Encrypt Isi File Taro di CSV Baru 

        try{
            $file = $req->file('file');

            $file->move(public_path(), 'licensekey.csv');
            
            // Decrypt Isi File buat taro di DB
            $Content = File::get('licensekey.csv');

            $decryptContent = decrypt($Content);

            $array_content = explode(';', $decryptContent); 

            $encryptlicense = Crypt::encrypt($array_content[2]);

            // New License
            DB::table('com_mstr')
                ->updateOrInsert(
                    ['com_code' => $array_content[0]],
                    ['com_name' => $array_content[1],
                     'com_desc' => $encryptlicense]
                );

            // session()->flash("updated","License Key Successfully Updated");
            alert()->success('Success','License Key Successfully Updated');
            return back();

        }catch(\InvalidArgumentException $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Username/Email Sudah Terdaftar']);
        }catch(\Exception $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Username/Email Sudah Terdaftar']);
        }catch(\Error $ex){
            return back()->withError($ex->getMessage())->withInput();
            //return redirect()->back()->with(['error'=>'Pastikan Data yang diinput sudah sesuai']);
        }

        

    }

    public function createlicensekey(Request $req){
        // function ini ga di client khusus IMI buat nge encrypt isi file yang akan dikasi ke client

        $file = $req->file('file');
        
        $fileContent = $file->get();

        $encryptedContent = encrypt($fileContent);

        File::put('licensekey.csv',$encryptedContent); 

        /*  Query Untuk saat Login & Create User
            $license = DB::table('com_mstr')
                            ->first();

            if(!is_null($license)){
                $decryptdata = decrypt($license->com_desc);

                $indexuser = strpos($decryptdata,'_',0);

                $indexdate = strpos($decryptdata,'_',7);

                $maxuser = substr($decryptdata, $indexuser + 1, $indexdate - $indexuser - 1);

                $date = substr($decryptdata, $indexdate + 1);
                $valuedate = strtotime($date);
                $newdate = date('d-m-Y',$valuedate);
                $now = Carbon::now()->format('d-m-Y');

                if($newdate <= $now){
                    dd('expired',$newdate,$now);
                }else{
                    dd('lanjut',$newdate,$now);
                }

                dd($date,$valuedate,$newdate,$indexuser,$indexdate,$maxuser,$now);
            }
        */
    }

    public function budgetapprove(Request $req){
        // dd('menu budget approval');

        $users = DB::table('users')
                ->where('role', '=', 'Purchasing')
                ->orWhere('role', '=', 'Admin')
                ->orderBy('role_type')
                ->orderBy('name')
                ->get();

        
        $datas = DB::table('approver_budget')
                ->first();


        return view('/setting/appr_budget', ['users'=>$users, 'datas'=>$datas]);
    }

    public function inputbudgetappr(Request $req){
        // dd($req->all());

        $countdata = DB::table('approver_budget')
            ->count();

        if($countdata == 0){

            DB::table('approver_budget')
            ->insert([
                'approver_budget' => $req->appr_budget,
                'alt_approver_budget' => $req->alt_appr_budget,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

        }else{

            DB::table('approver_budget')
                ->update([
                    'approver_budget' => $req->appr_budget,
                    'alt_approver_budget' => $req->alt_appr_budget,
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);

        }   



        alert()->success('Success', 'Budget Approver Successfully Created');
        return back();
    }

}
