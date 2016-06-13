<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Category;
use App\Order;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Finishing;
use DB;

use Request;

class FinishingController extends Controller { 

    public function __construct(Finishing $finishing, Category $category)
    {
        $this->finishing = $finishing;
        $this->category = $category;
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
            $post['sorts']['sortBy'] = 'f.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'f.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->finishing->getFinishingdata($post);

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'order.id', 'name' => 'Order ID'),
                        1=>array('key' => 'f.qty', 'name' => 'Qty'),
                        2=>array('key' => 'fc.category_name', 'name' => 'Finishing Item'),
                        3=>array('key' => 'c.client_company', 'name' => 'Client'),
                        4=>array('key' => 'f.start_time', 'name' => 'Start'),
                        5=>array('key' => 'f.end_time', 'name' => 'End'),
                        6=>array('key' => 'f.est', 'name' => 'EST Completion Time'),
                        7=>array('key' => 'f.status', 'name' => 'Done?'),
                        8=>array('key' => 'null', 'name' => '', 'sortable' => false)
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success);
        return response()->json($data);
    }
    /**
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
    public function DeleteFinishing()
    {
        $post = Input::all();

        if(!empty($post[0]))
        {
            $getData = $this->finishing->deleteFinishing($post[0]);
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
    * Update Finishing record
    * @params finishing array
    * @return json data
    */
    public function updateFinishing()
    {
        $post = Input::all();

        $finishingData['table'] = $post['table'];

        if($post['field'] == 'category_name')
        {
            $category = $this->category->getCategoryByName($post['value']);
            if(empty($category))
            {
                $category_id = $this->category->addcategory(array('category_name' => $post['value']));
            }
            else
            {
                $category_id = $category[0]->id;
            }
            $finishingData['field'] = array('category_id' => $category_id);
            $finishingData['where'] = array('id' => $post['id']);
        }
        if($post['field'] == 'job_name')
        {
            $finishing_data = $this->finishing->getFinishingDetailById($post['id']);

            $finishingData['field'] = array('job_name' => $post['value']);
            $finishingData['where'] = array('id' => $finishing_data[0]->order_id);
        }
        if($post['field'] == 'note')
        {
            $finishingData['field'] = array('note' => $post['value']);
            $finishingData['where'] = array('id' => $post['id']);
        }
        if($post['field'] == 'status')
        {
            $finishingData['field'] = array('status' => $post['value']);
            $finishingData['where'] = array('id' => $post['id']);
        }
        if(isset($post['start_time']))
        {
            $finishingData['field'] = array('start_time' => $post['start_time'],'est' => $post['est']);
            $finishingData['where'] = array('id' => $post['id']);
        }
        if(isset($post['end_time']))
        {
            $finishingData['field'] = array('end_time' => $post['end_time'],'est' => $post['est'],'status' => '1');
            $finishingData['where'] = array('id' => $post['id']);
        }

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

    public function removeFinishingItem($post)
    {
        $category = $this->category->getCategoryByName($post['item_name']);

        if(!empty($category))
        {
            $finishingData['table'] = 'finishing';
            $finishingData['field'] = array('is_delete' => '1');
            $finishingData['where'] = array('order_id' => $post['order_id'],'category_id' => $category[0]->id);

            $result = $this->finishing->updateFinishing($finishingData);
        }
    }
    public function addFinishingItem($post)
    {
        $category = $this->category->getCategoryByName($post['item_name']);

        if(!empty($category))
        {
            $finishingData = array('order_id' => $post['order_id'],'category_id' => $category[0]->id,'qty' => $post['total_qnty']);
            $result = $this->finishing->addFinishing($finishingData);
        }
    }
}