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
use PDF;

use Request;

class PurchaseController extends Controller { 

    public function __construct(Purchase $purchase,Common $common,Product $product,Order $order) 
    {
        $this->purchase = $purchase;
        $this->product = $product;
        $this->order = $order;
        $this->common = $common;
    }

    public function createPO()
    {
        $post = Input::all();

        if(!empty($post['company_id']) && !empty($post['order_id']))
        {
            $po_type = !empty($post['po_type'])?$post['po_type']:'';
            $order_data = $this->purchase->getOrderData($post['company_id'],$post['order_id'],$po_type);
            
            if(count($order_data)>0)
            {
                foreach ($order_data as $key=>$value) 
                {
                    $purchase_order_id = $this->purchase->insert_purchaseorder($post['order_id'],$key);
                    if($purchase_order_id=='0')
                    {
                        $response = array('success' => 0, 'message' => "Purchase order is already created.");
                        return response()->json(["data" => $response]);
                    }
                    else
                    {
                        foreach($order_data[$key] as $detail_key=>$detail_value) 
                        {
                            $purchase_order_line = $this->purchase->insert_purchase_order_line($detail_value,$purchase_order_id);
                        }
                    }
                    
                }
                $response = array('success' => 1, 'message' => "Purchase order created successfully.",'data'=>$order_data);
            }
            else
            {
                $response = array('success' => 0, 'message' => "Please select Product.");
            }
        }
        else
        {
            $order_data='';
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
       // print_r($post);exit;
       return response()->json(["data" => $response]);
    }

    /*=====================================
    TO GET PO AND SG SCREEN FIELDS VALUES 
    =====================================*/

/** 
 * @SWG\Definition(
 *      definition="listPurchase",
 *      type="object",
 *      required={"company_id", "id"},
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          type="string"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/purchase/ListPurchase",
 *  summary = "Purchasing List",
 *  tags={"Purchasing"},
 *  description = "Purchasing List",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Purchasing List",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/listPurchase")
 *  ),
 *  @SWG\Response(response=200, description="Purchasing List"),
 *  @SWG\Response(response="default", description="Purchasing List"),
 * )
 */

/** 
 * @SWG\Definition(
 *      definition="listPurchase",
 *      type="object",
 *      required={"company_id", "id"},
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          type="string"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/purchase/ListPurchase",
 *  summary = "Purchasing List",
 *  tags={"Purchasing"},
 *  description = "Purchasing List",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Purchasing List",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/listPurchase")
 *  ),
 *  @SWG\Response(response=200, description="Purchasing List"),
 *  @SWG\Response(response="default", description="Purchasing List"),
 * )
 */

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
            //$po = $this->purchase->GetPodata($po_id,$company_id);
            $poline = $this->purchase->GetPoLinedata($po_id,$company_id);

            //echo "<pre>"; print_r($poline); echo "</pre>"; die;
            if(count($poline)>0)
            {
                
                //c$unassign_order = $this->purchase->GetPoLinedata();

                $order_total = $this->purchase->getOrdarTotal($po_id);
                
               // $received_total = $this->purchase->getreceivedTotal($po_id);
               // $received_line = $this->purchase->GetPoReceived($po_id,$company_id);

              //  $list_vendors = $this->common->getAllVendors($company_id);

                $po_data = $poline[0];
                $result = array('po_data'=>$po_data,'poline'=>$poline,'order_total'=>$order_total);//,'received_total'=>$received_total,'received_line'=>$received_line,'order_id'=>$order_id,'list_vendors'=> $list_vendors );
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
         if(empty($post['po_id']) || empty($post['id']))
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
            return  response()->json(["data" => $response]);
            die();
        }
        else
        {
            $result = $this->purchase->EditOrderLine($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD);
            return  response()->json(["data" => $response]);
        }
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

    public function GetPoReceived($po_id,$company_id)
    {

        if(empty($po_id) || empty($company_id))
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS."- po_id, company_id");
            return  response()->json(["data" => $response]);
            die();
        }
        else
        {
            //$this->purchase->Update_Ordertotal($po_id);
            $result = $this->purchase->GetPoReceived($po_id,$company_id);
            if(count($result)>0)
            {
                $order_total = $this->purchase->getOrdarTotal($po_id);
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$result,'order_total'=>$order_total);
            } 
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
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
    public function AllMsiData($compay_id)
    {
        $query = DB::table('misc_type')->where('company_id','=',$compay_id)->select('id','value','company_id')->get();
        $ret_array = array();
        foreach ($query as $key => $value) {
            $ret_array[$value->id] = $value->value;
        }

        //echo "<pre>"; print_r($query); echo "</pre>"; die;
        return $ret_array;
    }
    public function createPDF()
    {
        $post = Input::all();
        $ArrPoline['arr_poline'] = json_decode($post['arr_poline']);
        //print_r($ArrPoline['arr_poline']);exit;

        PDF::AddPage('P','A4');
        PDF::writeHTML(view('pdf.company_po',$ArrPoline)->render());
        PDF::Output('company_po.pdf');
    }
}