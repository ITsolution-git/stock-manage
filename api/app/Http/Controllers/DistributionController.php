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
        parent::__construct();
        $this->order = $order;
        $this->common = $common;
        $this->product = $product;
        $this->distribution = $distribution;
    }

    public function getDistProductAddress() {

        $post_all = Input::all();

        $order_data = $this->common->GetTableRecords('orders',array('id' => $post_all['order_id']),array());

        $post_all['id'] = $post_all['order_id'];
        $dist_addr = $this->distribution->getDistAddress($post_all);
        unset($post_all['id']);

        $distribution_address = array();
        $client_distaddress = array();
        
        foreach ($dist_addr as $addr) {
        
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $distribution_address[] = $addr;
            if($addr->shipping_type_id == 1)
            {
                $addr->method_arr = $this->common->GetTableRecords('shipping_method',array('shipping_type_id' => 1),array());
            }
            else if($addr->shipping_type_id == 2)
            {
                $addr->method_arr = $this->common->GetTableRecords('shipping_method',array('shipping_type_id' => 2),array());
            }
            else
            {
                $addr->method_arr = array();   
            }
        }

        $products = $this->distribution->getAllDustributionProducts($post_all['order_id']);

       foreach ($products as $product) {
            $product->total = $this->distribution->getTotalAllocated($post_all['order_id'],$product->product_id);
        }

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
        $post['id'] = $post['order_id'];
        $dist_addr = $this->distribution->getDistAddress($post);

        $client_distaddress = array();
        $selected_addresses = array();
        
        foreach ($dist_addr as $addr) {
            
            $total_remaining_qnty = 0;
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;

            $result = $this->common->GetTableRecords('product_address_mapping',array('product_id' => $post['product_id'],'order_id' => $post['order_id']),array());

            if(empty($result))
            {
                $products = $this->distribution->getDistSizeByProduct($post['product_id'],$post['design_product_id']);
                
                foreach ($products as $row) {
                    $this->common->UpdateTableRecords('purchase_detail',array('id'=>$row->id),array('remaining_qnty'=>$row->qnty_purchased));
                    $row->remaining_qnty = $row->qnty_purchased;
                    $total_remaining_qnty += $row->qnty_purchased;
                }
                $addr->sizeArr = $products;
            }
            else
            {
                $product_address_id = 0;
                $result2 = $this->common->GetTableRecords('product_address_mapping',array('product_id' => $post['product_id'],'order_id' => $post['order_id'],'address_id' => $addr->id),array());
                if(empty($result2))
                {
                    $products = $this->distribution->getDistSizeByProduct($post['product_id'],$post['design_product_id']);
                }
                else
                {
                    $products = $this->distribution->getProductByAddress($result2[0]->id);
                    $product_address_size_mapping = $this->common->GetTableRecords('product_address_size_mapping',array('product_address_id' => $result2[0]->id),array());

                    if(!empty($product_address_size_mapping))
                    {
                        $product_address_id = $result2[0]->id;
                    }
                }

                $total_remaining_qnty = 0;
                foreach ($products as $row) {
                    $total_remaining_qnty += $row->remaining_qnty;
                    $row->product_address_id = $product_address_id;
                }
                $addr->sizeArr = $products;
            }
            
            $addr->total_remaining_qnty = $total_remaining_qnty;
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

            $size_data = $this->distribution->getSingleSizeTotal($product);

            if($product['product_address_id'] > 0 && $product['product_address_id'] != null)
            {
                $product_address_size_mapping = $this->common->GetTableRecords('product_address_size_mapping',array('product_address_id' => $product['product_address_id'],'purchase_detail_id' => $product['id']),array());
                $current_qnty = $product_address_size_mapping[0]->distributed_qnty;
                $total_size_qnty = $size_data[0]->distributed_qnty - $current_qnty;
                $max_qnty = $size_data[0]->qnty_purchased - $total_size_qnty;
            }
            else
            {
                $max_qnty = $product['remaining_qnty'];
            }

            if($product['distributed_qnty'] > $max_qnty)
            {
                $response = array('success'=>0,'message'=>'Enter valid quantity maximum quantity are '.$max_qnty);
                return response()->json($response);
            }
            if($product['distributed_qnty'] > 0)
            {
                $total = 1;
            }
        }

        $shipping_data = $this->common->GetTableRecords('product_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id']),array());
        $order_address_data = $this->common->GetTableRecords('order_shipping_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id']),array());

        $shipping_type_id = '';
        $shipping_method_id = '';
        
        if(!empty($order_address_data))
        {
            $shipping_type_id = $order_address_data[0]->shipping_type_id;
            $shipping_method_id = $order_address_data[0]->shipping_method_id;
        }

        if(empty($shipping_data))
        {
            $display_number = $this->common->getDisplayNumber('shipping',$post['company_id'],'company_id','id');
            $shipping_id = $this->common->InsertRecords('shipping',array('order_id' => $post['order_id'],'address_id' => $post['address_id'],'display_number' => $display_number,'company_id' => $post['company_id'],'shipping_type_id' => $shipping_type_id,'shipping_method' => $shipping_method_id));
            $product_address_id = $this->common->InsertRecords('product_address_mapping',array('product_id' => $post['product_id'], 'order_id' => $post['order_id'], 'address_id' => $post['address_id'],'shipping_id' => $shipping_id));
        }
        else
        {
            $product_address_data = $this->common->GetTableRecords('product_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id'],'product_id' => $post['product_id']),array());

            if(empty($product_address_data))
            {
                $product_address_id = $this->common->InsertRecords('product_address_mapping',array('product_id' => $post['product_id'], 'order_id' => $post['order_id'], 'address_id' => $post['address_id'],'shipping_id' => $shipping_data[0]->shipping_id));
            }
            else
            {
                $product_address_id = $shipping_data[0]->id;
            }
        }

        foreach ($post['products'] as $product) {

            $size_data = $this->distribution->getSingleSizeTotal($product);

            $current_qnty = 0;

            if($product['product_address_id'] > 0 && $product['product_address_id'] != null)
            {
                $product_address_size_mapping = $this->common->GetTableRecords('product_address_size_mapping',array('product_address_id' => $product['product_address_id'],'purchase_detail_id' => $product['id']),array());
                
                if(empty($product_address_size_mapping))
                {
                    $current_qnty = $product_address_size_mapping[0]->distributed_qnty;
                    $total_size_qnty = $size_data[0]->distributed_qnty - $current_qnty;
                    $max_qnty = $size_data[0]->qnty_purchased - $total_size_qnty;

                    $insertArr = array('product_address_id' => $product_address_id, 'purchase_detail_id' => $product['id'], 'distributed_qnty' => $product['distributed_qnty']);
                    $this->common->InsertRecords('product_address_size_mapping',$insertArr);

                    $remaining_qnty = $max_qnty - $product['distributed_qnty'];
                    $this->common->UpdateTableRecords('purchase_detail',array('id'=>$product['id']),array('remaining_qnty' => $remaining_qnty));
                }
                else
                {
                    $this->common->UpdateTableRecords('product_address_size_mapping',array('product_address_id' => $product['product_address_id'],'purchase_detail_id' => $product['id']),array('distributed_qnty' => $product['distributed_qnty']));

                    if($product_address_size_mapping[0]->distributed_qnty != $product['distributed_qnty'])
                    {
                        if($product_address_size_mapping[0]->distributed_qnty > $product['distributed_qnty'])
                        {
                            //$remaining_qnty = $product_address_size_mapping[0]->distributed_qnty - $product['distributed_qnty'];
                            $remaining_qnty = $product['qnty_purchased'] - $product['distributed_qnty'];
                        }
                        else
                        {
                            $current_qnty = $product_address_size_mapping[0]->distributed_qnty;
                            $total_size_qnty = $size_data[0]->distributed_qnty - $current_qnty;
                            $max_qnty = $size_data[0]->qnty_purchased - $total_size_qnty;
                            $remaining_qnty = $max_qnty - $product['distributed_qnty'];
                        }
                        $this->common->UpdateTableRecords('purchase_detail',array('id'=>$product['id']),array('remaining_qnty' => $remaining_qnty));
                    }
                }
            }
            else
            {
                $insertArr = array('product_address_id' => $product_address_id, 'purchase_detail_id' => $product['id'], 'distributed_qnty' => $product['distributed_qnty']);
                $this->common->InsertRecords('product_address_size_mapping',$insertArr);
                $max_qnty = $product['remaining_qnty'];

                $remaining_qnty = $max_qnty - $product['distributed_qnty'];
                $this->common->UpdateTableRecords('purchase_detail',array('id'=>$product['id']),array('remaining_qnty' => $remaining_qnty));
            }
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