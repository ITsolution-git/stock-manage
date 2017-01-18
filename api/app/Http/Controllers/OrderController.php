<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Order;
use App\Api;
use App\Common;
use App\Purchase;
use App\Product;
use App\Client;
use App\Company;
use App\Affiliate;
use DB;
use App;
use Request;
use Response;
//use Barryvdh\DomPDF\Facade as PDF;
use PDF;


class OrderController extends Controller { 

    public function __construct(Order $order,Common $common,Purchase $purchase,Product $product,Client $client,Affiliate $affiliate,Api $api,Company $company)
    {
        parent::__construct();
        $this->order = $order;
        $this->purchase = $purchase;
        $this->common = $common;
        $this->product = $product;
        $this->client = $client;
        $this->affiliate = $affiliate;
        $this->api = $api;
        $this->company = $company;
    }

/** 
* @SWG\Definition(
*      definition="orderList",
*      type="object",
*     
*      @SWG\Property(
*          property="cond",
*          type="object",
*
*               @SWG\Property(
*               property="company_id",
*               type="integer",
*               ),
*           
*               @SWG\Property(
*               property="params",
*               type="object",
*                    @SWG\Property(
*                    property="page",
*                    type="object",
*                       @SWG\Property(
*                       property="count",
*                       type="integer",
*                       ),
*                       @SWG\Property(
*                       property="page",
*                       type="integer",
*                       )
*               )
*         )
*
*      )
*  )
*/


  /**
 * @SWG\Post(
 *  path = "/api/public/order/listOrder",
 *  summary = "Order Listing",
 *  tags={"Order"},
 *  description = "Order Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Order Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/orderList")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Order Listing"),
 *  @SWG\Response(response="default", description="Order Listing"),
 * )
 */

    public function listOrder()
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
            $post['sorts']['sortBy'] = 'order.display_number';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'order.display_number';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->order->getOrderdata($post);
        $getAllDesigndata = $this->order->getAllDesigndata();

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'order.display_number', 'name' => 'Order ID'),
                        1=>array('key' => 'order.name', 'name' => 'Job Name'),
                        2=>array('key' => 'client.client_company', 'name' => 'Company'),
                        3=>array('key' => 'order.approval_id', 'name' => 'Order Status','sortable' => false),
                        4=>array('key' => 'order.created_date', 'name' => 'Date Created'),
                        5=>array('key' => 'null', 'name' => 'Sales Rep', 'sortable' => false),
                        6=>array('key' => 'users.name', 'name' => 'Account Manager', 'sortable' => false),
                        7=>array('key' => 'order.date_shipped', 'name' => 'Ship Date'),
                        8=>array('key' => 'null', 'name' => 'Operations', 'sortable' => false)
                        );

        if(empty($records))
        {
            $records = array('No Records found');
        }
        else
        {
            foreach($records as $data) {
                if(array_key_exists($data->id, $getAllDesigndata)) {
                    $data->design_pos = $getAllDesigndata[$data->id];
                } else {
                    $data->design_pos = array();
                }
            }
        }

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success);
        return $this->return_response($data);
    }

