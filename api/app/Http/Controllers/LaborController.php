<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Labor;
use App\Common;
use DB;
use App;
use Request;
use Response;



class LaborController extends Controller { 

    public function __construct(Labor $labor,Common $common)
    {
        parent::__construct();
        $this->labor = $labor;
        $this->common = $common;
    }



/** 
 * @SWG\Definition(
 *      definition="laborDetail",
 *      type="object",
 *      required={"company_id", "id"},
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/labor/laborDetail",
 *  summary = "Labor Detail",
 *  tags={"Labor"},
 *  description = "Labor Detail",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Labor Detail",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/laborDetail")
 *  ),
 *  @SWG\Response(response=200, description="Labor Detail"),
 *  @SWG\Response(response="default", description="Labor Detail"),
 * )
 */
    public function laborDetail() {
 
        $data = Input::all();

        $result = $this->labor->laborDetail($data);

         if(empty($result['labor']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }


        if (count($result) > 0) {
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['labor']
                                );
        } else {
            $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS,
                                'records' => $result['labor']
                            );
        } 
        return response()->json(["data" => $response]);

    }

      public function editLabor()
    {
        $post = Input::all();
      
        $post['laborData']['apply_days'] = implode(',', $post['laborData']['days_array']);
        unset($post['laborData']['days_array']);

       $this->common->UpdateTableRecords($post['table'],$post['cond'],$post['laborData']);
            $data = array("success"=>1,"message"=>UPDATE_RECORD);
            return response()->json(['data'=>$data]);

       $data = array("success"=>1,"message"=>INSERT_RECORD);
       return response()->json(['data'=>$data]);

    }

    public function addLabor()
    {
        $post = Input::all();

        $post['laborData']['apply_days'] = implode(',', $post['laborData']['days_array']);
        unset($post['laborData']['days_array']);
      
        $labor_id = $this->common->InsertRecords('labor',$post['laborData']);

       $data = array("success"=>1,"message"=>INSERT_RECORD,"id"=>$labor_id);
       return response()->json(['data'=>$data]);

    }

   
}