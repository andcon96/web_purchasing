<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class GoogleUserController extends Controller
{
    public function googleLogin(Request $request)  {
        $google_redirect_url = route('glogin');
        $gClient = new \Google_Client();
        $gClient->setApplicationName('webpurch');
        $gClient->setClientId('278990523742-j5oerfgl4fg4t0odc1eu80f7bls0vuqo.apps.googleusercontent.com');
        $gClient->setClientSecret('Zaswj2sXcuxl6EvS76TO9Ygt');
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey('AIzaSyB8hTGVvLnZW9Rrqkkjsq-EmI2dLqnxDSo');
        $gClient->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
        $google_oauthV2 = new \Google_Service_Oauth2($gClient);
        if ($request->get('code')){
            $gClient->authenticate($request->get('code'));
            $request->session()->put('token', $gClient->getAccessToken());
        }
        if ($request->session()->get('token'))
        {
            $gClient->setAccessToken($request->session()->get('token'));
        }
        if ($gClient->getAccessToken())
        {
            //For logged in user, get details from google using access token
            $guser = $google_oauthV2->userinfo->get();  
               
                $request->session()->put('name', $guser['name']);
                if ($user =User::where('email',$guser['email'])->first())
                {
                    //logged your user via auth login
                }else{
                    //register your user with response data
                }               
         return redirect()->route('user.glist');          
        } else
        {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }
    public function listGoogleUser(Request $request){
      $users = User::orderBy('id','DESC')->paginate(5);
     return view('users.list',compact('users'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function loadgdrive(Request $request){
        $service = new \Google_Service_Drive($this->gClient);
            $user=User::find(1);
            $this->gClient->setAccessToken(json_decode($user->access_token,true));
            if ($this->gClient->isAccessTokenExpired()) {
               
                // save refresh token to some variable
                $refreshTokenSaved = $this->gClient->getRefreshToken();
                // update access token
                $this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);               
                // // pass access token to some variable
                $updatedAccessToken = $this->gClient->getAccessToken();
                // // append refresh token
                $updatedAccessToken['refresh_token'] = $refreshTokenSaved;
                //Set the new acces token
                $this->gClient->setAccessToken($updatedAccessToken);
                
                $user->access_token=$updatedAccessToken;
                $user->save();                
            }
            
           $fileMetadata = new \Google_Service_Drive_DriveFile(array(
                'name' => 'ExpertPHP',
                'mimeType' => 'application/vnd.google-apps.folder'));
            $folder = $service->files->create($fileMetadata, array(
                'fields' => 'id'));
            printf("Folder ID: %s\n", $folder->id);
               
            
            $file = new \Google_Service_Drive_DriveFile(array(
                            'name' => 'cdrfile.jpg',
                            'parents' => array($folder->id)
                        ));
            $result = $service->files->create($file, array(
              'data' => file_get_contents(public_path('images/admin.jpg')),
              'mimeType' => 'application/octet-stream',
              'uploadType' => 'media'
            ));
            // get url of uploaded file
            $url='https://drive.google.com/open?id='.$result->id;
            dd($result);
    }
}
