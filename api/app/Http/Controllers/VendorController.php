<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Vendor;
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
    public function __construct(Vendor $vendor) {

        $this->vendor = $vendor;
       
    }

/**
* Vendor Listing controller        
* @access public index
* @return json data
*/

    public function index() {
 
        $result = $this->vendor->vendorList();
       
       
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
* Vendor Add controller      
* @access public add
* @param  array $data
* @return json data
*/
    public function add() {
      
      $vendor_contact = json_decode($_REQUEST['vendor_contact_data_all']);
    
        $data['vendor'] = array('name_company' => isset($_REQUEST['name_company']) ? $_REQUEST['name_company'] : '',
            'prime_address_city' => isset($_REQUEST['prime_address_city']) ? $_REQUEST['prime_address_city'] : '',
            'prime_address_state' => isset($_REQUEST['prime_address_state']) ? $_REQUEST['prime_address_state'] : '',
            'prime_address_zip' => isset($_REQUEST['prime_address_zip']) ? $_REQUEST['prime_address_zip'] : '',
            'prime_address1' => isset($_REQUEST['prime_address1']) ? $_REQUEST['prime_address1'] : '',
            'prime_address2' => isset($_REQUEST['prime_address2']) ? $_REQUEST['prime_address2'] : '',
            'prime_phone_no' => isset($_REQUEST['prime_phone_no']) ? $_REQUEST['prime_phone_no'] : '',
            'fax_number' => isset($_REQUEST['fax_number']) ? $_REQUEST['fax_number'] : '',

            'mailing_address1' => isset($_REQUEST['mailing_address1']) ? $_REQUEST['mailing_address1'] : '',
            'mailing_address2' => isset($_REQUEST['mailing_address2']) ? $_REQUEST['mailing_address2'] : '',
            'mailing_address_city' => isset($_REQUEST['mailing_address_city']) ? $_REQUEST['mailing_address_city'] : '',
            'mailing_address_state' => isset($_REQUEST['mailing_address_state']) ? $_REQUEST['mailing_address_state'] : '',
            'mailing_address_zip' => isset($_REQUEST['mailing_address_zip']) ? $_REQUEST['mailing_address_zip'] : '',


            'billing_address1' => isset($_REQUEST['billing_address1']) ? $_REQUEST['billing_address1'] : '',
            'billing_address2' => isset($_REQUEST['billing_address2']) ? $_REQUEST['billing_address2'] : '',
            'billing_address_city' => isset($_REQUEST['billing_address_city']) ? $_REQUEST['billing_address_city'] : '',
            'billing_address_state' => isset($_REQUEST['billing_address_state']) ? $_REQUEST['billing_address_state'] : '',
            'billing_address_zip' => isset($_REQUEST['billing_address_zip']) ? $_REQUEST['billing_address_zip'] : '',

            'url' => isset($_REQUEST['url']) ? $_REQUEST['url'] : '',
            'order_minimum' => isset($_REQUEST['order_minimum']) ? $_REQUEST['order_minimum'] : '',

            'note' => isset($_REQUEST['note']) ? $_REQUEST['note'] : '',


            'd_qb_terms' => isset($_REQUEST['d_qb_terms']) ? $_REQUEST['d_qb_terms'] : '',
            'd_qb_list_id' => isset($_REQUEST['d_qb_list_id']) ? $_REQUEST['d_qb_list_id'] : '',
            'd_qb_edit_sequence' => isset($_REQUEST['d_qb_edit_sequence']) ? $_REQUEST['d_qb_edit_sequence'] : '',
            'd_qb_full_name' => isset($_REQUEST['d_qb_full_name']) ? $_REQUEST['d_qb_full_name'] : '',
            'd_qb_is_active' => isset($_REQUEST['d_qb_is_active']) ? $_REQUEST['d_qb_is_active'] : '',
            'd_qb_results' => isset($_REQUEST['d_qb_results']) ? $_REQUEST['d_qb_results'] : '',


            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : '',
        );



                foreach($data['vendor'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['vendor'][$key]); 
                    } 
                } 



          $insertedid = $this->vendor->vendorAdd($data,$vendor_contact);

          if ($insertedid && $_FILES) {

                if (!$_FILES['image']['error'] && isset($insertedid)) {

                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = base_path() . "/public/uploads/vendor/" . $insertedid;
                    VendorController::create_dir($uploaddir);
                    
                    $newfilename = "vendor-".time().".".$extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->vendor->vendorImageUpdate($insertedid,$newfilename);
                    }
                }

            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
        }

        
        return response()->json(["data" => $response]);

    }


    /**
* Vendor Edit controller      
* @access public edit
* @param  array $data
* @return json data
*/
    public function edit() {

$vendor_contact = json_decode($_REQUEST['vendor_contact_data_all']);
         
          $data['vendor'] = array('id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : '',
            'name_company' => isset($_REQUEST['name_company']) ? $_REQUEST['name_company'] : '',
            'prime_address_city' => isset($_REQUEST['prime_address_city']) ? $_REQUEST['prime_address_city'] : '',
            'prime_address_state' => isset($_REQUEST['prime_address_state']) ? $_REQUEST['prime_address_state'] : '',
            'prime_address_zip' => isset($_REQUEST['prime_address_zip']) ? $_REQUEST['prime_address_zip'] : '',
            'prime_address1' => isset($_REQUEST['prime_address1']) ? $_REQUEST['prime_address1'] : '',
            'prime_address2' => isset($_REQUEST['prime_address2']) ? $_REQUEST['prime_address2'] : '',
            'prime_phone_no' => isset($_REQUEST['prime_phone_no']) ? $_REQUEST['prime_phone_no'] : '',
            'fax_number' => isset($_REQUEST['fax_number']) ? $_REQUEST['fax_number'] : '',

            'mailing_address1' => isset($_REQUEST['mailing_address1']) ? $_REQUEST['mailing_address1'] : '',
            'mailing_address2' => isset($_REQUEST['mailing_address2']) ? $_REQUEST['mailing_address2'] : '',
            'mailing_address_city' => isset($_REQUEST['mailing_address_city']) ? $_REQUEST['mailing_address_city'] : '',
            'mailing_address_state' => isset($_REQUEST['mailing_address_state']) ? $_REQUEST['mailing_address_state'] : '',
            'mailing_address_zip' => isset($_REQUEST['mailing_address_zip']) ? $_REQUEST['mailing_address_zip'] : '',


            'billing_address1' => isset($_REQUEST['billing_address1']) ? $_REQUEST['billing_address1'] : '',
            'billing_address2' => isset($_REQUEST['billing_address2']) ? $_REQUEST['billing_address2'] : '',
            'billing_address_city' => isset($_REQUEST['billing_address_city']) ? $_REQUEST['billing_address_city'] : '',
            'billing_address_state' => isset($_REQUEST['billing_address_state']) ? $_REQUEST['billing_address_state'] : '',
            'billing_address_zip' => isset($_REQUEST['billing_address_zip']) ? $_REQUEST['billing_address_zip'] : '',

            'url' => isset($_REQUEST['url']) ? $_REQUEST['url'] : '',
            'order_minimum' => isset($_REQUEST['order_minimum']) ? $_REQUEST['order_minimum'] : '',

            'note' => isset($_REQUEST['note']) ? $_REQUEST['note'] : '',


            'd_qb_terms' => isset($_REQUEST['d_qb_terms']) ? $_REQUEST['d_qb_terms'] : '',
            'd_qb_list_id' => isset($_REQUEST['d_qb_list_id']) ? $_REQUEST['d_qb_list_id'] : '',
            'd_qb_edit_sequence' => isset($_REQUEST['d_qb_edit_sequence']) ? $_REQUEST['d_qb_edit_sequence'] : '',
            'd_qb_full_name' => isset($_REQUEST['d_qb_full_name']) ? $_REQUEST['d_qb_full_name'] : '',
            'd_qb_is_active' => isset($_REQUEST['d_qb_is_active']) ? $_REQUEST['d_qb_is_active'] : '',
            'd_qb_results' => isset($_REQUEST['d_qb_results']) ? $_REQUEST['d_qb_results'] : '',


            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : '',
        );

         foreach($data['vendor'] as $key => $link) 
        { 

            if($link == '') 
            { 
                unset($data['vendor'][$key]); 
            } 
        } 



        
          $result = $this->vendor->vendorEdit($data['vendor']);
          $resultContact = $this->vendor->vendorContactEdit($vendor_contact,$_REQUEST['id']);

          if (count($result) > 0) {


            if ($_FILES) {

                if (!$_FILES['image']['error'] && isset($data['vendor']['id'])) {

                      

                     array_map('unlink', glob(base_path() . "/public/uploads/vendor/" . $data['vendor']['id']."/*"));

                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = base_path() . "/public/uploads/vendor/" . $data['vendor']['id'];
                    VendorController::create_dir($uploaddir);
                    
                   
                    $newfilename = "vendor-".time().".".$extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->vendor->vendorImageUpdate($data['vendor']['id'],$newfilename);
                    }
                }

            
        } 

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
          

          $result['vendor'][0]->all_url_photo = UPLOAD_PATH.'vendor/'.$result["vendor"][0]->id.'/'.$result['vendor'][0]->photo;


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
            chmod($dir_path, 0777);
        }
    }




}
