<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Shipping extends Model {

	
	public function getShippingList($post)
	{
        DB::enableQueryLog();

        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS o.id,o.login_id,o.display_number,o.name,o.shipping_status,c.client_company,misc_type.value as approval,o.approval_id,SUM(pd.qnty) as total')];


        $shippingData = DB::table('orders as o')
                        ->Join('client as c', 'o.client_id', '=', 'c.client_id')
                        ->Join('order_design as od', 'o.id', '=', 'od.order_id')
                        ->Join('design_product as dp','od.id','=','dp.design_id')
                        ->Join('purchase_detail as pd','dp.id','=','pd.design_product_id')
                        ->Join('misc_type as misc_type','o.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
                        ->select($listArray)
                        ->where('o.company_id','=',$post['company_id']);

                        if(isset($post['filter']['status'])) {
                            $shippingData = $shippingData->where('o.shipping_status','=',$post['filter']['status']);
                        }
                        
                        if($search != '')
                        {
                            $shippingData = $shippingData->Where(function($query) use($search)
                            {
                                $query->orWhere('o.display_number', 'LIKE', '%'.$search.'%')
//                                ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')   
                                ->orWhere('c.client_company', 'LIKE', '%'.$search.'%');
                            });
                        }
                        $shippingData = $shippingData->GroupBy('o.id');
                        $shippingData = $shippingData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        if(!empty($shippingData))
        {
            foreach ($shippingData as $shipping)
            {
                $total_distributed = DB::table('shipping as s')
                                    ->leftJoin('product_address_mapping as pam','s.id','=','pam.shipping_id')
                                    ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                                    ->select(DB::raw('SUM(pas.distributed_qnty) as distributed'))
                                    ->where('s.order_id','=',$shipping->id)
                                    ->get();
                
                $shipping->distributed = $total_distributed[0]->distributed;

                if($shipping->distributed == '0' || $shipping->distributed == '')
                {
                    $shipping->distributed = 0;
                }
            }
        }

        $returnData['allData'] = $shippingData;
        $returnData['count'] = $count[0]->Totalcount;

        return $returnData;

	}

/**
* Shipping Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/

    public function shippingDetail($data) {

        $whereShippingConditions = ['s.id' => $data['shipping_id']];

        $listArray = ['s.id as shipping_id','mt.value as job_status','o.id as order_id','o.name','cd.id as client_distribution_id','o.client_id','c.client_company','o.approval_id','misc_type.value as approval','s.cost_to_ship','s.tracking_number','o.display_number',
                        's.boxing_type','o.date_shipped as shipping_by','o.in_hands_by','s.shipping_type_id','s.date_shipped','s.fully_shipped','s.shipping_note','s.cost_to_ship','cd.*','o.f_approval','s.sku','st.name','st.code','s.shipping_method','o.shipping_status','o.login_id','o.is_blind','c.b_w_logo',
                        'o.date_shipped','o.in_hands_by','o.custom_po','s.shipping_note','s.display_number as shipping_display_number','c.display_number as client_display_number'];
       
        $shippingData = DB::table('shipping as s')
                        ->leftJoin('orders as o','s.order_id','=','o.id')
                        ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                        ->leftJoin('client as c','o.client_id','=','c.client_id')
                        ->leftJoin('client_distaddress as cd','s.address_id','=','cd.id')
                        ->leftJoin('state as st','cd.state','=','st.id')
                        ->leftJoin('misc_type as misc_type','o.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$data['company_id']))
                        ->select($listArray)
                        ->where($whereShippingConditions)
                        ->GroupBy('o.id')->get();


        $total_assigned = DB::table('purchase_order as po')
                                    ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
                                    ->select(DB::raw('SUM(pol.qnty_purchased - pol.short) as total'))
                                    ->where('po.order_id','=',$shippingData[0]->order_id)
                                    ->get();
                
        $shippingData[0]->total = $total_assigned[0]->total;

        $total_distributed = DB::table('product_address_mapping as pam')
                            ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                            ->select(DB::raw('SUM(pas.distributed_qnty) as distributed'))
                            ->where('pam.shipping_id','=',$data['shipping_id'])
                            ->get();
        
        $shippingData[0]->distributed = $total_distributed[0]->distributed;

        $whereItemConditions = ['pam.shipping_id' => $data['shipping_id']];
        $listItemsArray = ['pam.shipping_id','pd.id','pd.size','pd.qnty','pd.shipped_qnty','pd.boxed_qnty','pd.remaining_to_box','pd.max_pack','pd.hoody','p.name as product_name','p.brand_name','mt.value as size_group_name','c.name as color_name'];

        if(isset($data['overview']))
        {
            $GroupBy = 'p.id';
        }
        else
        {
            $GroupBy = 'pd.id';
        }

        $shippingItems = DB::select("SELECT mt.value as misc_value,p.name,p.brand_name,p.id as product_id,c.name as color_name,p.description,pd.id,pd.size,pol.qnty_purchased - pol.short as total, 
                                    pas.distributed_qnty as qnty,pd.remaining_qnty,pd.distributed_qnty,pas.product_address_id ,pd.boxed_qnty,
                                    pd.remaining_to_box,pd.max_pack,pd.hoody,pam.shipping_id,pd.design_product_id
                                FROM product_address_mapping as pam 
                                LEFT JOIN product_address_size_mapping as pas ON pam.id = pas.product_address_id 
                                LEFT JOIN purchase_detail as pd ON pas.purchase_detail_id = pd.id 
                                LEFT JOIN purchase_order_line as pol ON pd.id = pol.purchase_detail 
                                LEFT JOIN design_product as dp ON pd.design_product_id = dp.id
                                LEFT JOIN products as p ON dp.product_id = p.id
                                LEFT JOIN misc_type as mt ON dp.size_group_id = mt.id
                                LEFT JOIN color as c ON pd.color_id = c.id
                                WHERE pam.shipping_id = '".$data['shipping_id']."' 
                                GROUP BY ".$GroupBy." ");

        $combine_array = array();

        $combine_array['shipping'] = $shippingData;
        $combine_array['shippingItems'] = $shippingItems;

        return $combine_array;
    }

    public function getBoxItems($box_id)
    {
        $whereItemConditions = ['bi.box_id' => $box_id];
        $listItemsArray = ['sb.md','sb.id as box_id','sb.box_setting_id','sb.spoil','sb.actual','sb.re_allocate_to','bi.id as box_item_id','bi.item_id','pd.id','pd.size','sb.box_qnty as boxed_qnty','pd.remaining_to_box','pd.max_pack','pd.hoody','p.name as product_name','p.brand_name','mt.value as size_group_name','c.name as color_name'];

        $shippingBoxItems = DB::table('box_product_mapping as bi')
                        ->leftJoin('shipping_box as sb','bi.box_id','=','sb.id')
                        ->leftJoin('purchase_detail as pd','bi.item_id','=','pd.id')
                        ->leftJoin('design_product as dp','pd.design_product_id','=','dp.id')
                        ->leftJoin('products as p','dp.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','dp.size_group_id')
                        ->leftJoin('color as c','pd.color_id','=','c.id')
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
                $result = DB::table('box_product_mapping')->where('box_id', $id)->delete();
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

        $result = DB::select("SELECT mt.value as misc_value,p.name,p.brand_name,p.id as product_id,c.name as color_name,p.description,pd.id,pd.size,pol.qnty_purchased - pol.short as total,pd.remaining_qnty,pd.distributed_qnty,pas.product_address_id 
                                FROM purchase_order as po 
                                LEFT JOIN purchase_order_line as pol ON po.po_id = pol.po_id 
                                LEFT JOIN purchase_detail as pd ON pol.purchase_detail = pd.id 
                                LEFT JOIN product_address_size_mapping as pas ON pol.purchase_detail = pas.purchase_detail_id 
                                LEFT JOIN design_product as dp ON pd.design_product_id = dp.id
                                LEFT JOIN products as p ON dp.product_id = p.id
                                LEFT JOIN misc_type as mt ON dp.size_group_id = mt.id
                                LEFT JOIN color as c ON pd.color_id = c.id
                                WHERE po.order_id = '".$order_id."' AND pol.qnty_purchased > 0 AND pd.remaining_qnty > 0 
                                GROUP BY pd.id ");
        return $result;
    }

    public function getProductByAddress($data)
    {
        $listArr = ['mt.value as misc_value','p.name','c.name as color_name','p.description','pd.id as purchase_detail_id','pd.size','pas.distributed_qnty as old_distributed_qnty','pas.distributed_qnty','pd.remaining_qnty','pas.product_address_id'];
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
                    ->where('pas.distributed_qnty','>','0')
                    ->GroupBy('pd.id')
                    ->get();
        
        return $result;
    }

    public function getAllocatedAddress($data)
    {
        $result = DB::table('client_distaddress as cd')
                    ->leftJoin('product_address_mapping as pam','cd.id','=','pam.address_id')
                    ->select(DB::raw('GROUP_CONCAT(cd.id) as id'))
                    ->where('pam.order_id','=',$data['id'])
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

    public function getShippingBoxes($data)
    {
        $whereBoxConditions = ['sb.shipping_id' => $data['shipping_id']];

        $listItemsArray = ['sb.id','sb.box_qnty','sb.tracking_number','sb.box_setting_id',DB::raw('COUNT(bi.id) as number_of_box'),'sb.box_qnty as boxed_qnty','c.name as color_name','p.description as product_desc','pd.size','sb.md','sb.spoil','sb.actual','mt.value as size_group_name','p.name as product_name','p.brand_name'];

        $shippingBoxes = DB::table('shipping_box as sb')
                        ->leftJoin('box_product_mapping as bi','bi.box_id','=','sb.id')
                        ->leftJoin('purchase_detail as pd','bi.item_id','=','pd.id')
                        ->leftJoin('design_product as dp','pd.design_product_id','=','dp.id')
                        ->leftJoin('products as p','pd.product_id','=','p.id')
                        ->leftJoin('misc_type as mt','mt.id','=','dp.size_group_id')
                        ->leftJoin('color as c','pd.color_id','=','c.id')
                        ->select($listItemsArray)
                        ->where($whereBoxConditions)
                        ->GroupBy('sb.id')
                        ->get();

        return $shippingBoxes;
    }

    public function getTotalShipCharge($order_id)
    {
        $listArr = [DB::raw('SUM(cost_to_ship) as total')];
        $where = ['order_id' => $order_id];

        $result = DB::table('shipping')
                ->select($listArr)
                ->where($where)
                ->get();

        return $result;
    }

    public function getItemsByProductAddress($product_address_id)
    {
        $shippingItems = DB::select("SELECT 
                                    (pol.qnty_purchased - pol.short) - pd.remaining_qnty as qnty,pd.size
                                FROM product_address_mapping as pam 
                                LEFT JOIN product_address_size_mapping as pas ON pam.id = pas.product_address_id 
                                LEFT JOIN purchase_detail as pd ON pas.purchase_detail_id = pd.id 
                                LEFT JOIN purchase_order_line as pol ON pd.id = pol.purchase_detail 
                                LEFT JOIN design_product as dp ON pd.design_product_id = dp.id
                                LEFT JOIN products as p ON dp.product_id = p.id
                                LEFT JOIN misc_type as mt ON dp.size_group_id = mt.id
                                LEFT JOIN color as c ON pd.color_id = c.id
                                WHERE pas.product_address_id = '".$product_address_id."'
                                GROUP BY pd.id ");

        return $shippingItems;
    }

    public function getshippedProductsByOrder($order_id)
    {
        $listArr = ['mt.value as misc_value','p.name','p.brand_name','c.name as color_name','p.description','pd.id','pd.size','pas.distributed_qnty','pas.product_address_id'];
        $where = ['pam.order_id' => $order_id];

        $result = DB::table('product_address_mapping as pam')
                    ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                    ->leftJoin('purchase_detail as pd','pas.purchase_detail_id','=','pd.id')
                    ->leftJoin('design_product as dp','pd.design_product_id','=','dp.id')
                    ->leftJoin('products as p','pd.product_id','=','p.id')
                    ->leftJoin('misc_type as mt','dp.size_group_id','=','mt.id')
                    ->leftJoin('color as c','pd.color_id','=','c.id')
                    ->select($listArr)
                    ->where($where)
                    ->GroupBy('p.id')
                    ->get();
        
        return $result;
    }

    public function getShipToAddress($address_id)
    {
        $result = DB::table('product_address_mapping as pam')
                    ->leftJoin('client_distaddress as cd','pam.address_id','=','cd.id')
                    ->leftJoin('state as st','cd.state','=','st.id')
                    ->select('cd.*','st.code')
                    ->where('pam.address_id','=',$address_id)
                    ->GroupBy('cd.id')
                    ->get();
        
        return $result;
    }

    public function getShippingOrdersDetail($order_id)
    {
        $result = DB::table('shipping as s')
                    ->leftJoin('client_distaddress as cd','s.address_id','=','cd.id')
                    ->leftJoin('order_shipping_address_mapping as om','s.address_id','=','om.address_id')
                    ->select('cd.description','s.*','om.shipping_type_id','om.shipping_method_id')
                    ->where('s.order_id','=',$order_id)
                    ->GroupBy('s.address_id')
                    ->get();

        return $result;
    }

    public function getShippingProducts($shipping_id)
    {
        $returnData = array();

        $productData = DB::table('product_address_mapping as pam')
                        ->leftJoin('products as p', 'pam.product_id', '=', 'p.id')
                        ->select('p.id','p.name as product_name')
                        ->where('pam.shipping_id','=',$shipping_id)
                        ->GroupBy('p.id')
                        ->get();

        $total_qnty = 0;
        if(!empty($productData))
        {
            foreach ($productData as $product) {
                
                $result = DB::table('product_address_mapping as pam')
                    ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                    ->leftJoin('purchase_detail as pd','pas.purchase_detail_id','=','pd.id')
                    ->leftJoin('color as c', 'pd.color_id', '=', 'c.id')
                    ->select('pd.size','pas.distributed_qnty as qnty','c.name as color_name')
                    ->where('pam.shipping_id','=',$shipping_id)
                    ->where('pam.product_id','=',$product->id)
                    ->GroupBy('pas.purchase_detail_id')
                    ->get();

                foreach ($result as $row) {
            
                    $total_qnty += $row->qnty;
                    $product->sizeData[$row->color_name][] = $row;
                }
            }
        }

        $returnData['productData'] = $productData;
        $returnData['total_qnty'] = $total_qnty;

        return $returnData;
    }
}