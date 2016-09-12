<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Shipping extends Model {

	
	public function getShippingList($post)
	{
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS o.id,c.client_company,po.po_id,s.id as shipping_id')];

        $shippingData = DB::table('orders as o')
                         ->leftJoin('shipping as s', 's.order_id', '=', 'o.ids')
                         ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                         ->leftJoin('purchase_order as po', 'o.id', '=', 'po.order_id')
                         ->select($listArray)
                         ->where('o.is_complete','=','1')
                         ->where('o.company_id','=',$post['company_id']);
                            if($search != '')
                            {
                              $shippingData = $shippingData->Where(function($query) use($search)
                              {
                                  $query->orWhere('po.id', 'LIKE', '%'.$search.'%')
                                        ->orWhere('c.client_company', 'LIKE', '%'.$search.'%')
                                        ->orWhere('po.po_id', 'LIKE', '%'.$search.'%');
                              });
                            }
                            $shippingData = $shippingData->GroupBy('po.order_id')
                            ->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                            ->skip($post['start'])
                            ->take($post['range'])
                            ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

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

        $returnData = array();

        if($post['type'] == 'wait') {
            $returnData['allData'] = $waiting;
            $returnData['count'] = count($waiting);
        }
        else if($post['type'] == 'progress') {
            $returnData['allData'] = $progress;
            $returnData['count'] = count($progress);
        }
        else if($post['type'] == 'shipped') {
            $returnData['allData'] = $shipped;
            $returnData['count'] = count($shipped);
        }
        
        return $returnData;

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
        $listArray = ['s.id as shipping_id','mt.value as job_status','o.id as order_id','o.name','cd.id as client_distribution_id','o.client_id','c.client_company','o.approval_id','misc_type.value as approval','s.cost_to_ship','s.tracking_number',
                        's.boxing_type','o.shipping_by','o.in_hands_by','s.shipping_type_id','o.date_shipped','o.fully_shipped','s.shipping_note','s.cost_to_ship','cd.*','o.f_approval','s.sku','st.code'];

        $shippingData = DB::table('shipping as s')
                        ->leftJoin('orders as o','s.order_id','=','o.id')
                        ->leftJoin('misc_type as mt','mt.id','=','o.f_approval')
                        ->leftJoin('client as c','o.client_id','=','c.client_id')
                        ->leftJoin('client_distaddress as cd','s.address_id','=','cd.id')
                        ->leftJoin('state as st','cd.state','=','st.id')
                        ->leftJoin('misc_type as misc_type','o.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$data['company_id']))
                        ->select($listArray)
                        ->where($whereShippingConditions)->get();

        $whereItemConditions = ['pam.shipping_id' => $data['shipping_id']];
        $listItemsArray = ['pam.shipping_id','pd.id','pd.size','pd.qnty','pd.shipped_qnty','pd.boxed_qnty','pd.remaining_to_box','pd.max_pack','pd.hoody','p.name as product_name','mt.value as size_group_name','c.name as color_name'];

        if(isset($data['overview']))
        {
            $GroupBy = 'p.id';
        }
        else
        {
            $GroupBy = 'pd.id';
        }

        $shippingItems = DB::select("SELECT mt.value as misc_value,p.name,p.id as product_id,c.name as color_name,p.description,pd.id,pd.size,pol.qnty_purchased - pol.short as total, 
                                    (pol.qnty_purchased - pol.short) - pd.remaining_qnty as qnty,pd.remaining_qnty,pd.distributed_qnty,pas.product_address_id ,pd.boxed_qnty,
                                    pd.remaining_to_box,pd.max_pack,pd.hoody,pam.shipping_id
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
        $listItemsArray = ['sb.md','sb.id as box_id','sb.spoil','sb.actual','sb.re_allocate_to','bi.id as box_item_id','bi.item_id','pd.id','pd.size','sb.box_qnty as boxed_qnty','pd.remaining_to_box','pd.max_pack','pd.hoody','p.name as product_name','mt.value as size_group_name','c.name as color_name'];

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

        $result = DB::select("SELECT mt.value as misc_value,p.name,p.id as product_id,c.name as color_name,p.description,pd.id,pd.size,pol.qnty_purchased - pol.short as total,pd.remaining_qnty,pd.distributed_qnty,pas.product_address_id 
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

    public function getShippingBoxes($data)
    {
        $whereBoxConditions = ['sb.shipping_id' => $data['shipping_id']];

        $listItemsArray = ['sb.id','sb.box_qnty','sb.tracking_number',DB::raw('COUNT(bi.id) as number_of_box'),'sb.box_qnty as boxed_qnty','c.name as color_name','p.description as product_desc','pd.size','sb.md','sb.spoil','sb.actual','mt.value as size_group_name','p.name as product_name'];

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
}