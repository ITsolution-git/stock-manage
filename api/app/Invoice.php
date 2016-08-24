<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Invoice extends Model {

	public function listInvoice($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS o.id as order_id,i.id,i.qb_id,o.grand_total,o.in_hands_by,i.created_date')];

        $invoiceData = DB::table('invoice as i')
                        ->leftJoin('orders as o', 'o.id', '=', 'i.order_id')
                        ->select($listArray)
                        ->where('o.company_id', '=', $post['company_id']);

                        if($search != '')
                        {
                          $invoiceData = $invoiceData->Where(function($query) use($search)
                          {
                              $query->orWhere('o.id', 'LIKE', '%'.$search.'%')
                                    ->orWhere('i.created_date', 'LIKE', '%'.$search.'%')
                                    ->orWhere('o.grand_total', 'LIKE', '%'.$search.'%')
                                    ->orWhere('o.in_hands_by', 'LIKE', '%'.$search.'%');
                          });
                        }
                        $invoiceData = $invoiceData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->GroupBy('o.id')
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        $returnData = array();
        $returnData['allData'] = $invoiceData;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;

        return $invoiceData;  
    }

    public function getShippingByOrder($order_id)
    {
        
    }

}