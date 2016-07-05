<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Order;
use App\Common;
use App\Product;
use App\Distribution;
use DB;
use App;
use Request;
use Response;

class DistributionController extends Controller {

    public function __construct(Order $order,Common $common,Product $product, Distribution $distribution)
    {
        $this->order = $order;
        $this->common = $common;
        $this->product = $product;
        $this->distribution = $distribution;
    }

    public function getDistProductAddress() {

        $post_all = Input::all();

        $order_data = $this->common->GetTableRecords('orders',array('id' => $post_all['order_id']),array());

        $dist_addr = $this->common->GetTableRecords('client_distaddress',array('client_id' => $order_data[0]->client_id),array());

        $client_distaddress = array();
        foreach ($dist_addr as $addr) {
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $distribution_address[] = $addr;
        }

        $products = $this->distribution->getAllDustributionProducts($post_all['order_id']);

        $response = array(
                        'success' => 1, 
                        'message' => GET_RECORDS,
                        'products' => $products,
                        'distribution_address' => $distribution_address
                        );

        return response()->json($response);
    }

    public function getDistSizeByProduct()
    {
        $post = Input::all();
        $products = $this->distribution->getDistSizeByProduct($post['product_id']);

        $response = array(
                        'success' => 1, 
                        'message' => GET_RECORDS,
                        'products' => $products
                        );

        return response()->json($response);
    }

    public function getDistAddress()
    {
        $post = Input::all();
        $dist_addr = $this->distribution->getDistAddress($post);

        $client_distaddress = array();
        $selected_addresses = array();
        foreach ($dist_addr as $addr) {
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $distribution_address[] = $addr;

            $result = $this->common->GetTableRecords('item_address_mapping',array('item_id' => $post['product_id'],'address_id' => $addr->id,'order_id' => $post['order_id']),array());
            if(empty($result))
            {
                $addr->is_selected = 0;
            }
            else
            {
                $addr->is_selected = 1;
                $selected_addresses[] = $addr->id;
            }
        }

        $response = array(
                        'success' => 1, 
                        'message' => GET_RECORDS,
                        'addresses' => $distribution_address,
                        'selected_addresses' => $selected_addresses
                        );

        return response()->json($response);        
    }

    public function addEditDistribute()
    {
        $post = Input::all();

        $total = 0;
        foreach ($post['products'] as $product) {
            if($product['distributed_qnty'] > $product['qnty_purchased'])
            {
                $response = array('success'=>0,'message'=>'Please enter valid quantity');
                return response()->json($response);
            }
            if($product['distributed_qnty'] > 0)
            {
                $total = 1;
            }
        }

        if($total == 0)
        {
            $response = array('success'=>0,'message'=>'Please enter quantity to distribute');
            return response()->json($response);
        }

        $this->common->DeleteTableRecords('item_address_mapping',array('order_id' => $post['order_id'],'item_id' => $post['product_id']));

        foreach ($post['address_ids'] as $address_id) {
            $this->common->InsertRecords('item_address_mapping',array('item_id' => $post['product_id'], 'order_id' => $post['order_id'], 'address_id' => $address_id));
        }

        foreach ($post['products'] as $product) {
            $updateArr = array('distributed_qnty' => $product['distributed_qnty'], 'is_distribute' => 1);
            $this->common->UpdateTableRecords('purchase_detail',array('id'=>$product['id']),$updateArr);
        }

        if($post['action'] == 'add') {
            $message = 'Distribution added successfully';
        }
        else
        {
            $message = 'Distribution updated successfully';
        }

        $response = array('success'=>1,'message'=>$message);
        return response()->json($response);
    }
}