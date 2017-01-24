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
use TCPDF;

class InvoiceController extends Controller { 

    public function __construct(Common $common, Order $order, Product $product, Invoice $invoice, Client $client,Company $company,TCPDF $tdpdf)
    {
        //parent::__construct();
        $this->common = $common;
        $this->order = $order;
        $this->product = $product;
        $this->invoice = $invoice;
        $this->client = $client;
        $this->company = $company;
        $this->tdpdf = $tdpdf;
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
                        1=>array('key' => 'o.name', 'name' => 'Job Name'),
                        2=>array('key' => 'client.client_company', 'name' => 'Company'),
                        3=>array('key' => 'i.created_date', 'name' => 'Date'),
                        4=>array('key' => 'o.grand_total', 'name' => 'Invoice $ Amount'),
                        5=>array('key' => 'o.in_hands_by', 'name' => 'In Hands By'),
                        6=>array('key' => '', 'name' => 'Sync with Quickbooks', 'sortable' => false),
                        7=>array('key' => 'o.approval_id', 'name' => 'Order Status', 'sortable' => false),
                        8=>array('key' => '', 'name' => '', 'sortable' => false), 
                        9=>array('key' => '', 'name' => 'Option', 'sortable' => false),
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


                $order_data_all['order'][0]->sns_shipping_name = '';

            if($order_data_all['order'][0]->sns_shipping == '1') {
                $order_data_all['order'][0]->sns_shipping_name = 'Ground';
            } elseif ($order_data_all['order'][0]->sns_shipping == '2') {
                $order_data_all['order'][0]->sns_shipping_name = 'Next Day Air';
            } elseif ($order_data_all['order'][0]->sns_shipping == '3') {
                $order_data_all['order'][0]->sns_shipping_name = '2nd Day Air';
            } elseif ($order_data_all['order'][0]->sns_shipping == '16') {
                $order_data_all['order'][0]->sns_shipping_name = '3 Day Select';
            } elseif ($order_data_all['order'][0]->sns_shipping == '6') {
                $order_data_all['order'][0]->sns_shipping_name = 'Will Call / PickUp';
            }


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
            

            $order_data_all['order'][0]->sns_shipping_name = '';

            if($order_data_all['order'][0]->sns_shipping == '1') {
                $order_data_all['order'][0]->sns_shipping_name = 'Ground';
            } elseif ($order_data_all['order'][0]->sns_shipping == '2') {
                $order_data_all['order'][0]->sns_shipping_name = 'Next Day Air';
            } elseif ($order_data_all['order'][0]->sns_shipping == '3') {
                $order_data_all['order'][0]->sns_shipping_name = '2nd Day Air';
            } elseif ($order_data_all['order'][0]->sns_shipping == '16') {
                $order_data_all['order'][0]->sns_shipping_name = '3 Day Select';
            } elseif ($order_data_all['order'][0]->sns_shipping == '6') {
                $order_data_all['order'][0]->sns_shipping_name = 'Will Call / PickUp';
            }


            $order_data =  $order_data_all['order']; 


            $retutn_arr['invoice_data'] = array();
        }


