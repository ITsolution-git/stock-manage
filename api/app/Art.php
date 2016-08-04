<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Common;

class Art extends Model {


	public function __construct(Common $common) 
    {
        $this->common = $common;
    }


	public function Listing($post)
	{

		$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $admindata = DB::table('orders as ord')
        				->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
        				->select(DB::raw('SQL_CALC_FOUND_ROWS ord.id,cl.client_company'),DB::raw("(SELECT count(*) from artjob_screensets ass WHERE ass.order_id = ord.id AND ass.screen_active='1') as total_screen"))
        				->where('ord.is_delete','=','1')
		                ->where('ord.company_id','=',$post['company_id']);
		                if($search != '')               
		                 {
		                     $admindata = $admindata->Where(function($query) use($search)
		                     {
		                         $query->orWhere('ord.id', 'LIKE', '%'.$search.'%')
		                               ->orWhere('cl.client_company','LIKE', '%'.$search.'%');
		                     });
		                }
		                $admindata = $admindata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
		                ->skip($post['start'])
		                ->take($post['range'])
		                ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $admindata;
        $returnData['count'] = $count[0]->Totalcount;
        

       // echo "<pre>"; print_r($returnData); echo "</pre>"; die;
        return $returnData;
	}
	
	
	public function AllMsiData($compay_id)
    {
    	$query = DB::table('misc_type')->where('company_id','=',$compay_id)->select('id','value','company_id')->get();
    	$ret_array = array();
    	foreach ($query as $key => $value) {
    		$ret_array[$value->id] = $value->value;
    	}

    	//echo "<pre>"; print_r($query); echo "</pre>"; die;
    	return $ret_array;
    }
    
