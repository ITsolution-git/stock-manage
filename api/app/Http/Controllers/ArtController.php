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

    // ART LISTING PAGE
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

    //ARTJOB-  ART DETAIL TAB WITH POSITION AND ORDERLINE TAB DATA.
    public function Art_detail($art_id,$company_id)
    {
    	if(!empty($company_id) && !empty($art_id)	&& $company_id != 'undefined')
    	{
    		$art_position = $this->art->art_position($art_id,$company_id);
    		$art_orderline = $this->art->art_orderline($art_id,$company_id);
			$artjobscreen_list = $this->art->artjobscreen_list($art_id,$company_id);  // SCREEN LISTING DATA
			$graphic_size = $this->common->GetMicType('graphic_size');
			$artjobgroup_list = $this->art->artjobgroup_list($art_id,$company_id);

			$art_worklist = $this->art->art_worklist($art_id,$company_id);  // ART WORK LISTING DATA
			$allcolors = $this->common->getAllColorData();
			$wp_position = $this->common->GetMicType('position');
			foreach ($allcolors as $key => $value) {
				$allcolors[$key]->name = strtolower($value->name);
			}

    		$art_array  = array('art_position'=>$art_position,'art_orderline'=>$art_orderline,'artjobscreen_list'=>$artjobscreen_list,'graphic_size'=>$graphic_size,'artjobgroup_list'=>$artjobgroup_list,'art_worklist'=>$art_worklist,'allcolors'=>$allcolors,'wp_position'=>$wp_position);
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $art_array);
		}
    	else 
        {
            $response = array('success' => 2, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    //ARTJOB-  ART WORKPROOF POPUP DATA RETRIVE
    public function artworkproof_data($wp_id, $company_id)
    {
    	if(!empty($company_id) && !empty($wp_id)	&& $company_id != 'undefined')
    	{
    		$art_workproof = $this->art->artworkproof_data($wp_id,$company_id);
    		if(count($art_workproof)>0)
    		{
	    		$art_id = $art_workproof[0]->art_id;
	    		$get_artworkproof_placement = $this->art->get_artworkproof_placement($art_id,$company_id);

	    		


	    		$ret_array = array('art_workproof'=>$art_workproof,'get_artworkproof_placement'=>$get_artworkproof_placement);
	    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $ret_array);
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
        return  response()->json(["data" => $response]);
    }

    // ARTJOB-  SCREEN SETS TAB DATA LISTING
    public function artjobscreen_list($art_id, $company_id)
    {
    	if(!empty($company_id) && !empty($art_id)	&& $company_id != 'undefined')
    	{
    		$artjobscreen_list = $this->art->artjobscreen_list($art_id,$company_id);
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $artjobscreen_list);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    // ARTJOB-  GROUP TAB DATA LISTING
    public function artjobgroup_list($art_id, $company_id)
    {
    	if(!empty($company_id) && !empty($art_id)	&& $company_id != 'undefined')
    	{
    		$artjobgroup_list = $this->art->artjobgroup_list($art_id,$company_id);
    		$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $artjobgroup_list);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function update_orderScreen()
    {
    	$post = Input::all();
    	if(!empty($post['data']) && !empty($post['cond']))
    	{
    		$artjobgroup_list = $this->art->update_orderScreen($post);
    		$response = array('success' => 1, 'message' => UPDATE_RECORD);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);

    }
    public function ScreenListing($art_id,$company_id)
    {
    	if(!empty($company_id) && $company_id != 'undefined')
    	{
    		$scren_listing = $this->art->ScreenListing($art_id,$company_id);
    		if(count($scren_listing)>0)
    		{
    			$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $scren_listing);
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS);
    		}
    	}
    	else 
        {
            $response = array('success' => 2, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function SaveArtWorkProof()
    {
    	$post = Input::all();
    	//echo "<pre>"; print_r($post); echo "</pre>"; die;
    	if(!empty($post['wp_id']))
    	{
    		$val = array_filter($post['wp_placement']);
    		$post['wp_placement'] = implode(",", $val);
    		//echo "<pre>"; print_r($post['wp_placement']); echo "</pre>"; die;
    		$this->art->SaveArtWorkProof($post);
    		$response = array('success' => 1, 'message' => UPDATE_RECORD);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function Client_art_screen($client_id,$company_id)
    {
    	if(!empty($company_id) && !empty($client_id)	&& $company_id != 'undefined')
    	{
    		$Client_art_screen = $this->art->Client_art_screen($client_id,$company_id);
    		if(count($Client_art_screen)>0)
    		{
    			$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $Client_art_screen);
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS,'records' => $Client_art_screen);
    		}
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function Insert_artworkproof($line_id)
    {
    	if(!empty($line_id) && $line_id != 'undefined')
    	{
    		$wp_id = $this->art->Insert_artworkproof($line_id);
    		$response = array('success' => 1, 'message' => INSERT_RECORD,'records'=>$wp_id);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function screen_colorpopup ($screen_id,$company_id)
    {
    	if(!empty($company_id) && !empty($screen_id)	&& $company_id != 'undefined')
    	{
    		$screen_colorpopup = $this->art->screen_colorpopup($screen_id,$company_id);
    		$allcolors = $this->common->getAllColorData();
    		$graphic_size = $this->common->GetMicType('graphic_size');


    		foreach ($allcolors as $key => $value) 
			{
				$allcolors[$value->id]= $value->name;
			}

    		//echo "<pre>"; print_r($allcolors); echo "</pre>"; die;
    		if(count($screen_colorpopup)>0)
			{
				foreach ($screen_colorpopup as $key => $value) 
				{
					$screen_colorpopup[$key]->color_name = (!empty($value->color_name))? $allcolors[$value->color_name]:'';
					$screen_colorpopup[$key]->thread_color = (!empty($value->thread_color))? $allcolors[$value->thread_color]:'';
				}
			}	

    		$ret_array = array('screen_colorpopup'=>$screen_colorpopup,'graphic_size'=>$graphic_size);
    		
    		if(count($screen_colorpopup)>0)
    		{
    			$response = array('success' => 1, 'message' => GET_RECORDS,'records' => $ret_array);
    		}
    		else
    		{
    			$response = array('success' => 0, 'message' => NO_RECORDS,'records' => $ret_array);
    		}

    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
}