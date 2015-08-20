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


	public function __construct(Common $common) 
 	{
        $this->common = $common;

    }

	public function getAdminRoles()
	{
		$listRoels = $this->common->getAdminRoles();

		$success = count($listRoels);
		$message  = ($success>0)? GET_RECORDS:NO_RECORDS;

		$data = array("records" => $listRoels,"success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);

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
     * All types data.
     *
     * @param  
     * @return Data Response
     */

    public function type($type) {
 
        
        $result = $this->common->TypeList($type);
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

    public function getStaffRoles()
    {
        $result = $this->common->getStaffRoles();

         if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }



}