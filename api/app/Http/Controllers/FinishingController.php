<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Finishing;
use DB;
use Swagger\Annotations as SWG;

use Request;

class FinishingController extends Controller { 

	public function __construct(Finishing $finishing) 
 	{
        $this->finishing = $finishing;
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
* Finishing Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function finishingDetail() {
 
         $data = Input::all();
         

          $result = $this->finishing->finishingDetail($data);
          
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['finishing'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'finishing_position' => $result['finishing_position'],'finishing_line' => $result['finishing_line']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['finishing'],'client_data' => $result['client_data'],'client_main_data' => $result['client_main_data'],'finishing_position' => $result['finishing_position'],'finishing_line' => $result['finishing_line']);
        }
        
        return response()->json(["data" => $response]);

    }


   /**
   * Get Finishing notes.
   * @return json data
   */
    public function getFinishingNoteDetails($id)
    {

        $result = $this->finishing->getFinishingNoteDetails($id);
        return $this->return_response($result);
        
    }

    /**
    * Get Client Details by ID
    * @params finishing_id
    * @return json data
    */
    public function getFinishingDetailById($id)
    {
        $result = $this->finishing->getFinishingDetailById($id);
        return $this->return_response($result);
    }


    /**
    * Update Finishing Note tab record
    * @params finishing note array
    * @return json data
    */
    public function updateFinishingNotes()
    {
        $post = Input::all();
        $result = $this->finishing->updateFinishingNotes($post['data'][0]);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }

    /**
    * Delete finishing note tab record.
    * @params note_id
    * @return json data
    */
    public function deleteFinishingNotes($id)
    {
        $result = $this->finishing->deleteFinishingNotes($id);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }



   /**
   * Save Finishing notes.
   * @return json data
    */
    public function saveFinishingNotes()
    {

        $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');
 
        if(!empty($post['data']['finishing_id']) && !empty($post['data']['finishing_notes']))
        {
            $result = $this->finishing->saveFinishingNotes($post['data']);
            $message = INSERT_RECORD;
            $success = 1;
        }
        else
        {
            $message = MISSING_PARAMS.", id";
            $success = 0;
        }
        
        $data = array("success"=>$success,"message"=>$message);
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