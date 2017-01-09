<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Common;

class Art extends Model {


	public function __construct(Common $common) 
    {
        $this->common = $common;
    }


	public function Listing($post)
	{
		$this->common->getDisplayNumber('artjob_screensets',$post['company_id'],'company_id','id','yes');
		$search = ''; $client_filter='';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        if(isset($post['filter']['client'])) {
            $client_filter = $post['filter']['client'];
        }

        $admindata = DB::table('orders as ord')
        				->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
        				->leftJoin('misc_type as misc_type','ord.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
        				->select(DB::raw('SQL_CALC_FOUND_ROWS ord.id,ord.display_number,misc_type.value as approval,ord.approval_id,cl.client_company'),DB::raw("(SELECT count(*) from artjob_screensets ass WHERE ass.order_id = ord.id AND ass.screen_active='1') as total_screen"))
        				->where('ord.is_delete','=','1')
 		                ->where('ord.company_id','=',$post['company_id']);
		                if($search != '')               
		                 {
		                     $admindata = $admindata->Where(function($query) use($search)
		                     {
		                         $query->orWhere('ord.display_number', '=', $search)
		                               ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')
		                               ->orWhere('cl.client_company','LIKE', '%'.$search.'%');
		                     });
		                }
		                if($client_filter != '')               
		                 {
		                     $admindata = $admindata->Where(function($query) use($client_filter)
		                     {
		                         $query->whereIn('cl.client_id',$client_filter);
		                     });
		                }
		                $admindata = $admindata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
		                ->skip($post['start'])
		                ->take($post['range'])
		                ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $admindata;
        $returnData['count'] = $count[0]->Totalcount;
        

       // echo "<pre>"; print_r($returnData); echo "</pre>"; die;
        return $returnData;
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
    
    public function ScreenSets($post) // ART SCREEN DETAIL PAGE FOR SCREEN SETS
	{
		$query = DB::table('artjob_screensets as ass')

			->select('art.approval','or.name as order_name','or.company_id','or.created_date','cc.first_name','cc.last_name','cl.billing_email','cl.display_number','or.display_number as ord_displayId','cl.client_id','cl.client_company','mt.value as position_name','ass.screen_count','ass.screen_set','ass.positions','ass.display_number as screen_display','ass.id as screen_id','odp.color_stitch_count','ass.frame_size','ass.line_per_inch','ass.screen_width','ass.screen_height','odp.image_1','art.mokup_image','ass.screen_location','ass.screen_active','ass.order_id',DB::raw("(SELECT count(*) FROM artjob_screencolors WHERE screen_id=ass.id) as screen_total"),'or.approval_id')
				->join('art as art','art.order_id','=','ass.order_id')
				->join('orders as or','art.order_id','=','or.id')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->join('order_design as od','od.id','=','odp.design_id')
				->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
				->where('or.is_delete','=','1')
				->where('odp.is_delete','=','1')
				->where('od.is_delete','=','1')
				->where('or.company_id','=',$post['company_id'])
				->where('or.display_number','=',$post['display_number'])
				->orderBy('ass.id','desc')
				->get();

		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$value->created_date = date("m/d/Y",strtotime($value->created_date));

				$value->mokup_image_url= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/",$value->mokup_image);
				$value->mokup_logo_url= $this->common->checkImageExist($value->company_id.'/order_design_position/'.$value->positions."/",$value->image_1);

			}
		}
		return $query;
	}

	// CLIENT MODULE ART LISTING.
    public function Client_art_screen($client_id,$company_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select('ass.screen_set','ass.screen_width','ass.display_number','ass.screen_height','ass.positions','or.id as order_id','or.company_id','ass.id as screen_id','ass.mokup_image','odp.image_1')
				->join('order_design_position as odp','ass.positions','=','odp.id')	
				->join('order_design as od','od.id','=','odp.design_id')
				->join('orders as or','ass.order_id','=','or.id')
				->where('or.client_id','=',$client_id)
				->where('or.company_id','=',$company_id)
				->where('or.is_delete','=','1')
				->where('odp.is_delete','=','1')
				->where('od.is_delete','=','1')
				->where('ass.screen_active','=','1')
				->orderBy('ass.screen_order','asc')
				->orderBy('ass.screen_order','asc')
				->orderBy('ass.id','desc')
				->get();
		if(count($query)>0)
		{
			foreach ($query as $value) 
			{
				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/",$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/order_design_position/'.$value->positions."/",$value->image_1);
			}
		}
		return $query;
    }

