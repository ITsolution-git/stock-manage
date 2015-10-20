<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Purchase extends Model {
	
	function ListPurchase($id)
	{
		$result = DB::table('purchase_list as pl')
					->leftJoin('vendors as vd','pl.vendor_id','=','vd.id')
					->leftJoin('client as cl','pl.client_id','=','cl.client_id')
					->select('cl.client_company','vd.name_company','pl.*')
					->where('pl.status','=','1')
					->where('pl.type_value','=',$id)
					->get();
		return $result;
	}
	function ListPodata($id)
	{
		$result = DB::table('order as ol')
					->leftJoin('vendors as vd','pl.vendor_id','=','vd.id')
					->where('pl.status','=','1')
					->where('pl.type_value','=',$id)
					->get();
		return $result;
	}
	function ListSgData($id)
	{
		$result = DB::table('purchase_list as pl')
					->select('cl.client_company','vd.name_company','pl.*')
					->where('pl.status','=','1')
					->where('pl.type_value','=',$id)
					->get();
		return $result;
	}

}

?>