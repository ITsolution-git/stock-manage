<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use App\Art;
use DB;

use Request;

class ArtController extends Controller { 

	public function __construct(Art $art,Common $common) 
 	{
        $this->art = $art;
        $this->common = $common;
    }

    public function listing($company_id)
    {
    	if(!empty($company_id) 	&& $company_id != 'undefined')
    	{
    		$result = $this->art->listing($company_id);
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
		}
    	else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }
}