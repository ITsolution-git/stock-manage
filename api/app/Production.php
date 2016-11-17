<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use DateTime;
 
class Production extends Model {

    public function __construct(Common $common) 
    {
        $this->common = $common;
    }

    public function GetProductionList($post) {
        
        
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $production_data = DB::table('orders as ord')
                        ->select(DB::raw('SQL_CALC_FOUND_ROWS ord.name as order_name,ord.display_number as order_display, ord.id as order_id,cl.client_company ,ord.in_hands_by'))
                        ->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
                        ->where('ord.company_id','=',$post['company_id']);
		                if($search != '')               
		                  {
		                      $production_data = $production_data->Where(function($query) use($search)
		                      {
		                          $query->orWhere('ord.name', 'LIKE', '%'.$search.'%')
		                                ->orWhere('cl.client_company','LIKE', '%'.$search.'%');
		                      });
		                  }
                 $production_data = $production_data->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        if(count($production_data>0))
        {
         	foreach ($production_data as $key=>$value) 
          	{
            	$value->in_hands_by =($value->in_hands_by=='0000-00-00')?'':date('m/d/Y',strtotime($value->in_hands_by)) ;
            	$value->positionsData = $this->getScreensfromOrders($value->order_id,$post['company_id']);	
          	}
        }
       // echo "<pre>"; print_r($production_data); echo "</pre>"; die();
        $returnData['allData'] = $production_data;
        $returnData['count'] = $count[0]->Totalcount;
        
        return $returnData;

    }
    public function getScreensfromOrders($order_id,$company_id)
    {
	 	$production_subdata = DB::table('order_design as od')
                    ->select('mt.value','odp.id','ass.id as screenset','ass.screen_active','ass.approval','mt1.value as production_type','odp.image_1')
                     ->leftjoin('order_design_position as odp','odp.design_id','=','od.id')
                     ->leftjoin('artjob_screensets as ass','ass.positions','=','odp.id')
                     ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                     ->leftjoin('misc_type as mt1','mt1.id','=','odp.placement_type')
                     ->where('od.is_delete','=','1')
                     ->where('odp.is_delete','=','1')
                     ->where('od.order_id','=',$order_id)
                     ->get();
            foreach ($production_subdata as $key => $value) 
            {
        		if(!empty($value->image_1))
		        {
		            $value->image_1= file_exists(FILEUPLOAD.$company_id.'/order_design_position/'.$value->id.'/'.$value->image_1)?UPLOAD_PATH.$company_id.'/order_design_position/'.$value->id.'/'.$value->image_1:'';
		        }
            }
         return $production_subdata;
    }

 }