<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');

use App\Login;
use Mail;
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

        if(!empty($data['username']) && !empty($data['password']))
        {
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
                    Session::put('user_id', $result[0]->id);
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
                    $session['user_id'] = $result[0]->id;

                    $response = array('records'=>$session,'success' => 1, 'message' => LOGIN_SUCCESS);
                }
            } 
            else 
            {
                $response = array('success' => 0, 'message' => LOGIN_WRONG);
            }

        }
        else
        {
            $response = array('success' => 0, 'message' => FILL_PARAMS);
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
            $response = array('success' => 1, 'message' => "session there","username" => Session::get("username"),"role_session"=>Session::get("role_slug"));
        } else {
           $response = array('success' => 0, 'message' => LOGIN_WRONG);
        }
        return response()->json(["data" => $response]);
    }

    /**
     * Forgot Password
     *
     * @param  $email
     * @return Response
     */
    public function forgot_password()
    {
         $data = Input::all();

         //echo "<pre>"; print_r($data); echo "</pre>"; die;
         if(!empty($data))
         {
            $result = $this->login->forgot_password(trim($data['email']));
            if(count($result)>0)
            {
                $url = $this->login->ResetEmail($result[0]->email,$result[0]->id,$result[0]->password);
                $email = trim($result[0]->email);
                Mail::send('emails.send', ['url' => $url,'user'=>$result[0]->name,'email'=>trim($result[0]->email)], function($message) use ($email)
                {
                    $message->to($email, 'Hello, Please Click below link to change Stokkup Password.')->subject('Reset Password for Stokkup');
                });


                $response = array('success' => 1, 'message' => MAIL_SEND);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS.", Please check Email");
            }
            //echo "<pre>"; print_r($result); echo "</pre>"; die;
         }
         else
         {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
         }
         return response()->json(["data" => $response]);
    }

    /**
     * checnk user details from forget password email link
     *
     * @param  $url details
     * @return Response
     */
    public function check_user_password()
    {
        $data = Input::all();
        
        
         if(!empty($data)) 
         {
            $pos = strpos($data['string'],'&');
            if ($pos !== false) 
            {
                list($string, $email) = explode('&', trim($data['string'])); 
              
                $email = base64_decode($email);
               // echo $email;
                $result = $this->login->check_user_password(trim($string),trim($email));

                //echo "<pre>"; print_r($result); echo "</pre>"; die;
                if(count($result)>0)
                {
                    $response = array('success' => 1, 'message' => GET_RECORDS, 'result'=>$result);
                }
                else
                {
                    $response = array('success' => 0, 'message' =>  MAIL_LINK_EXPIRE);
                }
               
            }
            else
            {
                $response = array('success' => 0, 'message' => MISSING_PARAMS);
            }
         }
         else
         {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
         }
         return response()->json(["data" => $response]);
    }
    /**
     * checnk user details from forget password email link
     *
     * @param  $url details
     * @return Response
     */
    public function change_password()
    {
        $data = Input::all();
        if(!empty($data) && !empty($data['form_data']) && !empty($data['string'])) 
        {
            if($data['form_data']['password']!=$data['form_data']['confirm_password'])
            {
                $response = array('success' => 0, 'message' => PASSWORD_NOT_MATCH);
                return response()->json(["data" => $response]);
            }

            $pos = strpos($data['string'],'&');
            if ($pos !== false) 
            {
                list($string, $email) = explode('&', trim($data['string'])); 
              
                $email = base64_decode($email);
                //echo $string;
                $result = $this->login->check_user_password(trim($string),trim($email));

                //echo "<pre>"; print_r($result); echo "</pre>"; die;
                if(count($result)>0)
                {
                    ///echo $data['form_data']['password']; die;
                    $this->login->change_password(trim($string),trim($email),$data['form_data']['password']);
                    $response = array('success' => 1, 'message' => PASSWORD_CHANGE, 'result'=>$result);
                }
                else
                {
                    $response = array('success' => 0, 'message' =>  MAIL_LINK_EXPIRE);
                }
               
            }
            else
            {
                $response = array('success' => 0, 'message' => MISSING_PARAMS);
            }
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
         return response()->json(["data" => $response]);
    }


}
