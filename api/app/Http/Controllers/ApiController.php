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

        $this->login = $login;
        $this->api = $api;
      
    }

    /**
     * GET all API for Company
     *
     * @param  $url details
     * @return Response
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
    public function GetSNSData($snsid,$company_id) 
    {
    	if(!empty($company_id) && !empty($snsid))
        {
    		$records = $this->api->GetSNSData($snsid,$company_id);
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

    public function save_api()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']))
    	{
			$records = $this->api->save_api($post);
			$response = array('success' => 1, 'message' => UPDATE_RECORD);
		}
		else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return response()->json(["data" => $response]);
    }

}