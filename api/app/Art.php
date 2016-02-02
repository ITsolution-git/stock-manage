<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Art extends Model {

	public function Listing($company_id)
	{
		$Misc_data = $this->AllMsiData();

		
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
		$Misc_data = $this->AllMsiData();

		$query = DB::table('art as art')
				->select('op.*','art.art_id','art.notes','or.job_name','or.grand_total','or.f_approval')
				->join('orders as or','art.order_id','=','or.id')
				->join('order_positions as op','op.order_id','=','or.id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->where('art.art_id','=',$art_id)
				->get();

		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$query[$key]->f_approval = (!empty($value->f_approval))? $Misc_data[$value->f_approval]:'';
				$query[$key]->placement_type = (!empty($value->placement_type))? $Misc_data[$value->placement_type]:'';
				$query[$key]->position_id = (!empty($value->position_id))? $Misc_data[$value->position_id] : '';
				$query[$key]->dtg_size = (!empty($value->dtg_size))? $Misc_data[$value->dtg_size]:'';
				$query[$key]->dtg_on =  (!empty($value->dtg_size))?$Misc_data[$value->dtg_on]:'';
			}
		}
		return $query;
	}
	public function art_orderline($art_id,$company_id)
	{
		$Misc_data = $this->AllMsiData();
		$ret_array = array();
		$query = DB::table('art as art')
				->select('or.job_name','art.art_id','or.grand_total','or.f_approval','oo.id as line_id','oo.size_group_id','cl.name as product_color','pr.name as product_name','vn.name_company','pd.size', 'pd.qnty','pd.id as sizeid')
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
			foreach ($query as $key => $value) 
			{
				$ret_array['line_array'][$value->line_id]['job_name'] = $value->job_name;
				$ret_array['line_array'][$value->line_id]['product_color'] = $value->product_color;
				$ret_array['line_array'][$value->line_id]['product_name'] = $value->product_name;
				$ret_array['line_array'][$value->line_id]['name_company'] = $value->name_company;
				$ret_array['line_array'][$value->line_id]['size_group_id'] = (!empty($value->size_group_id))?$Misc_data[$value->size_group_id]:'';

				$ret_array['line_array'][$value->line_id]['size_array'][$kk]['size'] = (!empty($value->size))?$value->size:'';
				$ret_array['line_array'][$value->line_id]['size_array'][$kk]['qnty'] = (!empty($value->qnty))?$value->qnty:'';

				if($temp != $lock)
				{
					$kk ++;
					$lock = $temp;
				}
				$temp = $ret_array['line_array'][$value->line_id];


			}
		}

		//echo "<pre>"; print_r($ret_array); echo "</pre>"; die;
		return $ret_array;
	}
	public function AllMsiData()
    {
    	$query = DB::table('misc_type')->select('id','value')->get();
    	$ret_array = array();
    	foreach ($query as $key => $value) {
    		$ret_array[$value->id] = $value->value;
    	}
    	return $ret_array;
    }

}