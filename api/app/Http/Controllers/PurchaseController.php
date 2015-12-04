<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Purchase;
use DB;

use Request;

class PurchaseController extends Controller { 

	public function __construct(Purchase $purchase) 
 	{
        $this->purchase = $purchase;
    }
    public function ListPurchase()
    {
    	$post = Input::all();
    	
    	//echo "<pre>"; print_r($post); echo "</pre>"; die;
    	if(empty($post))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS."- Po_type");
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	$result = $this->purchase->ListPurchase($post['type']);
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
    public function GetPodata($po_id)
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
    		$order_total=''; $received_total='';$received_line='';
    		$po = $this->purchase->GetPodata($po_id);
    		$poline = $this->purchase->GetPoLinedata($po_id,'1');
    		$unassign_order = $this->purchase->GetPoLinedata();

	    		$order_total = $this->purchase->getOrdarTotal($po_id);
	    		$received_total = $this->purchase->getreceivedTotal($po_id);
	    		$received_line = $this->purchase->GetPoReceived($po_id);
    		if(count($po)>0)
    		{
    			$order_id = $po[0]->order_id;
	    		$result = array('po'=>$po,'poline'=>$poline,'unassign_order'=>$unassign_order,'order_total'=>$order_total,'received_total'=>$received_total,'received_line'=>$received_line,'order_id'=>$order_id );
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
    public function GetSgdata($id)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARAMS."- id");
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$result = $this->purchase->GetSgdata($id);
    	}
    }

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
    public function EditOrderLine()
    {
    	 $post = Input::all();
    	 $result = $this->purchase->EditOrderLine($post);
    	 $response = array('success' => 1, 'message' => GET_RECORDS);
    	 return  response()->json(["data" => $response]);
    }
    public function Receive_order()
    {
    	$post = Input::all();
    	$result = $this->purchase->Receive_order($post);
    	$response = array('success' => 1, 'message' => GET_RECORDS);
    	return  response()->json(["data" => $response]);
    }

	public function Update_shiftlock()
	{
		$post = Input::all();
		$result = $this->purchase->Update_shiftlock($post);
    	$response = array('success' => 1, 'message' => UPDATE_RECORD);
    	return  response()->json(["data" => $response]);
	}
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
}