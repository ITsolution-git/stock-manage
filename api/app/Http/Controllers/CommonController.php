<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use App\Company;
use App\Vendor;
use App\Client;
use App\Order;
use App\Purchase;
use App\Art;
use App\Labor;
use App\Machine;
use App\Production;
use DB;

use Request;
// Common Controller for default data
class CommonController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/

    public function __construct(Common $common, Company $company, Vendor $vendor, Purchase $purchase, Art $art, Client $client, Order $order, Labor $labor, Machine $machine,Production $production) 
    {
        parent::__construct();
        $this->common = $common;
        $this->company = $company;
        $this->vendor = $vendor;
        $this->purchase = $purchase;
        $this->art = $art;
        $this->client = $client;
        $this->order = $order;
        $this->labor = $labor;
        $this->machine = $machine;
        $this->production = $production;
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


    public function checkemailExist($email,$userid)
    {
        if(!empty($email) && isset($userid))
        {
            $getData = $this->common->checkemailExist($email,$userid);
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


/** 
 * @SWG\Definition(
 *      definition="miscList",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/getAllMiscData",
 *  summary = "Miscdata Listing",
 *  tags={"Misc"},
 *  description = "Miscdata Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Miscdata Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/miscList")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          require=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          require=true
*      ),
 *  @SWG\Response(response=200, description="Miscdata Listing"),
 *  @SWG\Response(response="default", description="Miscdata Listing"),
 * )
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
     public function getStaffList($company_id)
     {

        $result = $this->common->getStaffList($company_id);
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
 * @SWG\Definition(
 *      definition="InsertRecords",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *          @SWG\Property(
 *          property="login_id",
 *          type="integer",
 *         ),
 *           @SWG\Property(
 *          property="name",
 *          type="string",
 *         ),
 *          @SWG\Property(
 *          property="vendor_id",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="table",
 *          type="string",
 *         )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/InsertRecords",
 *  summary = "Insert records",
 *  tags={"Product"},
 *  description = "Insert records",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Insert records",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/InsertRecords")
 *  ),
 *  @SWG\Response(response=200, description="Insert records"),
 *  @SWG\Response(response="default", description="Insert records"),
 * )
 */


     public function InsertRecords()
     {
        $post = Input::all();

         //$post['data']['created_date'] = date('Y-m-d');
        // $post['data']['updated_date'] = date('Y-m-d');

        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['table']) && !empty($post['data']))
        {
            if($post['table'] == 'client_distaddress' && isset($post['data']['order_id']))
            {
                unset($post['data']['order_id']);
            }
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
        
        $post['sort']     =  empty($post['sort'])?     '' : $post['sort'];
        $post['sortcond'] =  empty($post['sortcond'])? '' : $post['sortcond'];
       
        if(!empty($post['table']))
        {
            $result = $this->common->GetTableRecords($post['table'],$post['cond'],$post['notcond'],$post['sort'],$post['sortcond']);
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

    /** 
 * @SWG\Definition(
 *      definition="updateOrderNotes",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"note_id"},
 *          @SWG\Property(
 *          property="note_id",
 *          type="integer",
 *         )
 *
 *      ),
 *
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"order_notes"},
 *          @SWG\Property(
 *          property="order_notes",
 *          type="string",
 *         )
 *         
 *
 *      ),
 *      @SWG\Property(
 *          property="table",
 *          type="string",
 *         )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/UpdateTableRecords",
 *  summary = "Update Order Note",
 *  tags={"Order"},
 *  description = "Update Order Note",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Update Order Note",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/updateOrderNotes")
 *  ),
 *  @SWG\Response(response=200, description="Update Order Note"),
 *  @SWG\Response(response="default", description="Update Order Note"),
 * )
 */

     public function UpdateTableRecords()
     {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die;

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $date_field = (empty($post['date_field']))? '':$post['date_field']; 
          
          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data'],$date_field);
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


       /** 
 * @SWG\Definition(
 *      definition="DeleteTableRecords",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *
 *      ),
 *
 *      @SWG\Property(
 *          property="table",
 *          type="string",
 *         )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/DeleteTableRecords",
 *  summary = "Delete Placement",
 *  tags={"Setting","Color","API"},
 *  description = "Delete Record",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Delete Record",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/DeleteTableRecords")
 *  ),
 *  @SWG\Response(response=200, description="Delete Record"),
 *  @SWG\Response(response="default", description="Delete Record"),
 * )
 */

     public function DeleteTableRecords()
     {
        $post = Input::all();
        

        if(!empty($post['table'])   && !empty($post['cond']))
        {

              $post['extra'] = empty($post['extra'])?'' : $post['extra'];  

              $result = $this->common->DeleteTableRecords($post['table'],$post['cond']);
              
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


 /**
 * @SWG\Get(
 *  path = "/api/public/common/getAllColorData",
 *  summary = "Get All colors",
 *  tags={"Color"},
 *  description = "Get All colors",
 *  @SWG\Response(response=200, description="Get All colors"),
 *  @SWG\Response(response="default", description="Get All colors"),
 * )
 */

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
        

        if(!empty($post['unlink_url'])) {
           $post['unlink_url'] = base_path() . "/public/uploads/" . $post['image_path']."/".$post['unlink_url'];
        }
       

        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['image_array']) && !empty($post['field']) && !empty($post['table']) && !empty($post['image_name']) && !empty($post['image_path']) && !empty($post['cond']) && !empty($post['value']))
        {
            $upload_image = $this->common->SaveImage($post);
            $response = array('success' => 1, 'message' => "Image Uploaded Successfully.",'records'=>$upload_image );
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

    /**
    * Insert record for any single table.
    * @params Table name, Post array
    * @return json data
    */


       /** 
 * @SWG\Definition(
 *      definition="InsertUserRecords",
 *      type="object",
 *     
 *    
 *
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"email"},
 *          @SWG\Property(
 *          property="email",
 *          type="string",
 *         ), 
 *           @SWG\Property(
 *          property="password",
 *          type="string",
 *         ),
 *           @SWG\Property(
 *          property="role_id",
 *          type="integer",
 *         ),
 *          @SWG\Property(
 *          property="parent_id",
 *          type="integer",
 *         )
 *         
 *
 *      ),
 *      @SWG\Property(
 *          property="table",
 *          type="string",
 *         )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/InsertUserRecords",
 *  summary = "Add Staff",
 *  tags={"Staff"},
 *  description = "Add Staff",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add Staff",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/InsertUserRecords")
 *  ),
 *  @SWG\Response(response=200, description="Add Staff"),
 *  @SWG\Response(response="default", description="Add Staff"),
 * )
 */

     public function InsertUserRecords()
     {
        $post = Input::all();
         $post['data']['password'] = md5($post['data']['password']);
         $post['data']['created_date'] = date('Y-m-d');
         $post['data']['updated_date'] = date('Y-m-d');
         

         $email = $this->common->checkemailExist($post['data']['email'],0);
            if(count($email)>0)
            {
                $message = "Email Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }


        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['table']) && !empty($post['data']))
        {
            $result = $this->common->InsertRecords($post['table'],$post['data']);
            $id = $result;

            $staff_arr = array('user_id' => $id,'company_id' => $post['data']['parent_id'],'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d'));
            $staff_id = $this->common->InsertRecords('staff',$staff_arr);


            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        
        $data = array("success"=>$success,"message"=>$message,"id"=>$staff_id);
        return response()->json(['data'=>$data]);
     }

     /**
    * UPDATE Image
    * @params Table name, Condition array, Post array,path of image
    * @return json data
    */
     public function deleteImage()
     {
        $post = Input::all();
       

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $result = $this->common->deleteImage($post['table'],$post['cond'],$post['data'],$post['image_delete']);
          $data = array("success"=>1,"message"=>DELETE_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
     }

     public function checkExistData($email,$id,$column_name,$table_name,$company_id)
    {
        if(!empty($email) && isset($id))
        {
            $getData = $this->common->checkExistData($email,$id,$column_name,$table_name,$company_id);
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
    * UPDATE record for any single table.
    * @params Table name, Condition array, Post array
    * @return json data
    */
     public function updateRecordsEmailVal()
     {
        $post = Input::all();
       
       if($post['column_name'] == 'prime_email' && $post['data']['prime_email'] != '') {
        
        $email = $this->common->checkExistData($post['data']['prime_email'],$post['cond']['id'],$post['column_name'],$post['table']);

            if(count($email)>0)
            {
                $message = "Email Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }
        }    


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
 * @SWG\Definition(
 *      definition="InsertVendor",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *          @SWG\Property(
 *          property="login_id",
 *          type="integer",
 *         ),
 *           @SWG\Property(
 *          property="name_company",
 *          type="string",
 *         ),
 *          @SWG\Property(
 *          property="email",
 *          type="string",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="table",
 *          type="string",
 *         )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/common/insertRecordsEmail",
 *  summary = "Add vendor",
 *  tags={"Vendor"},
 *  description = "Add vendor",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add vendor",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/InsertVendor")
 *  ),
 *  @SWG\Response(response=200, description="Add vendor"),
 *  @SWG\Response(response="default", description="Add vendor"),
 * )
 */

     public function insertRecordsEmail()
     {
        $post = Input::all();

         $post['data']['created_date'] = date('Y-m-d');
         $post['data']['updated_date'] = date('Y-m-d');

         $email = $this->common->checkExistData($post['data']['email'],0,'email','vendors',$post['data']['company_id']);
         $companyname = $this->common->checkExistData($post['data']['name_company'],0,'name_company','vendors',$post['data']['company_id']);
            if(count($email)>0)
            {
                $message = "Email Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }

            if(count($companyname)>0)
            {
                $message = "Company Already Exists";
                $success = 3;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }


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
     public function allColor()
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
             $allcolors = $this->common->GetTableRecords($post['table'],$post['cond'],$post['notcond']);

              foreach ($allcolors as $key => $value) 
            {
                $color_array[$value->id]= $value->name;
                $allcolors[$key]->name = strtolower($value->name);
            }
            
             return $this->return_response($allcolors);
        }
        else
        {

            $data = array("success"=>0,"message"=>MISSING_PARAMS);
            return response()->json(['data'=>$data]);
        }
     }

    /**
    * Get record for any table with Total count and Pagination parameters.
    * @params Testy Post data
    * @return json data, with Testy parameters
    */
    public function getTestyRecords()
    {
        $post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];

        
        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }
        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        

        $result=array();

        if($post['filter']['function']=='color_list') // COLOR LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'cl.id';
            }
            $result = $this->company->getColors($post);
            $header = array(
                            0=>array('key' => 'cl.name', 'name' => 'Name'),
                            1=>array('key' => '', 'name' => 'Action','sortable' => false),
                        );
        }
        if($post['filter']['function']=='size_list') // SIZE LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'pz.id';
            }
            $result = $this->company->getSizes($post);
            $header = array(
                            0=>array('key' => 'pz.name', 'name' => 'Name'),
                            1=>array('key' => '', 'name' => 'Action','sortable' => false),
                        );
        }

        if($post['filter']['function']=='company_list') // COMPANY LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'usr.id';
            }
            $result = $this->company->GetCompanyData($post);
            $header = array(
                array('key' => 'usr.display_number', 'name' => '#No'),
                array('key' => 'usr.name', 'name' => 'Name'),
                array('key' => 'usr.email', 'name' => 'Email'),
                array('key' => 'usr.created_date', 'name' => 'Create Date'),
                );

        }
        if($post['filter']['function']=='vendor_list') // VENDOR LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'id';
            }
            $result = $this->vendor->vendorList($post);
            $header = array(
                array('key' => 'display_number', 'name' => '#No'),
                array('key' => 'name_company', 'name' => 'Name'),
                array('key' => 'email', 'name' => 'Email'),
                array('key' => 'prime_phone_no', 'name' => 'Phone'),
                array('key' => '', 'name' => 'Action','sortable' => false)
                );

        }
        if($post['filter']['function']=='purchase_list') // PURCHASE LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'ord.display_number';
            }
            $result = $this->purchase->ListPurchase($post);
            $getAllPOdata = $this->purchase->getAllPOdata();


            if(empty($result['allData']))
            {
                $result['allData'] = array('No Records found');
            }
            else
            {
                foreach($result['allData'] as $data) {

                    if(array_key_exists($data->id, $getAllPOdata)) {
                        $data->design_po = $getAllPOdata[$data->id];
                    } else {
                        $data->design_po = array();
                    }
                }
            }

            $header = array(
                array('key' => 'ord.display_number', 'name' => 'Order Id'),
                array('key' => 'po.po_type', 'name' => 'PO Type'),
                array('key' => 'cl.client_company', 'name' => 'Client'),
                array('key' => 'ord.approval_id', 'name' => 'Order Status', 'sortable' => false),
                array('key' => 'po.date', 'name' => 'Created Date'),
                array('key' => '', 'name' => 'Operations', 'sortable' => false)
                );

        }
        if($post['filter']['function']=='purchase_notes') // PURCHASE NOTES LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'id';
            }
            $result = $this->purchase->getPurchaseNote($post);


            $header = array(
                array('key' => 'mt.value', 'name' => 'Position'),
                array('key' => 'odp.note', 'name' => 'Note Name'),
                array('key' => 'odp.description', 'name' => 'Note Description')
                );

        }
        if($post['filter']['function']=='receive_list') // RECEIVE PO LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'ord.display_number';
            }
            $result = $this->purchase->ListReceive($post);

            $getAllReceivedata = $this->purchase->getAllReceivedata();


            if(empty($result['allData']))
                {
                    $result['allData'] = array('No Records found');
                }
                else
                {
                    foreach($result['allData'] as $data) {

                        if(array_key_exists($data->id, $getAllReceivedata)) {
                            $data->design_po = $getAllReceivedata[$data->id];
                        } else {
                            $data->design_po = array();
                        }
                    }
                }

                
            $header = array(
               /* 0=>array('key' => 'po.po_id', 'name' => 'RO#'),*/
                array('key' => 'ord.display_number', 'name' => 'Order Id'),
                array('key' => 'po.po_type', 'name' => 'PO Type'),
                array('key' => 'cl.client_company', 'name' => 'Client'),
                /*4=>array('key' => 'v.name_company', 'name' => 'Vendor/Affiliate'),*/
                array('key' => 'ord.approval_id', 'name' => 'Order Status', 'sortable' => false),
                array('key' => 'po.date', 'name' => 'Created Date'),
                array('key' => '', 'name' => 'Operations', 'sortable' => false),
                );

        }
        if($post['filter']['function']=='vendor_contact') // RECEIVE PO LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'id';
            }
            $result = $this->vendor->vendorContacts($post);
            $header = array(
                array('key' => 'first_name', 'name' => 'First Name'),
                array('key' => 'last_name', 'name' => 'Last Name'),
                array('key' => 'prime_email', 'name' => 'Email'),
                array('key' => 'prime_phone', 'name' => 'Phone'),
                array('key' => 'is_main', 'name' => 'Main')
                );

        }
        if($post['filter']['function']=='sales_list') // RECEIVE PO LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'id';
            }
            $result = $this->vendor->SalesList($post);
            $header = array(
                array('key' => 'display_number', 'name' => '#No'),
                array('key' => 'sales_name', 'name' => 'Name'),
                array('key' => 'sales_email', 'name' => 'Email'),
                array('key' => 'sales_phone', 'name' => 'Phone'),
                array('key' => 'sales_created_date', 'name' => 'Created Date'),
                array('key' => '', 'name' => 'Action','sortable' => false)
                );
        }
        
        if($post['filter']['function']=='art_list') // ART LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'ord.display_number';
            }
            $result = $this->art->Listing($post);
            $header = array(
                0=>array('key' => 'ord.display_number', 'name' => 'Order Id'),
                1=>array('key' => 'cl.client_company', 'name' => 'Client'),
                2=>array('key' => 'ord.total_screen', 'name' => '#of Screen sets','sortable' => false),
                3=>array('key' => 'ord.approval_id', 'name' => 'Order Status', 'sortable' => false),
                4=>array('key' => '', 'name' => '','sortable' => false)
                );
        }
        if($post['filter']['function']=='art_list_screen') // ART SCREEN LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'asc.display_number';
            }
            $result = $this->art->Screen_Listing($post); 
            $header = array(
                array('key' => 'asc.display_number', 'name' => '#No'),
                array('key' => 'ord.id', 'name' => 'Screen Set Name'),
                array('key' => 'mt.value', 'name' => 'Position'),
                array('key' => 'cl.client_company', 'name' => 'Client'),
               /* 3=>array('key' => 'ord.approval_id', 'name' => 'Order Status', 'sortable' => false),*/
                array('key' => 'odp.color_stitch_count', 'name' => '#of Color'),
                array('key' => 'screen_width', 'name' => '#of Screen'),
                array('key' => 'asc.screen_width', 'name' => 'Frame size'),
                array('key' => '', 'name' => '','sortable' => false)
                );
        }
        if($post['filter']['function']=='art_notes') // SCREENSET COLOR NOTE
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'note.id';
            }
            $result = $this->art->getArtColorNote($post);
            $header = array(
                0=>array('key' => 'note.note_date', 'name' => 'Created date'),
                1=>array('key' => 'note.note_title', 'name' => 'Note Title'),
                2=>array('key' => 'note.note', 'name' => 'Note Description'),
                3=>array('key' => 'note.artapproval_display', 'name' => 'Show in Art Approval')
                );
        }
        if($post['filter']['function']=='order_notes') // PURCHASE NOTES LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'odp.id';
            }
            $result = $this->order->getOrderNoteDetail($post);
            $header = array(
                 0=>array('key' => '', 'name' => 'Notes','sortable' => false)
                );
        }
        if($post['filter']['function']=='machine_list') // VENDOR LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'id';
            }
            $result = $this->machine->machineList($post);
            $header = array(
                array('key' => 'machine_name', 'name' => 'Machine Name'),
                array('key' => 'machine_type', 'name' => 'Machine Type'),
                array('key' => 'run_rate', 'name' => 'Run Rate'),
                array('key' => 'color_count', 'name' => 'Color/Head Count'),
                array('key' => '', 'name' => 'Max Frame Size','sortable' => false),
                array('key' => '', 'name' => 'Operation Status','sortable' => false),
                array('key' => '', 'name' => 'Action','sortable' => false)
            );
         }   


        if($post['filter']['function']=='labor_list') // RECEIVE PO LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'l.id';
            }
            $result = $this->labor->laborList($post);
            $header = array(
                array('key' => 'l.shift_name', 'name' => 'Shift Name'),
                array('key' => 'l.shift_start_time', 'name' => 'Shift Start Time','sortable' => false),
                array('key' => 'l.shift_end_time', 'name' => 'Shift End Time','sortable' => false),
                array('key' => 'l.total_shift_hours', 'name' => 'Total Shift Hours','sortable' => false),
                array('key' => '', 'name' => 'Action','sortable' => false)
                );
        }

        if($post['filter']['function']=='production_list') // PRODUCTION LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'odp.id';
            }
            $result = $this->production->GetProductionList($post);
            $header = 
                array(
                    array('key' => '', 'name' => 'Asset','sortable' => false),
                    array('key' => 'ord.name', 'name' => 'Order Name'),
                    array('key' => 'mt.value', 'name' => 'Position'),
                    array('key' => 'cl.client_company', 'name' => 'Client'),
                    array('key' => 'mt1.value', 'name' => 'Production Type'),
                    array('key' => 'ord.in_hands_by', 'name' => 'In Hand date','sortable' => false),
                    array('key' => '', 'name' => 'Run Date','sortable' => false),
                    array('key' => '', 'name' => '','sortable' => false)
                );

        }

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $message = (empty($result['count']))?NO_RECORDS:GET_RECORDS;

        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => RECORDS_PAGE_RANGE,'size' => $result['count']);


        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$post['sorts']['sortBy'],'sortOrder' => $post['sorts']['sortOrder'],'success'=>$success,'message'=>$message);
        return  response()->json($data);
    }


    public function addEditClient()
    {
        $post = Input::all();
        $result = $this->company->getQBAPI($post['company_id']);

       if(empty($result)) {
             return 0;
         }

         $result = $this->client->GetAllclientDetailCompany($post['company_id']);

         foreach ($result as $key => $clientData) {
            
             if($clientData['main']['qid'] == 0) {

                $result_quickbook = app('App\Http\Controllers\QuickBookController')->createCustomer($clientData['main'],$clientData['contact']);
                $this->common->UpdateTableRecords('client',array('client_id' => $key),array('qid' => $result_quickbook));

               

             } else {

                $result_quickbook = app('App\Http\Controllers\QuickBookController')->updateCustomer($clientData['main'],$clientData['contact']);
                
             }
         }

          if($result_quickbook == '0') {
                    return 0;
                    
                  } else {
                    return 1;
                    
                  }
    }

    public function GetMiscApprovalData()
    {
        $post = Input::all();
        $result = $this->common->GetMiscApprovalData($post);
        return $this->return_response($result);
    }


     public function checkCompanyNameExist($name,$companyId)
    {
        if(!empty($name) && isset($companyId))
        {
            $getData = $this->common->checkNameExist($name,$companyId);
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



}