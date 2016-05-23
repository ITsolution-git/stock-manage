<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Affiliate extends Model {

/**
* Vendor listing array           
* @access public vendorList
* @return array $staffData
*/

    public function getAffiliateList($data) {
        
        $whereConditions = ['oam.order_id' => $data['id']];
        

        $listArray = ['a.name as affiliate_name','od.design_name','p.name as product_name','p.product_image','oam.note'];

        $affiliatesData = DB::table('order_affiliate_mapping as oam')
                         ->leftJoin('affiliates as a','oam.affiliate_id','=', 'a.id')
                         ->leftJoin('order_design as od','oam.design_id','=', 'od.id')
                         ->leftJoin('design_product as dp','oam.design_id','=', 'dp.design_id')
                         ->leftJoin('products as p','dp.product_id','=', 'p.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $affiliatesData;
    }

}