/** 
 * @SWG\Definition(
 *      definition="orderDetail",
 *      type="object",
 *     
 *     
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          ),
 *
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/orderDetail",
 *  summary = "Order Detail",
 *  tags={"Order"},
 *  description = "Order Detail",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Order Detail",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/orderDetail")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Order Detail"),
 *  @SWG\Response(response="default", description="Order Detail"),
 * )
 */
    public function orderDetail() {
 
        $data = Input::all();

        if(isset($data['affiliate_id']))
        {
            $affiliate_data = $this->common->GetTableRecords('order_affiliate_mapping',array('affiliate_id' => $data['affiliate_id']),array());
            $data['id'] = $affiliate_data[0]->order_id;
            $data['is_affiliate'] = true;
        }
        
        $result = $this->order->orderDetail($data);

         if(empty($result['order']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

        $result['order'][0]->sns_shipping_name = '';

        if($result['order'][0]->sns_shipping == '1') {
            $result['order'][0]->sns_shipping_name = 'Ground';
        } elseif ($result['order'][0]->sns_shipping == '2') {
            $result['order'][0]->sns_shipping_name = 'Next Day Air';
        } elseif ($result['order'][0]->sns_shipping == '3') {
            $result['order'][0]->sns_shipping_name = '2nd Day Air';
        } elseif ($result['order'][0]->sns_shipping == '16') {
            $result['order'][0]->sns_shipping_name = '3 Day Select';
        } elseif ($result['order'][0]->sns_shipping == '6') {
            $result['order'][0]->sns_shipping_name = 'Will Call / PickUp';
        }

        $finishing_count = $this->order->getFinishingCount($result['order'][0]->id);
        $total_shipped_qnty = $this->order->getShippedByOrder($data);
        $locations = $this->common->GetTableRecords('order_shipping_address_mapping',array('order_id' => $result['order'][0]->id),array());
        $dist_location = count($locations);
        $purchase_orders = $this->order->getPoByOrder($result['order'][0]->id,'po');
        $recieve_orders = $this->order->getPoByOrder($result['order'][0]->id,'ro');
        $notes_count = $this->order->getOrderNotes($result['order'][0]->id);
        $total_packing_charge = $this->order->getTotalPackingCharge($result['order'][0]->id);

        $result['order'][0]->total_shipped_qnty = $total_shipped_qnty ? $total_shipped_qnty : '0';
        $result['order'][0]->dist_location = $dist_location ? $dist_location : '0';
        $result['order'][0]->finishing_count = $finishing_count ? $finishing_count : '0';
        $result['order'][0]->notes_count = $notes_count ? $notes_count : '0';
        $result['order'][0]->total_packing_charge = $total_packing_charge ? $total_packing_charge : '0';

        $result['order'][0]->purchase_orders = $purchase_orders;
        $result['order'][0]->recieve_orders = $recieve_orders;


        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['order'],
                                'order_item' => array()
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['order'],
                                'order_item' => array()
                            );
        } 
        return response()->json(["data" => $response]);

    }

    /** 
 * @SWG\Definition(
 *      definition="updatePosition",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"color_stitch_count"},
 *          @SWG\Property(
 *          property="color_stitch_count",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *       @SWG\Property(
 *          property="design_id",
 *          type="integer",
 *        ),
 *       @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *        ),
 *       @SWG\Property(
 *          property="table",
 *          type="string",
 *        ),
 *         @SWG\Property(
 *          property="column_name",
 *          type="string",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/updatePositions",
 *  summary = "Update Position",
 *  tags={"Order"},
 *  description = "Update Position",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="In Data we have to specify the Column Name(Currently we are update color stitch count)",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/updatePosition")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Update Position"),
 *  @SWG\Response(response="default", description="Update Position"),
 * )
 */
     public function updatePositions()
     {
        $post = Input::all();
       
        
        if($post['column_name'] == 'position_id') {
            $result = $this->order->checkDuplicatePositions($post['design_id'],$post['data']['position_id']);

            if($result == '1' ) {
                $data = array("success"=>2,"message"=>"Duplicate");
                 return response()->json(['data'=>$data]);
            }
        }



        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $date_field = (empty($post['date_field']))? '':$post['date_field']; 

          if($post['column_name'] == 'color_stitch_count') {
            $post['data']['screen_fees_qnty'] = $post['data']['color_stitch_count'];
          }  

          if($post['column_name'] == 'placement_type') {

              $postNew = array();
              $postNew['cond']['company_id'] = $post['company_id'];
              $miscData = $this->common->getAllMiscDataWithoutBlank($postNew);

           
              if($miscData['placement_type'][$post['data']['placement_type']]->slug != 46) {
                $post['data']['dtg_size'] = 0;
                $post['data']['dtg_on'] = 0;
              }

          } 

          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data'],$date_field);

          if($post['column_name'] == 'position_id') 
          {

          $ord_display = $this->common->GetTableRecords('orders',array('id' => $post['order_id']),array());
          $ord_display = $ord_display['0']->display_number;

          $design_display = $this->common->GetTableRecords('order_design',array('id' => $post['design_id']),array());
          $design_display = $design_display['0']->display_number;



            $post['position'] = str_replace(" ","",strtolower(trim($post['position'])));
            $screen_set = $ord_display.'_'.$post['position'].'_'.$design_display;


            $this->common->getDisplayNumber('artjob_screensets',$post['company_id'],'company_id','id','yes');
            $this->common->UpdateTableRecords('artjob_screensets',array('positions' => $post['cond']['id']),array('screen_set' => $screen_set,'company_id'=>$post['company_id']));
          }  
            

          $data = array("success"=>1,"message"=>UPDATE_RECORD);

          $return = $this->calculateAll($post['order_id'],$post['company_id']);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
     }



      /** 
 * @SWG\Definition(
 *      definition="deletePosition",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"is_delete"},
 *          @SWG\Property(
 *          property="is_delete",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *       @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *        ),
 *       @SWG\Property(
 *          property="table",
 *          type="string",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/deleteOrderCommon",
 *  summary = "Delete Position",
 *  tags={"Order"},
 *  description = "Delete Position",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Delete Position",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/deletePosition")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Delete Position"),
 *  @SWG\Response(response="default", description="Delete Position"),
 * )
 */
     
     public function deleteOrderCommon()
     {
        $post = Input::all();

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $date_field = (empty($post['date_field']))? '':$post['date_field']; 
          
          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data'],$date_field);
          $data = array("success"=>1,"message"=>UPDATE_RECORD);

          $return = $this->calculateAll($post['order_id'],$post['company_id']);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
     }

    /**
    * Get Array
    * @return json data
    */
    public function return_response($data)
    {
        

        if (count($data) > 0) 
        {
            $response = $data;
        } 
        else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json($response);
    }
    
     /**
   * Save Color size.
   * @return json data
    */
    public function savePDF()
    {

        $order['order'] = json_decode($_POST['order']);
        $company_detail['company_detail'] = json_decode($_POST['company_detail']);

        $order['order']->created_date = date('m/d/Y',strtotime($order['order']->created_date));

        if($order['order']->date_shipped != '' && $order['order']->date_shipped != '0000-00-00'){
           $order['order']->date_shipped = date('m/d/Y',strtotime($order['order']->date_shipped));
        } else {
              $order['order']->date_shipped ='';
        }
      
        $combine_array = array_merge($order,$company_detail);
        
        PDF::AddPage('P','A4');
        PDF::writeHTML(view('pdf.order',array('data'=>$combine_array))->render());
   
        $pdf_url = "order-".$order['order']->id.".pdf";         
        $filename = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/pdf/". $pdf_url;
        PDF::Output($filename, 'F');
        return Response::download($filename);

    }
    

    public function sendEmail() {

        ini_set('memory_limit', '1024M');
        $post = Input::all();
        $email = trim($post['email']);
        $name = $post['name'];
        $fromemail = trim($post['from_email']);
        $email_array = explode(",",$email);
        $subject = $post['subject'];

        //echo "<pre>"; print_r($email_array); echo "</pre>"; die();
        if(!isset($post['mailMessage'])){
          $post['mailMessage'] = '';
        }

        if(!isset($post['invoice_id']))
        {
          $data = app('App\Http\Controllers\InvoiceController')->getInvoiceDetail(0,$post['company_id'],1,$post['order_id']);
          $file_path_old = FILEUPLOAD.'order_invoice_'.$post['order_id'].$post['company_id'].'.pdf';

          if(file_exists($file_path_old))
            {
                 unlink($file_path_old);
            }
         
          $file_path =  FILEUPLOAD.'order_invoice_'.$post['order_id'].$post['company_id'].'.pdf';
        }
        else
        {
          $data = app('App\Http\Controllers\InvoiceController')->getInvoiceDetail($post['invoice_id'],$post['company_id'],1);
          $file_path_old = FILEUPLOAD.'order_invoice_'.$post['invoice_id'].'.pdf';
           if(file_exists($file_path_old))
            {
                 unlink($file_path_old);
            }
          $file_path =  FILEUPLOAD.'order_invoice_'.$post['invoice_id'].'.pdf';           
        }

        $payment_link = '';

        if($data['order_data'][0]->grand_total > 0)
        {
          if($post['paid'] == '0')
          {
              $payment_data = $this->common->GetTableRecords('link_to_pay',array('order_id' => $post['order_id'],'payment_flag' => '0'),array(),'ltp_id','desc');

              if(empty($payment_data))
              {
                  $date = date_create();
                  $length = 25;
                  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                  $session_link = substr( str_shuffle( $chars ), 0, $length ).date_timestamp_get($date);

                  $this->common->InsertRecords('link_to_pay',array('order_id' => $post['order_id'],'balance_amount' => $post['balance'],'session_link' => $session_link));

                  $payment_link = SITE_HOST."/api/public/invoice/linktopay/".$session_link;
              }
              else
              {
                  $payment_link = SITE_HOST."/api/public/invoice/linktopay/".$payment_data[0]->session_link;
              }
          }
        }

        if(!file_exists($file_path))
        {
           
            PDF::AddPage('P','A4');
            PDF::writeHTML(view('pdf.invoice',$data)->render());
            PDF::Output($file_path,'F');
        }

        foreach ($email_array as $email)
        {
            Mail::send('emails.invoice', ['subject'=>$subject,'email'=>$email,'payment_link' => $payment_link,'mailMessage'=>$post['mailMessage']], function($message) use ($subject,$file_path,$email,$name,$fromemail)
            {
//                 $message->from('pdave@codal.com','Piyush Dave');
                $message->replyTo($fromemail,$name);
                $message->to(trim($email))->subject($subject);
                $message->attach($file_path);
            });                
        }

        $response = array('success' => 1, 'message' => 'Email has been sent successfully');
        return response()->json(["data" => $response]);
    }


    public function create_dir($dir_path) {

        if (!file_exists($dir_path)) {
           
            mkdir($dir_path, 0777, true);
        } else {
            exec("chmod $dir_path 0777");
        }
    }

