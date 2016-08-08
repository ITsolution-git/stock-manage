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
use Excel;
use App\Api;

class SettingController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Price $price,Common $common,Api $api) {

        $this->price = $price;
        $this->common = $common;
        $this->api = $api;
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

    public function downloadPricegridCSV()
    {
            $path = base_path().'/'; // change the path to fit your websites document structure
             
            $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', 'addpricegrid.xlsx'); // simple file name validation
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
    }
    public function uploadPricingCSV() {


            if(Input::hasFile('import_file')){

            $path = Input::file('import_file')->getRealPath();

            $data = Excel::selectSheetsByIndex(0,1,2,3,4,5,6,7,8)->load($path, function($reader) {

            })->get();

         
           // code start for price grid name
            if(!empty($data[0]) && $data->count()){

                foreach ($data[0] as $key => $value) {

                    $insert[] = ['name' => $value->name];
                }
               
                

                if(!empty($insert)) {

                       $insert[0]['company_id'] = $_POST['company_id'];
                       $insert[0]['created_date'] = date('Y-m-d');
                       

                       $price_grid = $this->common->InsertRecords('price_grid',$insert[0]);
                       $price_id = $price_grid;
                       
                }

            }

            // code end for price grid name

            // code start for charges
            if(!empty($data[1]) && $data[1]->count()){

                foreach ($data[1] as $key => $value) {

                    $charges[] = ['discharge' => $value->discharge,'foil' => $value->foil,'number_on_dark' => $value->number_on_dark,'poly_bagging' => $value->poly_bagging,'specialty' => $value->speciality,'folding' => $value->folding,'number_on_light' => $value->number_on_light,'press_setup' => $value->press_setup,'color_matching' => $value->color_matching,'hang_tag' => $value->hang_tag,'over_size' => $value->oversize,'printed_names' => $value->printed_name,'embroidered_names' => $value->embroidered_names,'ink_changes' => $value->ink_charges,'over_size_screens' => $value->oversize_screens,'screen_fees' => $value->screen_fees,'shipping_charge' => $value->shipping_charge];
                }
               
                if(!empty($charges)) {

                       $charges[0]['updated_date'] = date('Y-m-d');
                       $this->common->UpdateTableRecords('price_grid',array('id' => $price_id),$charges[0]);
                       
                }

            }

           // code end for charges

           // code start for charges List

                if(!empty($data[2]) && $data[2]->count()){

                foreach ($data[2] as $key => $value) {

                     $this->common->InsertRecords('price_grid_charges',array('price_id' => $price_id,'item' => $value->item,'charge' => $value->charge,'time' => $value->time,'is_per_line' => $value->available_to_per_line,'is_per_order' => $value->available_to_per_order,'is_per_screen_set' => $value->available_to_per_screen_set,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for charges List

            // code start for Screen printing primary

                if(!empty($data[3]) && $data[3]->count()){

                foreach ($data[3] as $key => $value) {

                     $this->common->InsertRecords('price_screen_primary',array('price_id' => $price_id,'range_high' => $value->high_range,'range_low' => $value->low_range,'pricing_1c' => $value->pricing_1c,'pricing_2c' => $value->pricing_2c,'pricing_3c' => $value->pricing_3c,'pricing_4c' => $value->pricing_4c,'pricing_5c' => $value->pricing_5c,'pricing_6c' => $value->pricing_6c,'pricing_7c' => $value->pricing_7c,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Screen printing primary

            // code start for Screen secondary primary

                if(!empty($data[4]) && $data[4]->count()){

                foreach ($data[4] as $key => $value) {

                     $this->common->InsertRecords('price_screen_secondary',array('price_id' => $price_id,'range_high' => $value->high_range,'range_low' => $value->low_range,'pricing_1c' => $value->pricing_1c,'pricing_2c' => $value->pricing_2c,'pricing_3c' => $value->pricing_3c,'pricing_4c' => $value->pricing_4c,'pricing_5c' => $value->pricing_5c,'pricing_6c' => $value->pricing_6c,'pricing_7c' => $value->pricing_7c,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Screen secondary primary

            // code start for Embroidery header

                if(!empty($data[5]) && $data[5]->count()){

                foreach ($data[5] as $key => $value) {

                     $embroidery_switch_id = $this->common->InsertRecords('embroidery_switch_count',array('price_id' => $price_id,'range_low_1' => $value->range_low_1,'range_high_1' => $value->range_high_1,'range_low_2' => $value->range_low_2,'range_high_2' => $value->range_high_2,'range_low_3' => $value->range_low_3,'range_high_3' => $value->range_high_3,'range_low_4' => $value->range_low_4,'range_high_4' => $value->range_high_4,'range_low_5' => $value->range_low_5,'range_high_5' => $value->range_high_5,'range_low_6' => $value->range_low_6,'range_high_6' => $value->range_high_6,'range_low_7' => $value->range_low_7,'range_high_7' => $value->range_high_7,'range_low_8' => $value->range_low_8,'range_high_8' => $value->range_high_8,'range_low_9' => $value->range_low_9,'range_high_9' => $value->range_high_9,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Embroidery header

            // code start for Embroidery Price

                if(!empty($data[6]) && $data[6]->count()){

                foreach ($data[6] as $key => $value) {

                     $this->common->InsertRecords('price_screen_embroidery',array('price_id' => $price_id,'embroidery_switch_id' => $embroidery_switch_id,'range_low' => $value->range_low,'range_high' => $value->range_high,'pricing_1c' => $value->pricing_1c,'pricing_2c' => $value->pricing_2c,'pricing_3c' => $value->pricing_3c,'pricing_4c' => $value->pricing_4c,'pricing_5c' => $value->pricing_5c,'pricing_6c' => $value->pricing_6c,'pricing_7c' => $value->pricing_7c,'pricing_8c' => $value->pricing_8c,'pricing_9c' => $value->pricing_9c,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Embroidery Price

            // code start for Embroidery Price

                if(!empty($data[7]) && $data[7]->count()){

                foreach ($data[7] as $key => $value) {

                     $this->common->InsertRecords('price_direct_garment',array('price_id' => $price_id,'range_low' => $value->range_low,'range_high' => $value->range_high,'pricing_1c' => $value->light_44,'pricing_2c' => $value->dark_44,'pricing_3c' => $value->light_66,'pricing_4c' => $value->dark_66,'pricing_5c' => $value->light_1010,'pricing_6c' => $value->dark_1010,'pricing_7c' => $value->light_1212,'pricing_8c' => $value->dark_1212,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Embroidery Price

            // code start for Garment Markup

                if(!empty($data[8]) && $data[8]->count()){

                foreach ($data[8] as $key => $value) {

                     $this->common->InsertRecords('price_garment_mackup',array('price_id' => $price_id,'range_low' => $value->range_low,'range_high' => $value->range_high,'percentage' => $value->percentage,'created_date' => date('Y-m-d'),'updated_date' => date('Y-m-d')));

                 }

            }

           // code end for Garment Markup
        }

       return redirect()->back();

        
    }
    public function uploadSnsCSV() {
        
/*        $response = array('success' => 1, 'message' => 'Data imported successfully');
        return response()->json(["data" => $response]);*/
        
        ini_set('display_errors', 1);
        ini_set("max_input_time", -1);
        /*$mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;*/

        $result_api = $this->api->getApiCredential(28,'api.sns','ss_detail');

        $credential = $result_api[0]->username.":".$result_api[0]->password;
 
        $curl = curl_init();

        // Inserting category start
        
        curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/categories/?mediatype=json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $category_result = curl_exec($curl);

        $category_all_data = json_decode($category_result);

        if(!empty($category_all_data))
        {
            $this->common->truncateTable('category');
            $this->common->truncateTable('product_brand_category');

            foreach ($category_all_data as $category) {
                $category_name = $category->name;
                $this->common->InsertRecords('category',array('id' => $category->categoryID,'category_name' => $category->name, 'category_image' => $category->image));
            }
        }

        // Inserting category ends

        // Inserting products start

        curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/styles/?mediatype=json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $product_result = curl_exec($curl);

        $product_all_data = json_decode($product_result);

        if(!empty($product_all_data))
        {
            foreach ($product_all_data as $product) {
                
                $product_data = $this->common->GetTableRecords('products',array('id' => $product->styleID),array());
                $product_name = $product->title;
                $description = $product->description;

                $product_arr = array('id' => $product->styleID, 'part_number' => $product->partNumber, 'vendor_id' => 1, 'name' => $product_name, 'description' => $description, 'product_image' => $product->styleImage);
                $product_id = $product->styleID;

                if(empty($product_data))
                {
                    $product_id = $this->common->InsertRecords('products',$product_arr);
                }
                else
                {
                    unset($product_arr['id']);
                    $this->common->UpdateTableRecords('products',array('id' => $product_id),$product_arr);
                }

                // product mapping with category

                if(!empty($product->categories))
                {
                    $categories = explode(",", $product->categories);
                    foreach ($categories as $category_id) {
                        $this->common->InsertRecords('product_brand_category',array('product_id'=>$product_id,'category_id'=>$category_id));
                    }
                }

                // product mapping with category ends

                curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/products/?style=".$product->partNumber."&mediaType=json");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl,CURLOPT_USERPWD,$credential);
                $product_detail = curl_exec($curl);

                $product_detail_data = json_decode($product_detail);

                if(!empty($product_detail_data))
                {
                    foreach ($product_detail_data as $data) {
                        
                        print_r($data);
                        $color_data = $this->common->GetTableRecords('color',array('name' => $data->colorName,'company_id'=>'0'),array());

                        if(empty($color_data))
                        {
                            $insert_color = array('name' => $data->colorName,'color_code' => $data->colorCode,'color_swatch_image' => $data->colorSwatchImage,'color_swatch_text_color' => $data->colorSwatchTextColor, 
                                                'color_front_image' => $data->colorFrontImage,'color_side_image' => $data->colorSideImage,'color_back_image' => $data->colorBackImage,'color1' => $data->color1,'color2'=>$data->color2);
                            $color_id = $this->common->InsertRecords('color',$insert_color);
                        }
                        else
                        {
                            $color_id = $color_data[0]->id;
                        }

                        $size_data = $this->common->GetTableRecords('product_size',array('name' => $data->sizeName,'is_sns'=>'1'),array());

                        if(empty($size_data))
                        {
                            $size_id = $this->common->InsertRecords('product_size',array('name' => $data->sizeName,'is_sns'=>'1'));
                        }
                        else
                        {
                            $size_id = $size_data[0]->id;
                        }

                        $color_size_data = $this->common->GetTableRecords('product_color_size',array('product_id'=>$product->styleID,'color_id'=>$color_id,'size_id'=>$size_id),array());

                        $insert = array('sku'=>$data->sku,'product_id'=>$product->styleID,'color_id'=>$color_id,'size_id'=>$size_id,'piece_price'=>$data->piecePrice,'dozen_price'=>$data->dozenPrice,
                                'case_price'=>$data->casePrice,'sale_price'=>$data->salePrice,'customer_price'=>$data->customerPrice);

                        if(empty($color_size_data))
                        {
                            $id = $this->common->InsertRecords('product_color_size',$insert);
                        }
                        else
                        {
                            $this->common->UpdateTableRecords('product_color_size',array('id' => $color_size_data[0]->id),$insert);
                        }
                    }
                }
            }
        }
        $response = array('success' => 1, 'message' => 'Data imported successfully');
        return response()->json(["data" => $response]);
        /*$mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = ($endtime - $starttime);
        echo "This page was created in ".$totaltime." seconds";
        curl_close($curl);*/
    }
}

