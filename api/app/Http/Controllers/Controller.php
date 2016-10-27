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
        $headers = Request::header('Authorization');
        $post = Input::all();
        if(empty($post['pdf_token']))
        {
			if (!empty($headers)){
				$token_data = $this->common->GetTableRecords('login_token',array('token' => $headers),array(),0,0,'token');
				if (empty($token_data)) {
					$message = "Invalid Token";
	          		$data = json_encode(array("data"=>["success"=>0,'message' =>$message]));
          			print_r($data);
          			Session::flush();
          			Auth::logout();
	          		exit;
				}
			}else{
				$data = array("success"=>0,'message' =>'Invalid Token');
            	$message = "Invalid Token";
          		$data = json_encode(array("data"=>["success"=>0,'message' =>$message]));
      			print_r($data);
      			Session::flush();
      			Auth::logout();
      			exit;
	        }
     	}        
    }
}