<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use DB;

use Request;
// Common Controller for default data
class CommonController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/

	public function __construct(Common $common) 
 	{
        $this->common = $common;

    }

/**
* Get Admin roles controller        
* @return json data
*/

	public function getAdminRoles()
	{
		$listRoels = $this->common->getAdminRoles();
		return $this->return_response($listRoels);
	}


	public function checkemailExist($email)
	{
		if(!empty($email))
		{
			$getData = $this->common->checkemailExist($email);
			$count = count($getData);
			$success = ($count>0)? '1':'2'; // 2 = EMAIL NOT EXISTS
			$message  = ($count>0)? GET_RECORDS:NO_RECORDS;
		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
		}

		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);

	}

/**
* Get All types controller   
* @param  string $type    
* @return json data
*/

    public function type($type) 
    {
        $result = $this->common->TypeList($type);
        return $this->return_response($result);
    }

/**
* Get All Staff roles controller   
* @return json data
*/

    public function getStaffRoles()
    {
        $result = $this->common->getStaffRoles();
        return return_response($result);
    }

/**
* Get All Vendors controller   
* @return json data
*/

    public function getAllVendors()
    {
        $result = $this->common->getAllVendors();
        return $this->return_response($result);
     }

    /**
    * Get Array
    * @return json data
    */
    public function return_response($result)
    {
        if (count($result) > 0) 
        {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } 
        else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }
}