    public function ScreenSets($post) // ART SCREEN DETAIL PAGE FOR SCREEN SETS
	{
		$query = DB::table('artjob_screensets as ass')
				->select('or.name as order_name','or.created_date','cc.first_name','cc.last_name','cl.client_id','cl.client_company','mt.value as position_name','ass.screen_count','ass.screen_set','ass.id as screen_id','odp.color_stitch_count','ass.frame_size','ass.line_per_inch','ass.screen_width','ass.screen_height','ass.screen_location','ass.screen_active','ass.order_id',DB::raw("(odp.color_stitch_count+odp.foil_qnty) as screen_total"))
				->join('art as art','art.order_id','=','ass.order_id')
				->join('orders as or','art.order_id','=','or.id')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
				->where('or.is_delete','=','1')
				->where('odp.is_delete','=','1')
				->where('or.company_id','=',$post['company_id'])
				->where('or.id','=',$post['order_id'])
				->get();

		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$value->created_date = date("m/d/Y",strtotime($value->created_date));
			}
		}
		return $query;
	}

	// CLIENT MODULE ART LISTING.
    public function Client_art_screen($client_id,$company_id)
    {
    	$Misc_data = $this->AllMsiData($company_id);

    	$query = DB::table('orders as or')
		->select('or.id as order_id','art.art_id','ass.graphic_size','ass.screen_logo','aaw.id as wp_id','ass.id as screen_id','ass.screen_set','aaw.wp_image')
		->join('art as art','art.order_id','=','or.id')
		->leftJoin('order_orderlines as ol','ol.order_id','=','or.id')
		->leftJoin('artjob_artworkproof as aaw','ol.id','=','aaw.orderline_id')
		->leftJoin('artjob_screensets as ass','ass.art_id','=','art.art_id')
		->where('or.is_delete','=','1')
		->where('or.company_id','=',$company_id)
		->where('or.client_id','=',$client_id)
		->get();
		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		$client_array = array();
		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				if(!empty($value->screen_id))
				{
					$client_array['screen'][$value->screen_id]['screen_set'] = $value->screen_set; 		
					$client_array['screen'][$value->screen_id]['graphic_size'] = (!empty($value->graphic_size))? $Misc_data[$value->graphic_size] : '';		
					$client_array['screen'][$value->screen_id]['screen_logo'] = (!empty($value->screen_logo))? UPLOAD_PATH.$company_id.'/art/'.$value->screen_id.'/'.$value->screen_logo : '';
					$client_array['screen'][$value->screen_id]['art_id'] = $value->art_id; 	
					$client_array['screen'][$value->screen_id]['screen_id'] = $value->screen_id; 	
				} 
				if(!empty($value->wp_id))
				{
					$client_array['art'][$value->wp_id]['wp_image'] = (!empty($value->wp_image))? UPLOAD_PATH.$company_id.'/art/'.$value->wp_id.'/'.$value->wp_image : '';
					$client_array['art'][$value->wp_id]['type'] = 'Art Work Screen'; 
					$client_array['art'][$value->wp_id]['art_id'] = $value->art_id;		
					$client_array['art'][$value->wp_id]['wp_id'] = $value->wp_id; 				
				}
			}
		}

		return $client_array;
    }

    // ART SCREEN SETS LISTING, ORDER POSITIONS
    public function Screen_Listing ($post)
    {
    			$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        $admindata = DB::table('order_design_position as odp')
					->select(DB::raw('SQL_CALC_FOUND_ROWS asc.screen_set,odp.id,odp.color_stitch_count,cl.client_company,mt.value,asc.screen_width'),DB::raw("(color_stitch_count+foil_qnty) as screen_total"))
					->join('artjob_screensets as asc','asc.positions','=','odp.id')
					->join('order_design as od','od.id','=','odp.design_id')
					->join('orders as ord','ord.id','=','od.order_id')
					->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
					->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
					->where('ord.is_delete','=','1')
					->where('odp.is_delete','=','1')
			        ->where('ord.company_id','=',$post['company_id']);
		            
		            if($search != '')               
	                {
	                    $admindata = $admindata->Where(function($query) use($search)
	                    {
	                        $query->orWhere('ord.id', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('asc.screen_width', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('cl.client_company','LIKE', '%'.$search.'%');

	                    });
	                }
	                $admindata = $admindata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
	                ->skip($post['start'])
	                ->take($post['range'])
	                ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $admindata;
        $returnData['count'] = $count[0]->Totalcount;
        

        //echo "<pre>"; print_r($returnData); echo "</pre>"; die;
        return $returnData;
    }
    
    //ARTDETAIL PAGE SCREEN SETS LISTING.
    public function GetScreenset_detail($position_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select(DB::raw("(odp.color_stitch_count+odp.foil_qnty) as screen_total"),'ord.id as order_id','od.id as design_id','odp.color_stitch_count','mt.value','ass.*')
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->join('order_design as od','odp.design_id','=','od.id')
				->join('orders as ord','ord.id','=','od.order_id')
				->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
				->where('ass.id','=',$position_id)
				->groupby('ass.id')
				->get();

				return $query;
    }
    // CREATE/ACTIVE SCREEN SETS
    public function create_screen($post)
    {
    	//echo "<pre>"; print_r($post); echo "</pre>"; die;
    	$alldata = $post['alldata'];
    	if(isset($alldata['screen_width']))
    	{
    		$value = str_replace(" ","",strtolower(trim($alldata['value'])));
    		$screen_set_name = $alldata['order_id']."_".$value."_".$alldata['design_id']."_".$alldata['screen_width']; 
    	}
    	$result = DB::table('artjob_screensets')->where('id','=',$alldata['id'])->update(array('screen_set'=>$screen_set_name,'screen_active'=>'1','frame_size'=>$alldata['frame_size'],'screen_location'=>$alldata['screen_location'],'line_per_inch'=>$alldata['line_per_inch'],'screen_date'=>date('Y-m-d'),'screen_width'=>$alldata['screen_width'],'screen_height'=>$alldata['screen_height']));

    	if(!empty($post['add_screen_color']))
    	{
    		foreach ($post['add_screen_color'] as $value) 
    		{
    			$result = $this->common->InsertRecords('artjob_screencolors',array("screen_id"=>$alldata['id'],'color_name'=>$value['id']));
    		}
    	}
    	if(!empty($post['remove_screen_color']))
    	{
    		foreach ($post['remove_screen_color'] as $value) 
    		{
    			$result = $this->common->DeleteTableRecords('artjob_screencolors',array('id'=>$value['id']));
    		}
    	}
    	return $result;
    }

 	//SCREEN SETS DETAIL PAGE COLOR LISTING
    public function GetscreenColor($screen_id)
    {
    	$query = DB::table('artjob_screencolors as acol')
				->select('or.name as order_name','or.company_id','or.id as order_id','or.created_date','cc.first_name','cc.last_name','cl.client_id','cl.client_company','ass.screen_set','ass.id as screen_id','ass.mokup_image','acol.*')
				->join('artjob_screensets as ass','acol.screen_id','=','ass.id')
				->join('orders as or','ass.order_id','=','or.id')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->where('ass.id','=',$screen_id)
				->groupby('acol.id')
				->orderBy('acol.head_location','asc')
				->get();
				return $query;
    }
    public function UpdateColorScreen($post)
    {
    	if(!empty($post['thread_display']['id']))
    	{
    		$post['thread_color'] = $post['thread_display']['id'];
    	}
    	else if(empty($post['thread_display']))
    	{
    		$post['thread_color'] ='';
    	}

    	$result = DB::table('artjob_screencolors')
    				->where('id','=',$post['id'])
    				->update(array('thread_color'=>$post['thread_color'],
    							   'inq'=>$post['inq'],
    							   'stroke'=>$post['stroke'],
    							   'squeegee'=>$post['squeegee'],
    							   'mesh_thread_count'=>$post['mesh_thread_count'],
    							   'head_location'=>$post['head_location']
    							   ));
    				return $result;
    }

}