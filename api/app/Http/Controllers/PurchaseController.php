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
    public function ListPurchase($id)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARMS."- id",'records' => $result);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	$result = $this->purchase->ListPurchase($id);
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
    public function GetPodata($id)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARMS."- id",'records' => $result);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$po_id=0;
    		$order_total=''; $received_total='';$received_line='';
    		$po = $this->purchase->GetPodata($id);
    		$poline = $this->purchase->GetPoLinedata($id,'1');
    		$unassign_order = $this->purchase->GetPoLinedata();

    		if(count($poline)>0)
    		{
    			$po_id = $poline[0]->po_id;
    			
    		}
    		else
    		{
				$po_id = $unassign_order[0]->po_id;
    		}
	    		
	    		$order_total = $this->purchase->getOrdarTotal($po_id);
	    		$received_total = $this->purchase->getreceivedTotal($po_id);
	    		$received_line = $this->purchase->GetPoReceived($po_id);
    		
    		$result = array('po'=>$po,'poline'=>$poline,'unassign_order'=>$unassign_order,'order_total'=>$order_total,'received_total'=>$received_total,'received_line'=>$received_line );
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
    	}
    	return  response()->json(["data" => $response]);
    }
    public function GetSgdata($id)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARMS."- id",'records' => $result);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$result = $this->purchase->GetSgdata($id);
    	}
    }

    public function ChangeOrderStatus($id,$val)
    {
    	if(empty($id))
    	{
    		$response = array('success' => 0, 'message' => MISSING_PARMS."- id",'records' => $result);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$result = $this->purchase->ChangeOrderStatus($id,$val);
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
    public function RemoveReceiveLine($id)
	{
		$result = $this->purchase->RemoveReceiveLine($id);
    	$response = array('success' => 1, 'message' => DELETE_RECORD);
    	return  response()->json(["data" => $response]);
	}
}