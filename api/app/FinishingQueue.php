<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class FinishingQueue extends Model {

    
    public function getFinishingdata($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS f.id,c.client_company,o.id as order_id,o.name,c.client_id,o.approval_id,o.display_number,fc.item as category_name,o.due_date,o.in_hands_by as in_hands_date')];

        $finishingData = DB::table('finishing as f')
                        ->leftJoin('orders as o', 'o.id', '=', 'f.order_id')
                        ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                        ->leftJoin('price_grid_charges as fc', 'f.category_id', '=', 'fc.id')
                        ->select($listArray)
                        ->where('o.company_id', '=', $post['company_id'])
                        ->where('fc.id', '>',0);
                        if($post['type'] == 'unscheduled')
                        {
                            $finishingData = $finishingData->Where('f.is_schedule','=',0);
                        }
                        if($post['type'] == 'scheduled')
                        {
                            $finishingData = $finishingData->Where('f.is_schedule','=',1);
                        }
                        if($search != '')
                        {
                            $finishingData = $finishingData->Where(function($query) use($search)
                            {
                                $query->orWhere('o.name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('c.client_company', 'LIKE', '%'.$search.'%')
                                    ->orWhere('fc.item', 'LIKE', '%'.$search.'%');
                            });
                        }
                        $finishingData = $finishingData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->GroupBy('f.id')
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        //dd(DB::getQueryLog());
        $returnData = array();
        $returnData['allData'] = $finishingData;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;

        return $finishingData;  
    }

    public function getFinishingByOrder($order_id)
    {
        $listArray = ['f.id','f.qty','fc.item as category_name','f.status','f.note','f.category_id','f.time','f.start_time','f.end_time','f.est','od.design_name','p.name as product_name','p.id as product_id','od.id as design_id','f.note'];

        $finishingData = DB::table('orders as o')
                        ->leftJoin('finishing as f', 'o.id', '=', 'f.order_id')
                        ->leftJoin('price_grid_charges as fc', 'f.category_id', '=', 'fc.id')
                        ->leftJoin('products as p', 'f.product_id', '=', 'p.id')
                        ->leftJoin('order_design as od', 'f.design_id', '=', 'od.id')
                        ->select($listArray)
                        ->where('f.is_delete', '=', '1')
                        ->where('f.order_id', '=', $order_id)
                        ->get();

        return $finishingData;
    }

    public function FinishingBoardData($company_id,$run_date)
    {
        $result = DB::table('position_schedule as ps')
                    ->select('cs.shift_name','mc.machine_name','odp.id as position_id','mt.value as position_name','ord.display_number','ord.name','ps.*')
                    ->leftjoin('company_shift as cs','cs.id','=','ps.shift_id')
                    ->leftjoin('machine as mc','mc.id','=','ps.machine_id')
                    ->leftjoin('order_design_position as odp','ps.position_id','=','odp.id')
                    ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                    ->leftJoin('order_design as od','od.id','=','odp.design_id')
                    ->leftJoin('orders as ord','ord.id','=','od.order_id')
                    ->where('od.company_id','=',$company_id)
                    ->where('ps.run_date','=',$run_date)
                    ->where('od.is_delete','=','1')
                    ->where('odp.is_delete','=','1')
                    ->orderBy('ord.id','desc')
                    ->orderBy('odp.id','desc')
                    ->get();

        $ret_array = array();            
        foreach($result as $key=>$value)
        {
            $ret_array[$value->machine_id]['machine_name'] = $value->machine_name;
            $ret_array[$value->machine_id]['machine_data'][$value->shift_id]['shift_name']=$value->shift_name;
            $ret_array[$value->machine_id]['machine_data'][$value->shift_id]['shift_data'][$value->position_id]=$value;
        }            
        return $ret_array;
    }
    public function FinishingBoardweekData($company_id,$start_date,$end_date)
    {
        //echo $start_date."=".$end_date; die();
        $result = DB::table('position_schedule as ps')
                    ->select('cs.shift_name','mc.machine_name','odp.id as position_id','mt.value as position_name','ord.display_number','ord.name','ps.*')
                    ->leftjoin('company_shift as cs','cs.id','=','ps.shift_id')
                    ->leftjoin('machine as mc','mc.id','=','ps.machine_id')
                    ->leftjoin('order_design_position as odp','ps.position_id','=','odp.id')
                    ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                    ->leftJoin('order_design as od','od.id','=','odp.design_id')
                    ->leftJoin('orders as ord','ord.id','=','od.order_id')
                    ->where('od.company_id','=',$company_id)
                    ->whereBetween('ps.run_date', [$start_date, $end_date])
                    ->where('od.is_delete','=','1')
                    ->where('odp.is_delete','=','1')
                    ->orderBy('ps.run_date','asc')
                    ->orderBy('ord.id','desc')
                    ->orderBy('odp.id','desc')
                    ->get();

        $ret_array = array();            
        foreach($result as $key=>$value)
        {
            $ret_array[$value->run_date]['date'] = date('l - m/d/Y',strtotime($value->run_date));
            $ret_array[$value->run_date]['date_data'][$value->shift_id]['shift_name']=$value->shift_name;
            $ret_array[$value->run_date]['date_data'][$value->shift_id]['shift_data'][$value->position_id]=$value;
        }            
        return $ret_array;
    }

    public function FinishingBoardMachineData($company_id,$run_date,$machine_id)
    {
        $where = (!empty($machine_id))?array('ps.machine_id'=>$machine_id):array();
        $result = DB::table('position_schedule as ps')
                    ->select('cs.shift_name','mc.machine_name','odp.id as position_id','mt.value as position_name','ord.display_number','ord.name','ps.*')
                    ->leftjoin('company_shift as cs','cs.id','=','ps.shift_id')
                    ->leftjoin('machine as mc','mc.id','=','ps.machine_id')
                    ->leftjoin('order_design_position as odp','ps.position_id','=','odp.id')
                    ->leftjoin('misc_type as mt','mt.id','=','odp.position_id')
                    ->leftJoin('order_design as od','od.id','=','odp.design_id')
                    ->leftJoin('orders as ord','ord.id','=','od.order_id')
                    ->where('od.company_id','=',$company_id)
                    ->where('ps.run_date','=',$run_date)
                    ->where('od.is_delete','=','1')
                    ->where($where)
                    ->where('odp.is_delete','=','1')
                    ->orderBy('ord.id','desc')
                    ->orderBy('odp.id','desc')
                    ->get();

        $ret_array = array();            
        foreach($result as $key=>$value)
        {
            $ret_array['shift_all'][]=$value;
            $ret_array['shifts'][$value->shift_id]['shift_name']=$value->shift_name;
            $ret_array['shifts'][$value->shift_id]['shift_data'][$value->position_id]=$value;
        }            
        return $ret_array;
    }
}