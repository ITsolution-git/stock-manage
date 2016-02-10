<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Purchase extends Model {
	
	function ListPurchase($type,$company_id)
	{
		$result = DB::table('purchase_order as po')
					->leftJoin('orders as ord','po.order_id','=','ord.id')
					->leftJoin('client as cl','ord.client_id','=','cl.client_id')
					->leftJoin('vendors as v','v.id','=','po.vendor_id')
					->select('cl.client_company','v.name_company','ord.id','ord.status','po.po_id','po.po_type')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->where('po.po_type','=',strtolower($type))
					->where('ord.company_id','=',$company_id)
					->GroupBy('po.po_id')
					->get();
		//echo "<pre>"; print_r($result); die();
		return $result;
	}
	function GetPodata($id,$company_id)
	{
		$result = DB::select("SELECT v.name_company,cl.client_company,ord.id,ord.job_name,ord.client_id,pg.name,vc.id as contact_id, vc.first_name,vc.last_name,v.url,po.*
		FROM purchase_order po
		Inner join orders ord on po.order_id = ord.id
		left join client cl on ord.client_id = cl.client_id
		left join order_orderlines oo on oo.order_id = ord.id
		left join order_positions op on op.order_id = ord.id
		left join price_grid pg on pg.id = ord.price_id
		Left join vendors v on v.id = po.vendor_id 
		left join vendor_contacts vc on vc.vendor_id = v.id
		where ord.status='1' AND ord.is_delete='1' AND ord.company_id = '".$company_id."'
		AND po.po_id='".$id."'
		GROUP BY po.po_id ");
		
		
		if(count($result)>0)
		{

			array_walk_recursive($result[0], function(&$item) {
	            $item = str_replace(array('0000-00-00'),array(''), $item);
	        });
		}
		
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
		$result = DB::table('purchase_order as po')
				  ->leftJoin('orders as ord','po.order_id','=','ord.id')
				  ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
				  ->leftJoin('purchase_detail as pd','pd.id','=','pol.line_id')
				  ->leftJoin('order_orderlines as oo','oo.id','=','pd.orderline_id')
				  ->leftJoin('misc_type as mt','mt.id','=','oo.size_group_id')
				  ->leftJoin('misc_type as mt1','mt1.id','=','oo.color_id')
				  ->leftJoin('products as p','p.id','=','oo.product_id')
				  ->leftJoin('vendors as v','v.id','=','po.vendor_id')
				  ->select('v.name_company','p.name as product_name','ord.job_name','po.po_id','mt.value as size_group','mt1.value as product_color','pd.size','pd.qnty','pol.*',DB::raw('(select sum(qnty_received) from purchase_received where poline_id=pd.id) as total_qnty') )
				  ->where('ord.status','=','1')
				  ->where('ord.is_delete','=','1')
				  ->where('pd.size','<>','')
				  ->where('pd.size','<>','0')
				  ->where('pd.qnty','<>','0')
				  ->where('pd.qnty','<>','')
				  ->where('pol.status','=',$postatus);


				  if(!empty($id))
				  {

				  	$result = $result->where('po.po_id','=',$id);
				  }
				  $result = $result->get();

				 //echo "<pre>"; print_r($result); die;
		return $result;
	}
	function getOrdarTotal($po_id)
	{
		$result = DB::table('purchase_order as po')
					->LeftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->where('po.po_id','=',$po_id)
				  	->where('pol.status','=','1')
				  	->select(DB::raw('sum(pol.qnty_ordered) as ordered'),'po.order_total as total_amount')
				  	->get();
				 // 	echo "<pre>"; print_r($result); die;
		return $result;	
	}
	function getreceivedTotal($po_id)
	{

		$result = DB::table('purchase_received as pr')
					->where('pr.po_id','=',$po_id)
					->select(DB::raw('sum(pr.qnty_received) as received'))
					->get();
		return $result;	
	}

	function ChangeOrderStatus($id,$value=0,$po_id)
	{
   		$result = DB::table('purchase_order_line')
   						->where('id','=',$id)
   						->update(array('status'=>$value,'po_id'=>$po_id));
   		$this->Update_Ordertotal($po_id);
    	return $result;
	}
	function Update_Ordertotal($po_id)
	{

		$value = DB::table('purchase_order as po')
					->leftJoin('purchase_order_line as pol','po.po_id','=','pol.po_id')
					->where('po.po_id','=',$po_id)
					->where('pol.status','=',1)
					->select(DB::raw('sum(pol.line_total) as total'),'po.vendor_charge')
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
		$result = DB::table('purchase_order_line')
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

		 $result = DB::table('purchase_order_line as pol')
		 			->join('purchase_received as pr','pol.id','=','pr.poline_id')
		 			->where('pol.id','=',$id)
		 			->select('pol.qnty_ordered',DB::raw('sum(pr.qnty_received) as receiver_total'))
		 			->get();
		 $short=0; $over=0;
		 if(count($result)>0)
		 {
		 	$short = ($result[0]->qnty_ordered > $result[0]->receiver_total)? $result[0]->qnty_ordered - $result[0]->receiver_total : 0;
		 	$over = ($result[0]->qnty_ordered < $result[0]->receiver_total)? $result[0]->receiver_total -$result[0]->qnty_ordered : 0 ;
		 	//echo $short."-".$over; die();
		 }
		 $result = DB::table('purchase_order_line')
   						->where('id','=',$id)
   						->update(array('short'=>$short,'over'=>$over));
    	 return $result;
	}
	function GetPoReceived($po_id,$company_id)
	{
		$result =  DB::table('purchase_order as po')
				  ->leftJoin('orders as ord','po.order_id','=','ord.id')
				  ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
				  ->leftJoin('purchase_detail as pd','pd.id','=','pol.line_id')
				  ->leftJoin('order_orderlines as oo','oo.id','=','pd.orderline_id')
				  ->leftJoin('misc_type as mt','mt.id','=','oo.size_group_id')
				  ->leftJoin('products as p','p.id','=','oo.product_id')
				  ->leftJoin('vendors as v','v.id','=','po.vendor_id')
				  ->join('purchase_received as pr','pr.poline_id','=','pol.id')
				  ->select('v.name_company','ord.job_name','po.po_id','mt.value as size_group','pr.id as pr_id','pr.poline_id','pr.qnty_received','pd.size','pd.qnty','pol.*')
				  ->where('pr.po_id','=',$po_id)
				  ->get();

				 // echo "<pre>"; print_r($result); die;
		return $result;
	}

	function Update_shiftlock($post)
	{
		$result = DB::table('purchase_order')
   						->where('po_id','=',$post['po_id'])
   						->update(array('shipt_block'=>$post['data']));
    	return $result;
	}
	function GetScreendata($po_id,$company_id)
	{
		$result =  DB::table('purchase_order as po')
				  ->leftJoin('orders as ord','po.order_id','=','ord.id')
				  ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
				  ->leftJoin('order_positions as op','op.id','=','pol.line_id')
				  ->leftJoin('vendors as v','v.id','=','po.vendor_id')
				  ->select('v.name_company','ord.job_name','po.po_id','ord.id as order_id','op.qnty','op.color_stitch_count','op.id as position_id','pol.*')
				  ->where('po.po_id','=',$po_id)
				  ->get();
				 // echo "<pre>"; print_r($result); die;
		return $result;
	}
	function EditScreenLine ($post)
	{
		//echo "<pre>"; print_r($post['po_id']) ; die;
		$post['line_total'] = $post['qnty'] * $post['unit_price'];

		$result = DB::table('order_positions')
   						->where('id','=',$post['position_id'])
   						->update(array('qnty'=>$post['qnty']));

		$result = DB::table('purchase_order_line')
   						->where('id','=',$post['id'])
   						->update(array('qnty_ordered'=>$post['qnty'],'unit_price'=>$post['unit_price'],'line_total'=>$post['line_total']));
   		$this->Update_Ordertotal($post['po_id']);
    	return $result;
	}

	function GetOrderLineData($id)
	{
		$listArray = ['o.*','p.description as product_description','p.name as product_name','c.name as color_name'];
        $whereOrderLineConditions = ['order_id' => $id];
        $orderLineData = DB::table('order_orderlines as o')
                        ->leftJoin('products as p','o.product_id','=','p.id')
                        ->leftJoin('color as c','o.color_id','=','c.id')
                        ->select($listArray)
                        ->where($whereOrderLineConditions)->get();
        return $orderLineData;                
	}

}

?>