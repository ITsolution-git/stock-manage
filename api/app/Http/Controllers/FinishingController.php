<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Category;
use App\Order;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Finishing;
use DB;

use Request;

class FinishingController extends Controller { 

    public function __construct(Finishing $finishing, Category $category, Common $common, Order $order)
    {
        parent::__construct();
        $this->finishing = $finishing;
        $this->category = $category;
        $this->common = $common;
        $this->order = $order;
    }

  
/** 
 * @SWG\Definition(
 *      definition="finishingList",
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
 *  path = "/api/public/finishing/listFinishing",
 *  summary = "Finishing Listing",
 *  tags={"Finishing"},
 *  description = "Finishing Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Finishing Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/finishingList")
 *  ),
 *  @SWG\Response(response=200, description="Finishing Listing"),
 *  @SWG\Response(response="default", description="Finishing Listing"),
 * )
 */
    public function listFinishing()
    {
        //$post = Input::all();
        //$data = $this->finishing->getFinishingdata($post);

        $post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'o.display_number';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'o.display_number';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->finishing->getFinishingdata($post);

        foreach ($result['allData'] as $data) {
            $inner_data = $this->finishing->getFinishingByOrder($data->order_id);

            foreach ($inner_data as $row) {
                if(!is_null($row->start_time)) {
                    $row->start_time = date('h:i A', strtotime($row->start_time));
                }
                if(!is_null($row->end_time)) {
                    $row->end_time = date('h:i A', strtotime($row->end_time));
                }
                if(!is_null($row->est)) {
                    if($row->est > 0) {
                        $row->est = substr($row->est,0, -3).' hrs';
                    }
                    else {
                        $row->est = '24:00 hrs';
                    }
                }
            }

            $data->order_finishing = $inner_data;
        }

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'o.display_number', 'name' => 'Order ID'),
                        1=>array('key' => 'o.name', 'name' => 'Job Name'),
                        2=>array('key' => 'c.client_company', 'name' => 'Client'),
                        3=>array('key' => 'null', 'name' => 'Operations', 'sortable' => false),
                        4=>array('key' => 'null', 'name' => 'Order Status', 'sortable' => false),
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success);
        return response()->json($data);
    }

    /**
    * Update Finishing record
    * @params finishing array
    * @return json data
    */
    public function updateFinishing()
    {
        $post = Input::all();

        $finishingData['field']['note'] = $post['note'];
        
        if($post['start_time'] != '')
        {
            $finishingData['field']['start_time'] = date('H:i', strtotime($post['start_time']));
        }
        else
        {
            $finishingData['field']['start_time'] = null;
        }
        if($post['end_time'] != '')
        {
            $finishingData['field']['end_time'] = date('H:i', strtotime($post['end_time']));
        }
        else
        {
            $finishingData['field']['end_time'] = null;
        }
        if($post['est'] != '')
        {
            $finishingData['field']['est'] = date('H:i', strtotime($post['est']));
        }
        else
        {
            $finishingData['field']['est'] = null;
        }
        $finishingData['where'] = array('id' => $post['id']);

        $result = $this->finishing->updateFinishing($finishingData);
        
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
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

    public function addRemoveToFinishing()
    {
        $post = Input::all();

        $design_data = $this->common->GetTableRecords('design_product',array('design_id'=>$post['product']['design_id'],'product_id'=>$post['product']['id']),array());
        $design = $design_data[0];

        if($post['item'] == 1)
        {
            $finishing_data = $this->common->GetTableRecords('finishing',array('order_id' => $post['order_id'],'design_id' => $post['product']['design_id'],'product_id' => $post['product']['id'],'category_id' => $post['item_id']),array());
            
            if($finishing_data[0]->status == '1')
            {
                $data = array("success"=>0,"message"=>'Finishing of this item is completed.');
                return response()->json(["data" => $data]);
            }
            
            $extra_charges = $design->extra_charges - $post['item_charge'];
            
            $update_arr = array('extra_charges' => $extra_charges);
            $this->common->UpdateTableRecords('design_product',array('design_id' => $design->design_id,'product_id' => $design->product_id),$update_arr);

            $this->common->DeleteTableRecords('order_item_mapping',array('order_id' => $post['order_id'],'design_id' => $post['product']['design_id'],'product_id' => $post['product']['id'],'item_id' => $post['item_id']));
            $this->common->DeleteTableRecords('finishing',array('order_id' => $post['order_id'],'design_id' => $post['product']['design_id'],'product_id' => $post['product']['id'],'category_id' => $post['item_id']));
        }
        else
        {
            if($post['product']['total_qnty'] > 0)
            {
                $extra_charges = $design->extra_charges + $post['item_charge'];

                $update_arr = array('extra_charges' => $extra_charges);
                $this->common->UpdateTableRecords('design_product',array('design_id' => $design->design_id,'product_id' => $design->product_id),$update_arr);
            }

            $insert_arr = array('order_id' => $post['order_id'],'item_id' => $post['item_id'],'design_id' => $post['product']['design_id'],'product_id' => $post['product']['id']);
            $shipping_id = $this->common->InsertRecords('order_item_mapping',$insert_arr);

            $item_data = array('category_id' => $post['item_id'],'order_id' => $post['order_id'],'qty' => $post['product']['total_qnty'],'design_id' => $post['product']['design_id'],'product_id' => $post['product']['id']);
            $result = $this->finishing->addFinishing($item_data);
        }
        $return = app('App\Http\Controllers\ProductController')->orderCalculation($post['product']['design_id']);

        $data = array("success"=>1);
        return response()->json(["data" => $data]);
    }
}