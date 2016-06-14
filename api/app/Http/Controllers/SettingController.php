<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Price;
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

class SettingController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Price $price,Common $common) {

        $this->price = $price;
        $this->common = $common;
       
    }

/**
* Price Listing controller        
* @access public price
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="priceList",
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
 *  path = "/api/public/admin/price",
 *  summary = "Pricegrid Listing",
 *  tags={"Setting"},
 *  description = "Pricegrid Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Pricegrid Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/priceList")
 *  ),
 *  @SWG\Response(response=200, description="Pricegrid Listing"),
 *  @SWG\Response(response="default", description="Pricegrid Listing"),
 * )
 */

    public function price() {
        $post = Input::all();
        $result = $this->price->priceList($post);
     
       
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
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['price'],'allPriceGrid' => $result['allPriceGrid'],'allScreenPrimary' => $result['allScreenPrimary'],'allScreenSecondary' => $result['allScreenSecondary'],'allGarmentMackup' => $result['allGarmentMackup'],'allGarment' => $result['allGarment'],'embroswitch' => $result['embroswitch'],'allEmbroidery' => $result['allEmbroidery']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['price'],'allPriceGrid' => $result['allPriceGrid'],'allScreenPrimary' => $result['allScreenPrimary'],'allScreenSecondary' => $result['allScreenSecondary'],'allGarmentMackup' => $result['allGarmentMackup'],'allGarment' => $result['allGarment'],'embroswitch' => $result['embroswitch'],'allEmbroidery' => $result['allEmbroidery']);
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
* Price Grid Duplicate Controller       
* @access public priceGridDuplicate
* @param  array $data
* @return json data
*/
    public function priceGridDuplicate() {

        $data = Input::all();

        

          $result = $this->price->priceGridDuplicate($data['price'],$data['price_grid'],$data['price_primary'],$data['price_secondary'],$data['garment_mackup'],$data['garment'],$data['embroswitch'],$data['allEmbroidery']);
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
          $resultPricePrimary = $this->price->priceChargesPrimaryEdit($data['price_primary'],$data['price']['id']);
          $resultPriceSecondary = $this->price->priceChargesSecondaryEdit($data['price_secondary'],$data['price']['id']);
          $resultGarmentMackup = $this->price->priceGarmentMackupEdit($data['garment_mackup'],$data['price']['id']);
          $resultDirectGarment = $this->price->priceDirectGarmentEdit($data['garment'],$data['price']['id']);
          $resultEmbroSwitch = $this->price->priceEmbroSwitchEdit($data['embroswitch'],$data['price']['id']);
          $resultPriceEmbro = $this->price->priceEmbroEdit($data['allEmbroidery'],$data['price']['id'],$data['embroswitch']['id']);

          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

    /**
* Price Duplicate controller      
* @access public duplicate
* @param  array $post
* @return json data
*/
    public function duplicate()
    {
        $post = Input::all();
       

        if(!empty($post[0]))
        {
            $getData = $this->price->priceGridDuplicate($post[0]);
            if($getData)
            {
                $message = INSERT_RECORD;
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
* Price Grid Primary Duplicate Controller       
* @access public priceGridPrimaryDuplicate
* @param  array $data
* @return json data
*/
    public function priceGridPrimaryDuplicate() {

        $data = Input::all();

        
        $resultPricePrimary = $this->price->priceChargesPrimaryEdit($data['price_primary'],$data['price_id']);
        $resultPriceSecondary = $this->price->priceChargesSecondaryEdit($data['price_primary'],$data['price_id']);

          if (count($resultPriceSecondary) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $resultPriceSecondary);
           }
        
        return response()->json(["data" => $response]);

    }


/**
* Placement Update data       
* @access public placementSave
* @param  array $data
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="placementSave",
 *      type="object",
 *      required={"updatedcolumn", "id","columnname"},
 *      @SWG\Property(
 *          property="updatedcolumn",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="columnname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/admin/placementSave",
 *  summary = "Placement Update",
 *  tags={"Setting"},
 *  description = "Placement Update",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Placement Update",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/placementSave")
 *  ),
 *  @SWG\Response(response=200, description="Placement Update"),
 *  @SWG\Response(response="default", description="Placement Update"),
 * )
 */



    public function placementSave() {

          $data = Input::all();
          $result = $this->price->placementSave($data);
         
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }



    /**
* Color Update data       
* @access public colorSave
* @param  array $data
* @return json data
*/

/** 
 * @SWG\Definition(
 *      definition="colorSave",
 *      type="object",
 *      required={"updatedcolumn", "id","columnname"},
 *      @SWG\Property(
 *          property="updatedcolumn",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="columnname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/admin/colorSave",
 *  summary = "Color Update",
 *  tags={"Color"},
 *  description = "Color Update",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Color Update",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/colorSave")
 *  ),
 *  @SWG\Response(response=200, description="Color Update"),
 *  @SWG\Response(response="default", description="Color Update"),
 * )
 */


    public function colorSave() {

          $data = Input::all();
          
          $color = $this->common->checkExistData($data['updatedcolumn'],$data['id'],'name','color',0);

           if(count($color)>0)
            {
                $message = "Color Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }

          $result = $this->price->colorSave($data);
         
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }


/**
* color Insert data       
* @access public colorInsert
* @param  array $data
* @return json data
*/



    /**
* Color Update data       
* @access public colorSave
* @param  array $data
* @return json data
*/

/** 
 * @SWG\Definition(
 *      definition="colorInsert",
 *      type="object",
 *      required={"updatedcolumn","columnname"},
 *      @SWG\Property(
 *          property="updatedcolumn",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="columnname",
 *          type="string"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/admin/colorInsert",
 *  summary = "Color Add",
 *  tags={"Color"},
 *  description = "Color Add",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Color Add",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/colorInsert")
 *  ),
 *  @SWG\Response(response=200, description="Color Add"),
 *  @SWG\Response(response="default", description="Color Add"),
 * )
 */


    public function colorInsert() {

          $data = Input::all();
         

          $color = $this->common->checkExistData($data['updatedcolumn'],0,'name','color',0);

          if(count($color)>0)
            {
                $message = "Color Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }



          $result = $this->price->colorInsert($data);
         
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }


/**
* Price Secondary controller      
* @access public priceSecondary
* @param  array $data
* @return json data
*/
    public function priceSecondary() {
 
         $data = Input::all();
         
          $result = $this->price->priceSecondary($data);
          
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'allScreenSecondary' => $result['allScreenSecondary']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'allScreenSecondary' => $result['allScreenSecondary']);
        }
        
        return response()->json(["data" => $response]);

    }
/*
    public function downloadPricegridCSV()
    {
            $path = base_path().'/'; // change the path to fit your websites document structure
             
            $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', 'addpricegrid.csv'); // simple file name validation
            $dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
            $fullPath = $path.$dl_file;
             
            if ($fd = fopen ($fullPath, "r")) {
                $fsize = filesize($fullPath);
                $path_parts = pathinfo($fullPath);
                $ext = strtolower($path_parts["extension"]);
                switch ($ext) {
                    case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
                    break;
                    // add more headers for other content types here
                    default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
                    break;
                }
                header("Content-length: $fsize");
                header("Cache-control: private"); //use this to open files directly
                while(!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
            }
            fclose ($fd);
            exit;
    }*/

  }

