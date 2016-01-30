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

}