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
         

            if($retutn_arr['invoice_data'][0]->payment_terms == '1')
            {
                $retutn_arr['invoice_data'][0]->payment_terms = '50% upfront and 50% on shipping';

            } else if($retutn_arr['invoice_data'][0]->payment_terms == '100')
            {
                $retutn_arr['invoice_data'][0]->payment_terms = '100% on Shipping';
            } else if($retutn_arr['invoice_data'][0]->payment_terms == '15')
            {
                $retutn_arr['invoice_data'][0]->payment_terms = 'Net 15';

            } else if($retutn_arr['invoice_data'][0]->payment_terms == '30')

            {
                $retutn_arr['invoice_data'][0]->payment_terms = 'Net 30';
            } else {
                $retutn_arr['invoice_data'][0]->payment_terms = 'No Terms';
            }

           
            

            $order_array = array('id'=>$order_id,'company_id' => $company_id);

            $order_data_all = $this->order->orderDetail($order_array);
            $order_data =  $order_data_all['order']; 


//            $order_data = $this->common->GetTableRecords('orders',array('id' => $order_id,'company_id' => $company_id),array());
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
            //$order_data = $this->common->GetTableRecords('orders',array('id' => $order_id,'company_id' => $company_id),array());

            $order_array = array('id'=>$order_id,'company_id' => $company_id);

            $order_data_all = $this->order->orderDetail($order_array);
            $order_data =  $order_data_all['order']; 


            $retutn_arr['invoice_data'] = array();
        }


         if(!empty($order_data))
        {   
                
            
                if($order_data[0]->date_shipped != '0000-00-00') {
                    $order_data[0]->date_shipped = date("m/d/Y", strtotime($order_data[0]->date_shipped));
                }
                else {
                    $order_data[0]->date_shipped = '';
                }

                if($order_data[0]->in_hands_by != '0000-00-00') {
                    $order_data[0]->in_hands_by = date("m/d/Y", strtotime($order_data[0]->in_hands_by));
                }
                else {
                    $order_data[0]->in_hands_by = '';
                }
                
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
            $pos_array = $this->order->getDesignPositionDetail($data);
            
          
             $design->positions = array_chunk($pos_array['order_design_position'], 2);
             

            $productData = $this->product->designProduct($data);


            
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id,'is_delete' => '1'),array());
            
            $total_product_qnty = 0;
            foreach ($size_data as $size) {
                $total_product_qnty += $size->qnty;
               
            }
            
            $design->total_product_qnty = $total_product_qnty;

            
            if(!empty($productData['productData'])) {
                $design->products = $productData['productData'];    
            }
            else
            {
                $design->products = array();
            }
        }

       

        $retutn_arr['all_design'] = $all_design;
        //print_r($retutn_arr);exit;

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

    // get stored card details with Authorized.net payment profile IDs
    public function getInvoiceCards($invoice_id,$company_id,$type=0){

        $post = Input::all();

        $retArray = DB::table('invoice as i')
            ->select('cppd.payment_profile_id', 'cppd.card_number', 'cppd.expiration')
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client_payment_profiles as cpp','cpp.client_id','=','o.client_id')
            ->leftJoin('client_payment_profiles_detail as cppd','cppd.cpp_id','=','cpp.cpp_id')
            ->where('i.id','=',$invoice_id)
            ->get();

        if(empty($retArray))
        {

           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        if($retArray[0]->payment_profile_id=='')
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
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getPaymentCard(){
        $post = Input::all();
        $cppd_id=$post['cppd_id'];

        $retutn_arr = array();
        
        $retArray = $this->common->GetTableRecords('client_payment_profiles_detail',array('payment_profile_id' => $cppd_id),array());

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        if(isset($retArray[0]->expiration)){
            $expiration=explode('/', $retArray[0]->expiration);
            $retArray[0]->expiration=$expiration;    
        }
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getSalesPersons(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        $retArray = DB::table('sales as s')
            ->select('s.sales_name', 's.id as sales_id')
            ->leftJoin('users as u','u.id','=','s.company_id')
            //->leftJoin('orders as o','o.sales_id','=','s.id')
            //->leftJoin('client as c','c.client_id','=','o.client_id')
            //->leftJoin('users as u1','u1.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('s.sales_delete','=','1')
            ->get();

        if(empty($retArray))
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
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

public function getNoQuickbook(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalInvoice'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('i.qb_id','=',0)
            ->get();

        if(empty($retArray))
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
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getSalesClosed(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalSales'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.sales_id','=',$sales_id)
            ->get();
        }else{
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalSales'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->get();
        }

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->totalSales=round($retArray[0]->totalSales, 0);
        //$tempFigure=explode(".", $retArray[0]->totalSales);
        //$retArray[0]->totalSales=$tempFigure;
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getUnpaid(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.balance_due) as totalUnpaid'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.is_paid','=','0')
            ->where('o.grand_total','>','o.total_payments')
            ->get();

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->totalUnpaid=round($retArray[0]->totalUnpaid, 0);
        //$tempFigure=explode(".", $retArray[0]->totalUnpaid);
        //$retArray[0]->totalUnpaid=$tempFigure;
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getAverageOrders(){
        $post = Input::all();
        $client_id=$post['company_id'];

        // Fetching average amount of order per invoiced
        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('AVG(o.grand_total) as avgOrderAmount'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.sales_id','=',$sales_id)
            ->get();
        }else{
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('AVG(o.grand_total) as avgOrderAmount'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->get();
        }

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->avgOrderAmount=round($retArray[0]->avgOrderAmount, 0);
        //$tempFigure=explode(".", $retArray[0]->avgOrderAmount);
        //$retArray[0]->avgOrderAmount=$tempFigure;

        // Fetching average number of items per invoiced
        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $order_design_data = DB::table('invoice as i')
            ->select('od.id as design_id', 'o.id as order_id')
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('order_design as od','od.order_id','=','o.id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.sales_id','=',$sales_id)
            ->where('od.status','=','1')
            ->where('od.is_delete','=','1')
            ->get();
        }else{
            $order_design_data = DB::table('invoice as i')
            ->select('od.id as design_id', 'o.id as order_id')
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('order_design as od','od.order_id','=','o.id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('od.status','=','1')
            ->where('od.is_delete','=','1')
            ->get();
        }

        if(!empty($order_design_data))
        {
            $size_data = array();
            $order_design = array();
            $orderIDs = array();
            $total_unit = 0;

            foreach ($order_design_data as $design)
            {
                $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->design_id,'is_delete' => '1'),array());

                $total_qnty = 0;
                foreach ($size_data as $size)
                {
                    $total_qnty += $size->qnty;
                }
                $total_unit += $total_qnty;
                $design->size_data = $size_data;
                $design->total_qnty = $total_qnty;
                $orderIDs[]=$design->order_id;
                //$order_design['all_design'][] = $design;
            }

            if($total_unit > 0)
            {
                $order_design['total_unit'] = $total_unit;
                $countOrders = count(array_unique($orderIDs));
                $retArray[0]->avgOrderItems=round($order_design['total_unit']/$countOrders,0);
                //$tempAvg=explode(".", $retArray[0]->avgOrderItems);
                //$retArray[0]->avgOrderItems=$tempAvg;
            }
        }

        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getLatestOrders(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        $retArray = DB::table('invoice as i')
            ->select('i.id as invoice_id', 'o.id as order_id', 'i.qb_id as quickbook_id', 'c.client_company', 'o.grand_total')
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.is_delete','=','1')
            ->orderBy('i.created_date','desc')
            ->take(5)
            ->get();

        if(empty($retArray))
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
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getEstimates(){
        $post = Input::all();
        $client_id=$post['company_id'];

        if( (isset($post['sales_id']) && $post['sales_id']!=0) || (isset($post['duration']) && $post['duration']!=0) ){
            $sales_id=$post['sales_id'];
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimated'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id);

            if(isset($post['sales_id']) && $post['sales_id']!=0){
                $retArray = $retArray->where('o.sales_id','=',$sales_id);
            }

            if(isset($post['duration']) && $post['duration']!=0){
                if($post['duration']=='1'){
                    $retArray = $retArray->where(DB::raw('i.created_date'), '=', DB::raw('CURDATE()'));
                }else if($post['duration']=='2'){
                    $retArray = $retArray->where(DB::raw('WEEK(i.created_date)'), '=', DB::raw('WEEK(CURDATE())-1'));
                }else if($post['duration']=='3'){
                    $retArray = $retArray->where(DB::raw('MONTH(i.created_date)'), '=', DB::raw('MONTH(CURDATE())-1'));
                }else if($post['duration']=='4'){
                    $retArray = $retArray->where(DB::raw('YEAR(i.created_date)'), '=', DB::raw('YEAR(CURDATE())-1'));
                }
            }
            $retArray = $retArray->where('o.is_paid','=','0')
            ->where('o.grand_total','>','o.total_payments')
            ->where('o.approval_id','=',2477)
            ->get();
        }else{
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimated'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.is_paid','=','0')
            ->where('o.grand_total','>','o.total_payments')
            ->where('o.approval_id','=',2477)
            ->get();    
        }

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->totalEstimated=round($retArray[0]->totalEstimated, 0);
        //$tempFigure=explode(".", $retArray[0]->totalEstimated);
        //$retArray[0]->totalEstimated=$tempFigure;
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getComparison(){
        $post = Input::all();
        $client_id=$post['company_id'];
        $companyYear = $this->common->GetTableRecords('staff',array('user_id' => $client_id, 'status'=>'1'),array(),0,0,'gross_year');
        $year1=$post['comparisonPeriod1'];
        $year2=$companyYear[0]->gross_year;
        
        $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimated'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            //->where(YEAR('i.created_date'),'=',YEAR(CURDATE()))
            ->where(DB::raw('YEAR(i.created_date)'), '=', DB::raw('YEAR(CURDATE())'))
            //->whereRaw('YEAR(i.created_date)' <= 'YEAR(CURDATE()')
            ->get();


        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $amountCurrent=round($retArray[0]->totalEstimated, 0);
        //$tempFigure=explode(".", $amountCurrent);
        //$retArray[0]->totalEstimated=$tempFigure;
        $retArray[0]->totalEstimated=$amountCurrent;
        $retArray[0]->year2=$year2;

        $retArrayPrevious = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimatedPrevious'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where(DB::raw('YEAR(i.created_date)'), '=',$year2)
            ->get();

        /*if(empty($retArrayPrevious))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }*/
        if(!empty($retArrayPrevious)){
            $amountPrevious=round($retArrayPrevious[0]->totalEstimatedPrevious, 0);
            $retArray[0]->totalEstimatedPrevious=$amountPrevious;
            if($amountPrevious!='0.00'){
                $retArray[0]->percentDifference = round((($amountCurrent*100) / $amountPrevious),0)-100;    
            }else{
                $retArray[0]->percentDifference = 0;
            }
        }

        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getUnshipped(){
        $post = Input::all();
        $client_id=$post['company_id'];
        
        $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.balance_due) as totalUnshipped'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('shipping as s','s.order_id','=','o.id')   
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('s.shipping_status','=','1')
            ->where('o.is_paid','=','0')
            ->where('o.grand_total','>','o.total_payments')
            ->get();

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->totalUnshipped=round($retArray[0]->totalUnshipped, 0);
        /*$tempFigure=explode(".", $retArray[0]->totalUnpaid);
        $retArray[0]->totalUnpaid=$tempFigure;*/
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }
    
    public function getFullShipped(){
        $post = Input::all();
        $client_id=$post['company_id'];

        if((isset($post['duration']) && $post['duration']!=0)){
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalShipped') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('shipping as s','s.order_id','=','o.id')   
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.date_shipped','>','s.fully_shipped');

            if($post['duration']=='1'){
                $retArray = $retArray->where(DB::raw('i.created_date'), '=', DB::raw('CURDATE()'));
            }else if($post['duration']=='2'){
                $retArray = $retArray->where(DB::raw('WEEK(i.created_date)'), '=', DB::raw('WEEK(CURDATE())-1'));
            }else if($post['duration']=='3'){
                $retArray = $retArray->where(DB::raw('MONTH(i.created_date)'), '=', DB::raw('MONTH(CURDATE())-1'));
            }else if($post['duration']=='4'){
                $retArray = $retArray->where(DB::raw('YEAR(i.created_date)'), '=', DB::raw('YEAR(CURDATE())-1'));
            }
            $retArray = $retArray->where('s.shipping_status','=','2')
            ->get();
        }else{
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalShipped') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('shipping as s','s.order_id','=','o.id')   
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.date_shipped','>','s.fully_shipped')
            ->where('s.shipping_status','=','2')
            ->get();    
        }

        

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->totalShipped=round($retArray[0]->totalShipped, 0);
        /*$tempFigure=explode(".", $retArray[0]->totalUnpaid);
        $retArray[0]->totalUnpaid=$tempFigure;*/
        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }

    public function getProduction(){
        $post = Input::all();
        $client_id=$post['company_id'];

        if((isset($post['sales_id']) && $post['sales_id']!=0)){
            $sales_id=$post['sales_id'];
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalProduction'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.sales_id','=',$sales_id)
            ->where('o.approval_id','=',2483)
            ->get();
        }else{
            $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalProduction'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.approval_id','=',2483)
            ->get();    
        }

        if(empty($retArray))
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
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }
}