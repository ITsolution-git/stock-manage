<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Affiliate;
use App\Order;
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
    public function __construct(Affiliate $affiliate,Common $common,Order $order) {
        $this->affiliate = $affiliate;
        $this->common = $common;
        $this->order = $order;
    }

    public function getAffiliateDetail()
    {
        $post = Input::all();
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('company_id' => $post['cond']['company_id']),array());
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $post['cond']['order_id']),array());

        $design_detail = array();

        foreach ($design_data as $design) {
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id),array());
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

        $order_design = $this->common->GetTableRecords('order_design',array('id' => $post['design_id'],'order_id' => $post['order_id'],'is_affiliate_design' => '0'),array());
        $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $post['design_id'],'is_affiliate_design' => '0'),array());
        $order_data = $this->common->GetTableRecords('orders',array('id' => $post['order_id'],'parent_order_id' => '0'),array());
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('id' => $post['affiliate_id']),array());

        $insert_arr = array(
                            'parent_order_id' => $post['order_id'],
                            'affiliate_id' => $post['affiliate_id'],
                            'client_id' => $order_data[0]->client_id,
                            'contact_main_id' => $order_data[0]->contact_main_id,
                            'price_id' => $affiliate_data[0]->price_grid,
                            'account_manager_id' => $order_data[0]->account_manager_id,
                            'name' => $order_data[0]->name,
                            'approval_id' => $order_data[0]->approval_id,
                            'sales_id' => $order_data[0]->sales_id,
                            'total_affiliate' => $post['total_affiliate'],
                            'total_not_assign' => $post['total_not_assign'],
                            'note' => $post['notes'],
                            'shop_invoice' => $post['shop_invoice'],
                            'additional_charges' => $post['additional_charges'],
                            'affiliate_invoice' => $post['affiliate_invoice'],
                            'total' => $post['total'],
                            'login_id' => $order_data[0]->login_id,
                            'company_id' => $order_data[0]->company_id
                            );

        $order_id = $this->common->InsertRecords('orders',$insert_arr);

        $insert_order_design = array(
                                    'order_id' => $order_id,
                                    'design_name' => $order_design[0]->design_name,
                                    'front_color_id' => $order_design[0]->front_color_id,
                                    'back_color_id' => $order_design[0]->back_color_id,
                                    'side_right_color_id' => $order_design[0]->side_right_color_id,
                                    'side_left_color_id' => $order_design[0]->side_left_color_id,
                                    'top_color_id' => $order_design[0]->top_color_id,
                                    'bottom_color_id' => $order_design[0]->bottom_color_id,
                                    'is_affiliate_design' => '1'
                                    );
        $design_id = $this->common->InsertRecords('order_design',$insert_order_design);

        $insert_design_product = array('design_id' => $design_id, 'product_id' => $design_product[0]->product_id, 'is_affiliate_design' => '1');

        $design_product_id = $this->common->InsertRecords('design_product',$insert_design_product);

        foreach($post['sizes'] as $row) {

            $insert_purchase_array = array(
                                            'design_id'=>$design_id,
                                            'size'=>$row['size'],
                                            'sku'=>$row['sku'],
                                            'price'=>$row['price'],
                                            'qnty'=>$row['affiliate_qnty'],
                                            'color_id'=>$row['color_id']
                                        );

            $this->common->InsertRecords('purchase_detail',$insert_purchase_array);
        }

        $response = array(
                            'success' => 1, 
                            'message' => INSERT_RECORD
                            );
        return response()->json(["data" => $response]);
    }

    public function getAffiliateData()
    {
        $data = Input::all();
        $records = array();

        $result = $this->order->orderDetail($data);

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

        $assigned = $this->affiliate->getAssignCount($data);
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
            $sizes = $this->affiliate->getAffiliateSizes($list->id);
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
}