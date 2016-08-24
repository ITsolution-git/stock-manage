<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Order;
use App\Product;
use App\Invoice;
use App\Common;
use App\Client;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;

use Request;

class InvoiceController extends Controller { 

    public function __construct(Common $common, Order $order, Product $product, Invoice $invoice, Client $client)
    {
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
        $this->invoice = $invoice;
        $this->client = $client;
    }

    public function listInvoice()
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
            $post['sorts']['sortBy'] = 'o.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'o.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->invoice->listInvoice($post);

        foreach ($result['allData'] as $row) {
            $row->created_date = date("m/d/Y", strtotime($row->created_date));
            if($row->in_hands_by != '0000-00-00')
            {
                $row->in_hands_by = date("m/d/Y", strtotime($row->in_hands_by));
            }
            else
            {
                $row->in_hands_by = '';
            }
        }

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'o.id', 'name' => 'Invoice'),
                        1=>array('key' => 'i.created_date', 'name' => 'Date'),
                        2=>array('key' => 'i.grand_total', 'name' => 'Invoice $ Amount'),
                        3=>array('key' => 'o.in_hands_by', 'name' => 'In Hands By'),
                        4=>array('key' => 'null', 'name' => 'Synced with Quickbooks', 'sortable' => false),
                        5=>array('key' => 'null', 'name' => '', 'sortable' => false),
                        6=>array('key' => 'null', 'name' => 'Option', 'sortable' => false),
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success);
        return response()->json($data);
    }
    public function getInvoiceDetail()
    {
    	$post = Input::all();

        $retutn_arr = array();
        
        $invoice_data = $this->common->GetTableRecords('invoice',array('id' => $post['invoice_id']),array());
        $order_id = $invoice_data[0]->order_id;

        $retutn_arr['invoice_data'] = $invoice_data;
        $retutn_arr['invoice_data'][0]->created_date = date("m/d/Y", strtotime($retutn_arr['invoice_data'][0]->created_date));

        $order_data = $this->common->GetTableRecords('orders',array('id' => $order_id),array());
        $retutn_arr['company_data'] = $this->common->getCompanyDetail($post['company_id']);

        if($retutn_arr['company_data'][0]->photo != '')
        {
            $retutn_arr['company_data'][0]->photo = FILEUPLOAD."/".$post['company_id']."/staff".$post['company_id'].$retutn_arr['company_data'][0]->photo;
        }
        $retutn_arr['addresses'] = $this->client->getAddress($order_data[0]->client_id);
        $retutn_arr['client_data'] = $this->common->GetTableRecords('client_contact',array('client_id' => $order_data[0]->client_id,'contact_main' => 1),array());
        $retutn_arr['price_grid_data'] = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $order_data[0]->price_id),array());

        $retutn_arr['order_data'] = $order_data;

        $retutn_arr['shipping_detail'] = $this->common->GetTableRecords('shipping',array('order_id' => $order_id),array());
        $all_design = $this->common->GetTableRecords('order_design',array('order_id' => $order_id,'is_delete' => '1'),array());

        foreach ($all_design as $design) {
            $data = array('company_id' => $post['company_id'],'id' => $design->id);
            $design->positions = $this->order->getDesignPositionDetail($data);
            $design->products = $this->product->designProduct($data);
        }

        $retutn_arr['all_design'] = $all_design;

        $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'allData' => $retutn_arr
                                );
        return response()->json(["data" => $response]);
    }
}