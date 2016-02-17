<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Product;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class ProductController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Product $product) {

        $this->product = $product;
       
    }

/**
* Product Listing controller        
* @access public index
* @return json data
*/

    public function index() {
 $post = Input::all();
        $result = $this->product->productList($post);
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

/**
* Product Delete controller      
* @access public delete
* @param  array $post
* @return json data
*/
    public function delete()
    {
        $post = Input::all();
      
        if(!empty($post[0]))
        {

            $getData = $this->product->productDelete($post[0]);
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
* Product Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function detail() {
 
         $data = Input::all();
         
          $result = $this->product->productDetail($data);


          $result['product'][0]->all_url_photo = UPLOAD_PATH.'product/'.$result["product"][0]->id.'/'.$result['product'][0]->photo;

           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['product']);
            } else {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['product']);
            }
        
        return response()->json(["data" => $response]);

    }

/**
* Product Add controller      
* @access public add
* @param  array $data
* @return json data
*/
    public function add() {

       
       $data['product'] = array('description' => isset($_REQUEST['description']) ? $_REQUEST['description'] : '',
                                'vendor_id' => isset($_REQUEST['vendor_id']) ? $_REQUEST['vendor_id'] : '',
                                'brand' => isset($_REQUEST['brand']) ? $_REQUEST['brand'] : '',
                                'vendor_sku' => isset($_REQUEST['vendor_sku']) ? $_REQUEST['vendor_sku'] : '',
                                'name' => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
                                'note' => isset($_REQUEST['note']) ? $_REQUEST['note'] : '',
                                'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : ''
          );

                foreach($data['product'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['product'][$key]); 
                    } 
                } 


          $insertedid = $this->product->productAdd($data);

          if ($insertedid && $_FILES) {

                if (!$_FILES['image']['error'] && isset($insertedid)) {

                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = base_path() . "/public/uploads/product/" . $insertedid;
                    ProductController::create_dir($uploaddir);
                    
                   
                    $newfilename = "product-".time().".".$extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->product->productImageUpdate($insertedid,$newfilename);
                    }
                }

            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
        }

        
        return response()->json(["data" => $response]);

    }

/**
* Product Edit controller      
* @access public edit
* @param  array $data
* @return json data
*/
    public function edit() {
 
          $data['product'] = array('id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : '',
            'description' => isset($_REQUEST['description']) ? $_REQUEST['description'] : '',
            'vendor_id' => isset($_REQUEST['vendor_id']) ? $_REQUEST['vendor_id'] : '',
            'brand' => isset($_REQUEST['brand']) ? $_REQUEST['brand'] : '',
            'vendor_sku' => isset($_REQUEST['vendor_sku']) ? $_REQUEST['vendor_sku'] : '',
            'name' => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            'note' => isset($_REQUEST['note']) ? $_REQUEST['note'] : '',
            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : ''
          );

                foreach($data['product'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['product'][$key]); 
                    } 
                } 

          $result = $this->product->productEdit($data['product']);
          

          if (count($result) > 0) {


            if ($_FILES) {

                if (!$_FILES['image']['error'] && isset($data['product']['id'])) {

                      $delete_dir = base_path() . "/public/uploads/product/" . $data['product']['id'];
                      exec('rm -rf '.escapeshellarg($delete_dir));


                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = base_path() . "/public/uploads/product/" . $data['product']['id'];
                    ProductController::create_dir($uploaddir);
                    
                   
                    $newfilename = "product-".time().".".$extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->product->productImageUpdate($data['product']['id'],$newfilename);
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

    public function getProductByVendor()
    {
        $post = Input::all();
        $data['where'] = $post;
        $data['fields'] = array('product_name');
        $result = $this->product->getVendorProducts($data);
        
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

}
