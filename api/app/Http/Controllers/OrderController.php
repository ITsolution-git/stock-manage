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
 *      definition="OrderList",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"client_id"},
 *          @SWG\Property(
 *          property="client_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="sales_id",
 *          type="integer",
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
 *     @SWG\Schema(ref="#/definitions/OrderList")
 *  ),
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
            $post['sorts']['sortBy'] = 'order.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'order.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->order->getOrderdata($post);
        $getAllDesigndata = $this->order->getAllDesigndata();

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'order.id', 'name' => 'Order ID'),
                        1=>array('key' => 'order.name', 'name' => 'Job Name'),
                        2=>array('key' => 'client.client_company', 'name' => 'Company'),
                        3=>array('key' => 'order.approval_id', 'name' => 'Approval'),
                        4=>array('key' => 'order.created_date', 'name' => 'Date Created'),
                        5=>array('key' => 'null', 'name' => 'Sales Rep', 'sortable' => false),
                        6=>array('key' => 'order.date_shipped', 'name' => 'Ship Date'),
                        7=>array('key' => 'null', 'name' => 'Operations', 'sortable' => false)
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
 *      definition="OrderDetail",
 *      type="object",
 *      required={"company_id", "id"},
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      )
 * )
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
 *     @SWG\Schema(ref="#/definitions/OrderDetail")
 *  ),
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
        $locations = $this->common->GetTableRecords('client_distaddress',array('client_id' => $result['order'][0]->client_id),array());
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


        //$order_items = $this->order->getOrderItemById($result['order'][0]->price_id);

        /*if(!empty($order_items))
        {
            //$items = $this->order->getItemsByOrder($data['id']);
            
            foreach ($order_items as $order_item)
            {
                $i = 0;
                foreach ($items as $item)
                {
                    if($item->item_id == $order_item->id)
                    {
                        $i = 1;
                    }
                }
                
                if($i == 1)
                {
                    $order_item->selected = '1';
                    $result['order_item'][] = $order_item;
                }
                else
                {
                    $order_item->selected = '0';
                    $result['order_item'][] = $order_item;
                }
            }
        }
        else
        {
            $result['order_item'] = array();
        }*/

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
 *      definition="OrderPositionDetail",
 *      type="object",
 *      required={"company_id", "id"},
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/getOrderPositionDetail",
 *  summary = "Order Position Tab Detail",
 *  tags={"Order"},
 *  description = "Order Position Tab Detail",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Order Position Tab Detail",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/OrderPositionDetail")
 *  ),
 *  @SWG\Response(response=200, description="Order Position Tab Detail"),
 *  @SWG\Response(response="default", description="Order Position Tab Detail"),
 * )
 */

    public function getOrderPositionDetail()
    {
        $data = Input::all();
        $result = $this->order->getOrderPositionDetail($data);
        
        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'order_position' => $result['order_position']
                            );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'order_position' => $result['order_position']
                            );
        }
        return response()->json(["data" => $response]);
    }

    /**
    * Insert record for any single table.
    * @params Table name, Post array
    * @return json data
    */
     public function insertPositions()
     {
        $post = Input::all();

        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['table']) && !empty($post['data']))
        {
            $result = $this->order->insertPositions($post['table'],$post['data']);
            $id = $result;
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        
        $data = array("success"=>$success,"message"=>$message,"id"=>$id);
        return response()->json(['data'=>$data]);
     }
    

    /**
    * UPDATE record for any single table.
    * @params Table name, Condition array, Post array
    * @return json data
    */
     public function updatePositions()
     {
        $post = Input::all();
        
        if($post['column_name'] == 'position_id') {
            $result = $this->order->checkDuplicatePositions($post['design_id'],$post['data']['position_id']);

            $screen_set = $post['order_id'].'_'.$post['position'].'_'.$post['design_id'];

            if($result == '1' ) {
                $data = array("success"=>2,"message"=>"Duplicate");
                 return response()->json(['data'=>$data]);
            }
        }

        //$positionData = $this->common->GetTableRecords('order_design_position',array('design_id' => $data['design_id']),array());

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $date_field = (empty($post['date_field']))? '':$post['date_field']; 

          if($post['column_name'] == 'color_stitch_count') {
            $post['data']['screen_fees_qnty'] = $post['data']['color_stitch_count'];
          }  

         
          
          $result = $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['data'],$date_field);

          if($post['column_name'] == 'position_id') {
             $this->common->UpdateTableRecords('artjob_screensets',array('positions' => $post['cond']['id']),array('screen_set' => $screen_set));
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
     * @SWG\Definition(
     *      definition="distributionDetail",
     *      type="object",
     *      required={"client_id", "order_id", "address_id"},
     *      @SWG\Property(
     *          property="client_id",
     *          type="integer"
     *      ),
     *      @SWG\Property(
     *          property="order_id",
     *          type="integer"
     *      ),
     *      @SWG\Property(
     *          property="address_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/distributionDetail",
     *  summary = "Order Line Tab Detail",
     *  tags={"Order"},
     *  description = "Order Line Tab Detail",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Order Line Tab Detail",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/distributionDetail")
     *  ),
     *  @SWG\Response(response=200, description="Order Line Tab Detail"),
     *  @SWG\Response(response="default", description="Order Line Tab Detail"),
     * )
     */

    public function distributionDetail()
    {
        $data = Input::all();
        $dist_addr = $this->common->GetTableRecords('client_distaddress',array('client_id' => $data['client_id']),array());

        $client_distaddress = array();
        foreach ($dist_addr as $addr) {
            $addr->full_address = $addr->attn ." ". $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $client_distaddress[] = $addr;
        }

        $array = array('order.id' => $data['order_id'],'is_distribute' => '0');
        $order_items = $this->order->getDistributionItems($array);

        if(empty($order_items))
        {
            $this->common->UpdateTableRecords('orders',array('id' => $data['order_id']),array('fully_shipped' => date('Y-m-d')));
        }
        else
        {
            $this->common->UpdateTableRecords('orders',array('id' => $data['order_id']),array('fully_shipped' => ''));        
        }

        if(isset($data['address_id']) && !empty($data['address_id']))
        {
            $array2 = array('order.id' => $data['order_id'],'is_distribute' => '1','ia.address_id' => $data['address_id']);
            $distributed_items = $this->order->getDistributedItems($array2);
        }
        else
        {
            $distributed_items = array();
        }

        $array3 = array('ia.order_id' => $data['order_id']);
        $distributed_address = $this->order->getDistributedAddress($array3);

        $distributed_address2 = array();

        foreach ($distributed_address as $addr) {
            $addr->full_address = $addr->attn ." ". $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $box_arr = $this->common->GetTableRecords('shipping_box',array('shipping_id' => $addr->shipping_id),array());

            if(empty($distributed_items) && $addr->print_on_pdf == '1')
            {
                $array2 = array('order.id' => $data['order_id'],'is_distribute' => '1','ia.address_id' => $addr->id);
                $distributed_items = $this->order->getDistributedItems($array2);
            }

            $actual_total = 0;
            foreach ($box_arr as $row) {
                $actual_total += $row->actual;
            }
            
            $addr->total_box = count($box_arr);
            $addr->actual_total = $actual_total;
            $distributed_address2[] = $addr;
        }


        if (count($client_distaddress) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'dist_addr' => $client_distaddress,
                                'order_items' => $order_items,
                                'distributed_items' => $distributed_items,
                                'distributed_address' => $distributed_address2
                            );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                );
        } 
        
        return response()->json(["data" => $response]);
    }

    public function addToDistribute()
    {
        $post = Input::all();

        if(!isset($post['item_id']))
        {
            $post['data'] = $post;

            $post['cond'] = array('order_id' => $post['order_id'],'address_id' => $post['address_id']);
            $post['notcond'] = array();

            $result = $this->common->GetTableRecords('item_address_mapping',$post['cond'],$post['notcond']);
            if(empty($result))
            {
                $shipping_arr = array('order_id' => $post['order_id'],'address_id' => $post['address_id'],'company_id' => $post['company_id'],'shipping_by' => date('Y-m-d', strtotime("+9 days")),'in_hands_by' => date('Y-m-d', strtotime("+14 days")));
                $shipping_id = $this->common->InsertRecords('shipping',$shipping_arr);

                $insert_arr = array();
                $insert_arr['data'] = array('address_id' => $post['address_id'], 'order_id' => $post['order_id'], 'shipping_id' => $shipping_id);

                $result = $this->common->InsertRecords('item_address_mapping',$insert_arr);
                $id = $result;
            }
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            if(isset($post['items']))
            {
                foreach ($post['items'] as $key => $value) {
                    
                    $insert_arr = array();

                    $arr = $this->common->GetTableRecords('item_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id']),array());
                    $insert_arr['shipping_id'] = $arr[0]->shipping_id;
                    $insert_arr['item_id'] = $value['id'];
                    $insert_arr['address_id'] = $post['address_id'];
                    $insert_arr['order_id'] = $post['order_id'];

                    $result = $this->common->InsertRecords('item_address_mapping',$insert_arr);
                    $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['item_id']),array('is_distribute' => '1'));
                }
            }
            else
            {
                $arr = $this->common->GetTableRecords('item_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id']),array());
                $post['shipping_id'] = $arr[0]->shipping_id;
                $result = $this->common->InsertRecords('item_address_mapping',$post);
                $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['item_id']),array('is_distribute' => '1'));
            }
            
            $success=1;
            $message=UPDATE_RECORD;
        }
        $this->common->UpdateTableRecords('orders',array('id' => $post['order_id']),array('shipping_by' => date('Y-m-d', strtotime("+9 days")),'in_hands_by' => date('Y-m-d', strtotime("+14 days"))));
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="removeFromDistribute",
     *      type="object",
     *      required={"order_id"},
     *      @SWG\Property(
     *          property="order_id",
     *          type="integer"
     *      ),
      *      @SWG\Property(
     *          property="address_id",
     *          type="integer"
     *      ),
      *      @SWG\Property(
     *          property="item_id",
     *          type="integer"
     *      ),
      *      @SWG\Property(
     *          property="shipping_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/removeFromDistribute",
     *  summary = "Distribution Delete",
     *  tags={"Order"},
     *  description = "Distribution Delete",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Distribution Delete",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/removeFromDistribute")
     *  ),
     *  @SWG\Response(response=200, description="Distribution Delete"),
     *  @SWG\Response(response="default", description="Distribution Delete"),
     * )
     */

    public function removeFromDistribute()
    {
        $post = Input::all();

        if(!isset($post['item_id']))
        {
            $item_data = $this->common->GetTableRecords('item_address_mapping',array('order_id' => $post['order_id'],'address_id' => $post['address_id']),array());

            if(!empty($item_data))
            {
                foreach ($item_data as $item) {
                    if($item->item_id > 0)
                    {
                        $this->common->UpdateTableRecords('distribution_detail',array('id' => $item->item_id),array('is_distribute' => '0'));
                    }
                }
            }

            $post['cond'] = array('order_id' => $post['order_id'],'address_id' => $post['address_id']);

            $this->common->DeleteTableRecords('item_address_mapping',$post['cond']);
            $this->common->DeleteTableRecords('shipping',$post['cond']);

            $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['item_id']),array('is_distribute' => '0'));
            
            $post['cond'] = array('order_id' => $post['order_id'],'item_id' => $post['item_id']);
            $this->common->DeleteTableRecords('item_address_mapping',$post['cond']);

            $boxarr = $this->common->GetTableRecords('box_item_mapping',array('item_id' => $post['item_id'],'shipping_id' => $post['shipping_id']),array());

            if(!empty($boxarr))
            {
                foreach ($boxarr as $value) {
                    $this->common->DeleteTableRecords('shipping_box',array('id' => $value->box_id));
                }
            }

            

            $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="updateDistributedQty",
     *      type="object",
     *      required={"id","qty"},
     *      @SWG\Property(
     *          property="id",
     *          type="integer"
     *      ),
      *      @SWG\Property(
     *          property="qty",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/updateDistributedQty",
     *  summary = "Update Distributed Qty",
     *  tags={"Order"},
     *  description = "Update Distributed Qty",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Update Distributed Qty",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/updateDistributedQty")
     *  ),
     *  @SWG\Response(response=200, description="Update Distributed Qty"),
     *  @SWG\Response(response="default", description="Update Distributed Qty"),
     * )
     */

    public function updateDistributedQty()
    {
        $post = Input::all();
        $dist_addr = $this->common->GetTableRecords('distribution_detail',array('id' => $post['id']),array());
        $qty = $dist_addr[0]->qnty - $post['qty'];

        $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['id']),array('qnty' => $post['qty']));

        if($qty > 0)
        {
            $insert_data = array(
                                'orderline_id' => $dist_addr[0]->orderline_id,
                                'order_id' => $dist_addr[0]->order_id,
                                'size' => $dist_addr[0]->size,
                                'qnty' => $qty,
                                'status' => '1',
                                'date' => $dist_addr[0]->date,
                                'is_distribute' => '0'
                                );

            $this->common->InsertRecords('distribution_detail',$insert_data);
        }
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

     /**
   * Save Color size.
   * @return json data
    */
    public function saveColorSize()
    {
        $post = Input::all();
        

         $result = $this->order->getProductDetail($post['product_id']);
         $colors_array = unserialize($result[0]->color_size_data);

         $static_array = array();
        
                
         $static_array[$post['color_id']] = array(array('name' => 'XS','piece_price' => 0),
                                         array('name' => 'S','piece_price' => 0),
                                         array('name' => 'M','piece_price' => 0),
                                         array('name' => 'L','piece_price' => 0),
                                         array('name' => 'XL','piece_price' => 0),
                                         array('name' => '2XL','piece_price' => 0),
                                         array('name' => '3XL','piece_price' => 0)); 
              
     
      if($colors_array){
      $merge_array = $colors_array + $static_array;
      } else {
        $merge_array = $static_array;
      }

      $colors_all_array = serialize($merge_array);
      
       $result = $this->order->updatePriceProduct($colors_all_array,$post['product_id']);
       $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /**
     * @SWG\Get(
     *  path = "/api/public/order/getProductDetailColorSize/{id}",
     *  summary = "Product Color Detail",
     *  tags={"Order"},
     *  description = "Product Color Detail",
     *  @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     description="Product Color Detail",
     *     type="integer",
     *     required=true
     *  ),
     *  @SWG\Response(response=200, description="Product Color Detail"),
     *  @SWG\Response(response="default", description="Product Color Detail"),
     * )
     */
   
    public function getProductDetailColorSize($id)
    {
        $result = $this->order->getProductDetailColorSize($id);
        return $this->return_response($result);
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
    
    /** 
     * @SWG\Definition(
     *      definition="productDetail",
     *      type="object",
     *      required={"id"},
     *      @SWG\Property(
     *          property="id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/productDetail",
     *  summary = "Get Product Detail",
     *  tags={"Order"},
     *  description = "Get Product Detail",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Get Product Detail",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/productDetail")
     *  ),
     *  @SWG\Response(response=200, description="Get Product Detail"),
     *  @SWG\Response(response="default", description="Get Product Detail"),
     * )
     */

    public function productDetail()
    {
        $post = Input::all();

        $result = $this->order->getProductDetail($post['id']);


        $colors = unserialize($result[0]->color_size_data);
      //  print_r($colors);exit;
        $color_all = array();
        $colorData = array();
        $colorName = array();

        if(!empty($colors)) {
            foreach($colors as $key=>$color) {
                $all_data = $this->product->GetColorDeail(array('id'=>$key));
                

                 $colorData[]['id'] = (string)$key;
                 $colorName[$key] = $all_data[0]->name;

            }
         }

        $combine_array['colorData'] = $colorData;
        $combine_array['allData'] = $colors;
        $combine_array['product_data'] = $result;
        $combine_array['color_name'] = $colorName;
        
      return response()->json(['data'=>$combine_array]);
        
        
    }

    public function updatePriceProduct()
    {
        $post = Input::all();
        $size_array_data = serialize($post['temp_array']);
        $result = $this->order->updatePriceProduct($size_array_data,$post['id']);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="deleteColorSize",
     *      type="object",
     *      required={"color_id","product_id"},
     *      @SWG\Property(
     *          property="color_id",
     *          type="integer"
     *      ),
            @SWG\Property(
     *          property="product_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/deleteColorSize",
     *  summary = "Delete Color Size",
     *  tags={"Order"},
     *  description = "Delete Color Size",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Delete Color Size",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/deleteColorSize")
     *  ),
     *  @SWG\Response(response=200, description="Delete Color Size"),
     *  @SWG\Response(response="default", description="Delete Color Size"),
     * )
     */
    
    public function deleteColorSize()
    {
        $post = Input::all();
        
        $result = $this->order->getProductDetail($post['product_id']);
        $colors_array = unserialize($result[0]->color_size_data);

        unset($colors_array[$post['color_id']]);
        $colors_all_array = serialize($colors_array);
      
       $result = $this->order->updatePriceProduct($colors_all_array,$post['product_id']);
       $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="sendEmail",
     *      type="object",
     *      required={"email","product_id","order_id"},
     *      @SWG\Property(
     *          property="email",
     *          type="string"
     *      ),
      *      @SWG\Property(
     *          property="product_id",
     *          type="integer"
     *      ),
        *    @SWG\Property(
     *          property="order_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/sendEmail",
     *  summary = "Send Email To User",
     *  tags={"Order"},
     *  description = "Send Email To User",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Send Email To User",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/sendEmail")
     *  ),
     *  @SWG\Response(response=200, description="Send Email To User"),
     *  @SWG\Response(response="default", description="Send Email To User"),
     * )
     */

    public function sendEmail() {

        $post = Input::all();
        $email = trim($post['email']);
        $email_array = explode(",",$email);

        $data = app('App\Http\Controllers\InvoiceController')->getInvoiceDetail($post['invoice_id'],$post['company_id'],1);

        $file_path =  FILEUPLOAD.'order_invoice_'.$post['invoice_id'].'.pdf';

        $payment_data = $this->common->GetTableRecords('link_to_pay',array('order_id' => $data['order_data'][0]->id),array(),'ltp_id','desc');

        if(empty($payment_data))
        {
            $payment_link = '';
        }
        else
        {
            $payment_link = SITE_HOST."api/public/invoice/linktopay/".$payment_data[0]->session_link;
        }

        if(!file_exists($file_path))
        {
            PDF::AddPage('P','A4');
            PDF::writeHTML(view('pdf.invoice',$data)->render());
            PDF::Output($file_path,'F');
        }

        foreach ($email_array as $email)
        {
            Mail::send('emails.invoice', ['email'=>$email,'payment_link' => $payment_link], function($message) use ($file_path,$email)
            {
                 $message->to($email)->subject('Invoice PDF');
                 $message->attach($file_path);
            });                
        }

        $response = array('success' => 1, 'message' => 'Email has been sent successfully');
        return response()->json(["data" => $response]);

      /* Mail::send('emails.pdfmail', ['user'=>'hardik Deliwala','email'=>$email_array], function($message) use ($email_array,$post,$attached_url)
        {
             $message->to($email_array)->subject('Order Acknowledgement #'.$post['order_id']);
             $message->attach($attached_url);
        });*/


        /* $to = "hdeliwala@codal.com";
         $subject = "This is subject";
         
         $message = "<b>This is HTML message.</b>";
         $message .= "<h1>This is headline.</h1>";
         
         $header = "From:abc@somedomain.com \r\n";
         $header .= "Cc:afgh@somedomain.com \r\n";
         $header .= "MIME-Version: 1.0\r\n";
         $header .= "Content-type: text/html\r\n";
         
         $retval = mail ($to,$subject,$message,$header);
         
         if( $retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }*/



        //PHPMailer Object
        $mail = new PHPMailer;

        //From email address and name
        $mail->From = "from@yourdomain.com";
        $mail->FromName = "Full Name";

        //To address and name
        $mail->addAddress("hdeliwala@codal.com", "Recepient Name");
        $mail->addAddress("recepient1@example.com"); //Recipient name is optional

        //Address to which recipient will reply
        $mail->addReplyTo("reply@yourdomain.com", "Reply");

        //CC and BCC
        $mail->addCC("cc@example.com");
        $mail->addBCC("bcc@example.com");

        //Send HTML or Plain Text email
        $mail->isHTML(true);

        $mail->Subject = "Subject Text";
        $mail->Body = "<i>Mail body in HTML</i>";
        $mail->AltBody = "This is the plain text version of the email content";

        if(!$mail->send()) 
        {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        else 
        {
            echo "Message has been sent successfully";
        }

        $response = array('success' => 1, 'message' => MAIL_SEND);
          
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
     *      definition="orderImageDetail",
     *      type="object",
     *      required={"id","company_id"},
       *     @SWG\Property(
     *          property="id",
     *          type="integer"
     *      ),
       *     @SWG\Property(
     *          property="company_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/orderImageDetail",
     *  summary = "Get Order Image Detail",
     *  tags={"Order"},
     *  description = "Get Order Image Detail",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Get Order Image Detail",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/orderImageDetail")
     *  ),
     *  @SWG\Response(response=200, description="Get Order Image Detail"),
     *  @SWG\Response(response="default", description="Get Order Image Detail"),
     * )
     */

    
    public function orderImageDetail() {
 
        $data = Input::all();

        $result = $this->order->orderImageDetail($data);

         if (count($result) > 0) {

        $result[0]->first_url_photo = UPLOAD_PATH.$data['company_id'].'/'.'order/'.$result[0]->id.'/'.$result[0]->first_logo;
        $result[0]->second_url_photo = UPLOAD_PATH.$data['company_id'].'/'.'order/'.$result[0]->id.'/'.$result[0]->second_logo;
        $result[0]->third_url_photo = UPLOAD_PATH.$data['company_id'].'/'.'order/'.$result[0]->id.'/'.$result[0]->third_logo;
        $result[0]->fourth_url_photo = UPLOAD_PATH.$data['company_id'].'/'.'order/'.$result[0]->id.'/'.$result[0]->fourth_logo;
        }

        
        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result[0]
                                
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => '');
        } 
        return response()->json(['data'=>$response]);
    }


