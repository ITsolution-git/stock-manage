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
}