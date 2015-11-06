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
					->leftJoin('purchase_order as pr','pr.order_id','=','ord.id')
					->leftJoin('vendors as v','v.id','=','p.vendor_id')
					->select('cl.client_company','v.name_company','ord.id','ord.status','ord.type_id','pr.po_id','ord.type_value')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->GroupBy('ord.id')
					->get();
		//echo "<pre>"; print_r($result); die();
		return $result;
	}
	function GetPodata($id)
	{
		$result = DB::select("SELECT p.name as product_name,po.shipt_block,po.po_id,po.vendor_charge,v.name_company,ord.id,ord.job_name,ord.client_id,pg.name, cc.first_name,cc.last_name,oo.*,v.url
		FROM orders ord
		left join order_orderlines oo on oo.order_id = ord.id
		left join purchase_order po on po.order_id = ord.id
		left join price_grid pg on pg.id = price_id
		left join client_contact cc on cc.client_id = ord.client_id AND contact_main='1'
		Left join products p on p.id = oo.product_id
		Left join vendors v on v.id = p.vendor_id 
		where ord.status='1' AND ord.is_delete='1' 
		AND po.po_id='".$id."'
		GROUP BY po.po_id ");

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
				  ->select('v.name_company','ord.job_name','po.po_id','mt.value as size_group','pd.*',DB::raw('(select sum(qnty_received) from purchase_received where poline_id=pd.id) as total_qnty') )
				  ->where('ord.status','=','1')
				  ->where('ord.is_delete','=','1')
				  ->where('oo.status','=','1')
				  ->where('oo.is_delete','=','1')
				  ->where('pd.size','<>','')
				  ->where('pd.size','<>','0')
				  ->where('pd.qnty','<>','0')
				  ->where('pd.qnty','<>','')
				  ->where('pd.status','=',$postatus);


				  if(!empty($id))
				  {

				  	$result = $result->where('pd.po_id','=',$id);
				  }
				  $result = $result->get();

				 //echo "<pre>"; print_r($result); die;
		return $result;
	}
	function getOrdarTotal($po_id)
	{
		$result = DB::table('purchase_order as po')
					->LeftJoin('purchase_detail as pd','pd.po_id','=','po.po_id')
					->where('po.po_id','=',$po_id)
				  	->where('pd.status','=','1')
				  	->select(DB::raw('sum(pd.qnty_ordered) as ordered'),'po.order_total as total_amount')
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

	function ChangeOrderStatus($id,$value=0,$po_id)
	{
   		$result = DB::table('purchase_detail')
   						->where('id','=',$id)
   						->update(array('status'=>$value,'po_id'=>$po_id));
   		$this->Update_Ordertotal($po_id);
    	return $result;
	}
	function Update_Ordertotal($po_id)
	{

		$value = DB::table('purchase_order as po')
					->leftJoin('purchase_detail as pd','po.po_id','=','pd.po_id')
					->where('po.po_id','=',$po_id)
					->where('pd.status','=',1)
					->select(DB::raw('sum(pd.line_total) as total'),'po.vendor_charge')
					->get();
		//echo "<pre>"; print_r($value); die;	
		
		if(count($value)>0)
		{	
			$sum = 	$value[0]->total + $value[0]->vendor_charge; 
	   		$result = DB::table('purchase_order')
	   						->where('po_id','=',$po_id)
	   						->update(array('order_total'=>$sum));
	    	return $result;
    	}
	}
	function EditOrderLine ($post)
	{
		//echo "<pre>"; print_r($post['po_id']) ; die;
		$post['line_total'] = $post['qnty_ordered'] * $post['unit_price'];
		$result = DB::table('purchase_detail')
   						->where('id','=',$post['id'])
   						->update(array('qnty_ordered'=>$post['qnty_ordered'],'unit_price'=>$post['unit_price'],'line_total'=>$post['line_total']));
   		$this->Update_Ordertotal($post['po_id']);
    	return $result;
	}
	function Receive_order($post)
	{
		 $result = DB::table('purchase_received')->insert(array('poline_id'=>$post['id'],'qnty_received'=>$post['qnty_ordered'],'po_id'=>$post['po_id']));
        return $result;
	}
	function short_over($id)
	{

		 $result = DB::table('purchase_detail as pd')
		 			->join('purchase_received as pr','pd.id','=','pr.poline_id')
		 			->where('pd.id','=',$id)
		 			->select('pd.qnty_ordered',DB::raw('sum(pr.qnty_received) as receiver_total'))
		 			->get();
		 $short=0; $over=0;
		 if(count($result)>0)
		 {
		 	$short = ($result[0]->qnty_ordered > $result[0]->receiver_total)? $result[0]->qnty_ordered - $result[0]->receiver_total : 0;
		 	$over = ($result[0]->qnty_ordered < $result[0]->receiver_total)? $result[0]->receiver_total -$result[0]->qnty_ordered : 0 ;
		 	//echo $short."-".$over; die();
		 }
		 $result = DB::table('purchase_detail')
   						->where('id','=',$id)
   						->update(array('short'=>$short,'over'=>$over));
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
				  ->select('v.name_company','ord.job_name','po.po_id','mt.value as size_group','pr.id as pr_id','pr.poline_id','pr.qnty_received','pd.*')
				  ->where('pr.po_id','=',$po_id)
				  ->get();

				  //echo "<pre>"; print_r($result); die;
		return $result;
	}

	function Update_shiftlock($post)
	{
		$result = DB::table('purchase_order')
   						->where('order_id','=',$post['po_id'])
   						->update(array('shipt_block'=>$post['data']));
    	return $result;
	}

}

?>