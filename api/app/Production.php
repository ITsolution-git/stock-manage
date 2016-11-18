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
                        ->select(DB::raw('SQL_CALC_FOUND_ROWS ord.name as order_name,ord.display_number as order_display, ord.id as order_id,cl.client_company ,ord.in_hands_by,mt.value,odp.id,ass.id as screenset,ass.screen_active,ass.approval,mt1.value as production_type,odp.image_1'))
                        ->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
                        ->leftjoin('order_design as od','ord.id','=','od.order_id')
                        ->leftjoin('order_design_position as odp','odp.design_id','=','od.id')
                        ->leftjoin('artjob_screensets as ass','ass.positions','=','odp.id')
                        ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                        ->leftjoin('misc_type as mt1','mt1.id','=','odp.placement_type')
                        ->where('od.is_delete','=','1')
                        ->where('odp.is_delete','=','1')
                        ->where('ord.company_id','=',$post['company_id']);
		                if($search != '')               
		                  {
		                      $production_data = $production_data->Where(function($query) use($search)
		                      {
		                          $query->orWhere('ord.name', 'LIKE', '%'.$search.'%')
                                        ->orWhere('ord.name', 'LIKE', '%'.$search.'%')
                                        ->orWhere('mt.value', 'LIKE', '%'.$search.'%')
		                                ->orWhere('mt1.value','LIKE', '%'.$search.'%');
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
                if($value->approval==1){$value->screen_icon = '2';} 
                elseif($value->screen_active=='1'){$value->screen_icon='1';} 
                else{$value->screen_icon='0';}
            	$value->in_hands_by =($value->in_hands_by=='0000-00-00')?'':date('m/d/Y',strtotime($value->in_hands_by)) ;
                $value->image_1= file_exists(FILEUPLOAD.$post['company_id'].'/order_design_position/'.$value->id.'/'.$value->image_1)?UPLOAD_PATH.$post['company_id'].'/order_design_position/'.$value->id.'/'.$value->image_1:'';
                $value->garment = $this->CheckWarehouseQuantity($value->id);
          	}
        }
        //echo "<pre>"; print_r($production_data); echo "</pre>"; die();
        $returnData['allData'] = $production_data;
        $returnData['count'] = $count[0]->Totalcount;
        
        return $returnData;

    }
    public function CheckWarehouseQuantity($PositionId)
    {
        $garment = DB::table('order_design_position as odp')
                        ->select(DB::raw('SUM(pol.qnty_ordered) as purchase'),DB::raw('SUM(pol.qnty_purchased) as received'))
                        ->leftjoin('order_design as od','odp.design_id','=','od.id')
                        ->leftjoin('purchase_detail as pd','pd.design_id','=','od.id')
                        ->leftjoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
                        ->leftjoin('purchase_order as po','po.po_id','=','pol.po_id')
                        ->where('odp.id','=',$PositionId)
                        ->where('po.is_active','=','1')
                        ->groupby('odp.id')
                        ->get();

                    
        if(count($garment)>0)
        {
            //$garment[0]->result = 1;
            if(empty($garment[0]->received) || $garment[0]->purchase > $garment[0]->received)
            {
                return $garment[0]->result = 1; // NO GARMENTS
            }
            else
            {
                return  $garment[0]->result = 0; // GARMENTS ARE AVAILABLE IN WAREHOUSE
            }
        }
         return 1;// NO GARMENTS
    }


 }