<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Shipping extends Model {

	
	public function getShippingdata($company_id)
	{

        $listArray = ['o.client_id','o.id as order_id','o.job_name','st.name','s.boxing_type','o.status','s.id as shipping_id','s.shipping_by','s.in_hands_by','s.date_shipped','s.fully_shipped','mt.value as job_status'];

        $shippingData = DB::table('orders as o')
                         ->Join('shipping as s', 'o.id', '=', 's.order_id')
                         ->leftJoin('shipping_type as st', 's.shipping_type_id', '=', 'st.id')
                         ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                         ->select($listArray)
                         ->get();
        return $shippingData;
	}

    public function getShippingOrders()
    {
        $shippingData = DB::table('orders as o')
                            ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                            ->select('o.id','o.job_name','o.ship_by','mt.value as job_status','o.client_id')
                            ->where('f_approval','>=','138')
                            ->where('f_approval','<=','149')
                            ->get();
        return $shippingData;
    }    


/**
* Shipping Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/

    public function shippingDetail($data) {


        $whereShippingConditions = ['s.id' => $data['shipping_id']];
        $listArray = ['s.id as shipping_id','mt.value as job_status','o.id as order_id','o.job_name','cd.id as client_distribution_id','o.client_id','c.client_company',
                        's.boxing_type','s.shipping_by','s.in_hands_by','s.shipping_type_id','s.date_shipped','s.fully_shipped','s.shipping_note','s.cost_to_ship','cd.*','o.f_approval'];

        $shippingData = DB::table('shipping as s')
                        ->leftJoin('orders as o','s.order_id','=','o.id')
                        ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                        ->leftJoin('client as c','o.client_id','=','c.client_id')
                        ->leftJoin('client_distaddress as cd','s.address_id','=','cd.id')
                        ->select($listArray)
                        ->where($whereShippingConditions)->get();

        $whereItemConditions = ['ia.shipping_id' => $data['shipping_id']];
        $listItemsArray = ['ia.shipping_id','d.id','d.size','d.qnty','d.shipped_qnty','d.boxed_qnty','d.remaining_to_box','d.max_pack','d.hoody','p.name as product_name','mt.value as size_group_name','mt2.value as color_name'];

        $shippingItems = DB::table('item_address_mapping as ia')
                        ->leftJoin('distribution_detail as d','ia.item_id','=','d.id')
                        ->leftJoin('order_orderlines as ol','d.orderline_id','=','ol.id')
                        ->leftJoin('products as p','ol.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('misc_type as mt2','mt2.id','=','ol.color_id')
                        ->select($listItemsArray)
                        ->where($whereItemConditions)
                        ->where('ia.item_id','!=','0')
                        ->get();

        $whereBoxConditions = ['sb.shipping_id' => $data['shipping_id']];
        $listItemsArray = ['sb.id as box_id','sb.box_qnty','sb.tracking_number'];

        $shippingBoxes = DB::table('shipping_box as sb')
                        ->select($listItemsArray)
                        ->where($whereBoxConditions)
                        ->get();

        $combine_array = array();

        $combine_array['shipping'] = $shippingData;
        $combine_array['shippingItems'] = $shippingItems;
        $combine_array['shippingBoxes'] = $shippingBoxes;

        return $combine_array;
    }

    public function getBoxItems($data)
    {
        $whereItemConditions = ['bi.box_id' => $data['box_id']];
        $listItemsArray = ['bi.id as box_item_id','d.id','d.size','d.qnty','d.shipped_qnty','d.boxed_qnty','d.remaining_to_box','d.max_pack','d.hoody','p.name as product_name','mt.value as size_group_name','mt2.value as color_name'];

        $shippingBoxItems = DB::table('box_item_mapping as bi')
                        ->leftJoin('distribution_detail as d','bi.item_id','=','d.id')
                        ->leftJoin('order_orderlines as ol','d.orderline_id','=','ol.id')
                        ->leftJoin('products as p','ol.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('misc_type as mt2','mt2.id','=','ol.color_id')
                        ->select($listItemsArray)
                        ->where($whereItemConditions)
                        ->where('bi.item_id','!=','0')
                        ->get();

        return $shippingBoxItems;
    }
}