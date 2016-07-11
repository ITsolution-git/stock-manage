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

            $result = $this->common->GetTableRecords('product_address_mapping',array('product_id' => $post['product_id'],'order_id' => $post['order_id']),array());

            if(empty($result))
            {
                $addr->is_selected = 0;

                $products = $this->distribution->getDistSizeByProduct($post['product_id']);

                foreach ($products as $row) {
                    $this->common->UpdateTableRecords('purchase_detail',array('id'=>$row->id),array('remaining_qnty'=>$row->qnty_purchased));
                    $row->remaining_qnty = $row->qnty_purchased;
                }

                $addr->sizeArr = $products;
            }
            else
            {
                $addr->is_selected = 1;
                $addr->sizeArr = $this->distribution->getProductByAddress($result[0]->id);
            }
            $distribution_address[$addr->id] = $addr;
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
            if($product['distributed_qnty'] > $product['remaining_qnty'])
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

        if($post['action'] == 'add')
        {
            $shipping_id = $this->common->InsertRecords('shipping',array('order_id' => $post['order_id'],'product_id' => $post['product_id']));

            $product_address_id = $this->common->InsertRecords('product_address_mapping',array('product_id' => $post['product_id'], 'order_id' => $post['order_id'], 'address_id' => $post['address_id'],'shipping_id' => $shipping_id));

            foreach ($post['products'] as $product) {
                
                $updateArr = array('product_address_id' => $product_address_id, 'purchase_detail_id' => $product['id'], 'distributed_qnty' => $product['distributed_qnty']);

                $this->common->InsertRecords('product_address_size_mapping',$updateArr);

                $remaining_qnty = $product['remaining_qnty'] - $product['distributed_qnty'];
                $this->common->UpdateTableRecords('purchase_detail',array('id'=>$product['id']),array('remaining_qnty' => $remaining_qnty));
            }
        }
        else
        {
            $shipping_data = $this->common->GetTableRecords('product_address_mapping',array('order_id' => $post['order_id'],'product_id' => $post['product_id']),array());   
            $shipping_id = $shipping_data[0]->shipping_id;
        }

        $message = 'Product allocated successfully';

        $response = array('success'=>1,'message'=>$message);
        return response()->json($response);
    }

    public function getProductByAddress()
    {
        $post = Input::all();

        $this->distribution->getProductByAddress($post);
    }
}