/** 
 * @SWG\Definition(
 *      definition="addOrder",
 *      type="object",
 *     
 *
 *       @SWG\Property(
 *          property="addressModel",
 *          type="object",
*         ),
*
 *      @SWG\Property(
 *          property="orderData",
 *          type="object",
 *          required={"client_company"},
 *          @SWG\Property(
 *          property="client_company",
 *          type="string",
 *         ),
 *           @SWG\Property(
 *          property="name",
 *          type="string",
 *         ),
 *          @SWG\Property(
 *          property="client",
 *          type="object",
*
*               @SWG\Property(
     *          property="client_company",
     *          type="string",
     *         ),
     *               @SWG\Property(
     *          property="client_id",
     *          type="integer",
     *         ),
     *
 *         )
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *       @SWG\Property(
 *          property="login_id",
 *          type="integer",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/addOrder",
 *  summary = "Add Order",
 *  tags={"Order"},
 *  description = "Add Order",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add Order",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/addOrder")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Add Order"),
 *  @SWG\Response(response="default", description="Add Order"),
 * )
 */
    public function addOrder()
    {
        $post = Input::all();

       
        $client_data = $this->client->GetclientDetail($post['orderData']['client']['client_id']);
        

        $dataMisc['cond']['company_id'] = $post['company_id'];
        
        $misc_data = $this->common->getAllMiscDataWithoutBlank($dataMisc);
        
        foreach($misc_data['approval'] as $row){
           if($row->slug == '137') {
                $estimation_id = $row->id;
              }
        }

        /*if(array_key_exists('sns_shipping', $post['orderData'])) {
        $post['orderdata']['sns_shipping'] = $post['orderData']['sns_shipping'];
        }*/

         $post['orderdata']['display_number'] = $this->common->getDisplayNumber('orders',$post['company_id'],'company_id','id');
         $post['orderdata']['name'] = $post['orderData']['name'];
         $post['orderdata']['approval_id'] = $estimation_id;
         $post['orderdata']['login_id'] = $post['login_id'];
         $post['orderdata']['company_id'] = $post['company_id'];
         $post['orderdata']['client_id'] = $post['orderData']['client']['client_id'];
         $post['orderdata']['is_blind'] = $client_data['main']['is_blind'];
         $post['orderdata']['created_date'] = date('Y-m-d');
         $post['orderdata']['updated_date'] = date('Y-m-d');
         $post['orderdata']['account_manager_id'] = $client_data['main']['account_manager'];
         $post['orderdata']['sales_id'] = $client_data['sales']['salesperson'];
         $post['orderdata']['price_id'] = $client_data['sales']['salespricegrid'];
         $post['orderdata']['tax_rate'] = $client_data['tax']['tax_rate'];
         $post['orderdata']['contact_main_id'] = $client_data['contact']['id'];
         
          $order_id = $this->common->InsertRecords('orders',$post['orderdata']);

           $insert_arr = array();
           $insert_arr['data'] = array('order_id' => $order_id, 'created_date' => date('Y-m-d'), 'updated_date' => date('Y-m-d'));
           $art_id = $this->common->InsertRecords('art',$insert_arr);
           $id = $art_id;



            if(sizeof($post['addressModel'] > 0)) {

                foreach($post['addressModel'] as $address){

                     $add_arr = array();
                       $add_arr['data'] = array('order_id' => $order_id,'address_id' => $address['id']);
                       $add_id = $this->common->InsertRecords('order_shipping_address_mapping',$add_arr);

                }

            }
                



           //$data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$order_id);
           // send display number other then order Id
           $data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$post['orderdata']['display_number']);
           
           return response()->json(['data'=>$data]);
    }


    /** 
 * @SWG\Definition(
 *      definition="addDesign",
 *      type="object",
*
 *      @SWG\Property(
 *          property="designData",
 *          type="object",
 *          required={"client_company"},
 *
 *          @SWG\Property(
 *          property="design_name",
 *          type="string",
 *         ),
 *           @SWG\Property(
 *          property="colors_count",
 *          type="string",
 *         ),
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *          @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *         ),
 *          @SWG\Property(
 *          property="run_rate",
 *          type="integer",
 *         )
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/addDesign",
 *  summary = "Add Design",
 *  tags={"Order"},
 *  description = "Add Design",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add Design",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/addDesign")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Add Design"),
 *  @SWG\Response(response="default", description="Add Design"),
 * )
 */

     public function addDesign()
    {
        $post = Input::all();

        $post['designData']['display_number'] = $this->common->getDisplayNumber('order_design',$post['designData']['company_id'],'company_id','id');
     
        if(isset($post['designData']['hands_date']) && $post['designData']['hands_date'] != '') {
          $post['designData']['hands_date'] = date("Y-m-d", strtotime($post['designData']['hands_date']));
        }

        if(isset($post['designData']['shipping_date']) && $post['designData']['shipping_date'] != '') {
          $post['designData']['shipping_date'] = date("Y-m-d", strtotime($post['designData']['shipping_date']));
        }
 
        if(isset($post['designData']['start_date']) && $post['designData']['start_date'] != '') {
            $post['designData']['start_date'] = date("Y-m-d", strtotime($post['designData']['start_date']));
         }
      
        $design_id = $this->common->InsertRecords('order_design',$post['designData']);

       $data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$design_id);
       return response()->json(['data'=>$data]);

    }


