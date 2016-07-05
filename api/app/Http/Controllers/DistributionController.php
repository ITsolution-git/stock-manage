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
        $dist_addr = $this->common->GetTableRecords('client_distaddress',array('client_id' => $post['client_id']),array());

        $client_distaddress = array();
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
            }
        }

        $response = array(
                        'success' => 1, 
                        'message' => GET_RECORDS,
                        'addresses' => $distribution_address
                        );

        return response()->json($response);        
    }
}