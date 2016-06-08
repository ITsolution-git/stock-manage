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

    public function getAffiliateData($data) {
        
        $whereConditions = ['oam.order_id' => $data['id']];
        

        $listArray = ['a.name as affiliate_name','od.design_name','p.name as product_name','p.product_image','oam.note','oam.id','oam.affiliate_id'];

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

    public function getAffiliateSizes($id)
    {
        $affiliatesData = DB::table('affiliate_product as oam')
                         ->select('id','size','qnty')
                         ->where('affiliate_id','=',$id)
                         ->where('is_delete','=',1)
                         ->GroupBy('id')
                         ->get();

        return $affiliatesData;
    }

    public function getAssignCount($data)
    {
        $whereConditions = ['oam.order_id' => $data['id'],'ap.is_delete' => 1];
        $listArray = [DB::raw('SUM(ap.qnty) as total')];
        $affiliatesData = DB::table('affiliate_product as ap')
                         ->leftJoin('order_affiliate_mapping as oam','oam.id','=','ap.affiliate_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $affiliatesData;
    }

    public function getUnassignCount($data)
    {
        $whereConditions = ['od.order_id' => $data['id'],'is_distribute' => '0','pd.is_delete' => 1];
        $listArray = [DB::raw('SUM(pd.qnty) as total')];
        $affiliatesData = DB::table('purchase_detail as pd')
                         ->leftJoin('order_design as od','pd.design_id','=','od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $affiliatesData;
    }

    public function getAffiliateList($data) {
        
        $whereConditions = ['oam.order_id' => $data['id']];

        $listArray = ['a.name as affiliate_name','oam.id','a.id as affiliate_id',DB::raw('COUNT(oam.design_id) as design_total')];

        $affiliatesData = DB::table('order_affiliate_mapping as oam')
                         ->leftJoin('affiliates as a','oam.affiliate_id','=', 'a.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->GroupBy('oam.affiliate_id')
                         ->get();

        return $affiliatesData;
    }

    public function getAffiliateDesign($affiliate_id)
    {
        $whereConditions = ['oam.affiliate_id' => $affiliate_id,'od.is_delete' => 1];

        $listArray = ['od.*','oam.id as affiliate_id'];

        $affiliatesData = DB::table('order_affiliate_mapping as oam')
                         ->leftJoin('order_design as od','od.id','=', 'oam.design_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $affiliatesData;
    }
}