<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Request;
use App\Api;
use App\Order;
use Response;
use DB;
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
			if (!empty($headers) )
			{
				$token_data = $this->common->GetTableRecords('login_token',array('token' => $headers),array(),0,0,'token');
				if (empty($token_data)) 
				{
					echo json_encode(['message' => 'Not valid token']); exit;
				}
			}
			else
			{
					echo json_encode(['message' => 'Not valid token']); exit;
			}
		}
    }
}