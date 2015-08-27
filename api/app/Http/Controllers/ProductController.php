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
 
        $result = $this->product->productList();
       
       
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
         
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['product']);
            } else {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['product']);
            }
        
        return response()->json(["data" => $response]);

    }


}
