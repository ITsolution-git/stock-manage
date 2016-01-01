<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Shipping;
use App\Common;
use App\Purchase;
use App\Order;
use DB;

use Request;

class ShippingController extends Controller { 

	public function __construct(Shipping $shipping,Common $common,Purchase $purchase,Order $order) 
 	{
        $this->shipping = $shipping;
        $this->purchase = $purchase;
        $this->common = $common;
    }

    /**
    * Get Array List of All Shipping details
    * @return json data
    */
    public function listShipping()
    {
        $post = Input::all();
    	$result = $this->shipping->getShippingdata($post[0]);
    	return $this->return_response($result);
    }

    /**
    * Get Array List of All Shipping details
    * @return json data
    */
    public function getShippingOrders()
    {
        $result = $this->shipping->getShippingOrders();
        return $this->return_response($result);
    }

    /**
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
	public function DeleteShipping()
	{
		$post = Input::all();


		if(!empty($post[0]))
		{
			$getData = $this->order->deleteShipping($post[0]);
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
    * Shipping Detail controller      
    * @access public detail
    * @param  array $data
    * @return json data
    */
    public function shippingDetail() {
 
        $data = Input::all();

        $result = $this->shipping->shippingDetail($data);
        $shipping_type = $this->common->GetTableRecords('shipping_type',array(),array());

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['shipping'],
                                'shipping_type' => $shipping_type,
                                'shippingItems' => $result['shippingItems']
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['shipping'],
                                'shipping_type' => $shipping_type,
                                'shippingItems' => $result['shippingItems']
                                );
        } 
        
        return response()->json(["data" => $response]);

    }


   /**
   * Get Shipping notes.
   * @return json data
   */
    public function getShippingNoteDetails($id)
    {

        $result = $this->order->getShippingNoteDetails($id);
        return $this->return_response($result);
        
    }

    /**
    * Get Client Details by ID
    * @params order_id
    * @return json data
    */
    public function getShippingDetailById($id)
    {
        $result = $this->order->getShippingDetailById($id);
        return $this->return_response($result);
    }


    /**
    * Update Shipping Note tab record
    * @params order note array
    * @return json data
    */
    public function updateShippingNotes()
    {
        $post = Input::all();
        $result = $this->order->updateShippingNotes($post['data'][0]);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /**
    * Delete order note tab record.
    * @params note_id
    * @return json data
    */
    public function deleteShippingNotes($id)
    {
        $result = $this->order->deleteShippingNotes($id);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }


   /**
   * Save Shipping notes.
   * @return json data
    */
    public function saveShippingNotes()
    {

        $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');
 
        if(!empty($post['data']['order_id']) && !empty($post['data']['order_notes']))
        {
            $result = $this->order->saveShippingNotes($post['data']);
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
    * Save Shippingline order.
    * @return json data
    */
    public function orderLineadd()
    {

        $post = Input::all();
        

        $post['data']['created_date']=date('Y-m-d');
 
       
            $result = $this->order->saveShippingLineData($post['data']);
            $message = INSERT_RECORD;
            $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }


    /**
   * Update Shippingline order.
   * @return json data
    */
    public function orderLineupdate()
    {
        $post = Input::all();

        $post['data']['created_date']=date('Y-m-d');
       
        $result = $this->order->updateShippingLineData($post['data']);
        $message = INSERT_RECORD;
        $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }

     /**
   * delete order line.
   * @return json data
    */
    public function deleteShippingLine()
    {
        $post = Input::all();
       
        $this->common->DeleteTableRecords('order_orderlines',array('id' => $post['id']));

        $purchase_detail = $this->common->GetTableRecords('purchase_detail',array('orderline_id' => $post['id']),array());

        foreach ($purchase_detail as $row) {
            $this->common->DeleteTableRecords('item_address_mapping',array('item_id' => $row->id));
        }
                
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
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
        //echo "<pre>"; print_r($post); echo "</pre>"; die;

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
    * Shipping Detail controller      
    * @access public detail
    * @param  array $data
    * @return json data
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
    public function return_response($result)
    {
        if (count($result) > 0) 
        {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } 
        else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }

    /**
    * Shipping Detail controller
    * @access public detail
    * @param  array $data
    * @return json data
    */
    public function distributionDetail()
    {
        $data = Input::all();
        $dist_addr = $this->common->GetTableRecords('client_distaddress',array('client_id' => $data['client_id']),array());

        $client_distaddress = array();
        foreach ($dist_addr as $addr) {
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
            $client_distaddress[] = $addr;
        }

        $array = array('order.id' => $data['order_id'],'is_distribute' => '0');
        $order_items = $this->order->getDistributionItems($array);

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
            $addr->full_address = $addr->address ." ". $addr->address2 ." ". $addr->city ." ". $addr->state ." ". $addr->zipcode ." ".$addr->country;
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
                $result = $this->common->InsertRecords('item_address_mapping',$post['data']);
                $id = $result;
            }
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $result = $this->common->InsertRecords('item_address_mapping',$post);
            $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['item_id']),array('is_distribute' => '1'));
            
            $success=1;
            $message=UPDATE_RECORD;
        }

        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }
    public function removeFromDistribute()
    {
        $post = Input::all();

        if(!isset($post['item_id']))
        {
            $item_data = $this->common->GetTableRecords('item_address_mapping',array('address_id' => $post['address_id']),array());

            foreach ($item_data as $item) {
                if($item->item_id > 0)
                {
                    $this->common->UpdateTableRecords('distribution_detail',array('id' => $item->item_id),array('is_distribute' => '0'));
                }
            }

            $post['cond'] = array('order_id' => $post['order_id'],'address_id' => $post['address_id']);

            $this->common->DeleteTableRecords('item_address_mapping',$post['cond']);

            $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        else
        {
            $this->common->UpdateTableRecords('distribution_detail',array('id' => $post['item_id']),array('is_distribute' => '0'));
            
            $post['cond'] = array('order_id' => $post['order_id'],'item_id' => $post['item_id']);
            $this->common->DeleteTableRecords('item_address_mapping',$post['cond']);

            $data = array("success"=>1,"message"=>UPDATE_RECORD);
        }
        return response()->json(['data'=>$data]);
    }



    /*=====================================
    / TO GET PO AND SG SCREEN DATA
    =====================================*/

    public function GetPodataAll($po_id)
    {
       
        $poline = $this->purchase->GetPoLinedata($po_id,'1');
              
        $result = array('poline'=>$poline);
        $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        return  response()->json(["data" => $response]);
    }

    public function updateShippingTask()
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
* Duplicate PO       
* @access public duplicatePoData
* @param  array $data
* @return json data
*/
    public function duplicatePoData() {

       $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');
        $post['data']['po_id']=$post[0];
       
       
        $result = $this->order->poDuplicate($post['data']);
          
        $data = array("success"=>1,"message"=>INSERT_RECORD);
        
        return response()->json(["data" => $data]);
    }

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
}