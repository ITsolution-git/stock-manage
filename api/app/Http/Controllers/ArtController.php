<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use App\Art;
use DB;
use File;

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
			$artjobgroup_list = $this->art->artjobgroup_list($art_id,$company_id); // GROUP LIST DATA

			$art_worklist = $this->art->art_worklist($art_id,$company_id);  // ART WORK LISTING DATA

			$graphic_size = $this->common->GetMicType('graphic_size');
			//$allcolors = $this->common->getAllColorData();
			$wp_position = $this->common->GetMicType('position');
			$art_approval = $this->common->GetMicType('approval');

    		$art_array  = array('art_position'=>$art_position,'art_orderline'=>$art_orderline,'artjobscreen_list'=>$artjobscreen_list,'graphic_size'=>$graphic_size,'artjobgroup_list'=>$artjobgroup_list,'art_worklist'=>$art_worklist,'wp_position'=>$wp_position,'art_approval'=>$art_approval);
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
    			$art_workproof[0]->logo_image =  UPLOAD_PATH.'art/'.$art_workproof[0]->art_id.'/'.$art_workproof[0]->wp_image;
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
    public function ScreenListing($company_id)
    {
    	if(!empty($company_id) && $company_id != 'undefined')
    	{
    		$scren_listing = $this->art->ScreenListing($company_id);
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
    		//echo FILEUPLOAD; die;
    		$post['save_image'] = $this->Ret_imageUrl($post['wp_image'],'Artwork-logo','art/'.$post['art_id']);

    		$this->art->SaveArtWorkProof($post);
    		$response = array('success' => 1, 'message' => UPDATE_RECORD);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    public function Ret_imageUrl($image_array,$image_name,$path)
    {
    	$png_url='';
    	if(!empty($image_array['base64'])){

            	$split = explode( '/',$image_array['filetype'] );
                $type = $split[1]; 

		        $png_url = $image_name."-".time().".".$type;
				$path = FILEUPLOAD.$path;
				
				if (!file_exists($path)) {
			            mkdir($path, 0777, true);
			        } else {
			         exec("chmod $path 0777");
			           // chmod($dir_path, 0777);
			        }
				$path = $path."/".$png_url;		
				$img = $image_array['base64'];
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
	    	}
	    	return $png_url;
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
    		$screen_arts = $this->art->screen_arts($screen_id,$company_id);
    		$screen_garments = $this->art->screen_garments($screen_id,$company_id);
    		$art_approval = $this->common->GetMicType('approval');
    		$color_array= array();
    		foreach ($allcolors as $key => $value) 
			{
				$color_array[$value->id]= $value->name;
				$allcolors[$key]->name = strtolower($value->name);
			}
			$screen_colorpopup[0]->logo_image = (!empty($screen_colorpopup[0]->screen_logo))? UPLOAD_PATH.'art/'.$screen_colorpopup[0]->art_id.'/'.$screen_colorpopup[0]->screen_logo:'';
    		//echo "<pre>"; print_r($allcolors); echo "</pre>"; die;
    		if(count($screen_colorpopup)>0)
			{
				foreach ($screen_colorpopup as $key => $value) 
				{
					$screen_colorpopup[$key]->color_name = (!empty($value->color_name))? $color_array[$value->color_name]:'';
					$screen_colorpopup[$key]->thread_color = (!empty($value->thread_color))? $color_array[$value->thread_color]:'';
				}
			}	

    		$ret_array = array('screen_colorpopup'=>$screen_colorpopup,'allcolors'=>$allcolors,'graphic_size'=>$graphic_size,'screen_garments'=>$screen_garments,'art_approval'=>$art_approval);
    		
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
    public function create_screen()
    {
    	$post = Input::all();

    	//echo "<pre>"; print_r($post['data']['art_id']); echo "</pre>"; die;
    	if(!empty($post['data']['art_id']))
    	{
    		$this->art->create_screen($post['data']);
    		$response = array('success' => 1, 'message' => INSERT_RECORD);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);

    }
    public function DeleteScreenRecord()
    {
    	$post = Input::all();

    	//echo "<pre>"; print_r($post['data']['art_id']); echo "</pre>"; die;
    	if(!empty($post['cond']['id']))
    	{
    		$this->art->DeleteScreenRecord($post['cond']);
    		$response = array('success' => 1, 'message' => DELETE_RECORD);
    	}
    	else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function art_worklist_listing($art_id,$company_id)
    {
    	if(!empty($company_id) && !empty($art_id)	&& $company_id != 'undefined')
    	{
    		$art_worklist = $this->art->art_worklist($art_id,$company_id);  // ART WORK LISTING DATA
    		$art_position = $this->art->art_position($art_id,$company_id);

    		$response = array('success' => 1, 'message' => GET_RECORDS,'art_worklist' => $art_worklist,'art_position'=>$art_position);
    	}
    	else 
        {
            $response = array('success' => 2, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
}