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
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $post['cond']['order_id'],'is_calculate' => '1'),array());

        $design_detail = array();

        foreach ($design_data as $design) {
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id,'is_delete' => '0'),array());
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

        $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $post['design_id'],'is_affiliate_design' => '0'),array());
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('id' => $post['affiliate_id']),array());

        $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $post['design_id']),array());
        unset($position_data[0]->id);
        unset($position_data[0]->design_id);
        $position_insert_data = json_decode(json_encode($position_data[0]),true);

        $insert_arr['parent_order_id'] = $post['order_id'];
        $insert_arr['affiliate_id'] = $post['affiliate_id'];
        $insert_arr['note'] = $post['notes'];

        $order_id = $this->common->InsertRecords('orders',$insert_arr);

        $insert_order_design['order_id'] = $order_id;
        $insert_order_design['is_affiliate_design'] = 1;

        $design_id = $this->common->InsertRecords('order_design',$insert_order_design);

        $position_insert_data['design_id'] = $design_id;

        $design_product_id = $this->common->InsertRecords('order_design_position',$position_insert_data);

        $insert_design_product = array('design_id' => $design_id, 'product_id' => $design_product[0]->product_id, 'is_affiliate_design' => '1');

        $design_product_id = $this->common->InsertRecords('design_product',$insert_design_product);

        foreach($post['sizes'] as $row) {

            $insert_purchase_array = array(
                                            'design_id'=>$design_id,
                                            'size'=>$row['size'],
                                            'sku'=>$row['sku'],
                                            'price'=>$row['price'],
                                            'qnty'=>$row['qnty'],
                                            'color_id'=>$row['color_id']
                                        );

            $this->common->InsertRecords('purchase_detail',$insert_purchase_array);
        }

        $this->common->UpdateTableRecords('order_design',array('id' => $post['design_id'],'is_affiliate_design' => '0'),array('is_calculate' => '0'));
        $this->common->UpdateTableRecords('design_product',array('design_id' => $post['design_id']),array('is_calculate' => '0'));
        $this->common->UpdateTableRecords('order_design_position',array('design_id' => $post['design_id']),array('is_calculate' => '0'));

        $return = app('App\Http\Controllers\OrderController')->calculateAll($post['order_id'],$order_data[0]->company_id);
        $return = app('App\Http\Controllers\OrderController')->calculateAll($order_id,$order_data[0]->company_id);

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
}