/** 
 * @SWG\Definition(
 *      definition="addOrder",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="orderData",
 *          type="object",
 *          required={"client_id"},
 *          @SWG\Property(
 *          property="client_id",
 *          type="integer",
 *         ),
 *           @SWG\Property(
 *          property="job_name",
 *          type="string",
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

        
        if(array_key_exists('sns_shipping', $post['orderData'])) {
        $post['orderdata']['sns_shipping'] = $post['orderData']['sns_shipping'];
        }


         $post['orderdata']['name'] = $post['orderData']['name'];
         $post['orderdata']['approval_id'] = $estimation_id;
         $post['orderdata']['login_id'] = $post['login_id'];
         $post['orderdata']['company_id'] = $post['company_id'];
         $post['orderdata']['client_id'] = $post['orderData']['client']['client_id'];
         $post['orderdata']['created_date'] = date('Y-m-d');
         $post['orderdata']['updated_date'] = date('Y-m-d');
         $post['orderdata']['account_manager_id'] = $client_data['main']['account_manager'];
         $post['orderdata']['sales_id'] = $client_data['sales']['salesperson'];
         $post['orderdata']['price_id'] = $client_data['sales']['salespricegrid'];
         $post['orderdata']['tax_rate'] = $client_data['tax']['tax_rate'];
         
          $order_id = $this->common->InsertRecords('orders',$post['orderdata']);

           $insert_arr = array();
           $insert_arr['data'] = array('order_id' => $order_id, 'created_date' => date('Y-m-d'), 'updated_date' => date('Y-m-d'));

           $art_id = $this->common->InsertRecords('art',$insert_arr);
           $id = $art_id;

           $data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$order_id);
           return response()->json(['data'=>$data]);

    }


     public function addDesign()
    {
        $post = Input::all();
     
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

    public function designListing() {
 
        $data = Input::all();
        $design_data = array();
      
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

     public function editDesign()
    {
        $post = Input::all();
       
       
        unset($post['designData']['order_number']);
        unset($post['designData']['is_complete']);
      
        if($post['designData']['hands_date'] != '')
        {
            $post['designData']['hands_date'] = date("Y-m-d", strtotime($post['designData']['hands_date']));
        }
        if($post['designData']['shipping_date'] != '')
        {
            $post['designData']['shipping_date'] = date("Y-m-d", strtotime($post['designData']['shipping_date']));
        }
        if($post['designData']['start_date'] != '')
        {
            $post['designData']['start_date'] = date("Y-m-d", strtotime($post['designData']['start_date']));
        }

        unset($post['designData']['price_id']);


       $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['designData']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
            return response()->json(['data'=>$data]);

       $data = array("success"=>1,"message"=>INSERT_RECORD);
       return response()->json(['data'=>$data]);

    }

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

        $screen_print_charge = 0;
        $screen_print_charge = 0;
        $embroidery_charge = 0;
        $direct_to_garment_charge = 0;
        $markup_default = 0;

        foreach ($result['order_design_position'] as $position) {

            $screen_print_charge = 0;
            $screen_print_charge = 0;
            $embroidery_charge = 0;
            $direct_to_garment_charge = 0;
            $markup_default = 0;

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
                        if($position_qty <= $primary->range_low)
                        {
                            if(isset($primary->$price_field))
                            {
                                $screen_print_charge = $primary->$price_field;
                                break;
                            }
                        }
                    }
                }
                elseif($miscData['placement_type'][$placement_type_id]->slug == 44)
                {
                    foreach($price_screen_secondary as $secondary)
                    {
                        $price_field = 'pricing_'.$color_stitch_count.'c';
                        if($position_qty <= $secondary->range_low)
                        {
                            if(isset($secondary->$price_field))
                            {
                                $screen_print_charge = $secondary->$price_field;
                                break;
                            }
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
                            $switch_id = $embroidery.id;
                            $embroidery_field = 'pricing_8c';
                        }
                        if($color_stitch_count >= $embroidery->range_low_9 && $color_stitch_count <= $embroidery->range_high_9)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_9c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_10 && $color_stitch_count <= $embroidery->range_high_10)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_10c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_11 && $color_stitch_count <= $embroidery->range_high_11)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_11c';
                        }
                        elseif($color_stitch_count >= $embroidery->range_low_12 && $color_stitch_count <= $embroidery->range_high_12)
                        {
                            $switch_id = $embroidery->id;
                            $embroidery_field = 'pricing_12c';
                        }
                    }

                    if($switch_id > 0)
                    {
                        $price_screen_embroidery = $this->common->GetTableRecords('price_screen_embroidery',array('embroidery_switch_id' => $switch_id),array());

                        foreach ($price_screen_embroidery as $embroidery2)
                        {
                            if($position_qty <= $embroidery2->range_low)
                            {
                                $embroidery_charge = $embroidery2->$embroidery_field;
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
                          
                          if($position_qty <= $garment->range_low)
                          {
                              $direct_to_garment_charge = $garment->$garment_field;
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

            $position->screen_print_charge = $screen_print_charge;
            $position->embroidery_charge = $embroidery_charge;
            $position->direct_to_garment_charge = $direct_to_garment_charge;
            $position->markup_default = $markup_default;

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

     public function editOrder()
    {
        $post = Input::all();

       
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


       $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['orderDataDetail']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
            return response()->json(['data'=>$data]);

       $data = array("success"=>1,"message"=>INSERT_RECORD);
       return response()->json(['data'=>$data]);

    }

    public function orderDetailInfo() {
 
        $data = Input::all();
        $result = $this->order->orderDetailInfo($data);
        $price_grid = $this->common->GetTableRecords('price_grid',array('is_delete' => '1','status' => '1','company_id' =>$result['order'][0]->company_id),array());
        $staff = $this->common->getStaffList($result['order'][0]->company_id);
        $brandCo = $this->common->getBrandCordinator($result['order'][0]->company_id);

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
                                'brandCo' => $brandCo
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['order'],
                                'price_grid' => $price_grid,
                                'staff' => $staff,
                                'brandCo' => $brandCo);
        } 
        return response()->json(["data" => $response]);

    }

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

        if($post['productData']['override'] == 0 || $post['productData']['override'] == '')
        {
            $return = app('App\Http\Controllers\ProductController')->orderCalculation($post['design_id']);
        }

        $data = array("success"=>1);
        return response()->json(["data" => $data]);
    }

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
            $post['sns_shipping'] = '1';
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
            "zip"=> $result_company[0]->prime_address_zip,
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


            
            
            $this->common->UpdateTableRecords('orders',array('id' => $post['id']),array('order_number' => $all_data[0]->orderNumber,'order_sns_status' => $all_data[0]->orderStatus));
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
          $result_quickbook_invoice = app('App\Http\Controllers\QuickBookController')->addInvoice($result_order,$result_charges,$result_quickbook,$result_qbProductId,$post['invoice_id'],$other_charges,$price_grid,$post['payment']);
          
          
          if($result_quickbook_invoice == '1') {
            $data_record = array("success"=>1,"message"=>"Invoice Generated Successfully");
            return response()->json(["data" => $data_record]);
          } else {
             $data_record = array("success"=>0,"message"=>"Please connect Quickbook");
            return response()->json(["data" => $data_record]);
          }


        } else {
          
          $result_quickbook_invoice = app('App\Http\Controllers\QuickBookController')->addInvoice($result_order,$result_charges,$result['main']['qid'],$result_qbProductId,$post['invoice_id'],$other_charges,$price_grid,$post['payment']);
          
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
 *      definition="addOrder",
 *      type="object",
 *     
 *
 *      @SWG\Property(
 *          property="orderData",
 *          type="object",
 *          required={"client_id"},
 *          @SWG\Property(
 *          property="client_id",
 *          type="integer",
 *         ),
 *           @SWG\Property(
 *          property="job_name",
 *          type="string",
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
 *  @SWG\Response(response=200, description="Add Order"),
 *  @SWG\Response(response="default", description="Add Order"),
 * )
 */
    public function addPosition()
    {
        $post = Input::all();

        $result = $this->order->checkDuplicatePositions($post['design_id'],$post['positionData']['position_id']);
        
        if($result == '1' ) {
            $data = array("success"=>2,"message"=>"Duplicate","id"=>'');
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
            $post['artdata']['screen_set'] = $post['order_id'].'_'.$post['position'].'_'.$post['design_id'];

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

         } else if($post['payment'] == '30') {
            $setDate  = date('Y-m-d', strtotime("+30 days"));

         } else {
           $setDate  = date('Y-m-d');
         }
         
        $orderData = array('order_id' => $post['order_id'], 'created_date' => date('Y-m-d'), 'payment_due_date' => $setDate);
        $id = $this->common->InsertRecords('invoice',$orderData);

        $qb_data = $this->common->GetTableRecords('invoice',array('id' => $id),array());
        $qb_id = $qb_data[0]->qb_id;

        $data = array("success"=>1,"message"=>INSERT_RECORD,"invoice_id" => $id,"qb_invoice_id" => $qb_id);
        return response()->json(['data'=>$data]);
    }

    public function paymentInvoiceCash()
    {
        $post = Input::all();

        $qb_data = $this->common->GetTableRecords('invoice',array('id' => $post['invoice_id']),array());
        $qb_id = $qb_data[0]->qb_id;
        $order_id = $qb_data[0]->order_id;

        
        if(isset($post['amount'])){
          $orderData = array('qb_id' => $qb_id,'order_id' => $order_id,'payment_amount' => $post['amount'],'payment_date' => date('Y-m-d'), 'payment_method' => 'Cash','authorized_TransId' => '','authorized_AuthCode' => '','qb_payment_id' => '', 'qb_web_reference' => '');

          $id = $this->common->InsertRecords('payment_history',$orderData);
        }

        $retArray = DB::table('payment_history as p')
            ->select(DB::raw('SUM(p.payment_amount) as totalAmount'), 'o.grand_total')
            ->leftJoin('orders as o','o.id','=',"p.order_id")
            ->where('p.order_id','=',$order_id)
            ->where('p.is_delete','=',1)
            ->get();

        $balance_due = $retArray[0]->grand_total - $retArray[0]->totalAmount;
        $amt=array('total_payments' => round($retArray[0]->totalAmount, 2), 'balance_due' => round($balance_due, 2));

        $this->common->UpdateTableRecords('orders',array('id' => $order_id),$amt);

        $data = array("success"=>1,'amt' =>$amt);
        return response()->json(['data'=>$data]);
    }

    public function paymentLinkToPay(){
      $post = Input::all();

      $retArray = DB::table('invoice as p')
            ->select('p.order_id', 'o.balance_due')
            ->leftJoin('orders as o','o.id','=',"p.order_id")
            ->where('p.id','=',$post['invoice_id'])
            ->get();
        
        $date = date_create();
        //echo date_timestamp_get($date);
        $length = 25;
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $session_link = substr( str_shuffle( $chars ), 0, $length ).date_timestamp_get($date);

        $orderData = array('order_id' => $retArray[0]->order_id,'balance_amount' => $retArray[0]->balance_due , 'session_link' => $session_link);

        $id = $this->common->InsertRecords('link_to_pay',$orderData);
        

        //$session_link="http://localhost/stokkup/link_to_pay.php?link=".$session_link;
        $session_link="http://".$_SERVER['SERVER_NAME']."/link_to_pay.php?link=".$session_link;
        

        $data = array("success"=>1,'session_link' =>$session_link);
        return response()->json(['data'=>$data]);
    }

    public function GetAllClientsLowerCase(){
       $post = Input::all();


      $result = $this->order->GetAllClientsLowerCase($post);
      
        $data = array("success"=>1,"message"=>"Success",'records' => $result);
        return response()->json(['data'=>$data]);
    }

}