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

    public function getAllVendors($company_id)
    {
        if(!empty($company_id))
        {
            $result = $this->common->getAllVendors($company_id);
        }
        else
        {
            $result = array();
        }
        return $this->return_response($result);
     }

/**
* Get All Misc Data controller   
* @return json data
*/

    public function getAllMiscData()
    {
         $post = Input::all();
        $result = $this->common->getAllMiscData($post);
        return $this->return_response($result);
    }

     public function getAllMiscDataWithoutBlank()
    {
        $post = Input::all();
        $result = $this->common->getAllMiscDataWithoutBlank($post);
        return $this->return_response($result);
    }


    

    /**
    * Get Array of All Misc Data with argument Type
    * @return json data
    */
     public function GetMicType($type,$company_id)
     {
        $result = $this->common->GetMicType($type,$company_id);
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
    * Get Array of field selection, condition and table name.
    * @return json data
    */
     public function getBrandCo()
     {

        $result = $this->common->getBrandCordinator();
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
            $id = $result;
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        
        $data = array("success"=>$success,"message"=>$message,"id"=>$id);
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
        if(empty($post['cond'])){
            $post['cond'] = array();
        }

        if(empty($post['notcond'])){
            $post['notcond'] = array();
        }
        
       
        if(!empty($post['table']))
        {
             $result = $this->common->GetTableRecords($post['table'],$post['cond'],$post['notcond']);
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
    * DELETE record for any single table.
    * @params Table name, Condition array
    * @return json data
    */
     public function DeleteTableRecords()
     {
        $post = Input::all();
        

        if(!empty($post['table'])   && !empty($post['cond']))
        {
          $result = $this->common->DeleteTableRecords($post['table'],$post['cond']);
          if($post['table'] == 'order_orderlines')
          {
            $this->common->DeleteTableRecords('purchase_detail',array('orderline_id' => $post['cond']['id']));
          }

          if($post['table'] == 'purchase_order')
          {
            $this->common->DeleteTableRecords('purchase_order_line',array('po_id' => $post['cond']['po_id']));
          }

          $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
     }

    public function getAllPlacementData()
    {
          $post = Input::all();
        $result = $this->common->getAllPlacementData($post);
        return $this->return_response($result);
    }

    public function getMiscData()
    {
        $post = Input::all();
        $result = $this->common->getMiscData($post);
        return $result;
    }

     public function getAllColorData()
    {
        $result = $this->common->getAllColorData();
        return $this->return_response($result);
    }


    /**
    * Get user id after login.
    * @return json data
    */
    public function CompanyService()
    {
        $post = Input::all();
        if(!empty($post['user_id']))
        {
            $result = $this->common->CompanyService($post['user_id']);
            //echo "<pre>"; print_r($result); die;
            if(!empty($result[0]->company_name))
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' =>$result[0]);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
            
        }
        else
        {
            $response = array('success' => 0, 'message' => NO_RECORDS);        
        }
        return response()->json(['data'=>$response]);
    }

    public function getCompanyDetail()
    {
        $post = Input::all();
        
        $listData = $this->common->getCompanyDetail($post[0]);
        return $this->return_response($listData);
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

    public function SaveImage()
    {
        $post = Input::all();

        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['image_array']) && !empty($post['field']) && !empty($post['table']) && !empty($post['image_name']) && !empty($post['image_path']) && !empty($post['cond']) && !empty($post['value']))
        {
            $upload_image = $this->common->SaveImage($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD,'records'=>$upload_image );
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function UpdateDate()
    {
        $post = Input::all();
        if(!empty($post['table']) && !empty($post['field']) && !empty($post['date']) && !empty($post['cond']) && !empty($post['value']))
        {
            $return_msg = $this->common->UpdateDate($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD,'records'=>$return_msg );
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
}