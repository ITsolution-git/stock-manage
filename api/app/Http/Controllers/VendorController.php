<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Vendor;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class VendorController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Vendor $vendor,Common $common) {

        parent::__construct();
        $this->vendor = $vendor;
        $this->common = $common;
       
    }

/**
* Vendor Listing controller        
* @access public index
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="vendorList",
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
 *  path = "/api/public/admin/vendor",
 *  summary = "Vendor Listing",
 *  tags={"Vendor"},
 *  description = "Vendor Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Vendor Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/vendorList")
 *  ),
 *  @SWG\Response(response=200, description="Vendor Listing"),
 *  @SWG\Response(response="default", description="Vendor Listing"),
 * )
 */

    public function index() {
         $post = Input::all();
        $result = $this->vendor->vendorList($post);
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

    /**
* Vendor Delete controller      
* @access public detail
* @param  array $post
* @return json data
*/
    public function delete()
    {
        $post = Input::all();
       
        if(!empty($post[0]))
        {
            $getData = $this->vendor->vendorDelete($post[0]);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
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
* Vendor Edit controller      
* @access public edit
* @param  array $data
* @return json data
*/
    public function edit() {
        $data = Input::all();
       
       
        $email = $this->common->checkExistData($data['vendor']['email'],$data['vendor']['id'],'email','vendors',$data['company_id']);
        $companyname = $this->common->checkExistData($data['vendor']['name_company'],$data['vendor']['id'],'name_company','vendors',$data['company_id']);

            if(count($email)>0)
            {
                $message = "Email Already Exists";
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

         
          $result = $this->vendor->vendorEdit($data['vendor']);
          

          if (count($result) > 0) {

         $response = array('success' => 1, 'message' => UPDATE_RECORD,'records' => $result);
           
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
        }
        

return response()->json(["data" => $response]);
    }


/**
* Vendor Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function detail() {
 
         $data = Input::all();
        

          $result = $this->vendor->vendorDetail($data);
          

          $result['vendor'][0]->all_url_photo = UPLOAD_PATH.$data['company_id'].'/vendor/'.$result["vendor"][0]->id.'/'.$result['vendor'][0]->photo;


           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['vendor'],'allContacts' => $result['allContacts']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['vendor'],'allContacts' => $result['allContacts']);
        }
        
        return response()->json(["data" => $response]);

    }



    /**
* Making the directory and given path     
* @access public create_dir
* @param  string $dir_path
*/

public function create_dir($dir_path) {

        if (!file_exists($dir_path)) {
           
            mkdir($dir_path, 0777, true);
        } else {
            exec("chmod $dir_path 0777");
        }
    }


    /**
* all products of vendor      
* @access productVendor
* @param  array $data
* @return json data
*/
    public function productVendor() {
 
         $data = Input::all();
        
          $result = $this->vendor->productVendor($data);
          
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }





}
