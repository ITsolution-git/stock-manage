<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');

use App\Login;
use App\Api;
use Mail;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use DB;
use Image;
use Request;



class ApiController extends Controller {  

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(Login $login, Api $api) {

        parent::__construct();
        $this->login = $login;
        $this->api = $api;
      
    }

    /**
     * GET all API for Company
     *
     * @param  $url details
     * @return Response
     */

    /**
 * @SWG\Get(
 *  path = "/api/public/api/GetCompanyApi/{company_id}",
 *  summary = "Get Company API Listing",
 *  tags={"API"},
 *  description = "Get Company API Listing",
 *  @SWG\Parameter(
 *     in="path",
 *     name="company_id",
 *     description="Get Company API Listing",
 *     type="integer",
 *     required=true
 *  ),
 *  @SWG\Response(response=200, description="Get Company API Listing"),
 *  @SWG\Response(response="default", description="Get Company API Listing"),
 * )
 */
    public function GetCompanyApi($company_id) 
    {
        if(!empty($company_id))
        {
            $records = $this->api->GetCompanyApi($company_id);
            if(count($records)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$records);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
            
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return response()->json(["data" => $response]);
    }
    /**
     * GET all API for Company
     *
     * @param  $url details
     * @return Response
     */
    public function getApiData() 
    {
        $post = Input::all();
       
        if(!empty($post['id']))
        {
            $records = $this->api->getApiData($post);
            if(count($records)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$records);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return response()->json(["data" => $response]);
    }


    /**
     * Check  API for Company
     *
     * @param  $url details
     * @return Response
     */
    public function checkApi() 
    {
         $post = Input::all();
        
        if(!empty($post['api_id']))
        {
            $records = $this->api->checkApi($post);
            if(count($records)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$records);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
            
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return response()->json(["data" => $response]);
    }

  

}