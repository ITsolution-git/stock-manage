<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Affiliate;
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
    public function __construct(Affiliate $affiliate,Common $common) {
        $this->affiliate = $affiliate;
        $this->common = $common;
    }

    public function getAffiliateDetail()
    {
        $post = Input::all();
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('company_id' => $post['cond']['company_id']),array());
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $post['cond']['order_id']),array());

        $design_detail = array();

        foreach ($design_data as $design) {
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id),array());
            foreach ($size_data as $size) {
                $size->affiliate_qnty = 0;
            }
            $design->size_data = $size_data;
            $design_detail[$design->id] = $design;
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

        print_r($id);exit;

    }
}