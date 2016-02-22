<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Art extends Model {

	public function Listing($company_id)
	{
		$Misc_data = $this->AllMsiData($company_id);

		$query = DB::table('art as art')
				->select('*')
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('client as cl','cl.client_id','=','or.client_id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->get();
				
		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$query[$key]->f_approval = (!empty($value->f_approval))?$Misc_data[$value->f_approval]:'';
			}
		}
		
		return $query;
	}
	public function art_position($art_id,$company_id)
	{
		$Misc_data = $this->AllMsiData($company_id);

		$query = DB::table('art as art')
				->select('op.*','art.art_id','art.notes','art.mokup_image','cl.client_company','or.job_name','or.id as order_id','or.grand_total','or.f_approval')
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('order_positions as op','op.order_id','=','or.id')
				->leftJoin('client as cl','cl.client_id','=','or.client_id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('art.art_id','=',$art_id)
				->get();

		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				//$query[$key]->f_approval = (!empty($value->f_approval))? $Misc_data[$value->f_approval]:'';
				$query[$key]->placement_type = (!empty($value->placement_type))? $Misc_data[$value->placement_type]:'';
				$query[$key]->position_id = (!empty($value->position_id))? $Misc_data[$value->position_id] : '';
				$query[$key]->dtg_size = (!empty($value->dtg_size))? $Misc_data[$value->dtg_size]:'';
				$query[$key]->dtg_on =  (!empty($value->dtg_on))?$Misc_data[$value->dtg_on]:'';
				$query[$key]->mokup_display_image =  (!empty($value->mokup_image))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->mokup_image:'';
			}
		}
		return $query;
	}
	public function art_orderline($art_id,$company_id)
	{
		$Misc_data = $this->AllMsiData($company_id);
		$ret_array = array();
		$query = DB::table('art as art')
				->select('or.job_name','art.art_id','or.grand_total','or.f_approval','oo.id as line_id','oo.size_group_id','oo.qnty as ordline_qnty','oo.client_supplied','cl.name as product_color','pr.name as product_name','vn.name_company','pd.size', 'pd.qnty','pd.id as sizeid','pd.art_group')
				->join('orders as or','art.order_id','=','or.id')
				->join('order_orderlines as oo','oo.order_id','=','or.id')
				->join('purchase_detail as pd','pd.orderline_id','=','oo.id')
				->leftJoin('color as cl','cl.id','=','oo.color_id')
				->leftjoin('products as pr','pr.id','=','oo.product_id')
				->leftjoin('vendors as vn','vn.id','=','oo.vendor_id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('art.art_id','=',$art_id)
				->get();

		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		if(count($query)>0)
		{
			$kk = 0;
			$temp = '';
			$lock = '';
			$line_count = -1;
			foreach ($query as $key => $value) 
			{
				if($value->size != '' && $value->qnty != '')
				{
					$temp = $value->line_id;
					if($temp != $lock)
					{
						$line_count++;
						$ret_array['line_array'][$line_count]['job_name'] = $value->job_name;
						$ret_array['line_array'][$line_count]['ordline_qnty'] = $value->ordline_qnty;
						$ret_array['line_array'][$line_count]['client_supplied'] = $value->client_supplied;
						$ret_array['line_array'][$line_count]['line_id'] = $value->line_id;
						$ret_array['line_array'][$line_count]['product_color'] = $value->product_color;
						$ret_array['line_array'][$line_count]['product_name'] = $value->product_name;
						$ret_array['line_array'][$line_count]['name_company'] = $value->name_company;
						$ret_array['line_array'][$line_count]['size_group_id'] = (!empty($value->size_group_id))?$Misc_data[$value->size_group_id]:'';

						$kk = 0;
						
						$lock = $temp;
					}
					$ret_array['line_array'][$line_count]['size_array'][$kk]['art_group'] = (!empty($value->art_group))?$value->art_group:'';
					$ret_array['line_array'][$line_count]['size_array'][$kk]['sizeid'] = (!empty($value->sizeid))?$value->sizeid:'';
    				$ret_array['line_array'][$line_count]['size_array'][$kk]['size'] = (!empty($value->size))?$value->size:'';
					$ret_array['line_array'][$line_count]['size_array'][$kk]['qnty'] = (!empty($value->qnty))?$value->qnty:'';
					$kk ++;
			    }
			}
		}

		//echo "<pre>"; print_r($ret_array); echo "</pre>"; die;
		return $ret_array;
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
    public function artworkproof_data($wp_id, $company_id)
    {
    	$Misc_data = $this->AllMsiData($company_id);
    	//$position_data = $this->AllMsiData($company_id);
    	$query = DB::table('order_orderlines as oo')
				->select('or.job_name','or.id as order_id','art.art_id','or.grand_total','or.f_approval','oo.id as line_id','oo.size_group_id','cl.name as product_color','pr.name as product_name','cln.client_company',DB::raw("GROUP_CONCAT(pl.misc_value) as placement_name"),'aaw.id as wp_id','wp_position','aaw.wp_desc','aaw.wp_screen','aaw.wp_placement','aaw.wp_image')
				->join('orders as or','oo.order_id','=','or.id')
				->leftJoin('art as art','art.order_id','=','or.id')
				->leftJoin('client as cln','cln.client_id','=','or.client_id')
				->leftJoin('color as cl','cl.id','=','oo.color_id')
				->leftjoin('products as pr','pr.id','=','oo.product_id')
				->leftJoin('artjob_artworkproof as aaw','aaw.orderline_id','=','oo.id')
				->leftJoin('placement as pl',DB::raw("FIND_IN_SET(pl.id,aaw.wp_placement)"),DB::raw(''),DB::raw(''))
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('aaw.id','=',$wp_id)
				->GroupBy('aaw.id')
				->get();

		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$query[$key]->wp_placement = explode(",",$value->wp_placement);
				$query[$key]->f_approval = (!empty($value->f_approval))? $Misc_data[$value->f_approval]:'';
				$query[$key]->size_group_id = (!empty($value->size_group_id))?$Misc_data[$value->size_group_id]:'';
			}
		}
		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		return $query;
    }

    public function artjobscreen_list($art_id,$company_id)
    {
    	//$Misc_data = $this->AllMsiData($company_id);	
		$query = DB::table('artjob_screensets as ass')
				->select('ass.*')
				->join('art as art','art.art_id','=','ass.art_id')
				->join('orders as ord','ord.id','=','art.order_id')
				->where('ord.company_id','=',$company_id)
				->where('ass.art_id','=',$art_id)
				->get();
				
		return $query;
    }

    public function artjobgroup_list($art_id,$company_id)
    {
		$query = DB::table('artjob_ordergroup as aog')
				->select('aog.*',DB::raw("GROUP_CONCAT(ass.screen_set) as screen_set"),DB::raw("(SELECT COUNT(art_group) FROM purchase_detail WHERE order_id= ord.id AND art_group= aog.id AND size<>'') as group_count"))
				->join('art as art','art.art_id','=','aog.art_id')
				->join('orders as ord','ord.id','=','art.order_id')
				->leftJoin('artjob_screensets as ass',DB::raw("FIND_IN_SET(ass.id,aog.screen_sets)"),DB::raw(''),DB::raw(''))
				->where('ord.company_id','=',$company_id)
				->where('aog.art_id','=',$art_id)
				->GroupBy('aog.id')
				->get();
		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$query[$key]->screen_array = explode(",",$value->screen_sets);
			}
		}
		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		return $query;
    }
    public function update_orderScreen($post)
    {
    	$fliter  =  array_filter($post['data']);
    	$data = implode(',',$fliter);
    	$result = DB::table('artjob_ordergroup')->where('id','=',$post['cond']['id'])->update(array("screen_sets" => $data));
		return $result;
    }
    public function ScreenListing($company_id)
	{
		$Misc_data = $this->AllMsiData($company_id);
		$query = DB::table('artjob_screensets as ass')
				->select('or.id','or.job_name','ass.screen_count','ass.screen_set','ass.graphic_size','art.art_id','ass.id as screen_id')
				->join('art as art','art.art_id','=','ass.art_id')
				->join('orders as or','art.order_id','=','or.id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->get();
			

		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$query[$key]->graphic_size = (!empty($value->graphic_size))?$Misc_data[$value->graphic_size]:'';
			}
		}
		return $query;
	}
	public function get_artworkproof_placement($art_id,$company_id)
    {
    	$query = DB::table('art as art')
				->select('or.id as order_id','or.job_name','art.art_id',DB::raw("GROUP_CONCAT(op.placementvalue) as proof_placementvalue"),DB::raw("GROUP_CONCAT(pl.misc_value) as placement_name"))
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('order_positions as op','op.order_id','=','or.id')
				->leftJoin('placement as pl',DB::raw("FIND_IN_SET(pl.id,op.placementvalue)"),DB::raw(''),DB::raw(''))
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('art.art_id','=',$art_id)
				->GroupBy('art.art_id')
				->get();

		$temp_array = array();
		if(count($query)>0)
		{
			$change_value =  explode(',',$query[0]->proof_placementvalue);
			$change_value = array_filter($change_value);
			$change_value = array_unique($change_value);
			$temp_array = array_values($change_value);
			$query[0]->proof_placementvalue = $temp_array;

			$change_value =  explode(',',$query[0]->placement_name);
			$change_value = array_filter($change_value);
			$query[0]->placement_name = array_unique($change_value);

			//echo "<pre>"; print_r($query); echo "</pre>"; die;
		}
		
		return $query;
    }
    public function SaveArtWorkProof($post)
    {
    	$save_array = array("wp_position" => $post['wp_position'],"wp_desc" => $post['wp_desc'],'wp_screen'=>$post['wp_screen'],'wp_image'=>$post['save_image'],'wp_placement'=>$post['wp_placement']);
    	if(empty($post['save_image'])){
    		unset($save_array['wp_image']);
    	}
    	$result = DB::table('artjob_artworkproof')->where('id','=',$post['wp_id'])->update($save_array);
    	return $result;
    }
    public function art_worklist($art_id,$company_id)
    {	
    			$Misc_data = $this->AllMsiData($company_id);

    	    	$query = DB::table('art as art')
				->select('or.id as order_id','or.job_name','art.art_id',DB::raw("GROUP_CONCAT(pl.misc_value) as placement_name"),'cl.name as product_color','ol.size_group_id','ol.color_id','ass.screen_set','ol.id as line_id','aaw.*')
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('order_orderlines as ol','ol.order_id','=','or.id')
				->join('artjob_artworkproof as aaw','ol.id','=','aaw.orderline_id')
				->leftJoin('color as cl','cl.id','=','ol.color_id')
				->leftJoin('placement as pl',DB::raw("FIND_IN_SET(pl.id,aaw.wp_placement)"),DB::raw(''),DB::raw(''))
				->leftJoin('artjob_screensets as ass','ass.id','=','aaw.wp_screen')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('art.art_id','=',$art_id)
				->GroupBy('aaw.id')
				->get();

				if(count($query)>0)
				{
					foreach ($query as $key => $value) 
					{
						$query[$key]->size_group_id = (!empty($value->size_group_id))? $Misc_data[$value->size_group_id] : '';
						$query[$key]->wp_position = (!empty($value->wp_position))? $Misc_data[$value->wp_position] : '';
						$query[$key]->wp_image_display = (!empty($value->wp_image))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->wp_image : '';
					}
				}

				return $query;
    }
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
					$client_array['screen'][$value->screen_id]['screen_logo'] = (!empty($value->screen_logo))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->screen_logo : '';
					$client_array['screen'][$value->screen_id]['art_id'] = $value->art_id; 	
					$client_array['screen'][$value->screen_id]['screen_id'] = $value->screen_id; 	
				} 
				if(!empty($value->wp_id))
				{
					$client_array['art'][$value->wp_id]['wp_image'] = (!empty($value->wp_image))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->wp_image : '';
					$client_array['art'][$value->wp_id]['type'] = 'Art Work Screen'; 
					$client_array['art'][$value->wp_id]['art_id'] = $value->art_id;		
					$client_array['art'][$value->wp_id]['wp_id'] = $value->wp_id; 				
				}
			}
		}

		return $client_array;
    }
    public function Insert_artworkproof($line_id)
    {
    	$result = DB::table('artjob_artworkproof')->insert(array("orderline_id"=>$line_id));

    	$wp_id = DB::getPdo()->lastInsertId();
    	return $wp_id;
    }
    public function screen_colorpopup ($screen_id,$company_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select('ord.id as order_id','ord.f_approval','asc.*','asc.id as color_id','ass.*','ass.id as screen_id',DB::raw('SUM(ol.qnty) as total_qnty'),'art.art_id')
				->join('art as art','art.art_id','=','ass.art_id')
				->join('orders as ord','ord.id','=','art.order_id')
				->join('order_orderlines as ol','ol.order_id','=','ord.id')
				->leftjoin('artjob_screencolors as asc','asc.screen_id','=','ass.id')
				->where('ord.company_id','=',$company_id)
				->where('ass.id','=',$screen_id)
				->groupby('asc.id')
				->get();

		return $query;
    }
    public function create_screen($post)
    {
    	$result = DB::table('artjob_screensets')->insert(array("art_id"=>$post['art_id'],'screen_date'=>date('Y-m-d')));
    	$screen_id = DB::getPdo()->lastInsertId();

    	$result = DB::table('artjob_screencolors')->insert(array("screen_id"=>$screen_id));

    	return $screen_id;
    }
    public function DeleteScreenRecord($post)
    {
    	$result = DB::table('artjob_screensets')->where('id','=',$post['id'])->Delete();
    	$result = DB::table('artjob_screencolors')->where('screen_id','=',$post['id'])->Delete();

    	return $result;
    }
    public function screen_arts ($screen_id,$company_id)
    {
    	$Misc_data = $this->AllMsiData($company_id);
    	$query = DB::table('artjob_screensets as ass')
				->select('aaw.*',DB::raw("GROUP_CONCAT(pl.misc_value) as wp_placement"),'ord.id as order_id','art.art_id')
				->join('art as art','art.art_id','=','ass.art_id')
				->join('orders as ord','ord.id','=','art.order_id')
				->leftjoin('artjob_artworkproof as aaw','aaw.wp_screen','=','ass.id')
				->leftJoin('placement as pl',DB::raw("FIND_IN_SET(pl.id,aaw.wp_placement)"),DB::raw(''),DB::raw(''))
				->where('ord.company_id','=',$company_id)
				->where('ass.id','=',$screen_id)
				->groupBy('aaw.id')
				->get();

		if(count($query)>0)
				{
					foreach ($query as $key => $value) 
					{
						$query[$key]->wp_position = (!empty($value->wp_position))? $Misc_data[$value->wp_position] : '';
						$query[$key]->wp_image = (!empty($value->wp_image))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->wp_image : '';
					}
				}		
		return $query;
    }
    public function screen_garments ($screen_id,$company_id)
    {
    	$query = DB::table('artjob_ordergroup as aog')
				->select('aog.*','pd.size','pd.qnty','ord.id as order_id','ord.job_name','aog.group_name','oo.color_id')
				->join('art as art','art.art_id','=','aog.art_id')
				->join('orders as ord','ord.id','=','art.order_id')
				->join('purchase_detail as pd','pd.art_group','=','aog.id')
				->join('order_orderlines as oo','oo.id','=','pd.orderline_id')
				->where('ord.company_id','=',$company_id)
				->whereRaw("FIND_IN_SET($screen_id,aog.screen_sets)")
				->where('pd.size','<>','')
				->where('pd.qnty','>',0)
				->get();

		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		return $query;
    }
}