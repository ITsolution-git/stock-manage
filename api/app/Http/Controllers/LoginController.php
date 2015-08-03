<?php

namespace App\Http\Controllers;




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
     * @return Response
     */

    public function loginverify() {
 
 $data = Input::all();


        $username = $data['username'];
        $password = $data['password'];
        
        $result = $this->login->verifylogin($username, $password);

        if (count($result) >= 1) {
            Session::put('username', $username);

            $response = array('success' => 1, 'message' => "Login Successfull");
        } else {
            Session::forget('username');

            $response = array('success' => 0, 'message' => "Wrong Credential");
        }
        return response()->json(["data" => $response]);
    }

    /**
     * log out
     *
     * @return Response
     */
    public function logout() {
        Session::forget('username');
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
           $response = array('success' => 0, 'message' => "Wrong Credential");
        }
        return response()->json(["data" => $response]);
    }

}
