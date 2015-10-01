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
          
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['order'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'order_position' => $result['order_position']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['order'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'order_position' => $result['order_position']);
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
	
}