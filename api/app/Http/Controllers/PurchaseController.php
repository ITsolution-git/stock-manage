<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Purchase;
use App\Order;
use App\Product;
use App\Common;
use DB;

use Request;

class PurchaseController extends Controller { 

	public function __construct(Purchase $purchase,Common $common,Product $product,Order $order) 
 	{
        $this->purchase = $purchase;
        $this->product = $product;
        $this->order = $order;
        $this->common = $common;
    }

    /*=====================================
	TO GET PO AND SG SCREEN FIELDS VALUES 
	=====================================*/

    public function ListPurchase()
    {
    	$post = Input::all();
    	
    	//echo "<pre>"; print_r($post); echo "</pre>"; die;
    	if(empty($post))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS."- Po_type");
    	}
    	else
    	{
	    	$result = $this->purchase->ListPurchase($post['type'],$post['company_id']);
	    	if (count($result) > 0) 
	        {
	            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
	        } 
	        else 
	        {
	            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
	        }
    	}
        return  response()->json(["data" => $response]);
    }

    /*=====================================
	/ TO GET PO AND SG SCREEN DATA
	/ ITS MAIN QUERY FOR WHOLE SCREEN DATA
	=====================================*/

    public function GetPodata($po_id,$company_id)
    {
    	if(empty($po_id) || empty($company_id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$this->purchase->Update_Ordertotal($po_id);
    		$po = $this->purchase->GetPodata($po_id,$company_id);
    		
    		if(count($po)>0)
    		{
    			$poline = $this->purchase->GetPoLinedata($po_id,'1',$company_id);
	    		$unassign_order = $this->purchase->GetPoLinedata();

		    	$order_total = $this->purchase->getOrdarTotal($po_id);
		    	
		    	$received_total = $this->purchase->getreceivedTotal($po_id);
		    	$received_line = $this->purchase->GetPoReceived($po_id,$company_id);

                $list_vendors = $this->common->getAllVendors($company_id);

    			$order_id = $po[0]->order_id;
	    		$result = array('po'=>$po,'poline'=>$poline,'unassign_order'=>$unassign_order,'order_total'=>$order_total,'received_total'=>$received_total,'received_line'=>$received_line,'order_id'=>$order_id,'list_vendors'=> $list_vendors );
	    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS);
	    		return  response()->json(["data" => $response]);
	    		die();
    		}
    	}
    	return  response()->json(["data" => $response]);
    }

	/*=====================================
	TO UNASSIGN AND ASSIGN ORDER LINE ITEMS, CHANGE FLAG AND UPDATE PO
	=====================================*/

    public function ChangeOrderStatus($id,$val,$po_id)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS."- id");
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$result = $this->purchase->ChangeOrderStatus($id,$val,$po_id);
    		$response = array('success' => 1, 'message' => GET_RECORDS);
    	}
    	return  response()->json(["data" => $response]);
    }

	/*=====================================
	TO CALCULATION ONE PO AND SG SCREEN TOTAL AMOUNT
	=====================================*/

    public function EditOrderLine()
    {
    	 $post = Input::all();
    	 $result = $this->purchase->EditOrderLine($post);
    	 $response = array('success' => 1, 'message' => GET_RECORDS);
    	 return  response()->json(["data" => $response]);
    }
    /*=====================================
	TO CALCULATION ONE CP AND CE SCREEN TOTAL AMOUNT
	=====================================*/

    public function EditScreenLine()
    {
    	 $post = Input::all();
    	 $result = $this->purchase->EditScreenLine($post);
    	 $response = array('success' => 1, 'message' => GET_RECORDS);
    	 return  response()->json(["data" => $response]);
    }

	/*=====================================
	TO GET RECEIVED ORDER FOR PO AND SG TAB
	=====================================*/

    public function Receive_order()
    {
    	$post = Input::all();
    	$result = $this->purchase->Receive_order($post);
    	$response = array('success' => 1, 'message' => GET_RECORDS);
    	return  response()->json(["data" => $response]);
    }

	/*=====================================
	UPDATE SHIFTLOCK FIELD
	=====================================*/

	public function Update_shiftlock()
	{
		$post = Input::all();
		$result = $this->purchase->Update_shiftlock($post);
    	$response = array('success' => 1, 'message' => UPDATE_RECORD);
    	return  response()->json(["data" => $response]);
	}

	/*=====================================
	TO MAINTAIN SHORT AND OVER COUNT, MATCH RECEIVED QNTY WITH ORDER QNTY
	=====================================*/

	public function short_over($id)
	{	
		if(empty($id))
		{
			$response = array('success' => 0, 'message' => MISSING_PARAMS."- PoLine ID");
    		return  response()->json(["data" => $response]);
    		die();
		}
    	else
    	{
    		$short_over = $this->purchase->short_over($id);
			$response = array('success' => 1, 'message' => UPDATE_RECORD);
    		return  response()->json(["data" => $response]);
    	}
	}

	/*=====================================
	TO GET SCREEN PRINT AND EMBRODIERY DATA
	=====================================*/

	public function GetScreendata($po_id,$company_id)
	{
    	if(empty($po_id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS."- po_id");
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$this->purchase->Update_Ordertotal($po_id);
    		$screen_data = $this->purchase->GetPodata($po_id,$company_id);


    		//echo "<pre>"; print_r($screen_data); echo "</pre>"; die;
    		if(count($screen_data)>0)
    		{
                $screen_line = $this->purchase->GetScreendata($po_id,$company_id);
                $order_total = $this->purchase->getOrdarTotal($po_id);
                $list_vendors = $this->common->getAllVendors($company_id);
    			$order_id = $screen_data[0]->order_id;
                $order_line_data = $this->purchase->GetOrderLineData($order_id);

                

                if(!empty($order_line_data))
                    {
                        $sum = 0;
                        foreach($order_line_data as $row)
                        {
                            $row->orderline_id = $row->id;
                          

                            $order_line_items = $this->order->getOrderLineItemById($row->id);
                            $count = 1;
                            $order_line = array();
                            foreach ($order_line_items as $line) {
                             
                                $line->number = $count;
                                $order_line[] = $line;
                                $count++;
                            }
                            $row->items = $order_line;

                            $order_line_data_new[] = $row;
                        }
                    }
                        else
                        {
                            $order_line_data_new = array();
                        }

	    		$result = array('screen_data'=>$screen_data,'screen_line'=>$screen_line,'order_total'=>$order_total,'order_id'=>$order_id,'list_vendors'=>$list_vendors,'order_line_data_new'=>$order_line_data_new );
	    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result );
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS);
	    		return  response()->json(["data" => $response]);
	    		die();
    		}
    	}
    	return  response()->json(["data" => $response]);
	}

    public function getPurchaseNote($id)
    {
        $result = $this->purchase->getPurchaseNote($id);
        return $this->return_response($result);
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
            $response = array('success' => 0, 'message' => NO_RECORDS);
        }
        return  response()->json(["data" => $response]);
    }
}