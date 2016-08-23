<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Order;
use App\Product;
use App\Invoice;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;

use Request;

class InvoiceController extends Controller { 

    public function __construct(Common $common, Order $order, Product $product, Invoice $invoice)
    {
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
        $this->invoice = $invoice;
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


    }
}