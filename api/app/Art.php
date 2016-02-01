<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Art extends Model {

	public function Listing($company_id)
	{
		$query = DB::table('art as art')
				->select('*')
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('client as cl','cl.client_id','=','or.client_id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->get();
		return $query;
	}
	public function art_position($art_id,$company_id)
	{
		$query = DB::table('art as art')
				->select('op.*','art.art_id','or.job_name','or.grand_total')
				->join('orders as or','art.order_id','=','or.id')
				->leftJoin('order_positions as op','op.order_id','=','or.id')
				->where('or.is_delete','=','1')
				->where('or.company_id','=',$company_id)
				->get();
		return $query;
	}

}