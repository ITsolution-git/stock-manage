<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Request;
use Auth;
use Session;
use App\Api;
use App\Order;
use Response;
use DB;
use View;
use Input;
use App;
use App\Common;

abstract class Controller extends BaseController {

    use DispatchesCommands, ValidatesRequests;

    public function __construct() {
    	  $common = new Common();
    	  $this->common = $common;
        $token = Request::header('Authorization');
        $UserId = Request::header('AuthUserId');
        $post = Input::all();

        if(empty($post['pdf_token']))  // CHECK PDF FILE TOKEN
        { 
      			if (!empty($token) && !empty($UserId))  // CHECK TOKEN AND USERID COMBINATION
            {
        				$token_data = $this->common->GetTableRecords('login_token',array('token' => $token,'user_id'=>$UserId),array(),0,0,'token');
        				if (empty($token_data)) 
                {
        	          $data = json_encode(array("data"=>["success"=>TOKEN_CODE,'message' =>"Sorry your Token is invalid or expired"]));
                  	Session::flush();
                  	print_r($data);
        	          exit;
    				    }
  			    }
            else
            {
    				    $data = json_encode(array("success"=>TOKEN_CODE,'message' =>'Invalid Token'));
          			Session::flush();
                print_r($data);
          			exit;
    	      }
       	}        
    }
} 