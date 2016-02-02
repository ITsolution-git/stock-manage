<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use App\Art;
use DB;

use Request;

class ArtController extends Controller { 

	public function __construct(Art $art,Common $common) 
 	{
        $this->art = $art;
        $this->common = $common;
    }

    public function listing($company_id)
    {
    	if(!empty($company_id) 	&& $company_id != 'undefined')
    	{
    		
        	
    		$result = $this->art->listing($company_id);
    		if(count($result)>0)
    		{
    			$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS);
    		}
    		
		}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }
    public function Art_detail($art_id,$company_id)
    {
    	if(!empty($company_id) && !empty($art_id)	&& $company_id != 'undefined')
    	{
    		$art_position = $this->art->art_position($art_id,$company_id);
    		$art_orderline = $this->art->art_orderline($art_id,$company_id);

    		$art_array  = array('art_position'=>$art_position,'art_orderline'=>$art_orderline);
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $art_array);
		}
    	else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }

}