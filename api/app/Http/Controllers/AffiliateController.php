<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Affiliate;
use App\Order;
use App\Product;
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

class AffiliateController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Affiliate $affiliate,Common $common,Order $order,Product $product) {
        parent::__construct();
        $this->affiliate = $affiliate;
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
    }

    public function getAffiliateDetail()
    {
        $post = Input::all();
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('company_id' => $post['cond']['company_id'],'is_delete' => '1','status' => '1'),array());
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $post['cond']['order_id'],'is_calculate' => '1'),array());

        $design_detail = array();

        foreach ($design_data as $design) {
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id,'is_delete' => '1'),array());
            if(!empty($size_data))
            {
                foreach ($size_data as $size) {
                    $size->affiliate_qnty = 0;
                }
                $design->size_data = $size_data;
                $design_detail[$design->id] = $design;
            }
        }

        $result['design_detail'] = $design_detail;
        $result['affiliate_data'] = $affiliate_data;

        $response = array(
                            'success' => 1, 
                            'message' => GET_RECORDS,
                            'records' => $result
                            );
        return response()->json(["data" => $response]);
    }

    public function addAffiliate()
    {
        $post = Input::all();

        $order_data = $this->common->GetTableRecords('orders',array('id' => $post['order_id'],'parent_order_id' => '0'),array());
        unset($order_data[0]->id);
        $insert_arr = json_decode(json_encode($order_data[0]),true);

        $order_design = $this->common->GetTableRecords('order_design',array('id' => $post['design_id'],'order_id' => $post['order_id'],'is_affiliate_design' => '0'),array());
        unset($order_design[0]->id);
        $insert_order_design = json_decode(json_encode($order_design[0]),true);

        $design_product = $this->common->GetTableRecords('design_product',array('id' => $post['design_product_id'],'is_affiliate_design' => '0'),array());
        $this->common->UpdateTableRecords('design_product',array('id' => $post['design_product_id']),array('assign_to_affiliate' => '1'));

        $affiliate_data = $this->common->GetTableRecords('affiliates',array('id' => $post['affiliate_id']),array());

        $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $post['design_id']),array());
        unset($position_data[0]->id);
        unset($position_data[0]->design_id);

        if(!empty($position_data))
        {
            $position_insert_data = json_decode(json_encode($position_data[0]),true);
        }

        $order_item_mapping = $this->common->GetTableRecords('order_item_mapping',array('order_id' => $post['order_id'],'design_id' => $post['design_id'],'product_id' => $design_product[0]->product_id),array());

        $insert_arr['parent_order_id'] = $post['order_id'];
        $insert_arr['affiliate_id'] = $post['affiliate_id'];
        $insert_arr['shop_invoice'] = $post['shop_invoice'];
        $insert_arr['affiliate_invoice'] = $post['affiliate_invoice'];
        $insert_arr['total'] = $post['total'];
        $insert_arr['note'] = $post['notes'];
        $insert_arr['price_id'] = $affiliate_data[0]->price_grid;

        $insert_arr['affiliate_display_number'] = $this->common->getAffiliateDisplayNumber('orders',$insert_arr['company_id'],'company_id','id');

        $order_id = $this->common->InsertRecords('orders',$insert_arr);

        $insert_order_design['display_number'] = $this->common->getDisplayNumber('order_design',$insert_arr['company_id'],'company_id','id');

        $insert_order_design['order_id'] = $order_id;
        $insert_order_design['is_affiliate_design'] = 1;

        $design_id = $this->common->InsertRecords('order_design',$insert_order_design);

        $position_insert_data['design_id'] = $design_id;

        if(!empty($position_data))
        {
            $design_product_id = $this->common->InsertRecords('order_design_position',$position_insert_data);
        }

        $extra_charges = 0;
        foreach($order_item_mapping as $item)
        {
            $price_grid_charges = $this->common->GetTableRecords('price_grid_charges',array('id' => $item->item_id),array());
            $charge = $price_grid_charges[0]->charge;
            $extra_charges += (int)$charge;
            $insert_item = json_decode(json_encode($item),true);
            $insert_item['design_id'] = $design_id;
            $insert_item['order_id'] = $order_id;
            $order_item_mapping_id = $this->common->InsertRecords('order_item_mapping',$insert_item);
        }

        $insert_design_product = array(
                                        'design_id' => $design_id,
                                        'product_id' => $design_product[0]->product_id,
                                        'avg_garment_cost' => $post['calculatedData']['avg_garment_cost'],
                                        'avg_garment_price' => $post['calculatedData']['avg_garment_price'],
                                        'print_charges' => $post['calculatedData']['print_charges'],
                                        'markup' => $post['calculatedData']['print_charges'],
                                        'markup_default' => $post['calculatedData']['markup_default'],
                                        'override' => $design_product[0]->override,
                                        'override_diff' => $design_product[0]->override_diff,
                                        'sales_total' => $post['calculatedData']['sales_total'],
                                        'total_line_charge' => $post['calculatedData']['total_line_charge'],
                                        'extra_charges' => $extra_charges,
                                        'size_group_id' => $design_product[0]->size_group_id,
                                        'warehouse' => $design_product[0]->warehouse,
                                        'is_supply' => $design_product[0]->is_supply,
                                        'is_affiliate_design' => '1'
                                    );

        $design_product_id = $this->common->InsertRecords('design_product',$insert_design_product);

        foreach($post['sizes'] as $row) {

            $insert_purchase_array = array(
                                            'design_id'=>$design_id,
                                            'design_product_id'=>$design_product_id,
                                            'product_id'=>$row['product_id'],
                                            'size'=>$row['size'],
                                            'sku'=>$row['sku'],
                                            'price'=>$row['price'],
                                            'qnty'=>$row['affiliate_qnty'],
                                            'color_id'=>$row['color_id']
                                        );

            $this->common->InsertRecords('purchase_detail',$insert_purchase_array);
        }

        $return = app('App\Http\Controllers\OrderController')->calculateAll($order_id,$order_data[0]->company_id);

        $response = array(
                            'success' => 1, 
                            'message' => INSERT_RECORD
                            );
        return response()->json(["data" => $response]);
    }


    /** 
 * @SWG\Definition(
 *      definition="affiliateData",
 *      type="object",
 *     
 *     
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          ),
 *
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/affiliate/getAffiliateData",
 *  summary = "Affiliate Data",
 *  tags={"Order"},
 *  description = "Affiliate Data",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Affiliate Data",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/affiliateData")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Affiliate Data"),
 *  @SWG\Response(response="default", description="Affiliate Data"),
 * )
 */

    public function getAffiliateData()
    {
        $data = Input::all();
        $records = array();

        $result = $this->order->orderDetail($data);

      /*   if(empty($result['order']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }*/
        

        $affiliateList = $this->affiliate->getAffiliateData($data);
        $assigned_total = 0;
        foreach($affiliateList as $list)
        {
            $sizes = $this->affiliate->getAffiliateSizes($list->design_id);
            $total = 0;
            foreach ($sizes as $size) {
                $total += $size->qnty;
            }
            $list->total = $total;
            $list->sizes = $sizes;
            $assigned_total += $total;
        }

        //$assigned = $this->affiliate->getAssignCount($data);
        $not_assigned = $this->affiliate->getUnassignCount($data);

        $result['order'][0]->assign = $assigned_total;//$assigned[0]->total ? $assigned[0]->total : '0';
        $result['order'][0]->total = $not_assigned[0]->total ? $not_assigned[0]->total : '0';

        $response = array(
                            'success' => 1, 
                            'message' => GET_RECORDS,
                            'records' => $result['order'][0],
                            'affiliateList' => $affiliateList
                            );
        return response()->json(["data" => $response]);
        return $this->return_response($data);
    }

    public function getAffiliateList()
    {
        $data = Input::all();
        $affiliateList = $this->affiliate->getAffiliateList($data);

        foreach($affiliateList as $list)
        {
            $sizes = $this->affiliate->getAffiliateSizes($list->design_id);
            $total = 0;
            foreach ($sizes as $size) {
                $total += $size->qnty;
            }
            $list->total = $total;
        }

        if(!empty($affiliateList))
        {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $affiliateList
                                );
        }
        else
        {
            $response = array(
                                    'success' => 0,
                                    'message' => GET_RECORDS,
                                    'records' => $affiliateList
                                    );
        }
        return response()->json(["data" => $response]);
        return $this->return_response($data);
    }

    public function getAffiliateDesignProduct()
    {
        $post = Input::all();
        $result = $this->product->getAffiliateDesignProduct($post['id']);
        if(!empty($result))
        {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result
                                );
        }
        else
        {
            $response = array(
                                    'success' => 0,
                                    'message' => GET_RECORDS,
                                    'records' => $result
                                    );
        }
        return response()->json(["data" => $response]);
        return $this->return_response($data);
    }

    public function affiliateCalculation()
    {
        $post = Input::all();
        $design_id = $post['design_id'];

        $order_data = $this->order->getOrderByDesign($design_id);
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('id' => $post['affiliate_id']),array());

        $price_id = $affiliate_data[0]->price_grid;
        $order_id = $order_data[0]->id;

        $price_grid_data = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $price_id),array());
        $price_grid = $price_grid_data[0];

        $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $design_id,'is_delete' => '1','is_calculate'=>'1','product_id' => $post['sizeData'][0]['product_id']),array());
        $product = $design_product[0];
                
        $total_qnty = 0;
        
        foreach ($post['sizeData'] as $size) {

             if(array_key_exists('affiliate_qnty', $size)) {
                $total_qnty += $size['affiliate_qnty'];
             } else {
                exit;
             }
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
                        $switch_id = 0;
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
                                $switch_id = $embroidery->id;
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
                        if($position->dtg_size > 0 && $position->dtg_on > 0)
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
        $unit_cost = 0;
        foreach($post['sizeData'] as $pd) {
            if($pd['affiliate_qnty'] > 0)
            {
                $price = $pd['price'];
                $unit_cost += $price * $pd['affiliate_qnty'];
                $line_qty += $pd['affiliate_qnty'];
            }
        }

        $total_shipping_charge = 0;
        
        if($price_grid->shipping_charge > 0)
        {
            $total_shipping_charge = $line_qty * $price_grid->shipping_charge;
        }
        $avg_garment_cost = ($unit_cost/$line_qty) + $total_shipping_charge;

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
        if($product->override > 0)
        {
            $per_item = $product->override;
        }
        else
        {
            $per_item = $avg_garment_price + $print_charges + $extraCharges;
        }
                
        $sales_total = $per_item * $line_qty;

        $calculatedData = array(
                            'avg_garment_cost' => round($avg_garment_cost,2),
                            'avg_garment_price' => round($avg_garment_price,2),
                            'print_charges' => round($print_charges,2),
                            'markup' => $markup,
                            'markup_default' => $markup_default,
                            'sales_total' => round($sales_total,2),
                            'total_line_charge' => round($per_item,2)
                            );

        $data = array("success"=>1,"message"=>"","calculatedData"=>$calculatedData);
        return $data;
    }
}