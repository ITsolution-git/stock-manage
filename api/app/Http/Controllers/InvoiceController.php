<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Order;
use App\Product;
use App\Invoice;
use App\Common;
use App\Client;
use App\Company;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;

use Request;
use PDF;

class InvoiceController extends Controller { 

    public function __construct(Common $common, Order $order, Product $product, Invoice $invoice, Client $client,Company $company)
    {
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
        $this->invoice = $invoice;
        $this->client = $client;
        $this->company = $company;
    }

    public function listInvoice()
    {
    	$post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];


        $result = $this->company->getQBAPI($post['company_id']);

        if($result[0]->is_sandbox == 0) {
            $quickbook_url = "https://sandbox.qbo.intuit.com/app/invoice?txnId=";

        } else if($result[0]->is_sandbox == 1) { 
               $quickbook_url = "https://sandbox.qbo.intuit.com/app/invoice?txnId="; 

        } else {

           $quickbook_url = "https://qbo.intuit.com/app/invoice?txnId=";
        }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     

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
                        2=>array('key' => 'o.grand_total', 'name' => 'Invoice $ Amount'),
                        3=>array('key' => 'o.in_hands_by', 'name' => 'In Hands By'),
                        4=>array('key' => '', 'name' => 'Synced with Quickbooks', 'sortable' => false),
                        5=>array('key' => '', 'name' => '', 'sortable' => false),
                        6=>array('key' => '', 'name' => 'Option', 'sortable' => false),
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success,'quickbook_url' => $quickbook_url);
        return response()->json($data);
    }
    public function getInvoiceDetail($invoice_id,$company_id,$type=0,$order_id=0)
    {
    	$post = Input::all();

        $retutn_arr = array();

        if($invoice_id != 0)
        {
            $invoice_data = $this->common->GetTableRecords('invoice',array('id' => $invoice_id),array());

            if(empty($invoice_data))
            {

               $response = array(
                                    'success' => 0, 
                                    'message' => NO_RECORDS
                                    ); 
               return response()->json(["data" => $response]);
            }


            $order_id = $invoice_data[0]->order_id;

            $retutn_arr['invoice_data'] = $invoice_data;
            $retutn_arr['invoice_data'][0]->created_date = date("m/d/Y", strtotime($retutn_arr['invoice_data'][0]->created_date));

            if($retutn_arr['invoice_data'][0]->payment_due_date != '0000-00-00')
            {
                $retutn_arr['invoice_data'][0]->payment_due_date = date("m/d/Y", strtotime($retutn_arr['invoice_data'][0]->payment_due_date));
            }
            else
            {
                $retutn_arr['invoice_data'][0]->payment_due_date = 'No Due Date';
            }

            $order_data = $this->common->GetTableRecords('orders',array('id' => $order_id,'company_id' => $company_id),array());


             if(empty($order_data))
            {

               $response = array(
                                    'success' => 0, 
                                    'message' => NO_RECORDS
                                    ); 
               return response()->json(["data" => $response]);
            }
        }
        else
        {
            $order_data = $this->common->GetTableRecords('orders',array('id' => $order_id,'company_id' => $company_id),array());
            $retutn_arr['invoice_data'] = array();
        }


        $retutn_arr['company_data'] = $this->common->getCompanyDetail($company_id);

        $staff = $this->common->GetTableRecords('staff',array('user_id' => $company_id),array());

        if($retutn_arr['company_data'][0]->photo != '')
        {
            $retutn_arr['company_data'][0]->photo = UPLOAD_PATH.$company_id."/staff/".$staff[0]->id."/".$retutn_arr['company_data'][0]->photo;
        }

        $retutn_arr['addresses'] = $this->client->getAddress($order_data[0]->client_id);
        $retutn_arr['client_data'] = $this->common->GetTableRecords('client_contact',array('client_id' => $order_data[0]->client_id,'contact_main' => 1),array());

        $retutn_arr['price_grid_data'] = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $order_data[0]->price_id),array());

        $retutn_arr['order_data'] = $order_data;

        $retutn_arr['shipping_detail'] = $this->common->GetTableRecords('shipping',array('order_id' => $order_id),array());

        if(!empty($retutn_arr['shipping_detail']))
        {
            foreach ($retutn_arr['shipping_detail'] as $shipping) {
                if($shipping->shipping_by != '0000-00-00') {
                    $shipping->shipping_by = date("m/d/Y", strtotime($shipping->shipping_by));
                }
                else {
                    $shipping->shipping_by = '';
                }
                if($shipping->in_hands_by != '0000-00-00') {
                    $shipping->in_hands_by = date("m/d/Y", strtotime($shipping->in_hands_by));
                }
                else {
                    $shipping->in_hands_by = '';
                }
                if($shipping->date_shipped != '0000-00-00') {
                    $shipping->date_shipped = date("m/d/Y", strtotime($shipping->date_shipped));
                }
                else {
                    $shipping->date_shipped = '';
                }
                if($shipping->fully_shipped != '0000-00-00') {
                    $shipping->fully_shipped = date("m/d/Y", strtotime($shipping->fully_shipped));
                }
                else {
                    $shipping->fully_shipped = '';
                }
            }
        }

        $all_design = $this->common->GetTableRecords('order_design',array('order_id' => $order_id,'is_delete' => '1'),array());

        foreach ($all_design as $design) {
            $data = array('company_id' => $company_id,'id' => $design->id);
            $design->positions = $this->order->getDesignPositionDetail($data);
            $productData = $this->product->designProduct($data);
            
            if(!empty($productData['productData'])) {
                $design->products = $productData['productData'];    
            }
            else
            {
                $design->products = array();
            }
        }

        $retutn_arr['all_design'] = $all_design;

        if($type == 1)
        {
            return $retutn_arr;
        }

        $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'allData' => $retutn_arr
                                );
        return response()->json(["data" => $response]);
    }

    public function createInvoicePdf()
    {
        $post = Input::all();
        $data = $this->getInvoiceDetail($post['invoice_id'],$post['company_id'],1);

        PDF::AddPage('P','A4');
        PDF::writeHTML(view('pdf.invoice',$data)->render());
        PDF::Output('order_invoice_'.$post['invoice_id'].'.pdf');
    }

    // get invoice history from payment history
    public function getInvoiceHistory($invoice_id,$company_id,$type=0){

        $post = Input::all();

        $retutn_arr = array();
        
        $invoice_data = $this->common->GetTableRecords('invoice',array('id' => $invoice_id),array());

         if(empty($invoice_data))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

        
        $order_id = $invoice_data[0]->order_id;

        $retArray = DB::table('payment_history')
            ->select('payment_id', 'payment_amount', 'payment_date', 'payment_method')
            ->where('order_id','=',$order_id)
            ->where('is_delete','=',1)
            ->get();

        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
            );
        return response()->json(["data" => $response]);
    }

    // get invoice payment stored for future use
    public function getInvoicePayment($invoice_id,$company_id,$type=0){

        $post = Input::all();

        $retutn_arr = array();
        
        $invoice_data = $this->common->GetTableRecords('invoice',array('id' => $invoice_id),array());

        $retArray = DB::table('invoice')
            ->select('creditFname', 'creditLname', 'creditCard', 'month', 'year', 'street', 'suite', 'city', 'state', 'zip')
            ->where('id','=',$invoice_id)
            ->get();

         if(empty($invoice_data))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $invoice_data
            );
        return response()->json(["data" => $response]);
    }
}