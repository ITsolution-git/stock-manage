<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Purchase extends Model {
	
	function ListPurchase($id)
	{
		$result = DB::table('orders as ord')
					->leftJoin('order_orderlines as oo','oo.order_id','=','ord.id')
					->leftJoin('client as cl','ord.client_id','=','cl.client_id')
					->leftJoin('products as p','p.id','=','oo.product_id')
					->leftJoin('vendors as v','v.id','=','p.vendor_id')
					->select('cl.client_company','v.name_company','ord.id','ord.status')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->GroupBy('ord.id')
					->get();
		//echo "<pre>"; print_r($result); die();
		return $result;
	}
	function GetPodata($id)
	{
		$result = DB::select("SELECT p.name as product_name,v.name_company,ord.id,ord.job_name,ord.client_id,pg.name, cc.first_name,cc.last_name,mt.value,oo.*
		FROM orders ord
		left join order_orderlines oo on oo.order_id = ord.id
		left join price_grid pg on pg.id = price_id
		left join client_contact cc on cc.client_id = ord.client_id AND contact_main='1'
		left join misc_type mt on mt.id = oo.size_group_id
		Left join products p on p.id = oo.product_id
		Left join vendors v on v.id = p.vendor_id 
		where ord.status='1' AND ord.is_delete='1' 
		AND ord.id='".$id."' ");
		
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