/** 
 * @SWG\Definition(
 *      definition="designListing",
 *      type="object",
 *     
 *     
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          ),
 *
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/designListing",
 *  summary = "Design Listing",
 *  tags={"Order"},
 *  description = "Design Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Design Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/designListing")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Design Listing"),
 *  @SWG\Response(response="default", description="Design Listing"),
 * )
 */

    public function designListing() {
 
        $data = Input::all();
        $design_data = array();
        
        $this->common->getDisplayNumber('order_design',$data['company_id'],'company_id','id','yes');
        $order_design_data = $this->common->GetTableRecords('order_design',array('status' => '1','is_delete' => '1','order_id' => $data['id']),array(),'id','desc');
        
        $size_data = array();
        $order_design = array();
         $total_unit = 0;

        foreach ($order_design_data as $design) {
           
            $size_data = $this->common->GetTableRecords('purchase_detail',array('design_id' => $design->id,'is_delete' => '1'),array());
            
            $total_qnty = 0;
            foreach ($size_data as $size) {
                $total_qnty += $size->qnty;
               
            }
            $total_unit += $total_qnty;

            $design->size_data = $size_data;
            $design->total_qnty = $total_qnty;
            
            $order_design['all_design'][] = $design;

        }

        if($total_unit > 0)
        {
            $order_design['total_unit'] = $total_unit;
        }
      
        if (count($order_design) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $order_design
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => '');
        } 
        return response()->json(["data" => $response]);

    }


     /** 
 * @SWG\Definition(
 *      definition="designDetail",
 *      type="object",
 *     
 *     
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *          )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/designDetail",
 *  summary = "Design Detail",
 *  tags={"Order"},
 *  description = "Design Detail",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Design Detail",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/designDetail")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Design Detail"),
 *  @SWG\Response(response="default", description="Design Detail"),
 * )
 */

    public function designDetail() {
 
        $data = Input::all();
        $result = $this->order->designDetail($data);

        if(empty($result['design']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }
         
         if($result['design'][0]->hands_date != '0000-00-00' && $result['design'][0]->hands_date != '') {
            $result['design'][0]->hands_date = date("n/d/Y", strtotime($result['design'][0]->hands_date));
         } else {
            $result['design'][0]->hands_date = '';
         }
         if($result['design'][0]->shipping_date != '0000-00-00' && $result['design'][0]->shipping_date != '') {
            $result['design'][0]->shipping_date = date("n/d/Y", strtotime($result['design'][0]->shipping_date));
         }else{
            $result['design'][0]->shipping_date = '';
         }
         if($result['design'][0]->start_date != '0000-00-00' && $result['design'][0]->start_date != '') {
            $result['design'][0]->start_date = date("n/d/Y", strtotime($result['design'][0]->start_date));
         } else {
            $result['design'][0]->start_date = '';
         }
       
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['design']
                                );
        
        return response()->json(["data" => $response]);

    }


      /** 
 * @SWG\Definition(
 *      definition="editDesign",
 *      type="object",
*
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="designData",
 *          type="object",
 *          required={"client_company"},
 *
 *          @SWG\Property(
 *          property="design_name",
 *          type="string",
 *         ),
 *           @SWG\Property(
 *          property="colors_count",
 *          type="string",
 *         ),
 *          @SWG\Property(
 *          property="run_rate",
 *          type="integer",
 *         )
 *      ),
*       @SWG\Property(
 *          property="table",
 *          type="string",
 *        ),
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/editDesign",
 *  summary = "Edit Design",
 *  tags={"Order"},
 *  description = "Edit Design",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Edit Design",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/editDesign")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Edit Design"),
 *  @SWG\Response(response="default", description="Edit Design"),
 * )
 */

     public function editDesign()
    {
        $post = Input::all();
        
       
       
        unset($post['designData']['order_number']);
        unset($post['designData']['is_complete']);
        unset($post['designData']['order_display_number']);
        unset($post['designData']['affiliate_display_number']);

        unset($post['designData']['price_id']);


       $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['designData']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
            return response()->json(['data'=>$data]);

       $data = array("success"=>1,"message"=>INSERT_RECORD);
       return response()->json(['data'=>$data]);

    }


    /** 
 * @SWG\Definition(
 *      definition="orderDesignPosition",
 *      type="object",
 *     
 *     
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          ),
 *
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/getDesignPositionDetail",
 *  summary = "Design Position Detail",
 *  tags={"Order"},
 *  description = "Design Position Detail",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Design Position Detail",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/orderDesignPosition")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Design Position Detail"),
 *  @SWG\Response(response="default", description="Design Position Detail"),
 * )
 */

    public function getDesignPositionDetail()
    {
        $data = Input::all();

        $order_data = $this->order->getOrderByDesign($data['id']);

        $price_id = $order_data[0]->price_id;
        $order_id = $order_data[0]->id;

        $price_grid_data = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $price_id),array());
        $price_grid = $price_grid_data[0];

        $post = array();
        $post['cond']['company_id'] = $data['company_id'];
        $miscData = $this->common->getAllMiscDataWithoutBlank($post);

        $price_garment_mackup = $this->common->GetTableRecords('price_garment_mackup',array('price_id' => $price_id),array());
        $price_screen_primary = $this->common->GetTableRecords('price_screen_primary',array('price_id' => $price_id),array());
        $price_screen_secondary = $this->common->GetTableRecords('price_screen_secondary',array('price_id' => $price_id),array());
        $price_direct_garment = $this->common->GetTableRecords('price_direct_garment',array('price_id' => $price_id),array());
        $embroidery_switch_count = $this->common->GetTableRecords('embroidery_switch_count',array('price_id' => $price_id),array());

        $result = $this->order->getDesignPositionDetail($data);

        foreach ($result['order_design_position'] as $position) {

            $screen_print_charge = 0;
            $screen_print_charge = 0;
            $embroidery_charge = 0;
            $direct_to_garment_charge = 0;
            $markup_default = 0;

            $screen_print_charge2 = 0;
            $screen_print_charge2 = 0;
            $embroidery_charge2 = 0;
            $direct_to_garment_charge2 = 0;
            $markup_default2 = 0;

            $screen_print_charge_qnty = 0;
            $screen_print_charge_qnty = 0;
            $embroidery_charge_qnty = 0;
            $direct_to_garment_charge_qnty = 0;
            $markup_default_qnty = 0;

            $position_qty = $position->qnty;
            $color_stitch_count = $position->color_stitch_count;
            $position->color_count = $position->color_stitch_count;
            
            if($position->placement_type > 0)
            {
                $placement_type_id =  $position->placement_type;
                $miscData['placement_type'][$placement_type_id]->slug;

                if($miscData['placement_type'][$placement_type_id]->slug == 43)
                {
                    foreach($price_screen_primary as $primary)
                    {
                        $price_field = 'pricing_'.$color_stitch_count.'c';

                        if($position_qty >= $primary->range_low && $position_qty <= $primary->range_high)
                        {
                            if(isset($primary->$price_field))
                            {
                                $screen_print_charge2 = $primary->$price_field;
                            }
                        }
                        if($position_qty <= $primary->range_low)
                        {
                            $screen_print_charge_qnty = $primary->range_low." - ".$primary->range_high;
                            if(isset($primary->$price_field))
                            {
                                $screen_print_charge = $primary->$price_field;                                
                            }
                            break;
                        }
                    }
                }
                elseif($miscData['placement_type'][$placement_type_id]->slug == 44)
                {
                    foreach($price_screen_secondary as $secondary)
                    {
                        $price_field = 'pricing_'.$color_stitch_count.'c';

                        if($position_qty >= $secondary->range_low && $position_qty <= $secondary->range_high)
                        {
                            if(isset($secondary->$price_field))
                            {
                                $screen_print_charge2 = $secondary->$price_field;
                            }
                        }
                        if($position_qty <= $secondary->range_low)
                        {
                            $screen_print_charge_qnty = $secondary->range_low." - ".$secondary->range_high;
                            if(isset($secondary->$price_field))
                            {
                                $screen_print_charge = $secondary->$price_field;                                
                            }
                            break;
                        }
                    }
                }
                elseif($miscData['placement_type'][$placement_type_id]->slug == 45)
                {
                    $switch_id = 0;
                    foreach($embroidery_switch_count as $embroidery)
                    {
                        $price_field = 'pricing_'.$color_stitch_count.'c';

                        if($color_stitch_count >= $embroidery->range_low_1 && $color_stitch_count <= $embroidery->range_high_1)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_1c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_2 && $color_stitch_count <= $embroidery->range_high_2)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_2c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_3 && $color_stitch_count <= $embroidery->range_high_3)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_3c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_4 && $color_stitch_count <= $embroidery->range_high_4)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_4c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_5 && $color_stitch_count <= $embroidery->range_high_5)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_5c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_6 && $color_stitch_count <= $embroidery->range_high_6)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_6c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_7 && $color_stitch_count <= $embroidery->range_high_7)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_7c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_8 && $color_stitch_count <= $embroidery->range_high_8)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_8c';
                        }
                        if($color_stitch_count >= $embroidery->range_low_9 && $color_stitch_count <= $embroidery->range_high_9)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_9c';
                        }
                        elseif($color_stitch_count > $embroidery->range_high_9)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_10c';
                        }
                    }

                    if($switch_id > 0)
                    {
                        $price_screen_embroidery = $this->common->GetTableRecords('price_screen_embroidery',array('embroidery_switch_id' => $switch_id),array());

                        foreach ($price_screen_embroidery as $embroidery2)
                        {
                            if($position_qty >= $embroidery2->range_low && $position_qty <= $embroidery2->range_high)
                            {
                                $embroidery_charge2 = $embroidery2->$embroidery_field;
                            }
                            if($position_qty <= $embroidery2->range_low)
                            {
                                $embroidery_charge_qnty = $embroidery2->range_low." - ".$embroidery2->range_high;
                                if(isset($embroidery2->$embroidery_field))
                                {
                                    $embroidery_charge = $embroidery2->$embroidery_field;    
                                }
                                break;
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

                            if($position_qty >= $garment->range_low && $position_qty <= $garment->range_high)
                            {
                                $direct_to_garment_charge2 = $garment->$garment_field;
                            }
                            if($position_qty <= $garment->range_low)
                            {
                                $direct_to_garment_charge_qnty = $garment->range_low." - ".$garment->range_high;
                                if(isset($garment->$garment_field))
                                {
                                    $direct_to_garment_charge = $garment->$garment_field;
                                }
                                break;
                            }
                        }
                    }
                }
                if(count($price_garment_mackup) > 0 && $position_qty > 0)
                {
                    foreach($price_garment_mackup as $value) {
                        
                        if($position_qty <= $value->range_low)
                        {
                            $markup_default = $value->percentage;
                            break;
                        }
                    }
                }
            }

            $position->screen_print_charge_qnty = $screen_print_charge_qnty;
            $position->embroidery_charge_qnty = $embroidery_charge_qnty;
            $position->direct_to_garment_charge_qnty = $direct_to_garment_charge_qnty;

            if($position_qty > 0)
            {
                $position->screen_print_charge = $screen_print_charge;
                $position->embroidery_charge = $embroidery_charge;
                $position->direct_to_garment_charge = $direct_to_garment_charge;
                $position->markup_default = $markup_default;

                $position->screen_print_charge2 = 0;
                $position->embroidery_charge2 = 0;
                $position->direct_to_garment_charge2 = 0;

                if($screen_print_charge2 > 0) {
                    $position->screen_print_charge2 = abs($screen_print_charge2 - $screen_print_charge);
                }
                if($embroidery_charge2 > 0) {
                    $position->embroidery_charge2 = abs($embroidery_charge2 - $embroidery_charge);
                }
                if($direct_to_garment_charge2 > 0) {
                    $position->direct_to_garment_charge2 = abs($direct_to_garment_charge2 - $direct_to_garment_charge);
                }
            }
            else
            {
                $position->screen_print_charge = 0;
                $position->embroidery_charge = 0;
                $position->direct_to_garment_charge = 0;
                $position->markup_default = 0;
            }

            $position->total_price += $screen_print_charge2 + $embroidery_charge2 + $direct_to_garment_charge2;

            if(isset($data['getNextPrice']) && $data['getNextPrice'] == 1 && isset($data['position_id']) && $data['position_id'] == $position->id)
            {
                $response = array(
                                'position' => $position
                            );
                return response()->json(["data" => $response]);
            }
        }
       
        if (count($result['order_design_position']) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'order_design_position' => $result['order_design_position'],
                                'total_pos_qnty' => $result['total_pos_qnty'],
                                'total_screen_fees' => $result['total_screen_fees']
                            );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'order_design_position' => $result['order_design_position'],
                                'total_pos_qnty' => 0,
                                'total_screen_fees' => 0
                            );
        }
        return response()->json(["data" => $response]);
    }


    /** 
 * @SWG\Definition(
 *      definition="editOrder",
 *      type="object",
 *     
 *
  *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *      ),
 *       @SWG\Property(
 *          property="addressModel",
 *          type="object",
*         ),
 *       @SWG\Property(
 *          property="addressModelOld",
 *          type="object",
*         ),
*
 *      @SWG\Property(
 *          property="orderDataDetail",
 *          type="object",
 *          required={"name"},
 *          @SWG\Property(
 *          property="name",
 *          type="string",
 *         ),
 *           @SWG\Property(
 *          property="account_manager_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="approval_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="client_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="contact_main_id",
 *          type="integer",
 *         ),
  *         @SWG\Property(
 *          property="custom_po",
 *          type="string",
 *         ),
   *         @SWG\Property(
 *          property="date_shipped",
 *          type="string",
 *         ),
    *         @SWG\Property(
 *          property="in_hands_by",
 *          type="string",
 *         ),
     *         @SWG\Property(
 *          property="is_blind",
 *          type="integer",
 *         ),
     *         @SWG\Property(
 *          property="date_start",
 *          type="string",
 *         ),
      *         @SWG\Property(
 *          property="price_id",
 *          type="integer",
 *         ),
       *         @SWG\Property(
 *          property="sales_id",
 *          type="integer",
 *         ),
        *         @SWG\Property(
 *          property="sns_shipping",
 *          type="integer",
 *         )
 *      ),
  *       @SWG\Property(
 *          property="table",
 *          type="string",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/editOrder",
 *  summary = "Edit Order",
 *  tags={"Order"},
 *  description = "Edit Order",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Edit Order",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/editOrder")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Edit Order"),
 *  @SWG\Response(response="default", description="Edit Order"),
 * )
 */

     public function editOrder()
    {
        $post = Input::all();


        $newAddressarray = array_column($post['addressModel'], 'id');
        
        $oldAddressarray = array_column($post['addressModelOld'], 'id');
        

        $addArrayDifference = array_diff($newAddressarray,$oldAddressarray);
        $removeArrayDifference = array_diff($oldAddressarray,$newAddressarray);


        if(sizeof($addArrayDifference > 0)) {

            foreach($addArrayDifference as $address){
                

                      $add_arr = array();
                       $add_arr['data'] = array('order_id' => $post['cond']['id'],'address_id' => $address);
                       $add_id = $this->common->InsertRecords('order_shipping_address_mapping',$add_arr);

                }

        }


        if(sizeof($removeArrayDifference > 0)) {

            foreach($removeArrayDifference as $removeAddress){
               
               $deleteResult = $this->common->DeleteTableRecords('order_shipping_address_mapping',array('order_id'=>$post['cond']['id'], 'address_id'=>$removeAddress));

                }

        }

        

        $orderdata = $this->common->GetTableRecords('orders',array('id'=>$post['cond']['id']));

        if($post['orderDataDetail']['in_hands_by'] != '')
        {
            $post['orderDataDetail']['in_hands_by'] = date("Y-m-d", strtotime($post['orderDataDetail']['in_hands_by']));
        }
        if($post['orderDataDetail']['date_shipped'] != '')
        {
            $post['orderDataDetail']['date_shipped'] = date("Y-m-d", strtotime($post['orderDataDetail']['date_shipped']));
        }
        if($post['orderDataDetail']['date_start'] != '')
        {
            $post['orderDataDetail']['date_start'] = date("Y-m-d", strtotime($post['orderDataDetail']['date_start']));
        }

        if($orderdata[0]->client_id != $post['orderDataDetail']['client_id'])
        {
            $client_data = $this->client->GetclientDetail($post['orderDataDetail']['client_id']);
            $post['orderDataDetail']['price_id'] = $client_data['sales']['salespricegrid'];
            $post['orderDataDetail']['sales_id'] = $client_data['sales']['salesperson'];
            $post['orderDataDetail']['account_manager_id'] = $client_data['main']['account_manager'];
            $post['orderDataDetail']['contact_main_id'] = $client_data['contact']['id'];
        }

        $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['orderDataDetail']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
            return response()->json(['data'=>$data]);

       $data = array("success"=>1,"message"=>INSERT_RECORD);
       return response()->json(['data'=>$data]);

    }


    /** 
 * @SWG\Definition(
 *      definition="orderDetailInfo",
 *      type="object",
 *     
 *     
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          ),
 *
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/orderDetailInfo",
 *  summary = "Order Detail All Info",
 *  tags={"Order"},
 *  description = "Order Detail All Info",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Order Detail All Info",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/orderDetailInfo")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Order Detail All Info"),
 *  @SWG\Response(response="default", description="Order Detail All Info"),
 * )
 */

    public function orderDetailInfo() {
 
        $data = Input::all();
        $result = $this->order->orderDetailInfo($data);
        $price_grid = $this->common->GetTableRecords('price_grid',array('is_delete' => '1','status' => '1','company_id' =>$result['order'][0]->company_id),array());
        $staff = $this->common->getStaffList($result['order'][0]->company_id);
        $brandCo = $this->common->getBrandCordinator($result['order'][0]->company_id);
        $contact_main = $this->common->GetTableRecords('client_contact',array('is_deleted' => '1','client_id' =>$result['order'][0]->client_id),array());

         if($result['order'][0]->in_hands_by != '0000-00-00' && $result['order'][0]->in_hands_by != '') {
            $result['order'][0]->in_hands_by = date("n/d/Y", strtotime($result['order'][0]->in_hands_by));
         } else {
            $result['order'][0]->in_hands_by = '';
         }
         if($result['order'][0]->date_shipped != '0000-00-00' && $result['order'][0]->date_shipped != '') {
            $result['order'][0]->date_shipped = date("n/d/Y", strtotime($result['order'][0]->date_shipped));
         }else{
            $result['order'][0]->date_shipped = '';
         }
         if($result['order'][0]->date_start != '0000-00-00' && $result['order'][0]->date_start != '') {
            $result['order'][0]->date_start = date("n/d/Y", strtotime($result['order'][0]->date_start));
         } else {
            $result['order'][0]->date_start = '';
         }

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['order'],
                                'price_grid' => $price_grid,
                                'staff' => $staff,
                                'brandCo' => $brandCo,
                                'contact_main' => $contact_main
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['order'],
                                'price_grid' => $price_grid,
                                'staff' => $staff,
                                'brandCo' => $brandCo,
                                'contact_main' => $contact_main);
        } 
        return response()->json(["data" => $response]);

    }


