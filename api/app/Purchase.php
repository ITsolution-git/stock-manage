<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Common;



class Purchase extends Model {

	public function __construct(Common $common) 
 	{
        $this->common = $common;
    }
	
	function ListPurchase($post)
	{
		$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        $this->common->getDisplayNumber('purchase_order',$post['company_id'],'company_id','po_id','yes');
		$result = DB::table('purchase_order as po')
					->leftJoin('orders as ord','po.order_id','=','ord.id')
					->leftJoin('client as cl','ord.client_id','=','cl.client_id')
					->leftJoin('vendors as v','v.id','=','po.vendor_id')
					->leftJoin('misc_type as misc_type','ord.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
					->select(DB::raw('SQL_CALC_FOUND_ROWS cl.client_company,v.name_company,ord.display_number,ord.id,ord.status,po.display_number as po_display,po.po_id,po.po_type,po.date,misc_type.value as approval,ord.approval_id'))
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->where('ord.company_id','=',$post['company_id']);

					if($search != '')               
                  	{
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('po.po_id', 'LIKE', '%'.$search.'%')
                                ->orWhere('ord.id','LIKE', '%'.$search.'%')
                                ->orWhere('cl.client_company','LIKE', '%'.$search.'%')
                                ->orWhere('v.name_company','LIKE', '%'.$search.'%')
                                ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')
                                ->orWhere('po.date','LIKE', '%'.$search.'%');
                      });
                  	}
                 $result = $result->GroupBy('po.order_id')
				 ->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
				 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
		
