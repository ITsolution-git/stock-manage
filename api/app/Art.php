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

		$search = ''; $client_filter='';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        if(isset($post['filter']['client'])) {
            $client_filter = $post['filter']['client'];
        }

        $admindata = DB::table('orders as ord')
        				->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
        				->select(DB::raw('SQL_CALC_FOUND_ROWS ord.id,cl.client_company'),DB::raw("(SELECT count(*) from artjob_screensets ass WHERE ass.order_id = ord.id AND ass.screen_active='1') as total_screen"))
        				->where('ord.is_delete','=','1')
        				->where('ord.is_complete','=','1')
		                ->where('ord.company_id','=',$post['company_id']);
		                if($search != '')               
		                 {
		                     $admindata = $admindata->Where(function($query) use($search)
		                     {
		                         $query->orWhere('ord.id', 'LIKE', '%'.$search.'%')
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

			->select('art.approval','or.name as order_name','or.company_id','or.created_date','cc.first_name','cc.last_name','cl.billing_email','cl.client_id','cl.client_company','mt.value as position_name','ass.screen_count','ass.screen_set','ass.id as screen_id','odp.color_stitch_count','ass.frame_size','ass.line_per_inch','ass.screen_width','ass.screen_height','ass.mokup_logo','art.mokup_image','ass.screen_location','ass.screen_active','ass.order_id',DB::raw("(odp.color_stitch_count+odp.foil_qnty) as screen_total"),'or.approval_id')
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
				->where('or.id','=',$post['order_id'])
				->orderBy('ass.screen_order')
				->get();

		if(count($query)>0)
		{
			foreach ($query as $key => $value) 
			{
				$value->created_date = date("m/d/Y",strtotime($value->created_date));

				$value->mokup_image_url= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_image);
				$value->mokup_logo_url= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo);

				//$value->mokup_logo_url  = (!empty($value->mokup_logo))?UPLOAD_PATH.$value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo:'';
				//$value->mokup_image_url  = (!empty($value->mokup_image))?UPLOAD_PATH.$value->company_id.'/art/'.$value->order_id."/".$value->mokup_image:'';
			}
		}
		return $query;
	}

	// CLIENT MODULE ART LISTING.
    public function Client_art_screen($client_id,$company_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select('ass.screen_set','ass.screen_width','ass.screen_height','or.id as order_id','or.company_id','ass.id as screen_id','ass.mokup_image','ass.mokup_logo')
				->join('order_design_position as odp','ass.positions','=','odp.id')	
				->join('order_design as od','od.id','=','odp.design_id')
				->join('orders as or','ass.order_id','=','or.id')
				->where('or.client_id','=',$client_id)
				->where('or.company_id','=',$company_id)
				->where('or.is_complete','=','1')
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
				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo);
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
					->select(DB::raw('SQL_CALC_FOUND_ROWS asc.screen_set,odp.id,odp.color_stitch_count,cl.client_company,mt.value,asc.screen_width,asc.id as screen_id'),DB::raw("(color_stitch_count+foil_qnty) as screen_total"))
					->join('artjob_screensets as asc','asc.positions','=','odp.id')
					->join('order_design as od','od.id','=','odp.design_id')
					->join('orders as ord','ord.id','=','od.order_id')
					->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
					->Join('misc_type as mt', 'mt.id', '=', 'odp.position_id')
					->where('ord.is_delete','=','1')
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
    
    //ARTDETAIL PAGE SCREEN SETS LISTING.
    public function GetScreenset_detail($position_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select(DB::raw("(odp.color_stitch_count+odp.foil_qnty) as screen_total"),'ord.id as order_id','od.id as design_id','odp.color_stitch_count','mt.value','ass.*')
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
    		$value = str_replace(" ","",strtolower(trim($alldata['value'])));
    		$screen_set_name = $alldata['order_id']."_".$value."_".$alldata['design_id']."_".$alldata['screen_width']; 
    	}
    	$result = DB::table('artjob_screensets')->where('id','=',$alldata['id'])->update(array('screen_set'=>$screen_set_name,'screen_active'=>'1','frame_size'=>$alldata['frame_size'],'screen_location'=>$alldata['screen_location'],'line_per_inch'=>$alldata['line_per_inch'],'screen_date'=>date('Y-m-d'),'screen_width'=>$alldata['screen_width'],'screen_height'=>$alldata['screen_height']));
    	$sort=1;
    	if(!empty($post['add_screen_color']))
    	{
    		foreach ($post['add_screen_color'] as $key=>$value) 
    		{
    			$result = $this->common->InsertRecords('artjob_screencolors',array("screen_id"=>$alldata['id'],'color_name'=>$value['id'],'thread_color'=>$value['thread_id'],'inq'=>$value['inq'],'head_location'=>$key+1));
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
    public function GetscreenColor($screen_id)
    {
    	$query = DB::table('artjob_screensets as ass')
				->select(DB::raw("(SELECT COUNT(*) FROM art_notes WHERE screenset_id=ass.id AND is_deleted='1') as note_total"),'or.name as order_name','or.company_id','or.id as order_id','or.created_date','cc.first_name','cc.last_name','cl.client_id','cl.client_company','ass.screen_set','ass.id as screen_id','ass.mokup_image','ass.mokup_logo','ass.approval','acol.*')
				->leftjoin('artjob_screencolors as acol','acol.screen_id','=','ass.id')
				->join('orders as or','ass.order_id','=','or.id')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->where('ass.id','=',$screen_id)
				->groupby('acol.id')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				return $query;
    }
    public function UpdateColorScreen($post)
    {
    	if(!empty($post['thread_display']['id']))
    	{
    		$post['thread_color'] = $post['thread_display']['id'];
    	}
    	else if(empty($post['thread_display']))
    	{
    		$post['thread_color'] ='';
    	}

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
					->select('*')
					->where('note.is_deleted','=','1')
					->where('note.screenset_id','=',$post['screenset_id']);

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
          	$result[$key]->note_date = ($result[$key]->note_date=='0000-00-00' || empty($result[$key]->note_date))?date("m/d/Y"):date('m/d/Y',strtotime($value->note_date));
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
	public function getArtApprovalPDFdata($order_id,$company_id)
	{
		$query = DB::table('artjob_screensets as ass')
				->select('or.name as order_name','or.company_id','or.in_hands_by','or.id as order_id','or.created_date','cc.first_name','cc.last_name','cl.client_id','cl.client_company','ass.screen_set','ass.id as screen_id','stf.first_name as f_name','stf.last_name as l_name','stf.prime_address_city','stf.prime_address_street','stf.prime_address_state','stf.prime_address_zip','stf.prime_phone_main','stf.photo as companyphoto','stf.id as staff_id','stf.prime_address1','art.mokup_image','ass.mokup_logo','ass.screen_height','ass.screen_width','acol.*','col1.name as pantone','col.name as color_name','cl.client_company','usr.name as companyname','cl.billing_email','od.design_name')
				->join('orders as or','ass.order_id','=','or.id')
				->join('order_design_position as odp','odp.id','=','ass.positions')
				->join('order_design as od','od.id','=','odp.design_id')
				->join('art as art','art.order_id','=','or.id')
				->leftjoin('artjob_screencolors as acol','acol.screen_id','=','ass.id')
				->leftjoin('color as col','col.id','=','acol.color_name')
				->leftjoin('color as col1','col1.id','=','acol.thread_color')
				->Join('client as cl', 'cl.client_id', '=', 'or.client_id')
				->leftJoin('client_contact as cc','cl.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
				->leftJoin('users as usr','usr.id','=','or.company_id')
				->leftJoin('staff as stf','stf.user_id','=','usr.id')
				->where('or.id','=',$order_id)
				->where('or.company_id','=',$company_id)
				->where('ass.screen_active','=','1')
				->groupby('acol.id')
				->orderBy('ass.screen_order','asc')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				$temp = array();
		foreach ($query as $key=>$value) 
		{
				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo);
				$value->companyphoto= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->companyphoto);

				$value->in_hands_by  = (!empty($value->in_hands_by)&& $value->in_hands_by!='0000-00-00')?date("m/d/Y",strtotime($value->in_hands_by)):'';
				$temp[$value->screen_id][] = $value;
		}
		
		//echo "<pre>"; print_r(FILEUPLOAD.$mokupImage_path); echo "</pre>"; die;
		$temp = array_values($temp);
		return $temp;
	}
	public function getPressInstructionPDFdata($screen_id,$company_id)
	{
		$query = DB::table('artjob_screencolors as acol')
				->select('or.name as order_name','or.company_id','or.id as order_id','ass.screen_set','ass.id as screen_id','stf.id as staff_id','stf.photo as companyphoto','ass.mokup_image','ass.mokup_logo','acol.*','acol.id as color_id','col.name as color_name','usr.name as companyname','p.name as product_name','pdtl.size','pdtl.qnty','col1.name as product_color','col2.name as pantone')
				->leftjoin('artjob_screensets as ass','acol.screen_id','=','ass.id')
				->join('order_design_position as odp','ass.positions','=','odp.id')	
				->join('order_design as od','od.id','=','odp.design_id')
				->leftjoin('design_product as dp','dp.design_id','=','od.id')
				->leftjoin('products as p','dp.product_id','=','p.id')
				->leftjoin('purchase_detail as pdtl','pdtl.design_id','=','od.id')
				->join('orders as or','ass.order_id','=','or.id')
				->leftjoin('color as col','col.id','=','acol.color_name')
				->leftjoin('color as col2','col2.id','=','acol.thread_color')
				->leftjoin('color as col1','col1.id','=','pdtl.color_id')
				->join('users as usr','usr.id','=','or.company_id')
				->leftJoin('staff as stf','stf.user_id','=','usr.id')
				->where('ass.id','=',$screen_id)
				->where('or.company_id','=',$company_id)
				/*->where('acol.is_complete','=','1')*/
				->where('or.is_delete','=','1')
				->where('pdtl.is_delete','=','1')
				->where('odp.is_delete','=','1')
				->where('od.is_delete','=','1')
				->orderBy('ass.screen_order','asc')
				->orderBy('acol.head_location','asc')
				->orderBy('acol.id','desc')
				->get();
				$color = array();
				$size = array();
			foreach ($query as $key=>$value) 
			{
				$value->mokup_image= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_image);
				$value->mokup_logo= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo);
				$value->companyphoto= $this->common->checkImageExist($value->company_id.'/art/'.$value->order_id."/".$value->companyphoto);

				$value->in_hands_by  = (!empty($value->in_hands_by)&& $value->in_hands_by!='0000-00-00')?date("m/d/Y",strtotime($value->in_hands_by)):'';
				$color[$value->color_id] = $value;
				$size[$value->size] = $value;
			}
		$color = array_values($color);
		$size = array_values($size);
		$pass = array('color'=>$color,'size'=>$size);
		return $pass;
	}

}