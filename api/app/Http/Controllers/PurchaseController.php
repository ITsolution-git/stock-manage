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
    		$result = $this->purchase->GetPoadata($id);
    		return  response()->json(["data" => $response]);
    		die();
    	}
    	else
    	{
    		$result = $this->purchase->GetPodata($id);
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
}