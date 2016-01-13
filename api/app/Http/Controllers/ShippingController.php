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
use App;
use Barryvdh\DomPDF\Facade as PDF;

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

        if(!empty($result['shippingBoxes']))
        {
            $shippingBoxes = array();
            $count = 1;
            foreach ($result['shippingBoxes'] as $row) {
                $row->count = $count.' of '.count($result['shippingBoxes']);
                $count++;
            }
        }

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['shipping'],
                                'shipping_type' => $shipping_type,
                                'shippingItems' => $result['shippingItems'],
                                'shippingBoxes' => $result['shippingBoxes']
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['shipping'],
                                'shipping_type' => $shipping_type,
                                'shippingItems' => $result['shippingItems'],
                                'shippingBoxes' => $result['shippingBoxes']
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

    public function CreateBoxShipment()
    {
        $post = Input::all();
        $box_arr = $this->common->GetTableRecords('shipping_box',array('shipping_id' => $post[0]['shipping_id']),array());

        if(empty($box_arr))
        {
            foreach ($post as $value) {
                if($value['qnty'] < $value['max_pack'])
                {
                    $insert_data = array('shipping_id' => $value['shipping_id'], 'box_qnty' => $value['qnty'], 'actual' => $value['qnty'], 'md' => '0', 'spoil' => '0');
                    $id = $this->common->InsertRecords('shipping_box',$insert_data);
                    $this->common->InsertRecords('box_item_mapping',array('box_id' => $id,'item_id' => $value['id'],'shipping_id' => $value['shipping_id']));
                }
                else
                {
                    $remaining_qty = $value['qnty'] % $value['max_pack'];
                    $div2 = $value['qnty'] / $value['max_pack'];
                    $main_qty = ceil($div2);

                    for ($i=1; $i <= $main_qty; $i++) {
                        if($i == $main_qty)
                        {
                            $insert_data = array('shipping_id' => $value['shipping_id'], 'box_qnty' => $remaining_qty);
                        }
                        else
                        {
                            $insert_data = array('shipping_id' => $value['shipping_id'], 'box_qnty' => $value['max_pack']);
                        }
                        $id = $this->common->InsertRecords('shipping_box',$insert_data);
                        $this->common->InsertRecords('box_item_mapping',array('box_id' => $id,'item_id' => $value['id']));
                    }
                }
                $this->common->UpdateTableRecords('distribution_detail',array('id' => $value['id']),array('boxed_qnty' => $value['qnty']));
            }
            $data = array("success"=>1,"message"=>INSERT_RECORD);
        }
        else
        {
            $data = array("success"=>0,"message"=>ALREADY_BOX);
        }
        return response()->json(["data" => $data]);
    }
    public function getBoxItems()
    {
        $result = array();
        $post = Input::all();
        $result = $this->shipping->getBoxItems($post);
        $box_item_arr = $this->common->GetTableRecords('shipping_box',array('shipping_id' => $post['shipping_id']),array());

        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'boxingItems' => $result,
                                'boxingAllItems' => $box_item_arr
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'boxingItems' => $result,
                                'boxingAllItems' => $box_item_arr
                                );
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
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
    public function DeleteBox()
    {
        $post = Input::all();


        if(!empty($post[0]))
        {
            $getData = $this->shipping->deleteBox($post[0]);
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

    public function createPDF()
    {
        $post = Input::all();

        $pdf = App::make('dompdf');

        if($post['shipping'] == 'manifest')
        {
            $pdf = PDF::loadView('pdf.shipping_manifest',$post);
            return $pdf->download('Print Manifest');
        }
        else if($post['shipping'] == 'report')
        {
            $pdf = PDF::loadView('pdf.shipping_report',array());
            return $pdf->download('Shipping Report');
        }
        else if($post['shipping'] == 'label')
        {
            $pdf = PDF::loadView('pdf.shipping_label',array());
            return $pdf->download('Shipping Label');
        }
    }
}