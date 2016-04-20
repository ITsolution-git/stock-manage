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
use App\Purchase;
use App\Product;
use App\Client;
use DB;
use App;
use Request;
use Response;
//use Barryvdh\DomPDF\Facade as PDF;
use PDF;


class OrderController extends Controller { 

    public function __construct(Order $order,Common $common,Purchase $purchase,Product $product,Client $client)
    {
        $this->order = $order;
        $this->purchase = $purchase;
        $this->common = $common;
        $this->product = $product;
        $this->client = $client;
        DB::enableQueryLog();
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

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];

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
        $records = $result['allData'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'order.id', 'name' => 'Order ID'),
                        1=>array('key' => 'order.name', 'name' => 'Job Name'),
                        2=>array('key' => 'client.client_company', 'name' => 'Company'),
                        3=>array('key' => 'order.approval_id', 'name' => 'Approval'),
                        4=>array('key' => 'order.created_date', 'name' => 'Date Created'),
                        5=>array('key' => 'null', 'name' => 'Sales Rep', 'sortable' => false),
                        6=>array('key' => 'order.date_shipped', 'name' => 'Ship Date'),
                        7=>array('key' => 'null', 'name' => 'Opeations', 'sortable' => false)
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order);
        return $this->return_response($data);
    }
    /**
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
    public function DeleteOrder()
    {
        $post = Input::all();


        if(!empty($post[0]))
        {
            $getData = $this->order->deleteOrder($post[0]);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
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
        $result = $this->order->orderDetail($data);
        $order_items = $this->order->getOrderItemById($result['order'][0]->price_id);

        if(!empty($order_items))
        {
            $items = $this->order->getItemsByOrder($data['id']);

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
        }

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['order'],
                                'order_item' => $result['order_item']
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['order'],
                                'order_item' => $result['order_item']);
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
     * @SWG\Definition(
     *      definition="OrderLineDetail",
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
     *  path = "/api/public/order/getOrderLineDetail",
     *  summary = "Order Line Tab Detail",
     *  tags={"Order"},
     *  description = "Order Line Tab Detail",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Order Line Tab Detail",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/OrderLineDetail")
     *  ),
     *  @SWG\Response(response=200, description="Order Line Tab Detail"),
     *  @SWG\Response(response="default", description="Order Line Tab Detail"),
     * )
     */

    public function getOrderLineDetail()
    {
        $data = Input::all();
        $result = $this->order->getOrderLineDetail($data);

        if(!empty($result['order_line_data']))
        {
            $sum = 0;
            foreach($result['order_line_data'] as $row)
            {
                $row->orderline_id = $row->id;
                $row->products = array();
                $row->colors = array();
                $row->productData = array();

                if($row->vendor_id > 0)
                {

                    $oldata = array();
                    $oldata['where'] = array('vendor_id' => $row->vendor_id);
                    $oldata['fields'] = array();
                    $row->products = $this->product->getVendorProducts($oldata);
                    $row->productData[]['id'] = (string)$row->product_id;
                }
                if($row->product_id > 0)
                {
                    $colors = $this->product->GetProductColor(array('id'=>$row->product_id));
                    $colors = unserialize($colors[0]->color_size_data);
                    if(!empty($colors))
                    {
                        foreach($colors as $key=>$color) {
                            $color_data = $this->product->GetColorDeail(array('id'=>$key));
                            $row->colors[] = $color_data[0];
                        }
                    }
                }

                $order_line_items = $this->order->getOrderLineItemById($row->id);
                $count = 1;
                $order_line = array();
                foreach ($order_line_items as $line) {
                 
                    $line->number = $count;
                    $order_line[] = $line;
                    $count++;
                }
                $row->items = $order_line;

                $result['order_line'][] = $row;
            }
        }
        else
        {
            $result['order_line'] = array();
        }

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'order_line' => $result['order_line'],
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'order_line' => $result['order_line']
                            );

        }

        return response()->json(["data" => $response]);
    }


 /**
 * @SWG\Get(
 *  path = "/api/public/order/getOrderNoteDetails/{id}",
 *  summary = "Order Notes Data",
 *  tags={"Order"},
 *  description = "Order Notes Data",
 *  @SWG\Parameter(
 *     in="path",
 *     name="id",
 *     description="Order Notes Data",
 *     type="integer",
 *     required=true
 *  ),
 *  @SWG\Response(response=200, description="Order Notes Data"),
 *  @SWG\Response(response="default", description="Order Notes Data"),
 * )
 */

    public function getOrderNoteDetails($id)
    {

        $result = $this->order->getOrderNoteDetails($id);
        return $this->return_response($result);
        
    }

    /**
    * Get Client Details by ID
    * @params order_id
    * @return json data
    */
   /* public function getOrderDetailById($id)
    {
        $result = $this->order->getOrderDetailById($id);
        return $this->return_response($result);
    }
*/


