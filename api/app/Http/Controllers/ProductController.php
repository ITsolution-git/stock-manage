<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Product;
use App\Common;
use App\Order;
use App\Api;
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
    public function __construct(Product $product,Common $common,Api $api,Order $order) {

        $this->product = $product;
        $this->common = $common;
        $this->api = $api;
        $this->order = $order;
       
    }

/**
* Product Listing controller        
* @access public index
* @return json data
*/


/**
* Product Listing controller        
* @access public index
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="productList",
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
 *  path = "/api/public/admin/product",
 *  summary = "Product Listing",
 *  tags={"Product"},
 *  description = "Product Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Product Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/productList")
 *  ),
 *  @SWG\Response(response=200, description="Product Listing"),
 *  @SWG\Response(response="default", description="Product Listing"),
 * )
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


          $result['product'][0]->all_url_photo = UPLOAD_PATH.$data['company_id'].'/product/'.$result["product"][0]->id.'/'.$result['product'][0]->photo;

           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['product']);
            } else {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['product']);
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
        $post_all = Input::all();
        //print_r($post_all);exit;
        $records = array();

        $post = $post_all['cond']['params'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = 15;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'p.name';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'p.name';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $whereData = array();
        
        $whereData['vendor_id'] = $post['filter']['vendor_id'];

        if($post['filter']['vendor_id'] != '')
        {
            if(isset($post['filter']['search']))
            {
                $whereData['search'] = $post['filter']['search'];
            }
        }
        if(isset($post['filter']['category_id']) && !empty($post['filter']['category_id']))
        {
            $whereData['category_id'] = $post['filter']['category_id'];
        }
        if(isset($post['filter']['color_id']) && !empty($post['filter']['color_id']))
        {
            $whereData['color_id'] = $post['filter']['color_id'];
        }
        if(isset($post['filter']['size_id']) && !empty($post['filter']['size_id']))
        {
            $whereData['size_id'] = $post['filter']['size_id'];
        }

        $data['where'] = $whereData;
        $data['paginate'] = $post;
        $data['fields'] = array();
        $header = array();
        
        $result = $this->product->getVendorProducts($data);
        $count = (empty($result['count']))?'1':$result['count'];

        if(empty($result['count']))
        {
            $success = 0;
        }
        else
        {
            $success = 1;
        }

        //$count = $result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $count);

        
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }


        $data = array('header'=>$header,'rows' => $result['allData'],'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'category_filter' => $result['category_data'],'color_filter' => $result['color_data'],'size_filter' => $result['size_data'],'success'=>$success);
        return  response()->json($data);
    }

    public function productDetailData() {
 
        $data = Input::all();
        $result_api = $this->api->getApiCredential($data['company_id'],'api.sns','ss_detail');
       
       // print_r($result_api[0]->password);exit;
        $credential = $result_api[0]->username.":".$result_api[0]->password;
 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/products/?style=".$data['product_id']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $result = curl_exec($curl);
        curl_close($curl);

        $all_data = json_decode($result);

        if(empty($all_data))
        {
            $data_record = array("success"=>0,"message"=>"This product is no longer exists");
            $record_data = $this->common->DeleteTableRecords('products',array('id' => $data['product_id']));
            return response()->json(["data" => $data_record]);
        }
       

        $allDetail = array();
        if($data['design_id'] != 0) {
            $allDetail = $this->product->getPurchaseDetail($data['design_product_id']);
        }
        

        foreach($all_data as $key => $data) {


         
        $color_data = $this->common->getColorId($data->colorName);

            if(!empty($color_data))
            {
                if($key == 0) {
                    $productAllData['colorSelection'] = $data->colorName;
                }

                $productAllData['colorData'][$data->colorName]['sizes'][$key]['color_id'] = $color_data[0]->id;

                if(!isset($productAllData['colorData'][$data->colorName]['total'])) {
                    $productAllData['colorData'][$data->colorName]['total'] = 0;
                }
                

                 
                if(count($allDetail) > 0) {
                
                    if(isset($allDetail[$color_data[0]->id][$data->sizeName])){
                        $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)$allDetail[$color_data[0]->id][$data->sizeName];
                    }
               
                } else {
                    $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)0;
                }


                  foreach ($data->warehouses as $warehouse_detail) {           
                        $productAllData['colorData'][$data->colorName]['sizes'][$key]['inventory'][$warehouse_detail->warehouseAbbr] = $warehouse_detail->qty;
                    }

               
                  

              //  $productAllData['colorData'][$data->colorName]['sizes'][$key]['warehouse'] = $warehouse;
               

                $productAllData['colorData'][$data->colorName]['sizes'][$key]['sizeName'] = $data->sizeName;
                $productAllData['colorData'][$data->colorName]['sizes'][$key]['sku'] = $data->sku;
                //$productAllData['colorData'][$data->colorName]['sizes'][$key]['caseQty'] = $data->caseQty;
                $productAllData['colorData'][$data->colorName]['colorSwatchImage'] = $data->colorSwatchImage;
                $productAllData['colorData'][$data->colorName]['colorSwatchTextColor'] = $data->colorSwatchTextColor;
                $productAllData['colorData'][$data->colorName]['sizes'][$key]['customerPrice'] = $data->customerPrice;
                $productAllData['colorData'][$data->colorName]['colorFrontImage'] = $data->colorFrontImage;
                $productAllData['colorData'][$data->colorName]['colorSideImage'] = $data->colorSideImage;
                $productAllData['colorData'][$data->colorName]['colorBackImage'] = $data->colorBackImage;
                $productAllData['colorData'][$data->colorName]['colorName'] = $data->colorName;

                if(isset($productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'])) {
                    $productAllData['colorData'][$data->colorName]['total'] += $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] * $productAllData['colorData'][$data->colorName]['sizes'][$key]['customerPrice'];
                }
                
            }
        }
      
        return response()->json(["data" => $productAllData]);
    }
    

    public function getCustomProduct() {

       $post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'product.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'product.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->product->getCustomProduct($post);
        

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'product.id', 'name' => 'ID'),
                        1=>array('key' => 'product.name', 'name' => 'Name'),
                        2=>array('key' => 'v.name_company', 'name' => 'Vendor')
                        
                        );


            $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success' => $success);
        return  response()->json($data);


    }

    public function addProduct() {

        $post = Input::all();

        $post['created_date']=date('Y-m-d');

        /*$record_data = $this->common->UpdateTableRecords('purchase_detail',array('design_id' => $post['id']),array('is_delete' => '0'));
        $record_update = $this->common->UpdateTableRecords('design_product',array('design_id' => $post['id']),array('is_delete' => '0'));*/

        $result = $this->product->addProduct($post);

        $return = 1;
        $return = $this->orderCalculation($post['id']);

        if($post['action'] == 'Edit') {
            $message = 'Product updated successfully';
        }
        else{
            $message = 'Product added successfully';
        }

        if(is_array($return))
        {
            $data = array("success"=>0,"message"=>$message,"status"=>$return['status']);
            return response()->json(["data" => $data]);
        }
        else
        {
            $data = array("success"=>1,"message"=>$message,"status"=>$return['status']);
            return response()->json(["data" => $data]);
        }
    }

    public function orderCalculation($design_id)
    {
        $order_data = $this->order->getOrderByDesign($design_id);

        $price_id = $order_data[0]->price_id;
        $order_id = $order_data[0]->id;

        $price_grid_data = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $price_id),array());
        $price_grid = $price_grid_data[0];

        $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $design_id,'is_delete' => '1','is_calculate'=>'1'),array());

        if(!empty($design_product))
        {
            foreach ($design_product as $product) {
                
                $total_qnty = 0;
                $purchase_detail = array();
                $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('design_product_id' => $product->id,'is_delete' => '1'),array());
                
                foreach ($purchase_detail as $size) {
                    $total_qnty += $size->qnty;
                }

                $price_garment_mackup = $this->common->GetTableRecords('price_garment_mackup',array('price_id' => $price_id),array());
                $price_screen_primary = $this->common->GetTableRecords('price_screen_primary',array('price_id' => $price_id),array());
                $price_screen_secondary = $this->common->GetTableRecords('price_screen_secondary',array('price_id' => $price_id),array());
                $price_direct_garment = $this->common->GetTableRecords('price_direct_garment',array('price_id' => $price_id),array());
                $embroidery_switch_count = $this->common->GetTableRecords('embroidery_switch_count',array('price_id' => $price_id),array());

                $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $design_id,'is_delete' => '1','is_calculate'=>'1'),array());
                $data = array();
                $data['cond']['company_id'] = $order_data[0]->company_id;
                $miscData = $this->common->getAllMiscDataWithoutBlank($data);

                $color_stitch_count = 0;
                $position_qty = 0;
                $discharge_qnty = 0;
                $speciality_qnty = 0;
                $foil_qnty = 0;
                $ink_charge_qnty = 0;
                $number_on_dark_qnty = 0;
                $number_on_light_qnty = 0;
                $oversize_screens_qnty = 0;
                $press_setup_qnty = 0;
                $screen_fees_qnty = 0;
                $screen_fees_qnty_total = 0;

                $print_charges = 0;
                $os = 0;
                $per_line_total = 0;
                $total_screens = 0;
                $total_press_setup = 0;

                if(count($position_data) > 0)
                {
                    foreach($position_data as $position) {

                        $color_stitch_count = $position->color_stitch_count;
                        $position_qty = $position_data[0]->qnty;
                        if($position_qty == 0 || $position_qty == '')
                        {
                            $data = array("success"=>0,"message"=>"Enter first position quantity","status"=>"error");
                            return $data;
                        }
                        
                        $foil_qnty = $position->foil_qnty;
                        $number_on_dark_qnty = $position->number_on_dark_qnty;
                        $oversize_screens_qnty = $position->oversize_screens_qnty;
                        $ink_charge_qnty = $position->ink_charge_qnty;
                        $number_on_light_qnty = $position->number_on_light_qnty;
                        $press_setup_qnty = $position->press_setup_qnty;
                        $discharge_qnty = $position->discharge_qnty;
                        $speciality_qnty = $position->speciality_qnty;
                        $screen_fees_qnty = $position->screen_fees_qnty;
                        
                        
                        $screen_fees_qnty_total += $position->screen_fees_qnty;

                        $calc_descharge =  $discharge_qnty * $price_grid->discharge;
                        $calc_speciality =  $speciality_qnty * $price_grid->specialty;
                        $calc_foil =  $foil_qnty * $price_grid->foil;

                        $calc_ink_charge = $price_grid->ink_changes / $position_qty * $ink_charge_qnty;
                        $calc_number_on_dark = $price_grid->number_on_dark / $position_qty * $number_on_dark_qnty;
                        $calc_number_on_light = $price_grid->number_on_light / $position_qty * $number_on_light_qnty;

                        $calc_oversize =  $oversize_screens_qnty * $price_grid->over_size_screens;
                        $calc_press_setup =  $press_setup_qnty * $price_grid->press_setup;
                        $calc_screen_fees =  $screen_fees_qnty * $price_grid->screen_fees;

                        $total_screens += $calc_screen_fees;
                        $total_press_setup += $calc_press_setup;

                        $calc_total = $calc_descharge + $calc_speciality + $calc_foil + $calc_ink_charge + $calc_number_on_dark + $calc_number_on_light;
                        $print_charges +=  $calc_total;

                        if($position->placement_type > 0)
                        {
                            $placement_type_id =  $position->placement_type;
                            $miscData['placement_type'][$placement_type_id]->slug;
                            
                            if($miscData['placement_type'][$placement_type_id]->slug == 43)
                            {
                                foreach($price_screen_primary as $primary) {
                                    
                                    $price_field = 'pricing_'.$color_stitch_count.'c';

                                    if($position_qty >= $primary->range_low && $position_qty <= $primary->range_high)
                                    {
                                        if(isset($primary->$price_field))
                                        {
                                            $print_charges += $primary->$price_field;
                                        }
                                    }
                                }
                            }
                            elseif($miscData['placement_type'][$placement_type_id]->slug == 44)
                            {
                                foreach($price_screen_secondary as $secondary) {
                                    
                                    $price_field = 'pricing_'.$color_stitch_count.'c';

                                    if($position_qty >= $secondary->range_low && $position_qty <= $secondary->range_high)
                                    {
                                        if(isset($secondary->$price_field))
                                        {
                                            $print_charges += $secondary->$price_field;
                                        }
                                    }
                                }
                            }
                            elseif($miscData['placement_type'][$placement_type_id]->slug == 45)
                            {
                                foreach($embroidery_switch_count as $embroidery) {
                                    
                                    $price_field = 'pricing_'.$color_stitch_count.'c';

                                    if($color_stitch_count >= $embroidery->range_low_1 && $color_stitch_count <= $embroidery->range_high_1)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_1c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_2 && $color_stitch_count <= $embroidery->range_high_2)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_2c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_3 && $color_stitch_count <= $embroidery->range_high_3)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_3c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_4 && $color_stitch_count <= $embroidery->range_high_4)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_4c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_5 && $color_stitch_count <= $embroidery->range_high_5)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_5c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_6 && $color_stitch_count <= $embroidery->range_high_6)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_6c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_7 && $color_stitch_count <= $embroidery->range_high_7)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_7c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_8 && $color_stitch_count <= $embroidery->range_high_8)
                                    {
                                        $switch_id = $embroidery.id;
                                        $embroidery_field = 'pricing_8c';
                                    }
                                    if($color_stitch_count >= $embroidery->range_low_9 && $color_stitch_count <= $embroidery->range_high_9)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_9c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_10 && $color_stitch_count <= $embroidery->range_high_10)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_10c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_11 && $color_stitch_count <= $embroidery->range_high_11)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_11c';
                                    }
                                    elseif($color_stitch_count >= $embroidery->range_low_12 && $color_stitch_count <= $embroidery->range_high_12)
                                    {
                                        $switch_id = $embroidery->id;
                                        $embroidery_field = 'pricing_12c';
                                    }
                                }

                                if($switch_id > 0)
                                {
                                    $price_screen_embroidery = $this->common->GetTableRecords('price_screen_embroidery',array('embroidery_switch_id' => $switch_id),array());

                                    foreach ($price_screen_embroidery as $embroidery2) {
                                        
                                        if($position_qty >= $embroidery2->range_low && $position_qty <= $embroidery2->range_high)
                                        {
                                            $print_charges += $embroidery2->$embroidery_field;
                                        }
                                    }
                                }
                            }
                            elseif($miscData['placement_type'][$placement_type_id]->slug == 46)
                            {
                                $dtg_size_id =  $position->dtg_size;
                                $miscData['dir_to_garment_sz'][$dtg_size_id]->slug;

                                $dtg_on_id = $position->dtg_on;
                                $miscData['direct_to_garment'][$dtg_on_id]->slug;

                                if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 17 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                    $garment_field = 'pricing_1c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 17 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                    $garment_field = 'pricing_2c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 18 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                    $garment_field = 'pricing_3c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 18 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                    $garment_field = 'pricing_4c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 19 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                    $garment_field = 'pricing_5c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 19 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                    $garment_field = 'pricing_6c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 20 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                    $garment_field = 'pricing_7c';
                                }
                                else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 20 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                    $garment_field = 'pricing_8c';
                                }

                                foreach($price_direct_garment as $garment) {
                                    
                                    if($position_qty >= $garment->range_low && $position_qty <= $garment->range_high)
                                    {
                                        $print_charges += $garment->$garment_field;
                                    }
                                }
                            }
                        }
                    }

                    if($product->markup > 0)
                    {
                        $markup = $product->markup;
                    }
                    else
                    {
                        $markup = 0;
                    }

                    $avg_garment_cost = 0;
                    $markup_default = 0;
                    if(count($price_garment_mackup) > 0 && $position_qty > 0)
                    {
                        foreach($price_garment_mackup as $value) {
                            
                            if($position_qty >= $value->range_low && $position_qty <= $value->range_high)
                            {
                                $markup_default = $value->percentage;
                            }
                        }
                    }

                    $item_price = 0;
                    $line_qty = 0;
                    foreach($purchase_detail as $pd) {
                        if($pd->qnty > 0)
                        {
                            $price = $pd->price;
                            $sum = $price + $price_grid->shipping_charge;
                            $avg_garment_cost += $sum;
                            $line_qty += $pd->qnty;
                        }
                    }

                    if($avg_garment_cost == 0)
                    {
                        $avg_garment_cost = $price_grid->shipping_charge;
                    }

                    if($markup > 0)
                    {
                        $garment_mackup = $markup/100;
                    }
                    else
                    {
                        $garment_mackup = $markup_default/100;
                    }

                    $avg_garment_price = $avg_garment_cost * $garment_mackup + $avg_garment_cost;

                    if($product->extra_charges > 0)
                    {
                        $extraCharges = $product->extra_charges;
                    }
                    else
                    {
                        $extraCharges = 0;
                    }

                    $per_item = $avg_garment_price + $print_charges + $extraCharges;
                    $sales_total = $per_item * $line_qty;

                    $update_arr = array(
                                        'avg_garment_cost' => round($avg_garment_cost,2),
                                        'avg_garment_price' => round($avg_garment_price,2),
                                        'print_charges' => round($print_charges,2),
                                        'markup' => $markup,
                                        'markup_default' => $markup_default,
                                        'sales_total' => round($sales_total,2),
                                        'total_line_charge' => round($per_item,2)
                                        );

                    $this->common->UpdateTableRecords('design_product',array('design_id' => $design_id,'product_id' => $product->product_id),$update_arr);
                }
            }
        }
        else
        {
            $update_arr = array(
                                'avg_garment_cost' => 0,
                                'avg_garment_price' => 0,
                                'print_charges' => 0,
                                'markup' => 0,
                                'markup_default' => 0,
                                'sales_total' => 0,
                                'total_line_charge' => 0
                                );

            $this->common->UpdateTableRecords('design_product',array('design_id' => $design_id),$update_arr);
        }
        
        $design_product_total = $this->order->getDesignTotal($order_id);

        $all_design = $this->common->GetTableRecords('order_design',array('order_id' => $order_id, 'is_delete' => '1'),array());

        $total_screens = 0;
        $total_press_setup = 0;
        $total_foil = 0;
        $total_number_on_dark = 0;
        $total_oversize_screens = 0;
        $total_ink_charge = 0;
        $total_number_on_light = 0;
        $total_discharge = 0;
        $total_speciality = 0;

        foreach ($all_design as $design) {
            
            $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $design->id,'is_delete' => '1'),array());
            
            foreach ($position_data as $row) {

                $press_setup_qnty = $row->press_setup_qnty;
                $screen_fees_qnty = $row->screen_fees_qnty;
                $foil_qnty = $row->foil_qnty;
                $number_on_dark_qnty = $row->number_on_dark_qnty;
                $oversize_screens_qnty = $row->oversize_screens_qnty;
                $ink_charge_qnty = $row->ink_charge_qnty;
                $number_on_light_qnty = $row->number_on_light_qnty;
                $discharge_qnty = $row->discharge_qnty;
                $speciality_qnty = $row->speciality_qnty;
                

                $calc_press_setup =  $press_setup_qnty * $price_grid->press_setup;
                $calc_screen_fees =  $screen_fees_qnty * $price_grid->screen_fees;
                $calc_foil =  $foil_qnty * $price_grid->foil;
                $calc_number_on_dark =  $number_on_dark_qnty * $price_grid->number_on_dark;
                $calc_oversize_screens =  $oversize_screens_qnty * $price_grid->over_size_screens;
                $calc_ink_charge =  $ink_charge_qnty * $price_grid->ink_changes;
                $calc_number_on_light =  $number_on_light_qnty * $price_grid->number_on_light;
                $calc_discharge =  $discharge_qnty * $price_grid->discharge;
                $calc_speciality =  $speciality_qnty * $price_grid->specialty;

                $total_screens += $calc_screen_fees;
                $total_press_setup += $calc_press_setup;
                $total_foil += $calc_foil;
                $total_number_on_dark += $calc_number_on_dark;
                $total_oversize_screens += $calc_oversize_screens;
                $total_ink_charge += $calc_ink_charge;
                $total_number_on_light += $calc_number_on_light;
                $total_discharge += $calc_discharge;
                $total_speciality += $calc_speciality;
            }
        }

        $order_charges_total =  $total_screens + $total_press_setup + $total_foil + $total_number_on_dark + $total_oversize_screens + $total_ink_charge + 
                                $total_number_on_light + $total_discharge + $total_speciality + $order_data[0]->separations_charge + $order_data[0]->rush_charge + 
                                $order_data[0]->distribution_charge + $order_data[0]->digitize_charge + $order_data[0]->shipping_charge +
                                $order_data[0]->setup_charge + $order_data[0]->artwork_charge;

        $order_total = $design_product_total + $order_charges_total - $order_data[0]->discount;
        $tax = $order_total * $order_data[0]->tax_rate/100;
        $grand_total = $order_total + $tax;
        $balance_due = $grand_total - $order_data[0]->total_payments;

        $update_order_arr = array(
                                'screen_charge' => $total_screens,
                                'press_setup_charge' => $total_press_setup,
                                'order_line_total' => round($design_product_total,2),
                                'order_total' => round($order_total,2),
                                'tax' => round($tax,2),
                                'grand_total' => round($grand_total,2),
                                'balance_due' => round($balance_due,2),
                                'order_charges_total' => round($order_charges_total,2)
                                );

        $this->common->UpdateTableRecords('orders',array('id' => $order_id),$update_order_arr);
        return true;
    }

     public function designProduct() {
 
        $data = Input::all();
        $result = $this->product->designProduct($data);


        if(empty($result))
        {
           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

/*        if($result['product_id']) {

            $productArray = ['id' => $result['product_id']];
            $result_product = $this->product->productDetail($productArray);
            $calculate_data = $this->common->GetTableRecords('design_product',array('design_id' => $result['design_id'],'is_delete' => '1'),array());
       }*/
       
      
/*            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['design_product'],
                                'productData' => $result_product,
                                'calculate_data' => $calculate_data,
                                'colorName' => $result['colorName'],
                                'colorId' => $result['colorId'],
                                'is_supply' => $result['is_supply']
                                );*/
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'productData' => $result,
                                );
        
        return response()->json(["data" => $response]);

    }

     public function deleteAddProduct()
    {
        $post = Input::all();
       
        if(!empty($post['design_id']) && !empty($post['product_id']))
        {
            $design = $this->common->GetTableRecords('design_product',array('design_id' => $post['design_id'],'product_id' => $post['product_id']),array());
            
            $this->common->UpdateTableRecords('design_product',array('design_id' => $post['design_id'],'product_id' => $post['product_id']),array('is_delete' => '0'));
            $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['design_id'],'product_id' => $post['product_id']));
            $order_data = $this->order->getOrderByDesign($post['design_id']);

            $message = DELETE_RECORD;
            $success = 1;
            
            $return = app('App\Http\Controllers\OrderController')->calculateAll($order_data[0]->id,$order_data[0]->company_id);
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

    public function uploadCSV()
    {
        $post = Input::all();
      
       if(isset($post["file"])){
        $filename=$_FILES["file"]["tmp_name"];
         if($_FILES["file"]["size"] > 0)
         {
            $file = fopen($filename, "r");
            $flag = true;
            while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
            {
                if($flag) { $flag = false; continue; }
                
                if($emapData[4] != '') {

                    if($emapData[0] != '') {


                           $vendor_data = $this->common->GetTableRecords('vendors',array('name_company' => trim($emapData[4]),'company_id' => $post['company_id']),array());
                           
                           if(count($vendor_data)>0) {
                             $vendor_id = $vendor_data[0]->id;
                          
                           } else {
                                $vendor_name = array(
                                    'name_company'=>$emapData[4],
                                    'created_date' => date('Y-m-d'),
                                    'company_id' => $post['company_id']
                                    
                                    );
                                  $result = $this->common->InsertRecords('vendors',$vendor_name);
                                  $vendor_id = $result;
                           }

                           $product_data = $this->common->GetTableRecords('products',array('name' => trim($emapData[0]),'company_id' => $post['company_id'],'vendor_id' => $vendor_id),array());
                           
                           if(count($product_data)>0) {
                             $product_id = $product_data[0]->id;
                          
                           } else {
                                $product_name = array(
                                    'name'=>$emapData[0],
                                    'description'=>$emapData[5],
                                    'created_date' => date('Y-m-d'),
                                    'company_id' => $post['company_id'],
                                    'vendor_id' => $vendor_id
                                    
                                    );
                                  $result = $this->common->InsertRecords('products',$product_name);
                                  $product_id = $result;
                           }
                           if($emapData[1] != '') {
                           $color_data = $this->common->GetTableRecords('color',array('name' => trim($emapData[1]),'company_id' => $post['company_id'],'is_sns' => 0),array());
                           
                           if(count($color_data)>0) {
                             $color_id = $color_data[0]->id;
                          
                           } else {
                               $color_name = array(
                                        'name'=>$emapData[1],
                                        'is_sns' => 0,
                                        'company_id' => $post['company_id']
                                        );
                                $result_color = $this->common->InsertRecords('color',$color_name);
                                $color_id = $result_color;
                           }
                         
                           if($emapData[2] != '') {
                          
                                   $size_data = $this->common->GetTableRecords('product_size',array('name' => trim($emapData[2]),'company_id' => $post['company_id'],'is_sns' => 0),array());
                                   
                                       if(count($size_data)>0) {
                                         $size_id = $size_data[0]->id;
                                      
                                       } else {
                                           $size_name = array(
                                                    'name'=>$emapData[2],
                                                    'is_sns' => 0,
                                                    'company_id' => $post['company_id']
                                                    );
                                            $result_size = $this->common->InsertRecords('product_size',$size_name);
                                            $size_id = $result_size;
                                       }
                                   $product_color_data = $this->common->GetTableRecords('product_color_size',array('product_id' => $product_id,'color_id' => $color_id,'size_id' => $size_id),array());
                                   
                                       if(count($product_color_data) == 0) {
                                            
                                            $product_color_size = array(
                                                        'product_id'=>$product_id,
                                                        'color_id' => $color_id,
                                                        'size_id' => $size_id,
                                                        'customer_price' => $emapData[3]
                                                        );
                                            $result_size_color = $this->common->InsertRecords('product_color_size',$product_color_size);
                                            $id = $result_size_color;
                                        }
                            }
                        }
                      }
                } 
              
            }
            fclose($file);
            return redirect()->back();
        }
    }    

  }


    public function getProductDetailColorSize()
    {
        $post = Input::all();
        $result = $this->product->getProductDetailColorSize($post);
        return response()->json(["data" => $result]);
    }

     public function addcolorsize()
    {
        $post = Input::all();
       
        if(!empty($post['product_id']))
        {
            $record_data = $this->product->addcolorsize($post);
            
            if($record_data)
            {
                $message = INSERT_RECORD;
                $success = 1;
            }
            else
            {
                $message = "There is some erroe in insert";
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

    public function deleteSizeLink()
    {
        $post = Input::all();
       
        if(!empty($post['product_id']))
        {
            if($post['size_id'] == 0) {
                $record_data = $this->common->DeleteTableRecords('product_color_size',array('product_id' => $post['product_id'],'color_id' => $post['color_id']));
            } else {
                $record_data = $this->common->DeleteTableRecords('product_color_size',array('product_id' => $post['product_id'],'color_id' => $post['color_id'],'size_id' => $post['size_id']));
            }
            
           
            if($record_data)
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

    public function downloadCSV()
    {
            $path = base_path().'/'; // change the path to fit your websites document structure
             
            $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', 'addproduct.csv'); // simple file name validation
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
    public function checkSnsAuth()
    {
        $data = Input::all();
        $result_api = $this->api->getApiCredential($data['company_id'],'api.sns','ss_detail');
       
       // print_r($result_api[0]->password);exit;
        $credential = $result_api[0]->username.":".$result_api[0]->password;
 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/categories/1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result);

        if(isset($response->message))
        {
            $data = array("success"=>0);
        }
        else
        {
            $data = array("success"=>1);
        }
        return response()->json(['data'=>$data]);
    }
    public function getProductCountByVendor()
    {
        $data = Input::all();
        $count = $this->product->getProductCountByVendor($data['vendor_id']);

        if($count > 0)
        {
            $success = 1;
        }
        else
        {
            $success = 0;
        }
        
        $data = array("success"=>$success);
        return response()->json(['data'=>$data]);
    }
    public function getVendorByProductCount()
    {
        $data = Input::all();
        $result = $this->product->getVendorByProductCount($data['company_id']);

        if($result)
        {
            $success = 1;
        }
        else
        {
            $success = 0;
        }
        $data = array("success"=>$success,"records"=>$result);
        return response()->json(['data'=>$data]);
    }
    public function getProductSize()
    {
        $data = Input::all();
        $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('design_product_id' => $data['design_product_id'],'is_delete' => '1'),array());
                
        foreach ($purchase_detail as $size) {
            $size->affiliate_qnty = (int)$size->qnty;
        }
        $data = array("success"=>1,"records"=>$purchase_detail);
        return response()->json(['data'=>$data]);
    }
    public function checkProductExist()
    {
        $data = Input::all();
        $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $data['design_id'],'product_id' => $data['product_id'],'is_delete' => '1'),array());

        if(!empty($design_product))
        {
            $success = 1;
        }
        else
        {
            $success = 0;
        }
        $data = array("success"=>$success,"records"=>$design_product);
        return response()->json(['data'=>$data]);
    }

    public function findTotal() {

        $post = Input::all();
       
        $total = 0;
        
        foreach($post['productData'] as $key => $data) {

             if(isset($data['qnty'])) {
                $total += $data['customerPrice'] * $data['qnty'];
             }
             
        }
        

        $data = array("success"=>1,"message"=>'Data',"total"=>$total);
        return response()->json(["data" => $data]);
        
    }
}