         if(!empty($order_data))
        {   
                
            
                if($order_data[0]->date_shipped != '0000-00-00' && $order_data[0]->date_shipped != NULL) {
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

       

        $retutn_arr['company_data'][0]->url = (!empty($retutn_arr['company_data'][0]->url) && preg_match('/http/',$retutn_arr['company_data'][0]->url) == false) ? "http://".$retutn_arr['company_data'][0]->url:$retutn_arr['company_data'][0]->url;


        if(!empty($order_data_all['order'][0]->order_blind))
        {
            $retutn_arr['company_data'][0]->photo= $this->common->checkImageExist($company_id.'/staff/'.$staff[0]->id."/",$retutn_arr['company_data'][0]->bw_photo);
          //  $retutn_arr['company_data'][0]->photo= $this->common->checkImageExist($company_id.'/client/'.$order_data_all['order'][0]->client_id."/",$order_data_all['order'][0]->b_w_logo);
        }
        else
        {
            $retutn_arr['company_data'][0]->photo= $this->common->checkImageExist($company_id.'/staff/'.$staff[0]->id."/",$retutn_arr['company_data'][0]->photo);
           // $retutn_arr['company_data'][0]->photo = UPLOAD_PATH.$company_id."/staff/".$staff[0]->id."/".$retutn_arr['company_data'][0]->photo;
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


        $price_garment_mackup = $this->common->GetTableRecords('price_garment_mackup',array('price_id' => $order_data[0]->price_id),array());
        $price_screen_primary = $this->common->GetTableRecords('price_screen_primary',array('price_id' => $order_data[0]->price_id),array());
        $price_screen_secondary = $this->common->GetTableRecords('price_screen_secondary',array('price_id' => $order_data[0]->price_id),array());
        $price_direct_garment = $this->common->GetTableRecords('price_direct_garment',array('price_id' => $order_data[0]->price_id),array());
        $embroidery_switch_count = $this->common->GetTableRecords('embroidery_switch_count',array('price_id' => $order_data[0]->price_id),array());

        $data = array();
        $data['cond']['company_id'] = $company_id;
        $miscData = $this->common->getAllMiscDataWithoutBlank($data);



        foreach ($all_design as $design) {
            
            $data = array('company_id' => $company_id,'id' => $design->id);
            $pos_array = $this->order->getDesignPositionDetail($data);

            foreach ($pos_array['order_design_position'] as $position) {

               $position->placement_type_charge = 0;


                            if($position->placement_type > 0)
                                        {
                                            $placement_type_id =  $position->placement_type;
                                            $miscData['placement_type'][$placement_type_id]->slug;
                                            
                                            if($miscData['placement_type'][$placement_type_id]->slug == 43)
                                            {
                                                foreach($price_screen_primary as $primary) {
                                                    
                                                    $price_field = 'pricing_'.$position->color_stitch_count.'c';

                                                    if($position->qnty >= $primary->range_low && $position->qnty <= $primary->range_high)
                                                    {
                                                        if(isset($primary->$price_field))
                                                        {
                                                            $position->placement_type_charge = $primary->$price_field;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif($miscData['placement_type'][$placement_type_id]->slug == 44)
                                            {
                                                foreach($price_screen_secondary as $secondary) {
                                                    
                                                    $price_field = 'pricing_'.$position->color_stitch_count.'c';

                                                    if($position->qnty >= $secondary->range_low && $position->qnty <= $secondary->range_high)
                                                    {
                                                        if(isset($secondary->$price_field))
                                                        {
                                                            $position->placement_type_charge = $secondary->$price_field;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif($miscData['placement_type'][$placement_type_id]->slug == 45)
                                            {
                                                $switch_id = 0;
                                                foreach($embroidery_switch_count as $embroidery) {
                                                    
                                                    $price_field = 'pricing_'.$position->color_stitch_count.'c';

                                                    if($position->color_stitch_count >= $embroidery->range_low_1 && $position->color_stitch_count <= $embroidery->range_high_1)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_1c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_2 && $position->color_stitch_count <= $embroidery->range_high_2)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_2c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_3 && $position->color_stitch_count <= $embroidery->range_high_3)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_3c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_4 && $position->color_stitch_count <= $embroidery->range_high_4)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_4c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_5 && $position->color_stitch_count <= $embroidery->range_high_5)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_5c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_6 && $position->color_stitch_count <= $embroidery->range_high_6)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_6c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_7 && $position->color_stitch_count <= $embroidery->range_high_7)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_7c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_8 && $position->color_stitch_count <= $embroidery->range_high_8)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_8c';
                                                    }
                                                    if($position->color_stitch_count >= $embroidery->range_low_9 && $position->color_stitch_count <= $embroidery->range_high_9)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_9c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_10 && $position->color_stitch_count <= $embroidery->range_high_10)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_10c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_11 && $position->color_stitch_count <= $embroidery->range_high_11)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_11c';
                                                    }
                                                    elseif($position->color_stitch_count >= $embroidery->range_low_12 && $position->color_stitch_count <= $embroidery->range_high_12)
                                                    {
                                                        $switch_id = $embroidery->id;
                                                        $embroidery_field = 'pricing_12c';
                                                    }
                                                }

                                                if($switch_id > 0)
                                                {
                                                    $price_screen_embroidery = $this->common->GetTableRecords('price_screen_embroidery',array('embroidery_switch_id' => $switch_id),array());

                                                    foreach ($price_screen_embroidery as $embroidery2) {
                                                        
                                                        if($position->qnty >= $embroidery2->range_low && $position->qnty <= $embroidery2->range_high)
                                                        {
                                                            $position->placement_type_charge = $embroidery2->$embroidery_field;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif($miscData['placement_type'][$placement_type_id]->slug == 46)
                                            {
                                                if($position->dtg_size > 0 && $position->dtg_on > 0)
                                                {
                                                    $dtg_size_id =  $position->dtg_size;
                                                    $miscData['dir_to_garment_sz'][$dtg_size_id]->slug;

                                                    $dtg_on_id = $position->dtg_on;
                                                    $miscData['direct_to_garment'][$dtg_on_id]->slug;

                                                    if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 17 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                                        $garment_field = 'pricing_1c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 17 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                                        $garment_field = 'pricing_2c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 18 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                                        $garment_field = 'pricing_3c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 18 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                                        $garment_field = 'pricing_4c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 19 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                                        $garment_field = 'pricing_5c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 19 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                                        $garment_field = 'pricing_6c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 20 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 16){
                                                        $garment_field = 'pricing_7c';
                                                    }
                                                    else if($miscData['dir_to_garment_sz'][$dtg_size_id]->slug == 20 && $miscData['direct_to_garment'][$dtg_on_id]->slug == 15){
                                                        $garment_field = 'pricing_8c';
                                                    }

                                                    foreach($price_direct_garment as $garment) {
                                                        
                                                        if($position->qnty >= $garment->range_low && $position->qnty <= $garment->range_high)
                                                        {
                                                            $position->placement_type_charge = $garment->$garment_field;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                    }

               
          
            $design->positions = array_chunk($pos_array['order_design_position'], 2);
            $design->positions_data = $pos_array['order_design_position'];

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
        $data = $this->getInvoiceDetail($post['invoice_id'],$post['company_id'],1,$post['order_id']);
        
        /*PDF::AddPage('P','A4');
        PDF::writeHTML(view('pdf.invoice',$data)->render());
        PDF::Output('order_invoice_'.$post['invoice_id'].'.pdf');

*/

        $pdf = $this->tdpdf;
        $pdf->FooterImg(FOOTER_IMAGE);
        $pdf->FooterImg(SITE_HOST."/assets/images/etc/footer-1.png",190);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage('P','A4');
        $pdf->writeHTML(view('pdf.invoice',$data)->render());
        $pdf->Output('order_invoice_'.$post['invoice_id'].'.pdf');
       


    }

    // get invoice history from payment history
    public function getInvoiceHistory($invoice_id,$company_id,$type=0){

        $post = Input::all();
        
        //$invoice_data = $this->common->GetTableRecords('invoice',array('id' => $invoice_id),array());
        $retArray = $this->invoice->getInvoiceHistory($post,$invoice_id);

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

    // get stored card details with Authorized.net payment profile IDs
    public function getInvoiceCards($invoice_id,$company_id,$type=0){

        $post = Input::all();

        $retArray = $this->invoice->getInvoiceCards($post,$invoice_id);

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

    /*
    public function getSalesPersons(){
        $post = Input::all();

        $retArray = $this->invoice->getSalesPersons($post);

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
    }*/

    /*public function getNoQuickbook(){
        $post = Input::all();
        $retArray = $this->invoice->getNoQuickbook($post);

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
    }*/

    public function getSalesClosed(){
        $post = Input::all();
        $retArray = $this->invoice->getSalesClosed($post);

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

    /*public function getUnpaid(){
        $post = Input::all();
        $retArray = $this->invoice->getUnpaid($post);

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
    }*/

    /*public function getAverageOrders(){
        $post = Input::all();
        $retArray = $this->invoice->getAverageOrders($post);

        $response = array(
            'success' => 1, 
            'message' => GET_RECORDS,
            'allData' => $retArray
        );
        return response()->json(["data" => $response]);
    }*/

    /*public function getLatestOrders(){
        $post = Input::all();
        $retArray = $this->invoice->getLatestOrders($post);

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
    }*/

    public function getEstimates(){
        $post = Input::all();
        $client_id=$post['company_id'];

        $estimate = $this->common->GetTableRecords('misc_type',array('company_id' => $client_id, 'slug'=>137),array(),0,0,'id');
        $estimate_id=$estimate[0]->id;

        $retArray = $this->invoice->getEstimates($post,$estimate_id);

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

    /*public function getComparison(){
        $post = Input::all();
        $client_id=$post['company_id'];
        $companyYear = $this->common->GetTableRecords('staff',array('user_id' => $client_id, 'status'=>'1'),array(),0,0,'gross_year');
        $year2=$companyYear[0]->gross_year;

        $retArray = $this->invoice->getComparison($post,$year2);

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
    }*/

    public function getUnshipped(){
        $post = Input::all();

        $retArray = $this->invoice->getUnshipped($post);

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

        $retArray = $this->invoice->getFullShipped($post);

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

      $allproductionIds = $this->common->getAllMiscDataProduction($client_id);
      $retArray = $this->invoice->getProduction($post,$allproductionIds);

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

    public function getFullDashboard(){
        $retArray =array();
        $post = Input::all();
        $client_id=$post['company_id'];

        // Fetch the list of sales persons
        $retArraySales = $this->invoice->getSalesPersons($post);
        if(!empty($retArraySales))
        {
            $retArray["salesPersons"] = $retArraySales;
        }

        //Average Orders
        $retArrayAverageOrders = $this->invoice->getAverageOrders($post);
        if(!empty($retArrayAverageOrders))
        {
            $retArray["averageOrders"] = $retArrayAverageOrders;
        }

        // Yearly Gross Compare
        $companyYear = $this->common->GetTableRecords('staff',array('user_id' => $client_id, 'status'=>'1'),array(),0,0,'gross_year');
        $year2=$companyYear[0]->gross_year;
        $retArrayYearlyComparison = $this->invoice->getComparison($post,$year2);
        if(!empty($retArrayYearlyComparison))
        {
            $retArray["yearlyComparison"] = $retArrayYearlyComparison;
        }

        // Latest Orders
        $retArrayLatestOrders = $this->invoice->getLatestOrders($post);
        if(!empty($retArrayLatestOrders))
        {
            $retArray["latestOrders"] = $retArrayLatestOrders;
        }

        // Orders not send to Quickbooks
        $retArrayNoQuickbook = $this->invoice->getNoQuickbook($post);
        if(!empty($retArrayNoQuickbook))
        {
            $retArray["noQuickbook"] = $retArrayNoQuickbook;
        }

        // Orders to be shipped
        $retArrayUnshipped = $this->invoice->getUnshipped($post);
        if(!empty($retArrayUnshipped))
        {
            $retArrayUnshipped[0]->totalUnshipped=round($retArrayUnshipped[0]->totalUnshipped, 0);
            $retArray["totalUnshipped"] = $retArrayUnshipped;
        }

        // Orders with Balances
        $retArrayUnpaid = $this->invoice->getUnpaid($post);
        if(!empty($retArrayUnpaid))
        {
            $retArrayUnpaid[0]->totalUnpaid=round($retArrayUnpaid[0]->totalUnpaid, 0);
            $retArray["totalUnpaid"] = $retArrayUnpaid;
        }

        // Numbers of Orders in Production
       /* $estimation_id = $this->common->GetTableRecords('misc_type',array('company_id' => $client_id, 'slug'=>137),array(),0,0,'id');
        $estimation_id=$estimation_id[0]->id;*/
        $allproductionIds = $this->common->getAllMiscDataProduction($client_id);
        $retArrayProduction = $this->invoice->getProduction($post,$allproductionIds);
        if(!empty($retArrayProduction))
        {
            $retArrayProduction[0]->totalProduction=round($retArrayProduction[0]->totalProduction, 0);
            $retArray["totalProduction"] = $retArrayProduction;
        }

        // Sales Closed
        $retArraySalesClosed = $this->invoice->getSalesClosed($post);
        if(!empty($retArraySalesClosed))
        {
            $retArraySalesClosed[0]->totalSales=round($retArraySalesClosed[0]->totalSales, 0);
            $retArray["salesClosed"] = $retArraySalesClosed;
        }

        $retArrayFullShipped = $this->invoice->getFullShipped($post);
        if(!empty($retArrayFullShipped))
        {
            $retArrayFullShipped[0]->totalShipped=round($retArrayFullShipped[0]->totalShipped, 0);
            $retArray["fullShipped"] = $retArrayFullShipped;
        }


        $estimate = $this->common->GetTableRecords('misc_type',array('company_id' => $client_id, 'slug'=>137),array(),0,0,'id');
        $estimate_id=$estimate[0]->id;

        $retArrayEstimates = $this->invoice->getEstimates($post,$estimate_id);

        if(!empty($retArrayEstimates))
        {
            $retArrayEstimates[0]->totalEstimated=round($retArrayEstimates[0]->totalEstimated, 0);
            $retArray["totalEstimated"] = $retArrayEstimates;
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