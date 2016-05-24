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

        $insert_arr = array(
                            'order_id' => $post['order_id'],
                            'affiliate_id' => $post['affiliate_id'],
                            'design_id' => $post['design_id'],
                            'total_affiliate' => $post['total_affiliate'],
                            'total_not_assign' => $post['total_not_assign'],
                            'note' => $post['notes'],
                            'shop_invoice' => $post['shop_invoice'],
                            'additional_charges' => $post['additional_charges'],
                            'affiliate_invoice' => $post['affiliate_invoice'],
                            'total' => $post['total']
                            );

        $id = $this->common->InsertRecords('order_affiliate_mapping',$insert_arr);

        foreach ($post['sizes'] as $size) {
            $this->common->InsertRecords('affiliate_product',array('affiliate_id' => $id,'size' => $size['size'],'qnty' => $size['affiliate_qnty']));
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

        foreach($affiliateList as $list)
        {
            $sizes = $this->affiliate->getAffiliateSizes($list->id);
            $total = 0;
            foreach ($sizes as $size) {
                $total += $size->qnty;
            }
            $list->total = $total;
            $list->sizes = $sizes;
        }

        $assigned = $this->affiliate->getAssignCount($data);
        $not_assigned = $this->affiliate->getUnassignCount($data);

        $result['order'][0]->assign = $assigned[0]->total;
        $result['order'][0]->total = $not_assigned[0]->total;

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