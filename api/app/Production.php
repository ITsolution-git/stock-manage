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
        if(isset($post['filter']['client'])) {
            $client_filter = $post['filter']['client'];
        }
        if(isset($post['filter']['production'])) {
            $production_filter = $post['filter']['production'];
        }
        if(isset($post['filter']['rundate'])) {
            $rundate_filter = $post['filter']['rundate'];
        }
        if(isset($post['filter']['inhandDate'])) {
            $inhandDate_filter = $post['filter']['inhandDate'];
        }
        $production_data = DB::table('orders as ord')
                        ->select(DB::raw('SQL_CALC_FOUND_ROWS ord.name as order_name,ord.display_number as order_display, ord.id as order_id,cl.client_company ,ord.in_hands_by,mt.value,odp.id,ass.id as screenset,ass.screen_active,ass.approval,mt1.value as production_type,odp.image_1,ps.run_date'))
                        ->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
                        ->leftjoin('order_design as od','ord.id','=','od.order_id')
                        ->leftjoin('order_design_position as odp','odp.design_id','=','od.id')
                        ->leftjoin('position_schedule as ps','ps.position_id','=','odp.id')
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
                                        ->orWhere('cl.client_company', 'LIKE', '%'.$search.'%')
    	                                ->orWhere('mt1.value','LIKE', '%'.$search.'%');
    	                    });
    	                }
                        if(!empty($rundate_filter))               
                        {
                            $production_data = $production_data->Where(function($query) use($rundate_filter)
                            {
                                $rundate_filter = date('Y-m-d',strtotime($rundate_filter));
                                $query->orWhere('ps.run_date', '=', $rundate_filter);
                            });
                        }
                        if(!empty($inhandDate_filter))               
                        {
                            $production_data = $production_data->Where(function($query) use($inhandDate_filter)
                            {
                                $inhandDate_filter = date('Y-m-d',strtotime($inhandDate_filter));
                                $query->orWhere('ord.in_hands_by', '=', $inhandDate_filter);
                            });
                        }
                        if(!empty($client_filter))               
                        {
                             $production_data = $production_data->Where(function($query) use($client_filter)
                             {
                                 $query->whereIn('cl.client_id',$client_filter);
                             });
                        }
                        if(!empty($production_filter))               
                        {
                             $production_data = $production_data->Where(function($query) use($production_filter)
                             {
                                 $query->whereIn('mt1.id',$production_filter);
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
            	$value->in_hands_by =($value->in_hands_by=='0000-00-00' || $value->in_hands_by=='')?'':date('m/d/Y',strtotime($value->in_hands_by)) ;
                $value->run_date =($value->run_date=='0000-00-00' || $value->run_date=='')?'':date('m/d/Y',strtotime($value->run_date)) ;
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

    // PRODUCTION POSITION DETAIL
    public function GetPositionDetails($PositionId,$company_id)
    {

       $positionInfo = DB::table('order_design_position as odp')
                        ->select('cs.shift_name','odp.id as position_id','cl.client_company','mt.value as position_name','mt1.value as inq','col.name as color_name','acol.thread_color','acol.mesh_thread_count','acol.squeegee','ass.screen_set','ass.screen_height','ass.line_per_inch','ass.screen_width','ass.frame_size','odp.note','odp.color_stitch_count','ass.screen_resolution','ass.screen_count','ass.screen_location',DB::raw('DATE_FORMAT(ps.run_date, "%m/%d/%Y") as run_date'),'mc.machine_name',DB::raw('DATE_FORMAT(ord.in_hands_by, "%m/%d/%Y") as in_hands_by'),'mc.machine_type','mc.screen_width as machine_width','mc.screen_height as machine_height',DB::raw("(odp.color_stitch_count+odp.foil_qnty) as screen_total"))
                        ->leftjoin('order_design as od','odp.design_id','=','od.id')
                        ->leftjoin('orders as ord','ord.id','=','od.order_id')
                        ->Join('client as cl', 'cl.client_id', '=', 'ord.client_id')
                        ->leftjoin('position_schedule as ps','ps.position_id','=','odp.id')
                        ->leftjoin('company_shift as cs','cs.id','=','ps.shift_id')
                        ->leftjoin('machine as mc','mc.id','=','ps.machine_id')
                        ->leftjoin('artjob_screensets as ass','ass.positions','=','odp.id')
                        ->leftjoin('artjob_screencolors as acol','ass.id','=','acol.screen_id')
                        ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                        ->leftjoin('misc_type as mt1','mt1.id','=','acol.inq')
                        ->leftjoin('color as col','col.id','=','acol.color_name')
                        ->where('odp.id','=',$PositionId)
                        ->get();
        foreach ($positionInfo as $key=>$value) 
        {
                array_walk_recursive($value, function(&$item) {
                            $item = str_replace(array('00/00/0000'),array(''), $item);
                        });
        }
        return $positionInfo;
    }

    public function GetGarmentDetail ($PositionId,$company_id)
    {
        $result = DB::table('order_design_position as odp')
                    ->select('p.name','pd.sku','pd.size','pd.qnty','pol.qnty_purchased','c.name as color_name','pol.qnty_ordered','pol.short','pol.over','pd.product_id','pol.line_total')
                    ->leftjoin('order_design as od','odp.design_id','=','od.id')
                    ->leftjoin('purchase_detail as pd','pd.design_id','=','od.id')
                    ->leftjoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
                    ->leftjoin('purchase_order as po','po.po_id','=','pol.po_id')
                    ->leftjoin('products as p','p.id','=','pd.product_id')
                    ->leftJoin('color as c','c.id','=','pd.color_id')
                    ->where('odp.id','=',$PositionId)
                    ->where('po.is_active','=','1')
                    ->get();
                $ret_array = array();
                if(count($result)>0)
                {

                    $ret_array['po_data']=array();
                    foreach ($result as $key=>$value) 
                    {
                        array_walk_recursive($value, function(&$item) {
                            $item = str_replace(array('00/00/0000'),array(''), $item);
                        });
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
        //echo "<pre>"; print_r($ret_array); echo "</pre>"; die();                    
        return $ret_array;
    }

 }