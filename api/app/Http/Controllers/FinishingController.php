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
    * Get Array List of All Finishing details
    * @return json data
    */
    /**
    *
    * @SWG\Api(
    *   path="/pet.{format}/{petId}",
    *   description="Get Array List of All Finishing details",
    *   @SWG\Operation(notes="Returns all finishing items"),
    *      @SWG\Parameter(...),
    *      @SWG\ResponseMessage(...)
    *   )
    * )
    */
    public function listFinishing()
    {
        $post = Input::all();
        $data = $this->finishing->getFinishingdata($post[0]);

        if(!empty($data))
        {
            foreach ($data as $value) {
                
                if ($value->category_id == '1' && $value->status == '0')
                {
                    $result['poly_bagging'][] = $value;
                }
                if ($value->category_id == '2' && $value->status == '0')
                {
                    $result['hang_tag'][] = $value;
                }
                if ($value->category_id == '3' && $value->status == '0')
                {
                    $result['tag_removal'][] = $value;
                }
                if ($value->category_id == '4' && $value->status == '0')
                {
                    $result['speciality'][] = $value;
                }
                if ($value->category_id == '5' && $value->status == '0')
                {
                    $result['packing'][] = $value;
                }
                if ($value->category_id == '6' && $value->status == '0')
                {
                    $result['sticker'][] = $value;
                }
                if ($value->category_id == '7' && $value->status == '0')
                {
                    $result['sew_on_women_tag'][] = $value;
                }
                if ($value->category_id == '8' && $value->status == '0')
                {
                    $result['inside_tag'][] = $value;
                }
                if ($value->status == '0')
                {
                    $result['active'][] = $value;
                }
                if ($value->status == '1')
                {
                    $result['completed'][] = $value;
                }
            }
        }
        else
        {
            $result = $data;
        }
        return $this->return_response($result);
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
            $finishingData['field'] = array('end_time' => $post['end_time'],'est' => $post['est']);
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

    public function removeFinishingItem()
    {
        $post = Input::all();
        $category = $this->category->getCategoryByName($post['item_name']);

        if(!empty($category))
        {
            $finishingData['table'] = 'finishing';
            $finishingData['field'] = array('is_delete' => '1');
            $finishingData['where'] = array('order_id' => $post['order_id'],'category_id' => $category[0]->id);

            $result = $this->finishing->updateFinishing($finishingData);
        }
    }
    public function addFinishingItem()
    {
        $post = Input::all();
        $category = $this->category->getCategoryByName($post['item_name']);

        if(!empty($category))
        {
            $finishingData = array('order_id' => $post['order_id'],'category_id' => $category[0]->id,'qty' => $post['total_qty']);
            $result = $this->finishing->addFinishing($finishingData);
        }
    }
}