/** 
 * @SWG\Definition(
 *      definition="updateOrderCharge",
 *      type="object",
 *
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"id"},
 *          @SWG\Property(
 *          property="id",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"rush_charge"},
 *          @SWG\Property(
 *          property="rush_charge",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *       @SWG\Property(
 *          property="table",
 *          type="string",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/updateOrderCharge",
 *  summary = "Update Order Charge",
 *  tags={"Order"},
 *  description = "Update Order Charge",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="In Data we have to specify the Column Name(Currently we are update Rush Charge)",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/updateOrderCharge")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Update Order Charge"),
 *  @SWG\Response(response="default", description="Update Order Charge"),
 * )
 */

    public function updateOrderCharge() {

        $post = Input::all();

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $date_field = (empty($post['date_field']))? '':$post['date_field']; 
          
          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data'],$date_field);
          $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }

        $return = $this->calculateAll($post['cond']['id'],$post['company_id']);
        return response()->json(['data'=>$data]);
    }

    public function updateMarkup() {

        $post = Input::all();

        $markup = $post['productData']['markup'];
        if($markup > 0)
        {
            $garment_mackup = $markup/100;
        }
        else
        {
            $garment_mackup = $post['productData']['markup_default']/100;
        }
        
        $avg_garment_price = $post['productData']['avg_garment_cost'] * $garment_mackup + $post['productData']['avg_garment_cost'];
        $avg_garment_price2 = round($avg_garment_price,2);

        $total_line_charge = $post['productData']['print_charges'] + $avg_garment_price2;
        $total_line_charge2 = round($total_line_charge,2);

        $this->common->UpdateTableRecords('design_product',array('design_id' => $post['design_id'],'product_id' => $post['productData']['id']),array('markup' => $markup));

        $return = app('App\Http\Controllers\ProductController')->orderCalculation($post['productData']['design_id']);

        $data = array("success"=>1);
        return response()->json(["data" => $data]);
    }

    public function updateOverride()
    {
        $post = Input::all();
        if($post['productData']['override'] > '0')
        {
            $subtract = $post['productData']['override'] - $post['productData']['total_line_charge'];
            $override_diff = round($subtract,2);
            $total_line_charge = $post['productData']['override'];

            $total_qnty = 0;
            foreach ($post['productData']['sizeData'] as $size) {
                $total_qnty += $size['qnty'];
            }

            $mul = $total_qnty * $total_line_charge;
            $sales_total =round($mul,2);

            $update_arr = array('total_line_charge' => $total_line_charge,'override' => $post['productData']['override'],'sales_total' => $sales_total,'override_diff' => $override_diff);
        }
        else
        {
            $update_arr = array('override_diff' => 0,'override' => 0);
        }
        $this->common->UpdateTableRecords('design_product',array('design_id' => $post['design_id'],'product_id' => $post['productData']['id']),$update_arr);

        $this->calculateAll($post['order_id'],$post['company_id']);

        $data = array("success"=>1);
        return response()->json(["data" => $data]);
    }


    /**
* @SWG\Get(
*  path = "/api/public/order/calculateAll/{order_id}/{company_id}",
*  summary = "Order Calculation",
*  tags={"Order"},
*  description = "Order Calculation",
*  @SWG\Parameter(
*     in="path",
*     name="order_id",
*     description="Order ID",
*     type="integer",
*     required=true
*  ),
*  @SWG\Parameter(
*     in="path",
*     name="company_id",
*     description="Company ID",
*     type="integer",
*     required=true
*  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
*  @SWG\Response(response=200, description="Order Calculation"),
*  @SWG\Response(response="default", description="Order Calculation"),
* )
*/

    public function calculateAll($order_id,$company_id)
    {
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $order_id,'is_delete' => '1'),array());

        if(!empty($design_data))
        {
            foreach ($design_data as $design) {
                $return = app('App\Http\Controllers\ProductController')->orderCalculation($design->id);
            }
        }
        else
        {
            $order_data = $this->common->GetTableRecords('orders',array('id' => $order_id),array());

            $order_charges_total =  $order_data[0]->separations_charge + $order_data[0]->rush_charge + $order_data[0]->distribution_charge + 
                                    $order_data[0]->digitize_charge + $order_data[0]->shipping_charge + $order_data[0]->setup_charge + 
                                    $order_data[0]->artwork_charge;

            if($order_charges_total > 0)
            {
                $order_total = $order_charges_total - $order_data[0]->discount;
            }
            else
            {
                $order_total = $order_charges_total;
            }

            $tax = $order_total * $order_data[0]->tax_rate/100;
            $grand_total = $order_total + $tax;
            $balance_due = $grand_total - $order_data[0]->total_payments;

            $update_order_arr = array(
                                    'screen_charge' => 0,
                                    'press_setup_charge' => 0,
                                    'order_line_total' => 0,
                                    'order_total' => round($order_total,2),
                                    'tax' => round($tax,2),
                                    'grand_total' => round($grand_total,2),
                                    'balance_due' => round($balance_due,2),
                                    'order_charges_total' => round($order_charges_total,2)
                                    );

            $this->common->UpdateTableRecords('orders',array('id' => $order_id),$update_order_arr);
        }
        $data = array("success"=>1);
        return response()->json(["data" => $data]);
    }

    public function snsOrder()
     {
        $post = Input::all();

      
        if($post['sns_shipping'] == '') {
            $post['sns_shipping'] = 1;
        }
        
        $result_company = $this->client->getStaffDetail($post['company_id']);

        $result_order = $this->product->getSnsProductDetail($post['id']);
        
       
        if(empty($result_order))
        {
            $data_record = array("success"=>0,"message"=>"There is no S&S product for particular this order");
            return response()->json(["data" => $data_record]);
        }
       

        $shippingAddress = array(
            "customer" => $post['company_name'],
            "attn" => $result_company[0]->first_name.' '.$result_company[0]->last_name,
            "address" => $result_company[0]->prime_address1,
            "city" => $result_company[0]->prime_address_city,
            "state"=> $result_company[0]->code,
            "zip"=> trim($result_company[0]->prime_address_zip),
            "residential"=> true);

        $lines = array();
        foreach($result_order as $order_data) {
                
                $lines[] = array(
                    "warehouseAbbr" => $order_data->warehouse,
                    "identifier" => $order_data->sku,
                    "qty" => $order_data->qnty);

            }

            $order_main_array = array("shippingAddress" => $shippingAddress,
                                          "shippingMethod"=> $post['sns_shipping'],
                                          "poNumber" => $post['id'],
                                          "emailConfirmation"=> $result_company[0]->email,
                                          "testOrder"=> true,
                                          "lines" =>  $lines);

       $order_json = json_encode($order_main_array);
       

        $result_api = $this->api->getApiCredential($post['company_id'],'api.sns','ss_detail');
       
        $credential = $result_api[0]->username.":".$result_api[0]->password;
        
 
        $curl = curl_init('https://api.ssactivewear.com/v2/orders/');                                                                      
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($curl, CURLOPT_POSTFIELDS, $order_json);                                                                  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($order_json))                                                                       
        );    
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $result = curl_exec($curl);
        curl_close($curl);

        $all_data = json_decode($result);
        
        
        if(!empty($all_data))
        {
            if(array_key_exists('code', $all_data)) {

                        if($all_data->code == 400) {
                     $data_record = array("success"=>0,"message"=>$all_data->errors[0]->message);
                     return response()->json(["data" => $data_record]);

                    }
            }
            
            $this->common->UpdateTableRecords('orders',array('id' => $post['id']),array('approved_by' => $post['user_id'],'order_number' => $all_data[0]->orderNumber,'order_sns_status' => $all_data[0]->orderStatus));
            $data_record = array("success"=>1,"message"=>"Order is successfully posted to S&S");
            
            return response()->json(["data" => $data_record]);
        } else {
             $data_record = array("success"=>0,"message"=>"There are no items added to post order");
            
            return response()->json(["data" => $data_record]);
        }
     }

     public function addInvoice()
     {
        $post = Input::all();

        if(!isset($post['quickbook_id'])){
          $post['quickbook_id'] = 0;
        }

        $result = $this->client->GetclientDetail($post['client_id']);
        $result_qbProductId = $this->company->getQBAPI($post['company_id']);
        $result_order = $this->order->GetOrderDetailAll($post['id']);

        $result_charges = $this->order->orderInfoData($post['company_id'],$post['id']);
        $other_charges = $this->order->orderChargeData($post['id']);

        $price_grid_data = $this->common->GetTableRecords('price_grid',array('status' => '1','id' => $result_charges[0]->price_id),array());
        $price_grid = $price_grid_data[0];


        if($result_qbProductId[0]->ss =='') {

            $data_record = array("success"=>0,"message"=>"Please complete Quickbook Setup First");
            return response()->json(["data" => $data_record]);
        }

        
        if($result['main']['qid'] == 0) {
          
          $result_quickbook = app('App\Http\Controllers\QuickBookController')->createCustomer($result['main'],$result['contact']);
          $this->common->UpdateTableRecords('client',array('client_id' => $post['client_id']),array('qid' => $result_quickbook));
          $result_quickbook_invoice = app('App\Http\Controllers\QuickBookController')->addInvoice($result_order,$result_charges,$result_quickbook,$result_qbProductId,$post['invoice_id'],$other_charges,$price_grid,$post['payment'],$post['id'],$post['quickbook_id'],$post['display_order_id']);
          
          
          if($result_quickbook_invoice == '1') {
            $data_record = array("success"=>1,"message"=>"Invoice Generated Successfully");
            return response()->json(["data" => $data_record]);
          } else {
             $data_record = array("success"=>0,"message"=>"Please connect Quickbook");
            return response()->json(["data" => $data_record]);
          }


        } else {
          
          $result_quickbook_invoice = app('App\Http\Controllers\QuickBookController')->addInvoice($result_order,$result_charges,$result['main']['qid'],$result_qbProductId,$post['invoice_id'],$other_charges,$price_grid,$post['payment'],$post['id'],$post['quickbook_id'],$post['display_order_id']);
          
          if($result_quickbook_invoice == '1') {
            $data_record = array("success"=>1,"message"=>"Invoice Generated Successfully");
            return response()->json(["data" => $data_record]);
          } else {
             $data_record = array("success"=>0,"message"=>"Please connect Quickbook again");
            return response()->json(["data" => $data_record]);
          }
          
        }
     }


     /** 
 * @SWG\Definition(
 *      definition="addPosition",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="positionData",
 *          type="object",
 *          required={"position_id"},
 *          @SWG\Property(
 *          property="position_id",
 *          type="integer",
 *         ),
 *           @SWG\Property(
 *          property="placement_type",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="qnty",
 *          type="integer",
 *         )
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *       @SWG\Property(
 *          property="design_id",
 *          type="integer",
 *        ),
 *       @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *        ),
 *       @SWG\Property(
 *          property="position",
 *          type="string",
 *        )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/addPosition",
 *  summary = "Add Position",
 *  tags={"Order"},
 *  description = "Add Position",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add Position",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/addPosition")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Add Position"),
 *  @SWG\Response(response="default", description="Add Position"),
 * )
 */
    public function addPosition()
    {
        $post = Input::all();

        $result = $this->order->checkDuplicatePositions($post['design_id'],$post['positionData']['position_id']);
        
        if($result == '1' ) {
            $data = array("success"=>2,"message"=>"This position already exists in this design.","id"=>'');
             return response()->json(['data'=>$data]);
        }
       
         $post['positiondata']['position_id'] = $post['positionData']['position_id'];
         $post['positiondata']['design_id'] = $post['design_id'];
         $post['positiondata']['qnty'] = $post['positionData']['qnty'];
         $post['positiondata']['placement_type'] = $post['positionData']['placement_type'];

         
         
          $id = $this->common->InsertRecords('order_design_position',$post['positiondata']);

         if($id > 0) {

          $post['artdata']['Positions'] = $id;
          $post['artdata']['order_id'] = $post['order_id'];
          $post['artdata']['company_id'] = $post['company_id'];
          $post['position'] = str_replace(" ","",strtolower(trim($post['position'])));


          $ord_display = $this->common->GetTableRecords('orders',array('id' => $post['order_id']),array());
          $ord_display = $ord_display['0']->display_number;

          $design_display = $this->common->GetTableRecords('order_design',array('id' => $post['design_id']),array());
          $design_display = $design_display['0']->display_number;


          $post['artdata']['screen_set'] = $ord_display.'_'.$post['position'].'_'.$design_display;

          $post['artdata']['display_number'] = $this->common->getDisplayNumber('artjob_screensets',$post['company_id'],'company_id','id','yes');
          $art_screen_id = $this->common->InsertRecords('artjob_screensets',$post['artdata']);
                    
         }
         

          $return = app('App\Http\Controllers\ProductController')->orderCalculation($post['design_id']);

           $data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$id);
           return response()->json(['data'=>$data]);

    }

    public function createInvoice()
    {
        $post = Input::all();

        if($post['payment'] == '15') {
            $setDate  = date('Y-m-d', strtotime("+15 days"));
        }
        else if($post['payment'] == '30') {
            $setDate  = date('Y-m-d', strtotime("+30 days"));
        } else {
            $setDate  = date('Y-m-d');
        }

        $ack= $this->common->GetTableRecords('misc_type',array('company_id' => $post['company_id'], 'slug'=>138),array(),0,0,'id');
        $ack_id=$ack[0]->id;
        $this->common->UpdateTableRecords('orders',array('id' => $post['order_id']),array('approval_id' => $ack_id));

        $display_number = $this->common->getDisplayNumber('invoice',$post['company_id'],'company_id','id');

        $orderData = array('order_id' => $post['order_id'], 'created_date' => date('Y-m-d'), 'payment_due_date' => $setDate, 'payment_terms' => $post['payment'], 'company_id' => $post['company_id'], 'display_number' => $display_number);
        $id = $this->common->InsertRecords('invoice',$orderData);

        $qb_data = $this->common->GetTableRecords('invoice',array('id' => $id),array());
        $qb_id = $qb_data[0]->qb_id;

        $data = array("success"=>1,"message"=>INSERT_RECORD,"invoice_id" => $id,"qb_invoice_id" => $qb_id,"display_number" => $display_number);
        return response()->json(['data'=>$data]);
    }

    public function paymentInvoiceCash()
    {
        $post = Input::all();

        $qb_data = $this->common->GetTableRecords('invoice',array('id' => $post['invoice_id']),array());
        $qb_id = $qb_data[0]->qb_id;
        $order_id = $qb_data[0]->order_id;
        if(isset($post['company_id'])){
          $company_id=$post['company_id'];
        }

        if(isset($post['invoice_id'])){

          if(isset($post['amount'])){
              $amount=round($post['amount'],2);
              $orderData = array('qb_id' => $qb_id,'order_id' => $order_id,'payment_amount' => $amount,'payment_date' => date('Y-m-d'), 'payment_method' => 'Cash','authorized_TransId' => '','authorized_AuthCode' => '','qb_payment_id' => '', 'qb_web_reference' => '');

              $id = $this->common->InsertRecords('payment_history',$orderData);
          }

          $pmt_data = $this->common->GetTableRecords('payment_history',array('order_id' => $order_id, 'is_delete' => '1'),array());

          if(count($pmt_data)>0){
              $retArray = DB::table('payment_history as p')
              ->select(DB::raw('SUM(p.payment_amount) as totalAmount'), 'o.grand_total')
              ->leftJoin('orders as o','o.id','=',"p.order_id")
              ->where('p.order_id','=',$order_id)
              ->where('p.is_delete','=',1)
              ->get();

              $balance_due = $retArray[0]->grand_total - $retArray[0]->totalAmount;
              $balance_due = round($balance_due, 2);
              $totalAmount = round($retArray[0]->totalAmount, 2);

              if($retArray[0]->grand_total > $retArray[0]->totalAmount){
                  $amt=array('is_paid' => '0');
              }else{
                  $paid = $this->common->GetTableRecords('misc_type',array('company_id' => $company_id, 'slug'=>568),array(),0,0,'id');
                  $paid_id=$paid[0]->id;
                  $amt=array('is_paid' => '1', 'approval_id' => $paid_id);
              }
          }else{
              $amt_data = $this->common->GetTableRecords('orders',array('id' => $order_id),array());
              $balance_due = round($amt_data[0]->grand_total,2);
              $totalAmount = "0.00";
          }
          $amt['total_payments'] = $totalAmount;
          $amt['balance_due'] = $balance_due;

          $this->common->UpdateTableRecords('orders',array('id' => $order_id),$amt);

          $data = array("success"=>1,'amt' =>$amt);
          return response()->json(['data'=>$data]);
      }else{
          $data = array("success"=>0,'message' =>"Payment could not be made.");
          return response()->json(['data'=>$data]);
      }
    }

    public function paymentLinkToPay(){
      $post = Input::all();

      $retArray = DB::table('invoice as p')
            ->select('p.order_id', 'o.balance_due', 'p.payment_terms')
            ->leftJoin('orders as o','o.id','=',"p.order_id")
            ->where('p.id','=',$post['invoice_id'])
            ->get();

        $pmtTerm=$retArray[0]->payment_terms;

        $date = date_create();
        //echo date_timestamp_get($date);
        $length = 25;
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $session_link = substr( str_shuffle( $chars ), 0, $length ).date_timestamp_get($date);

        $orderData = array('order_id' => $retArray[0]->order_id, 'session_link' => $session_link);

        $id = $this->common->InsertRecords('link_to_pay',$orderData);

        $session_link="http://".$_SERVER['SERVER_NAME']."/api/public/invoice/linktopay/".$session_link;
        
        $data = array("success"=>1,'session_link' =>$session_link);

        if($pmtTerm==1){
            $date = date_create();
            $session_another_link = substr( str_shuffle( $chars ), 0, $length ).date_timestamp_get($date);

            $orderData = array('order_id' => $retArray[0]->order_id, 'session_link' => $session_another_link);

            $id = $this->common->InsertRecords('link_to_pay',$orderData);

            $session_another_link="http://".$_SERVER['SERVER_NAME']."/api/public/invoice/linktopay/".$session_another_link;
            $data['session_another_link'] = $session_another_link;
        }
        return response()->json(['data'=>$data]);
    }