		//echo "<pre>"; print_r($result); echo "</pre>"; die;
        $check_array=array('po'=>'Purchase Order','sg'=>'Supplied Garments','ce'=>"Contract Embroidery",'cp'=>'Contract Print');
        if(count($result)>0)
        {
        	array_walk_recursive($result[0], function(&$item) {
	            $item = str_replace(array('0000-00-00'),array(''), $item);
	        });

          foreach ($result as $key=>$value) 
          {
            $result[$key]->date = (!empty($value->date)) ? date('m/d/Y',strtotime($value->date)) : '' ;
            $result[$key]->po_type =$check_array[$value->po_type] ;
          }
        }
		$count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;		
		//echo "<pre>"; print_r($result); die();
		return $returnData;
	}

	public function getAllPOdata()
    {
        $whereConditions = ['po.is_active' => '1'];
        $listArray = ['po.po_id','po.display_number','po.order_id','po.vendor_id','v.name_company'];
        $poData = DB::table('purchase_order as po')
        				->leftJoin('vendors as v','v.id','=','po.vendor_id')
                        ->select($listArray)
                        ->where($whereConditions)->get();
        $allData = array ();
        foreach($poData as $data) {
          
            $allData[$data->order_id][] = $data;
        }
        return $allData;
    }

    // *****
	function GetPodata($id,$company_id)
	{
		$result = DB::select("SELECT v.name_company,cl.client_company,ord.id,ord.job_name,ord.client_id,cl.display_number,pg.name,vc.id as contact_id, vc.first_name,vc.last_name,v.url,po.po_id,
							po.order_id,po.vendor_id,po.vendor_contact_id,po.po_type,po.shipt_block,po.vendor_charge,po.order_total,po.vendor_instruction,po.receive_note,po.complete,
							DATE_FORMAT(po.ship_date, '%m/%d/%Y') as ship_date,
							DATE_FORMAT(po.hand_date, '%m/%d/%Y') as hand_date,DATE_FORMAT(po.arrival_date, '%m/%d/%Y') as arrival_date,DATE_FORMAT(po.expected_date, '%m/%d/%Y') as expected_date,
							DATE_FORMAT(po.created_for_date, '%m/%d/%Y') as created_for_date,DATE_FORMAT(po.vendor_arrival_date, '%m/%d/%Y') as vendor_arrival_date,DATE_FORMAT(po.vendor_deadline, '%m/%d/%Y') as vendor_deadline,
							DATE_FORMAT(po.date, '%m/%d/%Y') as date
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
	// *****
	function ListSgData($id)
	{
		$result = DB::table('purchase_list as pl')
					->select('cl.client_company','vd.name_company','pl.*')
					->where('pl.status','=','1')
					->where('pl.type_value','=',$id)
					->get();
		return $result;
	}
	function GetPoLinedata($po_id,$company_id)
	{
		$result = DB::table('purchase_order as po')
					->JOIN('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->JOIN('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->JOIN('order_design as od','od.id','=','pd.design_id')
					->JOIN('orders as ord','od.order_id','=','ord.id')
					->JOIN('client as cl','ord.client_id', '=', 'cl.client_id')
				    ->JOIN('products as p','p.id','=','pd.product_id')
					->leftJoin('color as c','c.id','=','pd.color_id')
					->leftJoin('vendors as v','v.id','=','po.vendor_id')
					->leftJoin('vendor_contacts as vc','v.id','=',DB::raw("vc.vendor_id AND vc.is_main = '1' "))
					->select('vc.first_name','vc.last_name','v.name_company','v.url','cl.display_number','p.name as product_name','cl.client_company','po.vendor_instruction','po.vendor_charge','ord.display_number as ord_display','ord.name as order_name','c.name as product_color','pd.sku','pd.size','pd.qnty','po.display_number as po_display','po.po_id','po.order_id','po.vendor_id','po.vendor_contact_id','po.po_type','po.shipt_block','po.vendor_charge','po.order_total',DB::raw('DATE_FORMAT(ord.date_shipped, "%m/%d/%Y") as date_shipped'),
                      DB::raw('DATE_FORMAT(po.hand_date, "%m/%d/%Y") as hand_date'),DB::raw('DATE_FORMAT(po.arrival_date, "%m/%d/%Y") as arrival_date'),
                      DB::raw('DATE_FORMAT(po.expected_date, "%m/%d/%Y") as expected_date'),DB::raw('DATE_FORMAT(po.created_for_date, "%m/%d/%Y") as created_for_date'),
                      DB::raw('DATE_FORMAT(po.vendor_arrival_date, "%m/%d/%Y") as vendor_arrival_date'),DB::raw('DATE_FORMAT(po.vendor_deadline, "%m/%d/%Y") as vendor_deadline'),
                      'po.vendor_party_bill','po.ship_to','po.vendor_instruction','po.receive_note',DB::raw('DATE_FORMAT(po.date, "%m/%d/%Y") as date'),'po.complete','pol.*','ord.approval_id')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->where('pd.qnty','<>','0')
					->where('pd.qnty','<>','')
					->where('po.display_number','=',$po_id)
					->Where('ord.company_id','=',$company_id)
				  	->get();

		$check_array=array('po'=>'Purchase Order','sg'=>'Supplied Garments','ce'=>"Contract Embroidery",'cp'=>'Contract Print');
		//echo "<pre>"; print_r($result); echo "</pre>"; die;
		if(count($result)>0)
		{
			foreach ($result as $key=>$value) 
          	{
	            $result[$key]->po_type =$check_array[$value->po_type] ;
          	}
			array_walk_recursive($result[0], function(&$item) {
	            $item = str_replace(array('00/00/0000'),array(''), $item);
	        });

    		$count_note = DB::table('purchase_order as po')
			->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
			->leftJoin('purchase_detail as pd','pd.id','=','pol.purchase_detail')
			->leftJoin('order_design_position as odp','pd.design_id','=','odp.design_id')
			->select('*')
			->where('odp.is_delete','=','1')
			->where('pd.is_delete','=','1')
			->where('po.display_number','=',$po_id)
			->Where('po.company_id','=',$company_id)
			->GroupBy('odp.id')
			->get();

			//echo "<pre>"; print_r(count($count_note)); echo "</pre>"; die;
			$result[0]->total_notes = count($count_note);              
			return $result;
		}
	}
	function getOrdarTotal($po_id,$company_id=0)
	{
		if(!empty($company_id))
	    {
	        $where = ['po.display_number'=>$po_id, 'po.company_id'=>$company_id];
	    }
	    else
	    {
	        $where = ['po.po_id'=>$po_id];
	    }

		$result = DB::table('purchase_order as po')
					->LeftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->where($where)
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
	function Update_Ordertotal($po_id,$company_id=0)
	{
		if(!empty($company_id))
	    {
	        $where = ['po.display_number'=>$po_id, 'po.company_id'=>$company_id];
	    }
	    else
	    {
	        $where = ['po.po_id'=>$po_id];
	    }
		$value = DB::table('purchase_order as po')
					->leftJoin('purchase_order_line as pol','po.po_id','=','pol.po_id')
					->where($where)
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
	function GetPoReceived($po_id,$company_id=0)
	{
		if(!empty($company_id))
	    {
	        $where = ['po.display_number'=>$po_id, 'po.company_id'=>$company_id];
	    }
	    else
	    {
	        $where = ['po.po_id'=>$po_id];
	    }


		$result = DB::table('purchase_order as po')
					->JOIN('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->JOIN('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->JOIN('order_design as od','od.id','=','pd.design_id')
					->JOIN('orders as ord','od.order_id','=','ord.id')
					->JOIN('users as usr','usr.id','=','ord.company_id')
					->JOIN('staff as stf','stf.user_id','=','usr.id')
					->JOIN('client as cl','ord.client_id', '=', 'cl.client_id')
				    ->JOIN('products as p','p.id','=','pd.product_id')
					->leftJoin('color as c','c.id','=','pd.color_id')
					->leftJoin('vendors as v','v.id','=','po.vendor_id')
					->leftJoin('vendor_contacts as vc','v.id','=',DB::raw("vc.vendor_id AND vc.is_main = '1' "))
					->select('stf.first_name as f_name','stf.last_name as l_name','stf.prime_address_city','stf.prime_address_street','stf.prime_address_state','stf.prime_address_zip','stf.prime_phone_main','stf.photo as companyphoto','stf.id as staff_id','stf.prime_address1','usr.name as companyname','vc.first_name','vc.last_name','v.name_company','v.url','p.name as product_name','p.id as product_id','cl.client_company','cl.display_number','cl.billing_email','po.vendor_instruction','po.vendor_charge','ord.display_number as ord_display','po.display_number as po_display','ord.name as order_name','ord.custom_po','c.name as product_color','pd.sku','pd.size','pd.qnty',
						DB::raw('(select count(*) from purchase_notes where po_id=po.po_id) as total_note'),'po.po_id',
						'po.po_id','po.order_id','po.vendor_id','po.vendor_contact_id','po.po_type','po.shipt_block','po.vendor_charge','po.order_total',DB::raw('DATE_FORMAT(ord.date_shipped, "%m/%d/%Y") as date_shipped'),
                      DB::raw('DATE_FORMAT(po.hand_date, "%m/%d/%Y") as hand_date'),DB::raw('DATE_FORMAT(po.arrival_date, "%m/%d/%Y") as arrival_date'),
                      DB::raw('DATE_FORMAT(po.expected_date, "%m/%d/%Y") as expected_date'),DB::raw('DATE_FORMAT(po.created_for_date, "%m/%d/%Y") as created_for_date'),
                      DB::raw('DATE_FORMAT(po.vendor_arrival_date, "%m/%d/%Y") as vendor_arrival_date'),DB::raw('DATE_FORMAT(po.vendor_deadline, "%m/%d/%Y") as vendor_deadline'),
                      'po.vendor_party_bill','po.ship_to','po.vendor_instruction','po.receive_note',DB::raw('DATE_FORMAT(po.date, "%m/%d/%Y") as date'),'po.complete','pol.*','ord.approval_id')
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->where('pd.qnty','<>','0')
					->where('pd.qnty','<>','')
					->where($where)
				  	->get();

		$check_array=array('po'=>'Purchase Order','sg'=>'Supplied Garments','ce'=>"Contract Embroidery",'cp'=>'Contract Print');
		//echo "<pre>"; print_r($result); echo "</pre>"; die;
		$ret_array = array();
		if(count($result)>0)
		{

			$ret_array['po_data']=array();
			foreach ($result as $key=>$value) 
          	{
          		array_walk_recursive($value, function(&$item) {
	            	$item = str_replace(array('00/00/0000'),array(''), $item);
	        	});
	        	$result[0]->companyphoto= $this->common->checkImageExist($company_id.'/staff/'.$value->staff_id."/",$value->companyphoto);
	            $ret_array['receive'][$value->product_id]['data'][$value->size]= $value;
	            $ret_array['receive'][$value->product_id]['product'] = $value;
	            $ret_array['po_data']= $result[0];
          	}
          	$total_invoice = 0;
          	foreach ($ret_array['receive'] as $key => $value) 
          	{
          		$total_order = 0;
          		$rec_qnty = 0;
          		$short = 0;
          		foreach ($value['data'] as $key_ret_array => $value_ret_array) 
          		{
          			$total_order += $value_ret_array->qnty_ordered;
          			$rec_qnty += $value_ret_array->qnty_purchased;
          			$short += $value_ret_array->short;

          			if($value_ret_array->qnty_ordered>$value_ret_array->qnty_purchased)
          			{
          				$value['data'][$key_ret_array]->short_unit = ($value_ret_array->qnty_ordered - $value_ret_array->qnty_purchased);
          				$value['data'][$key_ret_array]->over_unit = 0;
          			}
          			else
          			{
          				$value['data'][$key_ret_array]->short_unit = 0;
          				$value['data'][$key_ret_array]->over_unit = ($value_ret_array->qnty_purchased- $value_ret_array->qnty_ordered);
          			}
          			
          			$total_invoice += $value_ret_array->line_total;
          			//$value['data'][$key_ret_array]['']
          		}
          		$ret_array['receive'][$key]['data'] = array_values($value['data']);
          		$ret_array['receive'][$key]['total_product'] = $total_order;
          		$ret_array['receive'][$key]['total_received'] = $rec_qnty;
          		$ret_array['receive'][$key]['total_defective'] = $short;
          		if($total_order>$rec_qnty)
          		{
          			$ret_array['receive'][$key]['total_remains'] = $total_order -$rec_qnty." Short";
          		}
          		else
          		{
          			$ret_array['receive'][$key]['total_remains'] = $rec_qnty -$total_order." Over";
          		}
          		
          		$ret_array['po_data']->total_invoice = $total_invoice;
          	}


	    }
		return $ret_array;
	}

	// *****
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
				  ->Join('purchase_order_line as pol','pol.po_id','=','po.po_id')
				  ->leftJoin('order_positions as op','op.id','=','pol.line_id')
				  ->select('op.description','po.po_id','ord.id as order_id','op.qnty','op.color_stitch_count','op.position_id as position','op.id as position_id','pol.*')
				  ->where('pol.po_id','=',$po_id)
				  ->where('ord.company_id','=',$company_id)
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

	public function getPurchaseNote($post)
   	{
       	$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

		$result = DB::table('purchase_order as po')
					->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->leftJoin('purchase_detail as pd','pd.id','=','pol.purchase_detail')
					->leftJoin('order_design_position as odp','pd.design_id','=','odp.design_id')
					->leftJoin('misc_type as mt','mt.id','=','odp.position_id')
					->select(DB::raw('SQL_CALC_FOUND_ROWS odp.note,mt.value,odp.description,odp.id,po.po_id'))
					->where('odp.is_delete','=','1')
					->where('pd.is_delete','=','1')
					->where('po.display_number','=',$post['display_number'])
					->where('po.company_id','=',$post['company_id']);

					if($search != '')               
                  	{
                        $result = $result->Where(function($query) use($search)
                        {
                            $query->orWhere('odp.note', 'LIKE', '%'.$search.'%');
                        });
                  	}
                 $result = $result->GroupBy('odp.id')->skip($post['start'])
                 ->take($post['range'])
                 ->get();
		

		$count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;		
		//echo "<pre>"; print_r($result); die();
		return $returnData;
	}
	public function getPlacementData($po_id)
	{
		$allcolors = $this->common->getAllColorData();
		foreach ($allcolors as $key => $value) 
			{
				$allcolors[$value->id] = $value->name;
			}
		$placement = DB::table('purchase_order as po')
		 				  ->leftJoin('purchase_placement as pp','pp.po_id','=','po.po_id')
		 				  ->leftJoin('artjob_artworkproof as wp','wp.id','=','pp.artwork_id')
		 				  ->leftJoin('artjob_screensets as scrn','wp.wp_screen','=','scrn.id')
		 				  ->leftJoin('artjob_screencolors as col','col.screen_id','=','scrn.id')
		 				  ->leftJoin('art as art','art.order_id','=','po.order_id')
	 				      ->select('po.po_id','art.art_id','wp.wp_image','wp.id as wp_id','pp.id as placement_id','pp.position_id','scrn.id as screen_id','col.id as color_id','col.color_name','col.thread_color','col.inq')
                          ->where('po.po_id','=',$po_id)
                          ->orderby('pp.id','asc')
                          ->get();
               
                $temp_array=array();
                $temp_array['placement']='';
                foreach ($placement as $key => $value) 
                {
                	$temp_array['placement'][$value->placement_id]['po_id'] = $value->po_id;
                	$temp_array['placement'][$value->placement_id]['placement_id']  = $value->placement_id;
                	$temp_array['placement'][$value->placement_id]['art_id']  = $value->art_id;
                	$temp_array['placement'][$value->placement_id]['position_id']  = $value->position_id;

                	if(!empty($value->wp_id))
                	{
	                	$temp_array['placement'][$value->placement_id]['wp_image'] = (!empty($value->wp_image))? UPLOAD_PATH.'art/'.$value->art_id.'/'.$value->wp_image:'';
	                	$temp_array['placement'][$value->placement_id]['wp_id']  = $value->wp_id;
	                	$temp_array['placement'][$value->placement_id]['screen_id']  = $value->screen_id;
	                	
	                	if(!empty($value->color_id))
	                	{
	                		$temp_array['placement'][$value->placement_id]['color'][$value->color_id]['color_name']  = (!empty($value->color_name))?$allcolors[$value->color_name]:'';
	                		$temp_array['placement'][$value->placement_id]['color'][$value->color_id]['thread_color']  = (!empty($value->thread_color))?$allcolors[$value->thread_color]:'';
	                		$temp_array['placement'][$value->placement_id]['color'][$value->color_id]['inq']  = $value->inq;
	                		$temp_array['placement'][$value->placement_id]['color'][$value->color_id]['color_id']  = $value->color_id;
	                		$temp_array['placement'][$value->placement_id]['color'] = array_values($temp_array['placement'][$value->placement_id]['color']);
	                	}
                   }

                }
              $temp_array['placement'] = array_values($temp_array['placement']);
       // echo "<pre>"; print_r($temp_array); echo "</pre>"; die;
        return $temp_array['placement'];
	}
	public function getOrderData($company_id,$order_id)
	{
		$result = DB::table('orders as o')
					->select('o.id as order_id','dp.product_id','pd.*','p.vendor_id')
					->Join('order_design as od','od.order_id','=','o.id')
					->Join('design_product as dp','dp.design_id','=','od.id')
					->Join('purchase_detail as pd','pd.design_product_id','=','dp.id')
					->Join('products as p','p.id','=','dp.product_id')
					->where('o.is_delete','=',1)
					->where('od.is_delete','=','1')
					->where('dp.is_delete','=',1)
					->where('o.id','=',$order_id)
					->where('o.company_id','=',$company_id)
					->get();
		if(count($result)>0)
		{
			foreach($result as $key=>$value)
			{
				$new_array[$value->vendor_id][] = $value;
			}
			return $new_array;
		}
		return $result;
		
	}
	public function insert_purchaseorder($order_id,$vendor_id,$po_type='po',$company_id)
	{
		/*$check = DB::table('purchase_order')
				->select('*')
				->where('order_id','=',$order_id)
				->where('vendor_id','=',$vendor_id)
				->get();

		//echo "<pre>"; print_r($check); echo "</pre>"; die;
		if(count($check)>0)
		{
			return 0 ;
		}
		else 
		{*/
			$disp_id = $this->common->getDisplayNumber('purchase_order',$company_id,'company_id','po_id','yes');
			$result = DB::table('purchase_order')->insert(array('order_id'=>$order_id,'vendor_id'=>$vendor_id,'date'=>CURRENT_DATE,'po_type'=>$po_type,'is_active'=>1,'company_id'=>$company_id,'display_number'=>$disp_id));
			$id = DB::getPdo()->lastInsertId();
        	return $id;	
		//}		

	}
	public function insert_purchase_order_line($post,$po_id)
	{
		$line_total = $post->price * $post->qnty;
		$result = DB::table('purchase_order_line')->insert(array('po_id'=>$po_id,'purchase_detail'=>$post->id,'qnty_ordered'=>$post->qnty,'unit_price'=>$post->price,'line_total'=>$line_total));
		$id = DB::getPdo()->lastInsertId();
        return $id;
	}

	function ListReceive($post)
	{
		$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

		$result = DB::table('purchase_order as po')
					->leftJoin('orders as ord','po.order_id','=','ord.id')
					->leftJoin('misc_type as misc_type','ord.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
					->leftJoin('client as cl','ord.client_id','=','cl.client_id')
					->leftJoin('vendors as v','v.id','=','po.vendor_id')
					->select(DB::raw('SQL_CALC_FOUND_ROWS cl.client_company,v.name_company,ord.display_number,ord.id,ord.status,po.display_number as po_display,po.po_id,po.po_type,po.date,misc_type.value as approval,ord.approval_id'))
					->where('ord.status','=','1')
					->where('ord.is_delete','=','1')
					->where('ord.company_id','=',$post['company_id'])
					->where('po.complete','=','1');

					if($search != '')               
                  	{
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('po.po_id', 'LIKE', '%'.$search.'%')
                                ->orWhere('ord.id','LIKE', '%'.$search.'%')
                                ->orWhere('cl.client_company','LIKE', '%'.$search.'%')
                                ->orWhere('v.name_company','LIKE', '%'.$search.'%')
                                ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')
                                ->orWhere('po.date','LIKE', '%'.$search.'%');
                      });
                  	}
                 $result = $result->GroupBy('po.order_id')
				 ->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
				 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
		
		//echo "<pre>"; print_r($result); echo "</pre>"; die;
        $check_array=array('po'=>'Purchase Order','sg'=>'Supplied Garments','ce'=>"Contract Embroidery",'cp'=>'Contract Print');
        if(count($result)>0)
        {
        	array_walk_recursive($result[0], function(&$item) {
	            $item = str_replace(array('0000-00-00'),array(''), $item);
	        });

          foreach ($result as $key=>$value) 
          {
            $result[$key]->date = (!empty($value->date)) ? date('m/d/Y',strtotime($value->date)) : '' ;
            $result[$key]->po_type =$check_array[$value->po_type] ;
          }
        }
		$count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;		
		//echo "<pre>"; print_r($result); die();
		return $returnData;
	}

	public function getAllReceivedata()
    {
        $whereConditions = ['po.complete' => '1'];
        $listArray = ['po.po_id','po.display_number','po.order_id','po.vendor_id','v.name_company'];
        $poData = DB::table('purchase_order as po')
        				->leftJoin('vendors as v','v.id','=','po.vendor_id')
                        ->select($listArray)
                        ->where($whereConditions)->get();
        $allData = array ();
        foreach($poData as $data) {
          
            $allData[$data->order_id][] = $data;
        }
        return $allData;
    }

}

?>