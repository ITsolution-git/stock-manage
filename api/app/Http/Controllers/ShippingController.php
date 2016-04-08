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
//use Barryvdh\DomPDF\Facade as PDF;

use Request;
use PDF;
class ShippingController extends Controller { 

    public function __construct(Shipping $shipping,Common $common,Purchase $purchase,Order $order) 
    {
        $this->shipping = $shipping;
        $this->purchase = $purchase;
        $this->common = $common;
    }

    public function addressValidate()
    {
        $address = new \Ups\Entity\Address();
        $address->setAttentionName('Test Test');
        $address->setBuildingName('Test');
        $address->setAddressLine1('Address Line 1');
        $address->setAddressLine2('Address Line 2');
        $address->setAddressLine3('Address Line 3');
        $address->setStateProvinceCode('NY');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setPostalCode('10000');

        $xav = new \Ups\AddressValidation('2D084297048602C5', 'Codal', 'Mobile1357');
        $xav->activateReturnObjectOnValidate(); //This is optional
        try {
            $response = $xav->validate($address, $requestOption = \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);
        } catch (Exception $e) {
            var_dump($e);
        }
        if ($response->noCandidates()) {
            echo "noCandidates";
            //Do something clever and helpful to let the use know the address is invalid
        }
        if ($response->isAmbiguous()) {
            echo "isAmbiguous";
            $candidateAddresses = $response->getCandidateAddressList();
            foreach($candidateAddresses as $address) {
                //Present user with list of candidate addresses so they can pick the correct one        
            }
        }
        if ($response->isValid()) {
            echo "isValid";
            $validAddress = $response->getValidatedAddress();
            print_r($validAddress);exit;
            //Show user validated address or update their address with the 'official' address
            //Or do something else helpful...
        }

        exit;
    }

    /**
    * Get Array List of All Shipping details
    * @return json data
    */

    /** 
 * @SWG\Definition(
 *      definition="shippingList",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/shipping/listShipping",
 *  summary = "Shipping Listing",
 *  tags={"Shipping"},
 *  description = "Shipping Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Shipping Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/shippingList")
 *  ),
 *  @SWG\Response(response=200, description="Shipping Listing"),
 *  @SWG\Response(response="default", description="Shipping Listing"),
 * )
 */
    public function listShipping()
    {
        $post = Input::all();

    	$result = $this->shipping->getShippingdata($post);
    	return $this->return_response($result);

    }

    /**
    * Get Array List of All Shipping details
    * @return json data
    */
    public function getShippingOrders()
    {
        $post = Input::all();
        $result = $this->shipping->getShippingOrders($post[0]);
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
        $shipping['shipping'] = json_decode($post['shipping']);
        $shipping['shipping_type'] = json_decode($post['shipping_type']);
        $shipping['shipping_items'] = json_decode($post['shipping_items']);
        $shipping['company_detail'] = json_decode($_POST['company_detail']);
        $shipping_boxes = json_decode($post['shipping_boxes']);

        $actual_total = 0;
        $xs_qnty = 0;
        $s_qnty = 0;
        $m_qnty = 0;
        $l_qnty = 0;
        $xl_qnty = 0;
        $xxl_qnty = 0;
        $xxxl_qnty = 0;
        $total_qnty = 0;
        $total_md = 0;
        $total_spoil = 0;


         $color_all_data = array();
        foreach ($shipping_boxes as $row) {

            $color_all_data[$row->color_name][$row->size] = $row->size;
            $color_all_data[$row->color_name]['desc'] = $row->product_desc;
            $color_all_data[$row->color_name][$row->size] = $row->qnty;

            $total_qnty += $row->qnty;
            $actual_total += $row->actual;
            $total_md += $row->md;
            $total_spoil += $row->spoil;
           
        }
      
       /*
        foreach ($shipping_boxes as $row) {

            if($row->size == 'XS') {
                $xs_qnty += $row->qnty;
            }
            else if($row->size == 'S') {
                $s_qnty += $row->qnty;
            }
            else if($row->size == 'M') {
                $m_qnty += $row->qnty;
            }
            else if($row->size == 'L') {
                $l_qnty += $row->qnty;
            }
            else if($row->size == 'XL') {
                $xl_qnty += $row->qnty;
            }
            else if($row->size == '2XL') {
                $xxl_qnty += $row->qnty;
            }
            else if($row->size == '3XL') {
                $xxxl_qnty += $row->qnty;
            }

           
        }*/

        $other_data['total_box'] = count($shipping_boxes);
        $other_data['total_pieces'] = $actual_total;

       /* $other_data['xs_qnty'] = $xs_qnty;
        $other_data['s_qnty'] = $s_qnty;
        $other_data['m_qnty'] = $m_qnty;
        $other_data['l_qnty'] = $l_qnty;
        $other_data['xl_qnty'] = $xl_qnty;
        $other_data['2xl_qnty'] = $xxl_qnty;
        $other_data['3xl_qnty'] = $xxxl_qnty;*/

        $other_data['total_qnty'] = $total_qnty;
        $other_data['total_md'] = $total_md;
        $other_data['total_spoil'] = $total_spoil;

        $shipping['shipping_boxes'] = $shipping_boxes;
        $shipping['other_data'] = $other_data;
        $shipping['color_all_data'] = $color_all_data;

        
        if($post['print_type'] == 'manifest')
        {
        
            PDF::AddPage('P','A4');
            PDF::writeHTML(view('pdf.shipping_manifest',$shipping)->render());
            PDF::Output('shipping_manifest.pdf');

        }
        else if($post['print_type'] == 'report')
        {
            PDF::AddPage('P','A4');
            PDF::writeHTML(view('pdf.shipping_report',$shipping)->render());
            PDF::Output('shipping_report.pdf');
        }
        else if($post['print_type'] == 'label')
        {
            PDF::AddPage('P','A4');
            PDF::writeHTML(view('pdf.shipping_label',$shipping)->render());
            PDF::Output('shipping_label.pdf');
        }
    }

    public function addRemoveAddressToPdf()
    {
        $post = Input::all();

        $this->common->UpdateTableRecords('item_address_mapping',array('order_id' => $post['order_id']),array('print_on_pdf' => '0'));
        $this->common->UpdateTableRecords('item_address_mapping',array('address_id' => $post['address_id'],'order_id' => $post['order_id']),array('print_on_pdf' => $post['print_on_pdf']));

        $success=1;
        $message=UPDATE_RECORD;
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);
    }
}