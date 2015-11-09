<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Order;
use DB;

use Request;

class OrderController extends Controller { 

	public function __construct(Order $order) 
 	{
        $this->order = $order;
    }

    /**
    * Get Array List of All Order details
    * @return json data
    */
    public function listOrder()
    {
    	$result = $this->order->getOrderdata();
    	return $this->return_response($result);
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
* Order Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function orderDetail() {
 
        $data = Input::all();

        $result = $this->order->orderDetail($data);

        if(!empty($result['order_line_data']))
        {
            foreach($result['order_line_data'] as $row)
            {
                $order_line = $this->order->getOrderLineItemById($row->id);
                $row->items = $order_line;
                $result['order_line'][] = $row;   
            }
        }
        else
        {
            $result['order_line'] = array();
        }

        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['order'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'order_position' => $result['order_position'],'order_line' => $result['order_line']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['order'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'order_position' => $result['order_position'],'order_line' => $result['order_line']);
        }
        
        return response()->json(["data" => $response]);

    }


   /**
   * Get Order notes.
   * @return json data
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
    public function getOrderDetailById($id)
    {
        $result = $this->order->getOrderDetailById($id);
        return $this->return_response($result);
    }


    /**
    * Update Order Note tab record
    * @params order note array
    * @return json data
    */
    public function updateOrderNotes()
    {
        $post = Input::all();
        $result = $this->order->updateOrderNotes($post['data'][0]);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /**
    * Delete order note tab record.
    * @params note_id
    * @return json data
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
        

        $post['data']['created_date']=date('Y-m-d');
 
       
            $result = $this->order->updateOrderLineData($post['data']);
            $message = INSERT_RECORD;
            $success = 1;
        
        $data = array("success"=>$success,"message"=>$message);
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
	
}