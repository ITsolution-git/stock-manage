<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Shipping extends Model {

	
	public function getShippingList($post)
	{
        $listArray = ['o.id','c.client_company','po.po_id'];

        $shippingData = DB::table('orders as o')
                         ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                         ->leftJoin('purchase_order as po', 'o.id', '=', 'po.order_id')
                         ->select($listArray)
                         ->where('o.is_complete','=','1')
                         ->where('o.company_id','=',$post['company_id'])
                         ->GroupBy('o.id')
                         ->get();

        $combine_array = array();
        $waiting = array();
        $shipped = array();
        $progress = array();

        foreach ($shippingData as $data) {
            
            $listArr = [DB::raw('SUM(pol.qnty_purchased - pol.short) as total'),'pol.purchase_detail'];
            $where = ['po.order_id' => $data->id];

            $result = DB::table('purchase_order as po')
                        ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
                        ->select($listArr)
                        ->where($where)
                        ->get();

            $listArr2 = [DB::raw('SUM(pas.distributed_qnty) as distributed'),'pas.purchase_detail_id'];
            $where2 = ['pam.order_id' => $data->id];

            $result2 = DB::table('product_address_mapping as pam')
                            ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                            ->select($listArr2)
                            ->where($where2)
                            ->get();

            if($result2[0]->distributed == '' || $result2[0]->distributed == '0')
            {
                $purchase_detail = DB::select("SELECT pol.purchase_detail, pol.qnty_purchased - pol.short as total FROM purchase_order as po 
                                                LEFT JOIN purchase_order_line as pol ON pol.po_id = po.po_id WHERE po.order_id = '".$data->id."' ");

                foreach($purchase_detail as $row)
                {
                    $value = DB::table('purchase_detail')
                            ->where('id','=',$row->purchase_detail)
                            ->update(array('remaining_qnty'=>$row->total));
                }
            }

            if($result[0]->total > 0)
            {
                if($result2[0]->distributed == '' || $result2[0]->distributed == '0')
                {
                    $waiting[] = $data;
                }
                else if($result2[0]->distributed == $result[0]->total)
                {
                    $shipped[] = $data;
                }
                else
                {
                    $progress[] = $data;
                }

                $data->total = $result[0]->total;
                $data->distributed = $result2[0]->distributed;
            }
        }

        $combine_array['waiting'] = $waiting;
        $combine_array['progress'] = $progress;
        $combine_array['shipped'] = $shipped;

        return $combine_array;
	}

    public function getShippingOrders($company_id)
    {
        $shippingData = DB::table('orders as o')
                            ->leftJoin('misc_type as mt','o.f_approval','=',DB::raw("mt.id AND mt.company_id = ".$company_id))
                            ->select('o.id','o.job_name','o.shipping_by','mt.value as job_status','o.client_id')
                            ->where('mt.slug','>=','138')
                            ->where('mt.slug','<=','149')
                            ->where('o.company_id','=',$company_id)
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
                        's.boxing_type','o.shipping_by','o.in_hands_by','s.shipping_type_id','o.date_shipped','o.fully_shipped','s.shipping_note','s.cost_to_ship','cd.*','o.f_approval','s.sku'];

        $shippingData = DB::table('shipping as s')
                        ->leftJoin('orders as o','s.order_id','=','o.id')
                        ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                        ->leftJoin('client as c','o.client_id','=','c.client_id')
                        ->leftJoin('client_distaddress as cd','s.address_id','=','cd.id')
                        ->select($listArray)
                        ->where($whereShippingConditions)->get();

        $whereItemConditions = ['ia.shipping_id' => $data['shipping_id']];
        $listItemsArray = ['ia.shipping_id','d.id','d.size','d.qnty','d.shipped_qnty','d.boxed_qnty','d.remaining_to_box','d.max_pack','d.hoody','p.name as product_name','mt.value as size_group_name','mt2.name as color_name'];

        $shippingItems = DB::table('item_address_mapping as ia')
                        ->leftJoin('distribution_detail as d','ia.item_id','=','d.id')
                        ->leftJoin('order_orderlines as ol','d.orderline_id','=','ol.id')
                        ->leftJoin('products as p','ol.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('color as mt2','mt2.id','=','ol.color_id')
                        ->select($listItemsArray)
                        ->where($whereItemConditions)
                        ->where('ia.item_id','!=','0')
                        ->get();

        $whereBoxConditions = ['sb.shipping_id' => $data['shipping_id']];

        $listItemsArray = ['sb.md','sb.id as box_id','sb.spoil','sb.actual','sb.re_allocate_to','sb.box_qnty','sb.tracking_number','bi.id as box_item_id','bi.item_id','d.id','d.size','d.qnty','d.shipped_qnty','sb.box_qnty as boxed_qnty','d.remaining_to_box','d.max_pack','d.hoody','p.name as product_name','p.description as product_desc','mt.value as size_group_name','mt2.name as color_name'];

        $shippingBoxes = DB::table('box_item_mapping as bi')
                        ->leftJoin('shipping_box as sb','bi.box_id','=','sb.id')
                        ->leftJoin('distribution_detail as d','bi.item_id','=','d.id')
                        ->leftJoin('order_orderlines as ol','d.orderline_id','=','ol.id')
                        ->leftJoin('products as p','ol.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('color as mt2','mt2.id','=','ol.color_id')
                        ->select($listItemsArray)
                        ->where($whereBoxConditions)
                        ->where('bi.item_id','!=','0')
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
        $listItemsArray = ['sb.md','sb.id as box_id','sb.spoil','sb.actual','sb.re_allocate_to','bi.id as box_item_id','bi.item_id','d.id','d.size','d.qnty','d.shipped_qnty','sb.box_qnty as boxed_qnty','d.remaining_to_box','d.max_pack','d.hoody','p.name as product_name','mt.value as size_group_name','mt2.name as color_name'];

        $shippingBoxItems = DB::table('box_item_mapping as bi')
                        ->leftJoin('shipping_box as sb','bi.box_id','=','sb.id')
                        ->leftJoin('distribution_detail as d','bi.item_id','=','d.id')
                        ->leftJoin('order_orderlines as ol','d.orderline_id','=','ol.id')
                        ->leftJoin('products as p','ol.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('color as mt2','mt2.id','=','ol.color_id')
                        ->select($listItemsArray)
                        ->where($whereItemConditions)
                        ->where('bi.item_id','!=','0')
                        ->get();

        return $shippingBoxItems;
    }

    public function deleteBox($id)
    {
        if(!empty($id))
        {
                $result = DB::table('shipping_box')->where('id', $id)->delete();
                $result = DB::table('box_item_mapping')->where('box_id', $id)->delete();
                return $result;
        }
        else
        {
                return false;
        }
    }

    public function getUnshippedProducts($order_id)
    {
        $listArr = ['mt.value as misc_value','p.name','c.name as color_name','p.description','pd.id','pd.size','pol.qnty_purchased','pd.remaining_qnty'];
        $where = ['po.order_id' => $order_id];

        $result = DB::select("SELECT mt.value as misc_value,p.name,c.name as color_name,p.description,pd.id,pd.size,pol.qnty_purchased - pol.short as total,pd.remaining_qnty,pd.distributed_qnty 
                                FROM purchase_order as po 
                                LEFT JOIN purchase_order_line as pol ON po.po_id = pol.po_id 
                                LEFT JOIN purchase_detail as pd ON pol.purchase_detail = pd.id 
                                LEFT JOIN product_address_size_mapping as pas ON pol.purchase_detail = pas.purchase_detail_id 
                                LEFT JOIN design_product as dp ON pd.design_product_id = dp.id
                                LEFT JOIN products as p ON dp.product_id = p.id
                                LEFT JOIN misc_type as mt ON dp.size_group_id = mt.id
                                LEFT JOIN color as c ON pd.color_id = c.id
                                WHERE po.order_id = '".$order_id."' AND pol.qnty_purchased > 0 
                                GROUP BY pd.id ");

/*        $result = DB::table('purchase_order as po')
                    ->leftJoin('purchase_order_line as pol','po.po_id','=','pol.po_id')
                    ->leftJoin('purchase_detail as pd','pol.purchase_detail','=','pd.id')
                    ->leftJoin('product_address_size_mapping as pas','pol.purchase_detail','=','pas.purchase_detail_id')
                    ->leftJoin('design_product as dp','pd.design_product_id','=','dp.id')
                    ->leftJoin('products as p','dp.product_id','=','p.id')
                    ->leftJoin('misc_type as mt','dp.size_group_id','=','mt.id')
                    ->leftJoin('color as c','pd.color_id','=','c.id')
                    ->select($listArr)
                    ->where('po.order_id','=',$order_id)
                    ->where('pol.qnty_purchased','>','0')
                    ->GroupBy('pd.id')
                    ->get();*/

        return $result;
    }

    public function getAllocatedAddress($data)
    {
        $result = DB::table('client_distaddress as cd')
                    ->leftJoin('product_address_mapping as pam','cd.id','=','pam.address_id')
                    ->select(DB::raw('GROUP_CONCAT(cd.id) as id'))
                    ->where('pam.order_id','=',$data->id)
                    ->GroupBy('pam.order_id')
                    ->get();

        return $result;
    }

    public function getUnAllocatedAddress($data)
    {
        $result = DB::table('product_address_mapping as pam')
                    ->rightJoin('client_distaddress as cd','cd.id','=','pam.address_id')
                    ->select('cd.*')
                    ->where('pam.order_id','=',$data->id)
                    ->where('pam.address_id','=',NULL)
                    ->where('cd.client_id','=',$data->client_id)
                    ->get();

        return $result;
    }

    public function getProductByAddress($data)
    {
        $listArr = ['mt.value as misc_value','p.name','c.name as color_name','p.description','pd.id','pd.size','pas.distributed_qnty'];
        $where = ['pam.order_id' => $data['order_id'], 'pam.address_id' => $data['address_id']];

        $result = DB::table('product_address_mapping as pam')
                    ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                    ->leftJoin('purchase_detail as pd','pas.purchase_detail_id','=','pd.id')
                    ->leftJoin('design_product as dp','pd.design_product_id','=','dp.id')
                    ->leftJoin('products as p','pd.product_id','=','p.id')
                    ->leftJoin('misc_type as mt','dp.size_group_id','=','mt.id')
                    ->leftJoin('color as c','pd.color_id','=','c.id')
                    ->select($listArr)
                    ->where($where)
                    ->GroupBy('pd.id')
                    ->get();
        
        return $result;
    }
}