    // ART SCREEN SETS LISTING, ORDER POSITIONS
    public function Screen_Listing ($post)
    {
    			$search = ''; $client_filter=''; $width_filter='';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        if(isset($post['filter']['width'])) {
            $width_filter = $post['filter']['width'];
        }
        if(isset($post['filter']['client'])) {
            $client_filter = $post['filter']['client'];
        }
        $admindata = DB::table('order_design_position as odp')
					->select(DB::raw('SQL_CALC_FOUND_ROWS asc.screen_set,odp.id,odp.color_stitch_count,cl.client_company,mt.value,asc.screen_width,asc.id as screen_id,asc.display_number,ord.approval_id,ord.id as order_id'),DB::raw("(SELECT count(*) FROM artjob_screencolors WHERE screen_id=asc.id) as screen_total"))
					->join('artjob_screensets as asc','asc.positions','=','odp.id')
					->join('order_design as od','od.id','=','odp.design_id')
					->join('orders as ord','ord.id','=','od.order_id')
					->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
					->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
					->where('ord.is_delete','=','1')
					->where('od.is_delete','=','1')
					->where('odp.is_delete','=','1')
					->where('ord.is_complete','=','1')
			        ->where('ord.company_id','=',$post['company_id']);
		            
		            if($search != '')               
	                {
	                    $admindata = $admindata->Where(function($query) use($search)
	                    {
	                        $query->orWhere('ord.id', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('asc.screen_width', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('asc.screen_set', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('mt.value', 'LIKE', '%'.$search.'%')
	                        	  ->orWhere('cl.client_company','LIKE', '%'.$search.'%');
	                    });
	                }
	                if($client_filter != '')               
		                {
		                    $admindata = $admindata->Where(function($query) use($client_filter)
		                    {
		                        $query->whereIn('cl.client_id',$client_filter);
		                    });
		                }
		            if($width_filter != '')               
		                {
		                    $admindata = $admindata->Where(function($query) use($width_filter)
		                    {
		                        $query->whereIn('asc.screen_width',$width_filter);
		                    });
		                }

	                $admindata = $admindata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
	                ->skip($post['start'])
	                ->take($post['range'])
	                ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $admindata;
        $returnData['count'] = $count[0]->Totalcount;
        

        //echo "<pre>"; print_r($returnData); echo "</pre>"; die;
        return $returnData;
    }
    
    //ARTDETAIL PAGE SCREEN SETS LISTING. XXXXXX
    public function GetScreenset_detail($position_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select(DB::raw("(SELECT count(*) FROM artjob_screencolors WHERE screen_id=ass.id) as screen_total"),'ord.id as order_id','od.id as design_id','odp.color_stitch_count','mt.value','ass.*','ord.company_id')
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->join('order_design as od','odp.design_id','=','od.id')
				->join('orders as ord','ord.id','=','od.order_id')
				->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
				->where('ass.id','=',$position_id)
				->groupby('ass.id')
				->get();

				return $query;
    }
    // CREATE/ACTIVE SCREEN SETS
    public function create_screen($post)
    {
    	//echo "<pre>"; print_r($post); echo "</pre>"; die;
    	$alldata = $post['alldata'];
    	if(isset($alldata['screen_width']))
    	{
            $design_display = $this->common->GetTableRecords('order_design',array('id' => $alldata['design_id']),array());
            $design_display = $design_display['0']->display_number;

    		$value = str_replace(" ","",strtolower(trim($alldata['value'])));
    		$screen_set_name = $post['display_order']."_".$value."_".$design_display."_".$alldata['screen_width']; 
    	}
    	$result = DB::table('artjob_screensets')->where('id','=',$alldata['id'])->update(array('screen_set'=>$screen_set_name,'screen_active'=>'1','frame_size'=>$alldata['frame_size'],'screen_location'=>$alldata['screen_location'],'line_per_inch'=>$alldata['line_per_inch'],'screen_date'=>date('Y-m-d'),'screen_width'=>$alldata['screen_width'],'screen_height'=>$alldata['screen_height']));
    	$sort=1;
    	if(!empty($post['add_screen_color']))
    	{
    		foreach ($post['add_screen_color'] as $key=>$value) 
    		{
    			$result = $this->common->InsertRecords('artjob_screencolors',array("screen_id"=>$alldata['id'],'color_name'=>$value['id'],'thread_color'=>$value['thread_color'],'inq'=>$value['inq'],'head_location'=>$key+1));
    			$sort=$key+1;
    		}
    	}
    	if(!empty($post['change_color']))
    	{
    		foreach ($post['change_color'] as $key=>$value) 
    		{
    			$result = $this->common->UpdateTableRecords('artjob_screencolors',array('id'=>$value['id']),array('thread_color'=>$value['thread_color'],'inq'=>$value['inq'],'head_location'=>$sort));
    			$sort++;
    		}
    	}
    	if(!empty($post['remove_screen_color']))
    	{
    		foreach ($post['remove_screen_color'] as $value) 
    		{
    			$result = $this->common->DeleteTableRecords('artjob_screencolors',array('id'=>$value['id']));
    		}
    	}
    	return $result;
    }

 	//SCREEN SETS DETAIL PAGE COLOR LISTING
    public function GetscreenColor($screen_id,$company_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select(DB::raw("(SELECT COUNT(*) FROM art_notes WHERE screenset_id=ass.id AND is_deleted='1') as note_total"),'or.name as order_name','or.company_id','or.display_number as order_display','or.id as order_id',DB::raw('DATE_FORMAT(or.created_date, "%m/%d/%Y") as created_date'),'cc.first_name','cc.last_name','cl.display_number','cl.client_id','cl.client_company','ass.screen_set','ass.display_number as screen_display','ass.mokup_image','odp.image_1','ass.positions','ass.approval','acol.*','mt.value as ink_value','ass.id as screen_id')
				->leftjoin('artjob_screencolors as acol','acol.screen_id','=','ass.id')
				->join('orders as or','ass.order_id','=','or.id')
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->leftJoin('misc_type as mt','mt.id','=','acol.inq')
				->where('ass.display_number','=',$screen_id)
				->where('ass.company_id','=',$company_id)
				->groupby('acol.id')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				//echo "<pre>"; print_r($query); echo "</pre>"; die;
				return $query;
    }
    public function UpdateColorScreen($post)
    {

    	$result = DB::table('artjob_screencolors')
    				->where('id','=',$post['id'])
    				->update(array('thread_color'=>$post['thread_color'],
    							   'inq'=>$post['inq'],
    							   'stroke'=>$post['stroke'],
    							   'squeegee'=>$post['squeegee'],
    							   'mesh_thread_count'=>$post['mesh_thread_count'],
    							   'head_location'=>$post['head_location'],
    							   'is_complete'=>'1'
    							   ));
    				return $result;
    }
    public function getArtColorNote($post)
   	{
       	$search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

		$result = DB::table('art_notes as note')
					->leftjoin('artjob_screensets as ass','ass.id','=','note.screenset_id')
					->select('note.*')
					->where('note.is_deleted','=','1')
					->where('ass.display_number','=',$post['display_number'])
					->where('ass.company_id','=',$post['company_id']);

					if($search != '')               
                  	{
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('note.note_title', 'LIKE', '%'.$search.'%')
                                ->orWhere('note.note','LIKE', '%'.$search.'%')
                                ->orWhere('note.note_date','LIKE', '%'.$search.'%');
                      });
                  	}
                 $result = $result->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
				 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
		
		//echo "<pre>"; print_r($result); echo "</pre>"; die;
        if(count($result)>0)
        {
          	foreach ($result as $key=>$value) 
          	{
          		$value->note_date = ($value->note_date=='0000-00-00' || empty($value->note_date))?date("m/d/Y"):date('m/d/Y',strtotime($value->note_date));
          		$value->artapproval_display = ($value->artapproval_display=='0')? false: true;
          	}
        }
		$count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;		
		//echo "<pre>"; print_r($result); die();
		return $returnData;
	}
	public function getScreenSizes($company_id)
	{
		$query = DB::table('artjob_screensets as ass')
				->select('ass.screen_width as label','ass.screen_width as id')
				->join('orders as ord','ass.order_id','=','ord.id')
				->where('ord.company_id','=',$company_id)
				->where('ass.screen_width','<>','')
				->where('ass.screen_active','=','1')
				->groupby('ass.screen_width')
				->orderBy('ass.screen_width','asc')
				->get();
				return $query;
	}
	public function change_sortcolor($post)
	{
		foreach ($post as $key=>$value) 
		{
			if(!empty($value['id']))
			{
				DB::table('artjob_screencolors')->where('id','=',$value['id'])->update(array('head_location'=>$key+1));
			}
		}
	}
	public function change_sortscreen($post)
	{
		foreach ($post as $key=>$value) 
		{
			if(!empty($value['screen_id']))
			{
				DB::table('artjob_screensets')->where('id','=',$value['screen_id'])->update(array('screen_order'=>$key));
			}
		}
	}
	public function array_values_recursive( $array ) 
	{
	    $array = array_values( $array );
	    for ( $i = 0, $n = count( $array ); $i < $n; $i++ ) 
	    {
	        $element = $array[$i];
	        if ( is_array( $element ) ) 
	        {
	            $array[$i] = $this->array_values_recursive( $element );
	        }
    	}
    return $array;
	}

	// ART APPROVAL ALL PRODUCTS FROM ORDER
	public function getArtApprovalProducts($order_id,$company_id)
	{
		$query = DB::table('orders as or')
					->select('or.name as order_name','or.company_id','or.date_shipped','or.in_hands_by','or.id as order_id','or.custom_po','p.name as product_name','pdtl.product_id','pdtl.size','pdtl.qnty','col.name as product_color','pdtl.price',
					DB::raw("(SELECT SUM(qnty) FROM purchase_detail WHERE product_id =p.id and design_id=od.id) as total_product"))
					->join('order_design as od','od.order_id','=','or.id')
					->leftjoin('design_product as dp','dp.design_id','=','od.id')
					->leftjoin('products as p','dp.product_id','=','p.id')
					->leftjoin('purchase_detail as pdtl','pdtl.design_product_id','=','dp.id')	
					->leftjoin('color as col','col.id','=','pdtl.color_id')
					
					->where('or.id','=',$order_id)
					->where('or.company_id','=',$company_id)
					->where('od.is_delete','=','1')		
					->where('dp.is_delete','=','1')
					->get();
				$product = array();
				foreach ($query as $key=>$value) 
				{
					$product[$value->product_id]['product_name']= $value->product_name;
					$product[$value->product_id]['product_color'] = $value->product_color;
					$product[$value->product_id]['product_id'] = $value->product_id;
					$product[$value->product_id]['summary'][$value->size]= $value->qnty;
					$product[$value->product_id]['total_product'] = $value->total_product;
					$product[$value->product_id]['price'] = $value->price;

				}
		return $product;
	}

	// ART APPROVAL PDF
	public function getArtApprovalPDFdata($order_id,$company_id)
	{
		$query = DB::table('artjob_screensets as ass')
				->select('or.name as order_name','or.custom_po','inv.payment_due_date','or.date_shipped','or.company_id','cl.is_blind','or.in_hands_by','or.id as order_id','or.created_date','cc.first_name','cc.last_name','cl.client_id','cl.client_company','ass.screen_set','ass.id as screen_id','stf.first_name as f_name','stf.last_name as l_name','stf.prime_address_city','stf.prime_address_street','stf.prime_address_state','stf.prime_address_zip','stf.prime_phone_main','stf.photo as companyphoto','stf.id as staff_id','stf.prime_address1','art.mokup_image','odp.image_1','ass.screen_height','ass.positions','ass.line_per_inch','ass.frame_size','ass.screen_width','ass.screen_location','acol.*','col.name as color_name','col.color_code','cl.client_company','usr.name as companyname','usr1.name as account_manager','cl.billing_email','cl.blind_text','cl.b_w_logo','od.design_name','an.note_title','an.note','an.id as note_id','an.screenset_id as notscreen','mt.value as inq','ca.address','mt1.slug as placement_type','mt2.value as position_name','ca.street','ca.city','st.code as state_name','ca.postal_code')
				->leftjoin('art_notes as an','an.screenset_id','=',DB::raw("ass.id AND is_deleted = '1' AND artapproval_display='1'"))
				->join('orders as or','ass.order_id','=','or.id')
				->leftJoin('users as usr','usr.id','=','or.company_id')
				->leftjoin('users as usr1','usr1.id','=','or.account_manager_id')
				->leftJoin('staff as stf','stf.user_id','=','usr.id')
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->join('order_design as od','od.id','=','odp.design_id')
				->join('art as art','art.order_id','=','or.id')
				->leftjoin('artjob_screencolors as acol','acol.screen_id','=','ass.id')
				->leftjoin('color as col','col.id','=','acol.color_name')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->leftJoin('client_address as ca','cl.client_id','=',DB::raw("ca.client_id AND ca.address_shipping = '1' "))
				->leftJoin('misc_type as mt','mt.id','=',DB::raw("acol.inq AND mt.type = 'art_type'"))
				->leftJoin('misc_type as mt1','mt1.id','=',DB::raw("odp.placement_type AND mt1.type = 'placement_type'"))
				->leftJoin('misc_type as mt2','mt2.id','=',DB::raw("odp.position_id AND mt2.type = 'position'"))
				->leftJoin('state as st','st.id','=',"ca.state")
				->leftjoin('invoice as inv','inv.order_id','=','or.id')
				->where('or.id','=',$order_id)
				->where('or.company_id','=',$company_id)
				->where('odp.is_delete','=','1')
				->where('od.is_delete','=','1')
				->where('ass.screen_active','=','1')
				->orderBy('ass.screen_order','asc')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				$transfer = array();
		foreach ($query as $key=>$value) 
		{
				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/",$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/order_design_position/'.$value->positions."/",$value->image_1);
				
				if(!empty($value->is_blind))
				{
					$value->companyphoto= $this->common->checkImageExist($value->company_id.'/client/'.$value->client_id."/",$value->b_w_logo);
				}
				else
				{
					$value->companyphoto= $this->common->checkImageExist($value->company_id.'/staff/'.$value->staff_id."/",$value->companyphoto);
				}
				

				$value->in_hands_by  = (!empty($value->in_hands_by)&& $value->in_hands_by!='0000-00-00')?date("m/d/Y",strtotime($value->in_hands_by)):'';
				$value->date_shipped  = (!empty($value->date_shipped)&& $value->date_shipped!='0000-00-00')?date("m/d/Y",strtotime($value->date_shipped)):'';
				$value->payment_due_date  = (!empty($value->payment_due_date)&& $value->payment_due_date!='0000-00-00')?date("m/d/Y",strtotime($value->payment_due_date)):'';

				$transfer[$value->screen_id]['colors'][$value->id] = $value;

				if(!empty($value->note_id))
				{
					$transfer[$value->screen_id]['art_notes'][$value->note_id] = "<b>- </b>".$value->note;
				}
		}
		
		
		$transfer = $this->array_values_recursive($transfer);
		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		return $transfer;
	}


	// PRESS INSTRUCTION PDF
	public function getPressInstructionPDFdata($screen_id,$company_id)
	{
		//echo $screen_id."-".$company_id;
		$query = DB::table('artjob_screensets as ass')
				->select('or.name as order_name','or.company_id','inv.payment_due_date','or.date_shipped','or.in_hands_by','or.id as order_id','or.custom_po','ass.screen_set','ass.screen_width','ass.screen_height','ass.id as screen_id','stf.id as staff_id','stf.photo as companyphoto','ass.mokup_image','odp.image_1','acol.*','acol.id as color_id','odp.design_id','odp.note','col.name as color_name','col.color_code','ass.positions','usr1.name as account_manager','usr.name as companyname','p.name as product_name','pdtl.product_id','cl.client_company','cl.is_blind','cl.b_w_logo','cl.client_id','pdtl.size','pdtl.qnty','col1.name as product_color','mt.value as inq','mt1.slug as placement_type','mt2.value as position_name',
					DB::raw("(SELECT SUM(qnty) FROM purchase_detail WHERE product_id =p.id and design_id=od.id) as total_product"),'ca.address','ca.street','ca.city','st.code as state_name','ca.postal_code')
				->leftjoin('artjob_screencolors as acol','acol.screen_id','=','ass.id')
				->join('order_design_position as odp','ass.positions','=','odp.id')	
				->join('order_design as od','od.id','=','odp.design_id')
				->leftjoin('design_product as dp','dp.design_id','=','od.id')
				->leftjoin('products as p','dp.product_id','=','p.id')
				->leftjoin('purchase_detail as pdtl','pdtl.design_product_id','=','dp.id')
				->join('orders as or','ass.order_id','=','or.id')
				->leftjoin('color as col','col.id','=','acol.color_name')
				->leftjoin('color as col1','col1.id','=','pdtl.color_id')
				->join('users as usr','usr.id','=','or.company_id')
				->leftjoin('users as usr1','usr1.id','=','or.account_manager_id')
				->leftJoin('staff as stf','stf.user_id','=','usr.id')
				->leftJoin('misc_type as mt','mt.id','=','acol.inq')
				->leftJoin('misc_type as mt1','mt1.id','=','odp.placement_type')
				->leftJoin('misc_type as mt2','mt2.id','=',DB::raw("odp.position_id AND mt2.type = 'position'"))
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_address as ca','cl.client_id','=',DB::raw("ca.client_id AND ca.address_shipping = '1' "))
				->leftJoin('state as st','st.id','=',"ca.state")
				->leftjoin('invoice as inv','inv.order_id','=','or.id')
				->where('ass.id','=',$screen_id)
				->where('or.company_id','=',$company_id)
				/*->where('acol.is_complete','=','1')*/
				->where('or.is_delete','=','1')
				->where('odp.is_delete','=','1')
				->where('od.is_delete','=','1')
				->orderBy('ass.screen_order','asc')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				$color = array();
				$size = array();
				//echo "<pre>"; print_r($query); echo "</pre>"; die;
			foreach ($query as $key=>$value) 
			{

				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/",$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/order_design_position/'.$value->positions."/",$value->image_1);
				if(!empty($value->is_blind))
				{
					$value->companyphoto= $this->common->checkImageExist($value->company_id.'/client/'.$value->client_id."/",$value->b_w_logo);
				}
				else
				{
					$value->companyphoto= $this->common->checkImageExist($value->company_id.'/staff/'.$value->staff_id."/",$value->companyphoto);
				}

				$value->payment_due_date  = (!empty($value->payment_due_date)&& $value->payment_due_date!='0000-00-00')?date("m/d/Y",strtotime($value->payment_due_date)):'';
				$value->in_hands_by  = (!empty($value->in_hands_by)&& $value->in_hands_by!='0000-00-00')?date("m/d/Y",strtotime($value->in_hands_by)):'';
				$value->date_shipped  = (!empty($value->date_shipped)&& $value->date_shipped!='0000-00-00')?date("m/d/Y",strtotime($value->date_shipped)):'';
				$color[$value->color_id] = $value;
				

				$size[$value->product_id]['product_name']= $value->product_name;
				$size[$value->product_id]['product_color'] = $value->product_color;
				$size[$value->product_id]['product_id'] = $value->product_id;
				$size[$value->product_id]['summary'][$value->size]= $value->qnty;
				$size[$value->product_id]['total_product'] = $value->total_product;
			
			}
		$color = array_values($color);
		//	$size = array_values($size);
		$pass = array('color'=>$color,'size'=>$size);
		return $pass;
	}

}