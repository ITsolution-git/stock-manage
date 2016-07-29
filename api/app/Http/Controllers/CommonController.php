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
use App\Purchase;
use App\Art;
use DB;

use Request;
// Common Controller for default data
class CommonController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/

    public function __construct(Common $common, Company $company, Vendor $vendor, Purchase $purchase, Art $art ) 
    {
        $this->common = $common;
        $this->company = $company;
        $this->vendor = $vendor;
        $this->purchase = $purchase;
        $this->art = $art;

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
                0=>array('key' => 'usr.name', 'name' => 'Name'),
                1=>array('key' => 'usr.email', 'name' => 'Email'),
                2=>array('key' => 'usr.created_date', 'name' => 'Create Date'),
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
                0=>array('key' => 'name_company', 'name' => 'Name'),
                1=>array('key' => 'email', 'name' => 'Email'),
                2=>array('key' => 'prime_phone_no', 'name' => 'Phone'),
                );

        }
        if($post['filter']['function']=='purchase_list') // PURCHASE LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'po.po_id';
            }
            $result = $this->purchase->ListPurchase($post);
            $header = array(
                0=>array('key' => 'po.po_id', 'name' => 'PO#'),
                1=>array('key' => 'ord.id', 'name' => 'Order Id'),
                2=>array('key' => 'po.po_type', 'name' => 'PO Type'),
                3=>array('key' => 'cl.client_company', 'name' => 'Client'),
                4=>array('key' => 'v.name_company', 'name' => 'Vendor/Affiliate'),
                5=>array('key' => 'po.date', 'name' => 'Created Date'),
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
                0=>array('key' => 'note.note_date', 'name' => 'Created date'),
                1=>array('key' => 'note.note_title', 'name' => 'Note Name'),
                2=>array('key' => 'note.note', 'name' => 'Note Description')
                );

        }
        if($post['filter']['function']=='receive_list') // RECEIVE PO LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'po.po_id';
            }
            $result = $this->purchase->ListReceive($post);
            $header = array(
                0=>array('key' => 'po.po_id', 'name' => 'RO#'),
                1=>array('key' => 'ord.id', 'name' => 'Order Id'),
                2=>array('key' => 'po.po_type', 'name' => 'PO Type'),
                3=>array('key' => 'cl.client_company', 'name' => 'Client'),
                4=>array('key' => 'v.name_company', 'name' => 'Vendor/Affiliate'),
                5=>array('key' => 'po.date', 'name' => 'Created Date'),
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
                0=>array('key' => 'first_name', 'name' => 'First Name'),
                1=>array('key' => 'last_name', 'name' => 'Last Name'),
                2=>array('key' => 'prime_email', 'name' => 'Email'),
                3=>array('key' => 'prime_phone', 'name' => 'Phone'),
                4=>array('key' => 'is_main', 'name' => 'Main')
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
                0=>array('key' => 'sales_name', 'name' => 'Name'),
                1=>array('key' => 'sales_email', 'name' => 'Email'),
                2=>array('key' => 'sales_phone', 'name' => 'Phone'),
                3=>array('key' => 'sales_created_date', 'name' => 'Created Date')
                );
        }
        
        if($post['filter']['function']=='art_list') // ART LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'ord.id';
            }
            $result = $this->art->Listing($post);
            $header = array(
                0=>array('key' => 'ord.id', 'name' => 'Order Id'),
                1=>array('key' => 'cl.client_company', 'name' => 'Client'),
                2=>array('key' => 'ord.total_screen', 'name' => '#of Screen sets','sortable' => false)
                );
        }
        if($post['filter']['function']=='art_list_screen') // ART SCREEN LISTING CONDITION
        {
            if(!isset($post['sorts']['sortBy'])) 
            {
                $post['sorts']['sortBy'] = 'ord.id';
            }
            $result = $this->art->Screen_Listing($post);
            $header = array(
                0=>array('key' => 'ord.id', 'name' => 'Screen Set Name'),
                1=>array('key' => 'mt.value', 'name' => 'Position'),
                2=>array('key' => 'cl.client_company', 'name' => 'Client'),
                3=>array('key' => 'odp.color_stitch_count', 'name' => '#of Color'),
                4=>array('key' => 'screen_width', 'name' => '#of Screen'),
                5=>array('key' => 'asc.screen_width', 'name' => 'Frame size')
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



}