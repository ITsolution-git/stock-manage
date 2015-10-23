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
					->select('cl.client_company','v.name_company','ord.id','ord.status','ord.type_id','ord.type_value')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->GroupBy('ord.id')
					->get();
		//echo "<pre>"; print_r($result); die();
		return $result;
	}
	function GetPodata($id)
	{
		$result = DB::select("SELECT p.name as product_name,po.shipt_block,v.name_company,v.url,ord.id,ord.job_name,ord.client_id,pg.name, cc.first_name,cc.last_name,oo.*
		FROM orders ord
		left join order_orderlines oo on oo.order_id = ord.id
		left join purchase_order po on po.order_id = ord.id
		left join price_grid pg on pg.id = price_id
		left join client_contact cc on cc.client_id = ord.client_id AND contact_main='1'
		Left join products p on p.id = oo.product_id
		Left join vendors v on v.id = p.vendor_id 
		where ord.status='1' AND ord.is_delete='1' 
		AND ord.id='".$id."'
		GROUP BY ord.id ");

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
	function GetPoLinedata($id=0,$postatus=0)
	{
		$result = DB::table('orders as ord')
				  ->leftJoin('purchase_order as po','po.order_id','=','ord.id')
				  ->leftJoin('order_orderlines as oo','oo.order_id','=','ord.id')
				  ->leftJoin('purchase_detail as pd','pd.orderline_id','=','oo.id')
				  ->leftJoin('misc_type as mt','mt.id','=','oo.size_group_id')
				  ->leftJoin('products as p','p.id','=','oo.product_id')
				  ->leftJoin('vendors as v','v.id','=','p.vendor_id')
				  ->select('v.name_company','ord.job_name','po.po_id','mt.value as size_group','pd.*')
				  ->where('ord.status','=','1')
				  ->where('ord.is_delete','=','1')
				  ->where('oo.status','=','1')
				  ->where('oo.is_delete','=','1')
				  ->where('pd.status','=',$postatus);


				  if($id!=0)
				  {
				  	//$result = $result->where('ord.id','=',$id);
				  }
				  $result = $result->get();

				  //echo "<pre>"; print_r($result); die;
		return $result;
	}
	function getOrdarTotal($po_id)
	{
		$result = DB::table('purchase_detail as pd')
					->where('pd.po_id','=',$po_id)
				  	->where('pd.status','=','1')
				  	->select(DB::raw('sum(pd.qnty_ordered) as ordered'))
				  	->get();
		return $result;	
	}
	function getreceivedTotal($po_id)
	{
		$result = DB::table('purchase_received as pd')
					->where('pd.po_id','=',$po_id)
					->select(DB::raw('sum(pd.qnty_received) as received'))
					->get();
		return $result;	
	}

	function ChangeOrderStatus($id,$value=0)
	{
   		$result = DB::table('purchase_detail')
   						->where('id','=',$id)
   						->update(array('status'=>$value));
    	return $result;
	}
	function EditOrderLine ($post)
	{
		$result = DB::table('purchase_detail')
   						->where('id','=',$post['id'])
   						->update(array('qnty_ordered'=>$post['qnty_ordered'],'unit_price'=>$post['unit_price'],'line_total'=>$post['line_total']));
    	return $result;
	}
	function Receive_order($post)
	{
		 $result = DB::table('purchase_received')->insert(array('poline_id'=>$post['id'],'qnty_received'=>$post['qnty_ordered'],'po_id'=>$post['po_id']));
        return $result;
	}
	
	function GetPoReceived($po_id)
	{
		$result = DB::table('orders as ord')
				  ->leftJoin('purchase_order as po','po.order_id','=','ord.id')
				  ->leftJoin('order_orderlines as oo','oo.order_id','=','ord.id')
				  ->leftJoin('purchase_detail as pd','pd.orderline_id','=','oo.id')
				  ->leftJoin('misc_type as mt','mt.id','=','oo.size_group_id')
				  ->leftJoin('products as p','p.id','=','oo.product_id')
				  ->leftJoin('vendors as v','v.id','=','p.vendor_id')
				  ->join('purchase_received as pr','pr.poline_id','=','pd.id')
				  ->select('v.name_company','ord.job_name','po.po_id','mt.value as size_group','pr.id as pr_id','pr.qnty_received','pd.*')
				  ->where('pr.po_id','=',$po_id)
				  ->get();

				  //echo "<pre>"; print_r($result); die;
		return $result;
	}
	function RemoveReceiveLine($id)
	{
		$result = DB::table('purchase_received')->where('id', '=',$id )->delete();
		return $result;
	}
	function Update_shiftlock($post)
	{
		$result = DB::table('purchase_order')
   						->where('order_id','=',$post['order_id'])
   						->update(array('shipt_block'=>$post['data']));
    	return $result;
	}

}

?>