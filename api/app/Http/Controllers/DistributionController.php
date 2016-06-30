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
        $distribution_address = $this->common->GetTableRecords('client_distaddress',array('client_id' => $order_data[0]->client_id),array());

        $products = $this->distribution->getAllDustributionProducts($post_all['order_id']);
    }
}