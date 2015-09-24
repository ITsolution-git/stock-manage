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
        return $this->return_response($result);
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
* Get All Misc Data controller   
* @return json data
*/

    public function getAllMiscData()
    {
        $result = $this->common->getAllMiscData();
        return $this->return_response($result);
    }


     public function GetMicType($type)
     {
        $result = $this->common->GetMicType($type);
        return $this->return_response($result);
     }
    
    /**
    * Get Array of field selection, condition and table name.
    * @return json data
    */
     public function getStaffList()
     {

        $result = $this->common->getStaffList();
        return $this->return_response($result);
     }
    /**
    * Insert record for any single table.
    * @params Table name, Post array
    * @return json data
    */
     public function InsertRecords()
     {
        $post = Input::all();

        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['table']) && !empty($post['data']))
        {
            $result = $this->common->InsertRecords($post['table'],$post['data']);
            $message = INSERT_RECORD;
            $success = 1;
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
    * Get record for any single table.
    * @params Table name, Condition array
    * @return json data
    */
     public function GetTableRecords()
     {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['table']))
        {
             $result = $this->common->GetTableRecords($post['table'],$post['cond']);
             return $this->return_response($result);
        }
        else
        {

            $data = array("success"=>0,"message"=>MISSING_PARAMS);
            return response()->json(['data'=>$data]);
        }
     }

    /**
    * UPDATE record for any single table.
    * @params Table name, Condition array, Post array
    * @return json data
    */
     public function UpdateTableRecords()
     {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die;

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data']);
          $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
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