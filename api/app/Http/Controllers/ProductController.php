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
            $whereData['search'] = $post['filter']['search'];
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
       

       $allDetail = array();
       if($data['design_id'] != 0) {
        $allDetail = $this->product->getPurchaseDetail($data['design_id']);
       }

        foreach($all_data as $key => $data) {
             
            $color_data = $this->common->getColorId($data->colorName);

            if($key == 0) {
                $productAllData['colorSelection'] = $data->colorName;
            }

            $productAllData['colorData'][$data->colorName]['sizes'][$key]['color_id'] = $color_data[0]->id;

            if(count($allDetail) > 0) {
            
                if(isset($allDetail[$data->sizeName])){
                    $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)$allDetail[$data->sizeName];
                }
           
            } else {
                $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)0;
            }
        
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['sizeName'] = $data->sizeName;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['sku'] = $data->sku;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['caseQty'] = $data->caseQty;
            $productAllData['colorData'][$data->colorName]['colorSwatchImage'] = $data->colorSwatchImage;
            $productAllData['colorData'][$data->colorName]['colorSwatchTextColor'] = $data->colorSwatchTextColor;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['customerPrice'] = $data->customerPrice;
            $productAllData['colorData'][$data->colorName]['colorFrontImage'] = $data->colorFrontImage;
            $productAllData['colorData'][$data->colorName]['colorSideImage'] = $data->colorSideImage;
            $productAllData['colorData'][$data->colorName]['colorBackImage'] = $data->colorBackImage;
            $productAllData['colorData'][$data->colorName]['colorName'] = $data->colorName;

            return response()->json(["data" => $productAllData]);
        }
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
                        1=>array('key' => 'product.name', 'name' => 'Name')
                        
                        );


            $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success' => $success);
        return  response()->json($data);


    }

    public function addProduct() {

        $post = Input::all();
        /*$post['created_date']=date('Y-m-d');
        $record_delete = $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['id']));
        $record_delete = $this->common->DeleteTableRecords('design_product',array('design_id' => $post['id']));
        $post['record_delete']=$record_delete;
        $result = $this->product->addProduct($post);
        $message = INSERT_RECORD;
        $success = 1;

        $total_qnty = 0;
        foreach ($post['productData'] as $size) {
            $total_qnty += $size['qnty'];
        }*/
        
        $order_data = $this->order->getOrderByDesign($post['id']);
        $price_id = $order_data[0]->price_id;
        $order_id = $order_data[0]->id;

        $price_grid_data = $this->common->GetTableRecords('price_grid',array('is_delete' => '1','status' => '1','id' => $price_id),array());
        $price_grid = $price_grid_data[0];

        $price_garment_mackup = $this->common->GetTableRecords('price_garment_mackup',array('price_id' => $price_id),array());
        $price_screen_primary = $this->common->GetTableRecords('price_screen_primary',array('price_id' => $price_id),array());
        $price_screen_secondary = $this->common->GetTableRecords('price_screen_secondary',array('price_id' => $price_id),array());
        $price_direct_garment = $this->common->GetTableRecords('price_direct_garment',array('price_id' => $price_id),array());
        $embroidery_switch_count = $this->common->GetTableRecords('embroidery_switch_count',array('price_id' => $price_id),array());

        $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $post['id']),array());
        $data = array();
        $data['cond']['company_id'] = $post['company_id'];
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
                if($position_qty == 0)
                {
                    $data = array("success"=>0,"message"=>"Enter first position quantity","status"=>"error");
                    return response()->json(['data'=>$data]);
                }
                $discharge_qnty = $position->discharge_qnty;
                $speciality_qnty = $position->speciality_qnty;
                $foil_qnty = $position->foil_qnty;
                $ink_charge_qnty = $position->ink_charge_qnty;
                $number_on_dark_qnty = $position->number_on_dark_qnty;
                $number_on_light_qnty = $position->number_on_light_qnty;
                $oversize_screens_qnty = $position->oversize_screens_qnty;
                $press_setup_qnty = $position->press_setup_qnty;
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
                    else if($miscData['placement_type'][$placement_type_id]->slug == 44)
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
                    else if($miscData['placement_type'][$placement_type_id]->slug == 45)
                    {
                        foreach($embroidery_switch_count as $embroidery) {
                            
                            $price_field = 'pricing_'.$color_stitch_count.'c';

                            if($color_stitch_count >= $embroidery->range_low_1 && $color_stitch_count <= $embroidery->range_high_1)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_1c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_2 && $color_stitch_count <= $embroidery->range_high_2)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_2c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_3 && $color_stitch_count <= $embroidery->range_high_3)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_3c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_4 && $color_stitch_count <= $embroidery->range_high_4)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_4c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_5 && $color_stitch_count <= $embroidery->range_high_5)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_5c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_6 && $color_stitch_count <= $embroidery->range_high_6)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_6c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_7 && $color_stitch_count <= $embroidery->range_high_7)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_7c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_8 && $color_stitch_count <= $embroidery->range_high_8)
                            {
                                $switch_id = $embroidery.id;
                                $embroidery_field = 'pricing_8c';
                            }
                            if($color_stitch_count >= $embroidery->range_low_9 && $color_stitch_count <= $embroidery->range_high_9)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_9c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_10 && $color_stitch_count <= $embroidery->range_high_10)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_10c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_11 && $color_stitch_count <= $embroidery->range_high_11)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_11c';
                            }
                            else if($color_stitch_count >= $embroidery->range_low_12 && $color_stitch_count <= $embroidery->range_high_12)
                            {
                                $switch_id = $embroidery->id;
                                $embroidery_field = 'pricing_12c';
                            }
                        }
                    }
                }
            }

            /* Add product Code */

            $post['created_date']=date('Y-m-d');
            $record_delete = $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['id']));
            $record_delete = $this->common->DeleteTableRecords('design_product',array('design_id' => $post['id']));
            $post['record_delete']=$record_delete;
            $result = $this->product->addProduct($post);
            $message = INSERT_RECORD;
            $success = 1;

            /*********************/

            $markup = 0;
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
            foreach($post['productData'] as $product) {
                if($product['qnty'] > 0)
                {
                    $sum = $product['customerPrice'] + $price_grid->shipping_charge;
                    $avg_garment_cost += $sum;
                    $line_qty += $product['qnty'];
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

            $per_item = $avg_garment_price + $print_charges;
            $sales_total = $per_item * $line_qty;

            $update_arr = array(
                                'avg_garment_cost' => $avg_garment_cost,
                                'avg_garment_price' => $avg_garment_price,
                                'print_charges' => $print_charges,
                                'markup' => $markup,
                                'markup_default' => $markup_default,
                                'sales_total' => $sales_total,
                                'total_line_charge' => $per_item
                                );

            $this->common->UpdateTableRecords('design_product',array('product_id' => $post['product_id']),$update_arr);

            $total_qnty = 0;
            foreach ($post['productData'] as $size) {
                $total_qnty += $size['qnty'];
            }

            $design_data = $this->order->getDesignByOrder($order_id);
            
            $design_product_total = 0;
            foreach ($design_data as $design) {
                    $design_product_total += $design->sales_total;
            }

            $order_total = $total_screens + $total_press_setup + $design_product_total;
            $tax = $order_total / $order_data[0]->tax_rate/100;
            $grand_total = $order_total + $tax;
            $balance_due = $grand_total - $order_data[0]->total_payments;

            $update_order_arr = array(
                                    'screen_charge' => $total_screens,
                                    'press_setup_charge' => $total_press_setup,
                                    'order_line_total' => $design_product_total,
                                    'order_total' => $order_total,
                                    'tax' => $tax,
                                    'grand_total' => $grand_total,
                                    'balance_due' => $balance_due
                                    );

            $this->common->UpdateTableRecords('orders',array('id' => $order_id),$update_order_arr);

            $data = array("success"=>$success,"message"=>$message);
            return response()->json(['data'=>$data]);
        }
        else
        {
            $data = array("success"=>0,"message"=>"Please enter atleast one position","status"=>"error");
            return response()->json(['data'=>$data]);
        }

        /*$scope.order.order_line_total = order_line_total.toFixed(2);

        var sales_order_total = parseFloat($scope.order.order_line_total) + parseFloat($scope.order.order_charges_total);
        $scope.order.sales_order_total = sales_order_total.toFixed(2);
        
        var grand_total = parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge) + parseFloat($scope.order.order_line_total) + parseFloat($scope.order.tax);
        $scope.order.grand_total = grand_total.toFixed(2);

        var order_data = {};
        order_data.data = {'order_line_total' : $scope.order.order_line_total,'sales_order_total' : $scope.order.sales_order_total,'grand_total':$scope.order.grand_total};
        order_data.cond = {id: $scope.order_id};
        order_data['table'] ='orders'
        $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
            $scope.updateOrderLine($scope.orderLineAll,orderline_id);
        });*/
    }

     public function designProduct() {
 
        $data = Input::all();
        $result = $this->product->designProduct($data);

        if(empty($result['design_product']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

        if($result['product_id']) {

            $productArray = ['id' => $result['product_id']];
            $result_product = $this->product->productDetail($productArray);
            $calculate_data = $this->common->GetTableRecords('design_product',array('design_id' => $result['design_id']),array());        
       }
       
       
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['design_product'],
                                'productData' => $result_product,
                                'calculate_data' => $calculate_data,
                                'colorName' => $result['colorName']
                                );
        
        return response()->json(["data" => $response]);

    }

     public function deleteAddProduct()
    {
        $post = Input::all();
       
        if(!empty($post['id']))
        {
            $record_data = $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['id']));
            $record_delete = $this->common->DeleteTableRecords('design_product',array('design_id' => $post['id']));
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
}