/** 
 * @SWG\Definition(
 *      definition="allClient",
 *      type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/GetAllClientsLowerCase",
 *  summary = "Get All Client",
 *  tags={"Order"},
 *  description = "Get All Client",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Get All Client",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/allClient")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Get All Client"),
 *  @SWG\Response(response="default", description="Get All Client"),
 * )
 */

    public function GetAllClientsLowerCase(){
       $post = Input::all();


      $result = $this->order->GetAllClientsLowerCase($post);
      
        $data = array("success"=>1,"message"=>"Success",'records' => $result);
        return response()->json(['data'=>$data]);
    }


    public function updateInvoicePayment()
    {
        $post = Input::all();


         if($post['payment'] == '15') {
            $setDate  = date('Y-m-d', strtotime("+15 days"));

         } else if($post['payment'] == '30') {
            $setDate  = date('Y-m-d', strtotime("+30 days"));

         } else {
           $setDate  = date('Y-m-d');
         }

          $ack= $this->common->GetTableRecords('misc_type',array('company_id' => $post['company_id'], 'slug'=>138),array(),0,0,'id');
          $ack_id=$ack[0]->id;

          $this->common->UpdateTableRecords('orders',array('id' => $post['order_id']),array('approval_id' => $ack_id));


         
        $this->common->UpdateTableRecords('invoice',array('id' => $post['invoice_id']),array('payment_due_date' => $setDate,'payment_terms' => $post['payment']));

        $data = array("success"=>1,"message"=>UPDATE_RECORD,"invoice_id" => $post['invoice_id']);
        return response()->json(['data'=>$data]);
    }




