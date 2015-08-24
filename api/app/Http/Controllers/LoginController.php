<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');

use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use DB;
use Image;
use Request;

class LoginController extends Controller {  
    

/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Login $login) {

        $this->login = $login;
       
    }

    /**
     * Check login functionality.
     *
     * @param  $username,$password
     * @return Response- session
     */

    public function loginverify() {
 
        $data = Input::all();


        $username = htmlentities(trim($data['username']));
        $password = htmlentities($data['password']);
        
        $result = $this->login->verifylogin($username, $password);

        if (count($result) > 0) 
        {
            if(empty($result[0]->title) || empty( $result[0]->slug) || empty($result[0]->email ))
            {
                $response = array('success' => 0, 'message' => SOMETHING_WRONG);
            }
            else
            {
                Session::put('username', $username);
                Session::put('password', md5($password));
                Session::put('useremail', $result[0]->email);
                Session::put('role_title', $result[0]->title);
                Session::put('role_slug', $result[0]->slug);
                Session::put('name', $result[0]->name);
                $loginid = $this->login->loginRecord($result[0]->id);
                Session::put('login_id', $loginid);
                
                $session = array();
                $session['name'] = $result[0]->name;
                $session['username'] = $username;
                $session['password'] = md5($password);
                $session['useremail'] = $result[0]->email;
                $session['role_title'] = $result[0]->title;
                $session['role_slug'] = $result[0]->slug;
                $session['login_id'] = $loginid;

                $response = array('records'=>$session,'success' => 1, 'message' => LOGIN_SUCCESS);
            }
        } 
        else 
        {
            $response = array('success' => 0, 'message' => LOGIN_WRONG);
        }
        return response()->json(["data" => $response]);
    }

    /**
     * log out
     *
     * @return Response
     */
    public function logout() {

        if (!empty(Session::get("login_id"))) 
        {
            $loginid =Session::get("login_id");
            $this->login->loginRecordUpdate($loginid);
        }
          Session::flush(); 
          $response = array('success' => 1, 'message' => "Log out");
        return response()->json(["data" => $response]);
    }

    /**
     * log out
     *
     * @return Response
     */
    public function check_session() {
  
        if (!empty(Session::get("username"))) {
            $response = array('success' => 1, 'message' => "session there","username" => Session::get("username"));
        } else {
           $response = array('success' => 0, 'message' => LOGIN_WRONG);
        }
        return response()->json(["data" => $response]);
    }

}
