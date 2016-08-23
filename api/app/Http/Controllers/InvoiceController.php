<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Order;
use App\Product;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;

use Request;

class InvoiceController extends Controller { 

    public function __construct(Common $common, Order $order, Product $product)
    {
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
    }

    public function invoiceList()
    {
    	$post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'f.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'f.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';
    }
    public function getInvoiceDetail()
    {
    	$post = Input::all();


    }
}