/** 
 * @SWG\Definition(
 *      definition="allClientAddre3ss",
 *      type="object",
 *          required={"client_id"},
 *          @SWG\Property(
 *          property="client_id",
 *          type="integer",
 *          )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/GetAllClientsAddress",
 *  summary = "Get All Client Address",
 *  tags={"Order"},
 *  description = "Get All Client Address",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Get All Client Address",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/allClientAddre3ss")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Get All Client Address"),
 *  @SWG\Response(response="default", description="Get All Client Address"),
 * )
 */

     public function GetAllClientsAddress(){
       $post = Input::all();
       

      $result = $this->order->GetAllClientsAddress($post);


       if (count($result) > 0) 
        {
            $data = array("success"=>1,"message"=>"Success",'records' => $result);
        } 
        else 
        {
            $data = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }

        
        return response()->json(['data'=>$data]);
    }

    /** 
 * @SWG\Definition(
 *      definition="allOrderAddress",
 *      type="object",
 *     
 *     
 *          required={"order_id"},
 *          @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *          )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/allOrderAddress",
 *  summary = "All Order Address",
 *  tags={"Order"},
 *  description = "All Order Address",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="All Order Address",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/allOrderAddress")
 *  ),
*      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="All Order Address"),
 *  @SWG\Response(response="default", description="All Order Address"),
 * )
 */
     public function allOrderAddress(){
       $post = Input::all();
       

      $result = $this->order->allOrderAddress($post);


       if (count($result) > 0) 
        {
            $data = array("success"=>1,"message"=>"Success",'records' => $result);
        } 
        else 
        {
            $data = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }

        
        return response()->json(['data'=>$data]);
    }


    public function reOrder()
    {
        $post = Input::all();
        
        $order_data = $this->common->GetTableRecords('orders',array('id' => $post['order_id']),array());
        unset($order_data[0]->id);
        $order_data[0]->display_number = $this->common->getDisplayNumber('orders',$post['company_id'],'company_id','id');
        $insert_arr = json_decode(json_encode($order_data[0]),true);
        $order_id = $this->common->InsertRecords('orders',$insert_arr);

        $address_data = $this->common->GetTableRecords('order_shipping_address_mapping',array('order_id' => $post['order_id']),array());

        if(!empty($address_data))
        {
            foreach ($address_data as $address) {
                
                unset($address->id);
                $address->order_id = $order_id;
                $insert_address = json_decode(json_encode($address),true);
                $this->common->InsertRecords('order_shipping_address_mapping',$insert_address);
            }
        }
        

        $order_design = $this->common->GetTableRecords('order_design',array('order_id' => $post['order_id'], 'is_delete' => '1'),array());

        if(!empty($order_design))
        {
            foreach ($order_design as $design) {
                
                $old_design_id = $design->id;
                unset($design->id);
                $design->display_number = $this->common->getDisplayNumber('order_design',$post['company_id'],'company_id','id');
                $design->order_id = $order_id;
                $insert_order_design = json_decode(json_encode($design),true);
                $design_id = $this->common->InsertRecords('order_design',$insert_order_design);

                $design_product = $this->common->GetTableRecords('design_product',array('design_id' => $old_design_id,'is_delete' => '1'),array());

                if(!empty($design_product))
                {
                    foreach ($design_product as $product) {
                        
                        $old_design_product_id = $product->id;
                        unset($product->id);
                        $product->design_id = $design_id;
                        $insert_design_product = json_decode(json_encode($product),true);
                        $design_product_id = $this->common->InsertRecords('design_product',$insert_design_product);

                        $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('design_product_id' => $old_design_product_id,'is_delete' => '1'),array());

                        if(!empty($purchase_detail))
                        {
                            foreach ($purchase_detail as $purchase) {

                                unset($purchase->id);
                                $purchase->design_product_id = $design_product_id;
                                $purchase->design_id = $design_id;
                                $insert_size = json_decode(json_encode($purchase),true);
                                $purchase_detail_id = $this->common->InsertRecords('purchase_detail',$insert_size);
                            }
                        }

                        $packing_item = $this->common->GetTableRecords('order_item_mapping',array('order_id' => $post['order_id'],'design_id' => $old_design_id,'product_id' => $product->product_id),array());

                        if(!empty($packing_item))
                        {
                            foreach ($packing_item as $pack) {
                                $pack->order_id = $order_id;
                                $pack->design_id = $design_id;
                                $packing = json_decode(json_encode($pack),true);
                                $order_item_mapping_id = $this->common->InsertRecords('order_item_mapping',$packing);
                            }
                        }
                    }
                }

                $position_data = $this->common->GetTableRecords('order_design_position',array('design_id' => $old_design_id),array());

                if(!empty($position_data))
                {
                    foreach ($position_data as $position) {
                        
                        unset($position->id);
                        $position->design_id = $design_id;
                        $insert_design_position = json_decode(json_encode($position),true);
                        $design_position_id = $this->common->InsertRecords('order_design_position',$insert_design_position);
                    }
                }
            }
        }
        $data = array("success"=>1,"message"=>"Order created successfully. Please wait we are redirecting to you...","display_number"=>$order_data[0]->display_number);
        return response()->json(['data'=>$data]);
    }
}

