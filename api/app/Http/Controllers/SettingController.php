<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Price;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class SettingController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Price $price) {

        $this->price = $price;
       
    }

/**
* Price Listing controller        
* @access public price
* @return json data
*/

    public function price() {
 
        $result = $this->price->priceList();
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

/**
* Price Detail controller      
* @access public priceDetail
* @param  array $data
* @return json data
*/
    public function priceDetail() {
 
         $data = Input::all();
         

          $result = $this->price->priceDetail($data);
          
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['price'],'allPriceGrid' => $result['allPriceGrid']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['price'],'allPriceGrid' => $result['allPriceGrid']);
        }
        
        return response()->json(["data" => $response]);

    }


/**
* Price Delete controller      
* @access public delete
* @param  array $post
* @return json data
*/
    public function delete()
    {
        $post = Input::all();
       
        if(!empty($post[0]))
        {
            $getData = $this->price->priceDelete($post[0]);
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
* Price Add Controller       
* @access public priceAdd
* @param  array $data
* @return json data
*/
    public function priceAdd() {

        $data = Input::all();

          $result = $this->price->priceAdd($data['price'],$data['price_grid']);
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

/**
* Price Edit Controller       
* @access public priceEdit
* @param  array $data
* @return json data
*/

    public function priceEdit() {

       
         $data = Input::all();
         
         
          $result = $this->price->priceEdit($data['price']);
          $resultContact = $this->price->priceChargesEdit($data['price_grid'],$data['price']['id']);

          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }




}
