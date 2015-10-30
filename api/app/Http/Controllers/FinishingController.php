<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Category;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Finishing;
use DB;
use Swagger\Annotations as SWG;

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
        $data = $this->finishing->getFinishingdata();

        if(!empty($data))
        {
            foreach ($data as $value) {
                
                $result['all'][] = $value;
                
                if ($value->category_id == '1')
                {
                    $result['poly_bagging'][] = $value;
                }
                if ($value->category_id == '2')
                {
                    $result['hang_tag'][] = $value;
                }
                if ($value->category_id == '3')
                {
                    $result['tag_removal'][] = $value;
                }
                if ($value->category_id == '4')
                {
                    $result['speciality'][] = $value;
                }
                if ($value->category_id == '5')
                {
                    $result['packing'][] = $value;
                }
                if ($value->category_id == '6')
                {
                    $result['sticker'][] = $value;
                }
                if ($value->category_id == '7')
                {
                    $result['sew_on_women_tag'][] = $value;
                }
                if ($value->category_id == '8')
                {
                    $result['inside_tag'][] = $value;
                }
                if ($value->status == '1')
                {
                    $result['active'][] = $value;
                }
                if ($value->status == '2')
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

        if($post['field'] == 'category_name')
        {
            $category = $this->category->getCategoryByName($post['value']);
            if(empty($category))
            {
                $category = $this->category->addcategory(array());
            }
        }
        exit;
        $result = $this->finishing->updateFinishingNotes($post['data'][0]);
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
	
}