/*
    public function updateOrderNotes()
    {
        $post = Input::all();
        $result = $this->order->updateOrderNotes($post['data'][0]);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }*/

    /**
    * Delete order note tab record.
    * @params note_id
    * @return json data
    */


/**
 * @SWG\Get(
 *  path = "/api/public/order/deleteOrderNotes/{id}",
 *  summary = "Order Notes Delete",
 *  tags={"Order"},
 *  description = "Order Notes Delete",
 *  @SWG\Parameter(
 *     in="path",
 *     name="id",
 *     description="Order Notes Delete",
 *     type="integer",
 *     required=true
 *  ),
 *  @SWG\Response(response=200, description="Order Notes Delete"),
 *  @SWG\Response(response="default", description="Order Notes Delete"),
 * )
 */


    public function deleteOrderNotes($id)
    {
        $result = $this->order->deleteOrderNotes($id);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }


   /**
   * Save Order notes.
   * @return json data
    */


   /** 
 * @SWG\Definition(
 *      definition="saveOrderNotes  ",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="data",
 *          type="object",
 *          required={"order_notes"},
 *          @SWG\Property(
 *          property="order_notes",
 *          type="string",
 *         ),
 *         @SWG\Property(
 *          property="order_id",
 *          type="integer",
 *         ),
 *         @SWG\Property(
 *          property="user_id",
 *          type="integer",
 *         )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/order/saveOrderNotes",
 *  summary = "Add Notes in particular order",
 *  tags={"Order"},
 *  description = "Add Notes in particular order",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Add Notes in particular order",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/saveOrderNotes")
 *  ),
 *  @SWG\Response(response=200, description="Add Notes in particular order"),
 *  @SWG\Response(response="default", description="Add Notes in particular order"),
 * )
 */

    public function saveOrderNotes()
    {

        $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');
 
        if(!empty($post['data']['order_id']) && !empty($post['data']['order_notes']))
        {
            $result = $this->order->saveOrderNotes($post['data']);
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $message = MISSING_PARAMS.", id";
            $success = 0;
        }
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }

    /**
    * Save Orderline order.
    * @return json data
    */
    public function orderLineadd()
    {
        $post = Input::all();

        $post['data']['created_date']=date('Y-m-d');
       
        $result = $this->order->saveOrderLineData($post['data']);

        $shipping_id = $this->common->InsertRecords('artjob_artworkproof',array('orderline_id' => $result));
        $message = INSERT_RECORD;
        $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }


    /**
   * Update Orderline order.
   * @return json data
    */
    public function orderLineupdate()
    {
        $post = Input::all();

        if(!isset($post['data']['avg_garment_cost']))
        {
            $post['data']['avg_garment_cost'] = '0';
        }
        if(!isset($post['data']['avg_garment_price']))
        {
            $post['data']['avg_garment_price'] = '0';
        }
        if(!isset($post['data']['print_charges']))
        {
            $post['data']['print_charges'] = '0';
        }
        if(!isset($post['data']['peritem']))
        {
            $post['data']['peritem'] = '0';
        }
        if(!isset($post['data']['per_line_total']))
        {
            $post['data']['per_line_total'] = '0';
        }

        $post['data']['order_id'] = $post['data']['order_id'];
        $post['data']['size_group_id'] = $post['data']['size_group_id'];
        $post['data']['product_id'] = $post['data']['product_id'];
        $post['data']['vendor_id'] = $post['data']['vendor_id'];
        $post['data']['color_id'] = $post['data']['color_id'];
        $post['data']['qnty'] = $post['data']['qnty'];
        $post['data']['avg_garment_cost'] = round($post['data']['avg_garment_cost'],2);
        $post['data']['avg_garment_price'] = round($post['data']['avg_garment_price'],2);
        $post['data']['print_charges'] = round($post['data']['print_charges'],2);
        $post['data']['markup'] = $post['data']['markup'];
        $post['data']['markup_default'] = $post['data']['markup_default'];
        $post['data']['override'] = $post['data']['override'];
        $post['data']['peritem'] = round($post['data']['peritem'],2);
        $post['data']['os'] = $post['data']['os'];
        $post['data']['per_line_total'] = round($post['data']['per_line_total'],1);
        $post['data']['override_diff'] = $post['data']['override_diff'];
       

/*        if($post['data']['product_name'] != '')
        {
            $product_data = $this->common->GetTableRecords('products',array('name' => $post['data']['product_name']),array());
            $post['data']['product_id'] = $product_data[0]->id;
        }*/

        $post['data']['created_date']=date('Y-m-d');
       
        $result = $this->order->updateOrderLineData($post['data']);
        $message = INSERT_RECORD;
        $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="deleteOrderLine",
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
     *  path = "/api/public/order/deleteOrderLine",
     *  summary = "Order Line Delete",
     *  tags={"Order"},
     *  description = "Order Line Delete",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Order Line Delete",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/deleteOrderLine")
     *  ),
     *  @SWG\Response(response=200, description="Order Line Delete"),
     *  @SWG\Response(response="default", description="Order Line Delete"),
     * )
     */

    public function deleteOrderLine()
    {
        $post = Input::all();
       
        $this->common->DeleteTableRecords('order_orderlines',array('id' => $post['id']));

        $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('orderline_id' => $post['id']),array());

        foreach ($purchase_detail as $row) {
            $this->common->DeleteTableRecords('item_address_mapping',array('item_id' => $row->id));
        }
                
        $data = array("success"=>1,"message"=>DELETE_RECORD);
        return response()->json(['data'=>$data]);
    }    


   /**
   * Save Button Data.
   * @return json data
   */

    public function saveButtonData()
    {
        $post = Input::all();

        $post['created_date']=date('Y-m-d');
        
        $result = $this->order->saveButtonData($post);
        $message = INSERT_RECORD;
        $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
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
//        echo "<pre>"; print_r($post); echo "</pre>"; die;

        if(!empty($post['table']) && !empty($post['data'])  && !empty($post['cond']))
        {
          $result = $this->order->updatePositions($post['table'],$post['cond'],$post['data']);
          $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
     }


/**
    * Save po.
    * @return json data
    */
    public function savePO()
    {
        $post = Input::all();

      
        $post['created_date']=date('Y-m-d');
        $result = $this->order->savePO($post);
        $message = INSERT_RECORD;
        $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }

    /** 
     * @SWG\Definition(
     *      definition="PODetail",
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
     *  path = "/api/public/order/PODetail",
     *  summary = "PO Detail",
     *  tags={"Order"},
     *  description = "PO Detail",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="PO Detail",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/PODetail")
     *  ),
     *  @SWG\Response(response=200, description="PO Detail"),
     *  @SWG\Response(response="default", description="PO Detail"),
     * )
     */

    public function PODetail() {
 
        $data = Input::all();

        $result = $this->order->POorderDetail($data);

       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'order_po_data' => $result['order_po_data']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'order_po_data' => $result['order_po_data']);
        }
        
        return response()->json(["data" => $response]);

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
            @SWG\Property(
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
            @SWG\Property(
     *          property="address_id",
     *          type="integer"
     *      ),
            @SWG\Property(
     *          property="item_id",
     *          type="integer"
     *      ),
            @SWG\Property(
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

    public function updateOrderTask()
    {
        $post = Input::all();

        if(isset($post['data']['task_name']) && $post['data']['task_name'] != '')
        {
            $task_arr = $this->common->GetTableRecords('task',array('task_name' => $post['data']['task_name']),array());

            if(empty($task_arr))
            {
                $task_id = $this->common->InsertRecords('task',array('task_name' => $post['data']['task_name']));
            }
            else
            {
                $task_id = $task_arr[0]->id;
            }
            $post['data']['task_id'] = $task_id;
            unset($post['data']['task_name']);
        }

        if(isset($post['data']['result_name']) && $post['data']['result_name'] != '')
        {
            $result_arr = $this->common->GetTableRecords('result',array('result_name' => $post['data']['result_name']),array());

            if(empty($result_arr))
            {
                $result_id = $this->common->InsertRecords('result',array('result_name' => $post['data']['result_name']));
            }
            else
            {
                $result_id = $result_arr[0]->id;
            }
            $post['data']['result_id'] = $result_id;
            unset($post['data']['result_name']);
        }

        $post['data']['user_id'] = implode(',', $post['data']['user_id']);

        if($post['action'] == 'update')
        {
            $this->common->UpdateTableRecords('order_tasks',$post['cond'],$post['data']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $post['data']['date_added'] = date('Y-m-d');
            $this->common->InsertRecords('order_tasks',$post['data']);
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
            @SWG\Property(
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
     * @SWG\Definition(
     *      definition="duplicatePoData",
     *      type="object",
     *      required={"po_id"},
     *      @SWG\Property(
     *          property="po_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/duplicatePoData",
     *  summary = "Duplicate PO",
     *  tags={"Order"},
     *  description = "Duplicate PO",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Duplicate PO",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/duplicatePoData")
     *  ),
     *  @SWG\Response(response=200, description="Duplicate PO"),
     *  @SWG\Response(response="default", description="Duplicate PO"),
     * )
     */

    public function duplicatePoData() {

       $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');
        $post['data']['po_id']=$post['po_id'];
       
       
        $result = $this->order->poDuplicate($post['data']);
          
        $data = array("success"=>1,"message"=>INSERT_RECORD);
        
        return response()->json(["data" => $data]);
    }

     /** 
     * @SWG\Definition(
     *      definition="getTaskList",
     *      type="object",
     *      required={"order_id"},
     *      @SWG\Property(
     *          property="order_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/getTaskList",
     *  summary = "Get Task List",
     *  tags={"Order"},
     *  description = "Get Task List",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Get Task List",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/getTaskList")
     *  ),
     *  @SWG\Response(response=200, description="Get Task List"),
     *  @SWG\Response(response="default", description="Get Task List"),
     * )
     */

    public function getTaskList()
    {
        $post = Input::all();
        $task_detail = $this->order->getTaskList($post['order_id']);

        if (count($task_detail) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'task_detail' => $task_detail
                            );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'task_detail' => array()
                                );
        } 
        
        return response()->json(["data" => $response]);
    }

    /** 
     * @SWG\Definition(
     *      definition="getTaskDetails",
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
     *  path = "/api/public/order/getTaskDetails",
     *  summary = "Get Task Details",
     *  tags={"Order"},
     *  description = "Get Task Details",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Get Task Details",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/getTaskDetails")
     *  ),
     *  @SWG\Response(response=200, description="Get Task Details"),
     *  @SWG\Response(response="default", description="Get Task Details"),
     * )
     */

    public function getTaskDetails()
    {
        $post = Input::all();
        $users = $this->common->GetTableRecords('users',array(),array('role_id' => '7'));
        $task = $this->common->GetTableRecords('task',array(),array());
        $result = $this->common->GetTableRecords('result',array(),array());

        $task_detail = array();

        if(!empty($post['id']) > 0)
        {
            $task_detail = $this->order->getTaskDetail($post['id']);
            $task_detail[0]->user_id = explode(',', $task_detail[0]->user_id);
        }

        $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'users' => $users,
                                'tasks' => $task,
                                'result' => $result,
                                'task_detail' => $task_detail
                            );
        return response()->json(["data" => $response]);
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

        $all_company['all_company'] = json_decode($_POST['all_company']);
        $client_main_data['client_main_data'] = json_decode($_POST['client_main_data']);
        $staff_list['staff_list'] = json_decode($_POST['staff_list']);
        $company_detail['company_detail'] = json_decode($_POST['company_detail']);
        $embroidery_switch_count['embroidery_switch_count'] = json_decode($_POST['embroidery_switch_count']);
        $price_screen_primary['price_screen_primary'] = json_decode($_POST['price_screen_primary']);
        $price_grid['price_grid'] = json_decode($_POST['price_grid']);
        $total_qty['total_qty'] = json_decode($_POST['total_qty']);
        $order_item['order_item'] = json_decode($_POST['order_item']);
        $order_position['order_position'] = json_decode($_POST['order_position']);
        $order_line['order_line'] = json_decode($_POST['order_line']);
        $order['order'] = json_decode($_POST['order']);

        $order_image['order_image'] = json_decode($_POST['order_image']);

       
        if($company_detail['company_detail'][0]->photo != '') {
            $company_detail['company_detail'][0]->photo = UPLOAD_PATH.$company_detail['company_detail'][0]->id.'/'.'staff/'.$company_detail['company_detail'][0]->id.'/'.$company_detail['company_detail'][0]->photo;
        }

        
        $uploaddirfirst = '';
        $uploaddirsecond = '';
        $uploaddirthird = '';
        $uploaddirfourth = '';

        if($order_image['order_image']->first_logo != '') {
        $uploaddirfirst = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/order/".$order['order']->id.'/'.$order_image['order_image']->first_logo;

        }
        if($order_image['order_image']->second_logo != '') {
            $uploaddirsecond = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/order/".$order['order']->id.'/'.$order_image['order_image']->second_logo;
        }
        if($order_image['order_image']->third_logo != '') {
             $uploaddirthird = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/order/".$order['order']->id.'/'.$order_image['order_image']->third_logo;
        }
        if($order_image['order_image']->fourth_logo != '') {
             $uploaddirfourth = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/order/".$order['order']->id.'/'.$order_image['order_image']->fourth_logo;
        }

       $counter = 0;
       $order_image_pdf = array();
       if (file_exists($uploaddirfirst) && $counter < 2) {
        $counter++;
        $order_image_pdf[] = $order_image['order_image']->first_url_photo;


       } 

       if (file_exists($uploaddirsecond) && $counter < 2) {
          $counter++;
        $order_image_pdf[] = $order_image['order_image']->second_url_photo;
          
       }

       if (file_exists($uploaddirthird) && $counter < 2) {
          $counter++;
        $order_image_pdf[] = $order_image['order_image']->third_url_photo;
           
       }

       if (file_exists($uploaddirfourth) && $counter < 2) {
          $counter++;
        $order_image_pdf[] = $order_image['order_image']->fourth_url_photo;
          
       }

     $order_image_pdf_data['order_image_pdf'] = $order_image_pdf;
     

     
        $array3 = array('ia.order_id' => $order['order']->id);
        $distributed_address['distributed_address'] = $this->order->getDistributedAddress($array3);

        

        $order['order']->created_date = date('m/d/Y',strtotime($order['order']->created_date));
       

        if($order['order']->shipping_by != '' && $order['order']->shipping_by != '0000-00-00'){
           $order['order']->shipping_by = date('m/d/Y',strtotime($order['order']->shipping_by));
        } else {
              $order['order']->shipping_by ='';
        }

        if($order['order']->in_hands_by != '' && $order['order']->in_hands_by != '0000-00-00'){
            $order['order']->in_hands_by = date('m/d/Y',strtotime($order['order']->in_hands_by));
        } else {
            $order['order']->in_hands_by ='';
        }
        $order_misc['order_misc'] = json_decode($_POST['order_misc']);
        $combine_array = array_merge($order_position,$order_line,$order,$order_misc,$order_item,$order_misc,$total_qty,$price_grid,$price_screen_primary,$embroidery_switch_count,$company_detail,$staff_list,$all_company,$client_main_data,$distributed_address,$order_image_pdf_data);
     
        PDF::AddPage('P','A4');
        PDF::writeHTML(view('pdf.order',array('data'=>$combine_array))->render());
     //   PDF::Output('order.pdf');

      $pdf_url = "order-".$order['order']->id.".pdf";         
      $filename = base_path() . "/public/uploads/".$company_detail['company_detail'][0]->id."/pdf/". $pdf_url;
     
      PDF::Output($filename, 'F');
      return Response::download($filename);

    }

    /** 
     * @SWG\Definition(
     *      definition="AssignSize",
     *      type="object",
     *      required={"orderline_id","product_id","color_id","order_id"},
     *      @SWG\Property(
     *          property="orderline_id",
     *          type="integer"
     *      ),
            @SWG\Property(
     *          property="product_id",
     *          type="integer"
     *      ),
            @SWG\Property(
     *          property="color_id",
     *          type="integer"
     *      ),
            @SWG\Property(
     *          property="order_id",
     *          type="integer"
     *      )
     * )
     */

     /**
     * @SWG\Post(
     *  path = "/api/public/order/AssignSize",
     *  summary = "Assign color size to order line",
     *  tags={"Order"},
     *  description = "Assign color size to order line",
     *  @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Assign color size to order line",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/AssignSize")
     *  ),
     *  @SWG\Response(response=200, description="Assign color size to order line"),
     *  @SWG\Response(response="default", description="Assign color size to order line"),
     * )
     */

    public function AssignSize()
    {
        $post = Input::all();
        $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('orderline_id' => $post['orderline_id']),array());
        $sizeData = $this->product->GetProductColor(array('id'=>$post['product_id']));
        $sizeData = unserialize($sizeData[0]->color_size_data);
        $sizeData = $sizeData[$post['color_id']];

        $count = count($sizeData);
        $inner_count = 1;

        $this->common->UpdateTableRecords('purchase_detail',array('orderline_id' => $post['orderline_id']),array('size' => '','price' => '0','is_distribute' => '0','qnty' => '0'));
        $this->common->UpdateTableRecords('distribution_detail',array('orderline_id' => $post['orderline_id']),array('size' => '','price' => '0','is_distribute' => '0','qnty' => '0'));
        $this->common->UpdateTableRecords('order_orderlines',array('id' => $post['orderline_id']),array('qnty' => '0','per_line_total' => '0'));

        foreach ($purchase_detail as $key => $value) {
            
            if($inner_count <= $count)
            {
                $update_data = array('size' => $sizeData[$key]['name'],
                                    'price' => $sizeData[$key]['piece_price'],
                                    'qnty' => '0'
                                    );

                $this->common->UpdateTableRecords('purchase_detail',array('id' => $value->id),$update_data);
                $this->common->UpdateTableRecords('distribution_detail',array('id' => $value->id),$update_data);
                $this->common->DeleteTableRecords('item_address_mapping',array('item_id' => $value->id,'order_id' => $post['order_id']));
                $inner_count++;
            }
        }
        $this->common->UpdateTableRecords('order_orderlines',array('id' => $post['orderline_id']),array('color_id' => $post['color_id']));
        $data = array("success"=>1,"message"=>INSERT_RECORD);
        return response()->json(['data'=>$data]);
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
            @SWG\Property(
     *          property="product_id",
     *          type="integer"
     *      ),
            @SWG\Property(
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
        $attached_url = UPLOAD_PATH.$post['company_id'].'/pdf/order-'.$post['order_id'].'.pdf';
       
       $uploaddir = base_path() . "/public/uploads/".$post['company_id']."/pdf/order-".$post['order_id'].'.pdf';
       
       if (file_exists($uploaddir)) {
         
       } else {
        $response = array('success' => 0, 'message' => "Email Attachement is blank");
        return response()->json(["data" => $response]);
        exit;
       }
        
 
        Mail::send('emails.pdfmail', ['user'=>'hardik Deliwala','email'=>$email_array], function($message) use ($email_array,$post,$attached_url)
        {
             $message->to($email_array)->subject('Order Acknowledgement #'.$post['order_id']);
             $message->attach($attached_url);
        });

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
            @SWG\Property(
     *          property="id",
     *          type="integer"
     *      ),
            @SWG\Property(
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
       
        $client_data = $this->client->GetclientDetail($post['orderData']['client_id']);

        $dataMisc['cond']['company_id'] = $post['company_id'];
        
        $misc_data = $this->common->getAllMiscDataWithoutBlank($dataMisc);
        
        foreach($misc_data['approval'] as $row){
           if($row->slug == '137') {
                $estimation_id = $row->id;
              }
        }

         $post['orderdata']['name'] = $post['orderData']['name'];
         $post['orderdata']['approval_id'] = $estimation_id;
         $post['orderdata']['login_id'] = $post['login_id'];
         $post['orderdata']['company_id'] = $post['company_id'];
         $post['orderdata']['client_id'] = $post['orderData']['client_id'];
         $post['orderdata']['created_date'] = date('Y-m-d');
         $post['orderdata']['updated_date'] = date('Y-m-d');
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

}