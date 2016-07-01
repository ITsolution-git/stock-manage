<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Order extends Model {

	
	public function getOrderdata($post)
	{
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        $created_date = '';
        if(isset($post['filter']['created_date']) && $post['filter']['created_date'] != '') {
            //$created_date = $post['filter']['created_date'];
            $created_date = date("Y-m-d", strtotime($post['filter']['created_date']));
        }

        $whereConditions = ['order.is_delete' => '1','order.company_id' => $post['company_id'],'order.parent_order_id' => '0'];

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS order.client_id,order.id,order.name,order.created_date,order.approved_date,order.date_shipped,
                      order.status,order.approval_id,client.client_company,misc_type.value as approval,staff.first_name,staff.last_name')];

        $orderData = DB::table('orders as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
                         ->leftJoin('staff as staff','order.sales_id','=', 'staff.id')
                         ->leftJoin('misc_type as misc_type','order.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
                         ->select($listArray)
                         ->where($whereConditions);
                        
                        if($search != '')
                        {
                          $orderData = $orderData->Where(function($query) use($search)
                          {
                              $query->orWhere('order.name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('staff.first_name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')
                                    ->orWhere('client.client_company', 'LIKE', '%'.$search.'%');
                          });
                        }
                        if(isset($post['filter']['seller']))
                        {
                          $orderData = $orderData->whereIn('order.sales_id', $post['filter']['seller']);
                        }
                        if(isset($post['filter']['client']))
                        {
                          $orderData = $orderData->whereIn('order.client_id', $post['filter']['client']);
                        }
                        if($created_date != '')
                        {
                          $orderData = $orderData->where('order.created_date', $created_date);
                        }
                        $orderData = $orderData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        //dd(DB::getQueryLog());
        $returnData = array();
        $returnData['allData'] = $orderData;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
	}

/**
* Order Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function orderDetail($data) {

      
        $whereConditions = ['order.is_delete' => "1",'order.id' => $data['id'],'order.company_id' => $data['company_id']];
        
        $listArray = ['order.*','order.name as order_name','client.client_company','misc_type.value as approval','staff.first_name',
                      'staff.last_name','users.name','cc.first_name as client_first_name',
                      'cc.last_name as client_last_name','price_grid.name as price_grid_name'];

        $orderDetailData = DB::table('orders as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
                         ->leftJoin('staff as staff','order.sales_id','=', 'staff.id')
                         ->leftJoin('users as users','order.account_manager_id','=', 'users.id')
                         ->leftJoin('client_contact as cc','order.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "));
                         if(isset($data['is_affiliate']))
                         {
                               $orderDetailData = $orderDetailData->leftJoin('order_affiliate_mapping as oam','order.id','=', 'oam.order_id');
                               $orderDetailData = $orderDetailData->leftJoin('affiliates as a','oam.affiliate_id','=', 'a.id');
                               $orderDetailData = $orderDetailData->leftJoin('price_grid as price_grid','a.price_grid','=', 'price_grid.id');
                         }
                         else
                         {
                            $orderDetailData = $orderDetailData->leftJoin('price_grid as price_grid','order.price_id','=', 'price_grid.id');
                         }
                         $orderDetailData = $orderDetailData->leftJoin('misc_type as misc_type','order.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$data['company_id']))
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        $combine_array['order'] = $orderDetailData;
        return $combine_array;
    }

    public function getOrderPositionDetail($data)
    {
        $whereOrderPositionConditions = ['order_id' => $data['id']];
        $orderPositionData = DB::table('order_positions')->where($whereOrderPositionConditions)->get();
        
        foreach ($orderPositionData as $key=>$alldata){

                    if($alldata->placementvalue){
                         $orderPositionData[$key]->placementvalue = explode(',', $alldata->placementvalue);
                    }

                    if($alldata->sizegroupvalue){
                        $orderPositionData[$key]->sizegroupvalue = explode(',', $alldata->sizegroupvalue);
                    }
        }
        $combine_array['order_position'] = $orderPositionData;

        return $combine_array;
    }

    /**
    * Order line Details           
    * @access public getOrderDetailById
    * @param  int $orderline_id
    * @return array $result
    */

    public function getOrderLineItemByColor($product_id,$color_id)
    {
        $listArray = ['pc.*','p.name as size'];

        $productColorSizeData = DB::table('product_size as p')
                         ->leftJoin('product_color_size as pc', 'p.id', '=', 'pc.size_id')
                         ->select($listArray)
                         ->where('pc.product_id','=',$product_id)
                         ->where('pc.color_id','=',$color_id)
                         ->get();

        return $productColorSizeData;
    }

    public function getOrderLineItemById($id)
    {
        $result = DB::table('purchase_detail')->where('orderline_id','=',$id)->get();
        return $result;
    }

    /**
    * Order item Details           
    * @access public getOrderItemById
    * @param  int $item_id
    * @return array $result
    */

    public function getOrderItemById($id)
    {
        $whereConditions = ['price_id' => $id,'is_per_order' => '1'];
        $result = DB::table('price_grid_charges')->where($whereConditions)->get();
        return $result;
    }

    /**
    * Order item Details           
    * @access public getItemsByOrder
    * @param  int $item_id
    * @return array $result
    */

    public function getItemsByOrder($id)
    {
        $whereConditions = ['order_id' => $id];
        $result = DB::table('order_item_mapping')->where($whereConditions)->get();
        return $result;
    }


     public function insertPositions($table,$records)
    {

        
         if(array_key_exists('placementvalue', $records) && is_array($records['placementvalue'])) {
          $records['placementvalue'] = implode(',', $records['placementvalue']);
       
           }

          if(array_key_exists('sizegroupvalue', $records) && is_array($records['sizegroupvalue'])) {
              $records['sizegroupvalue'] = implode(',', $records['sizegroupvalue']);
           
          }
      


        $result = DB::table($table)->insert($records);

        $id = DB::getPdo()->lastInsertId();

        return $id;
    }


   
    public function updatePositions($table,$cond,$data)
    {

      if(array_key_exists('placementvalue', $data)  && is_array($data['placementvalue'])) {
          $data['placementvalue'] = implode(',', $data['placementvalue']);
       
      }

      if(array_key_exists('sizegroupvalue', $data)  && is_array($data['sizegroupvalue'])) {
          $data['sizegroupvalue'] = implode(',', $data['sizegroupvalue']);
       
      }
      
       
       

         $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->update($data);
        return $result;
    }

    public function getDistributionItems($data)
    {
        $listArray = ['pd.id','ol.product_id','ol.vendor_id','ol.color_id','ol.size_group_id','pd.size','pd.qnty','mt.value as size_group_name','mt2.name as color_name','p.name','v.name_company as main_contact_person'];

        $orderData = DB::table('orders as order')
                        ->select($listArray)
                        ->leftJoin('order_orderlines as ol', 'order.id', '=', 'ol.order_id')
                        ->leftJoin('distribution_detail as pd', 'ol.id', '=', 'pd.orderline_id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('products as p','p.id','=','ol.product_id')
                        ->leftJoin('vendors as v','v.id','=','ol.vendor_id')
                        ->leftJoin('color as mt2','mt2.id','=','ol.color_id')
                        ->where($data)
                        ->where('pd.qnty','!=','')
                        ->get();
        return $orderData;  
    }

    public function getDistributedAddress($data)
    {
        $listArray = ['cd.*','ia.*','o.job_name'];

        $orderData = DB::table('client_distaddress as cd')
                        ->select($listArray)
                        ->leftJoin('item_address_mapping as ia', 'cd.id', '=', 'ia.address_id')
                        ->leftJoin('orders as o', 'ia.order_id', '=', 'o.id')
                        ->where($data)
                        ->GroupBy('ia.address_id')
                        ->get();
        return $orderData;  
    }

    public function getDistributedItems($data)
    {
        $listArray = ['pd.id','ol.product_id','ol.vendor_id','ol.color_id','ol.size_group_id','pd.size','pd.qnty','mt.value as size_group_name','mt2.name as color_name','p.name','v.name_company as main_contact_person','pd.shipped_qnty','ia.shipping_id'];

        $orderData = DB::table('orders as order')
                        ->select($listArray)
                        ->leftJoin('order_orderlines as ol', 'order.id', '=', 'ol.order_id')
                        ->leftJoin('distribution_detail as pd', 'ol.id', '=', 'pd.orderline_id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('products as p','p.id','=','ol.product_id')
                        ->leftJoin('vendors as v','v.id','=','ol.vendor_id')
                        ->leftJoin('color as mt2','mt2.id','=','ol.color_id')
                        ->leftJoin('item_address_mapping as ia', 'pd.id', '=', 'ia.item_id')
                        ->where($data)
                        ->get();
        return $orderData;
    }

/**
* Insert Order Note           
* @access public saveColorSize
* @param  array $post
* @return array $result
*/


public function saveColorSize($post)
   {

    for ($x = 1; $x <= 7; $x++) {
        $post['size_id'] = $x;
        $post['price'] = 0;
        $result = DB::table('product_color_size')->insert($post);
        
     } 

       return $result;
   }


/**
* Product Color Size Details           
* @access public getOrderNoteDetails
* @param  int $productId
* @return array $result
*/ 

     public function getProductDetailColorSize($id)
    {
       
        $whereConditions = ['p.product_id' => $id,'p.status' => '1','p.is_delete' => '1','c.status' => '1','c.is_delete' => '1','pz.status' => '1','pz.is_delete' => '1'];
        $listArray = ['p.id','p.product_id','p.color_id','p.size_id','p.price','c.name as color','pz.name as size'];

        $productColorSizeData = DB::table('product_color_size as p')
                         ->Join('color as c', 'c.id', '=', 'p.color_id')
                         ->Join('product_size as pz', 'pz.id', '=', 'p.size_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();


        $whereColorData = ['product_id' => $id];
        $productMainData = DB::table('product_color_size')->where($whereColorData)->get();

        $color_array = array();
        $colorData = array();
        foreach ($productMainData as $key=>$alldata){
          
            if(!in_array($alldata->color_id,$color_array)) {
                array_push($color_array, $alldata->color_id); 
                 $colorData[]['id'] = $alldata->color_id;
            }
        }

        $combine_array['productColorSizeData'] = $productColorSizeData;
        $combine_array['ColorData'] = $colorData;
        return $combine_array;
    }

    public function GetProductColor($product_id)
    {
        $listArray = ['c.id','c.name'];

        $productColorSizeData = DB::table('products as p')
                         ->leftJoin('color as c', 'c.id', '=', 'p.color_id')
                         ->select($listArray)
                         ->where('p.product_id','=',$product_id)
                         ->GroupBy('c.id')
                         ->get();

        return $productColorSizeData;
    }

  public function getProductDetail($product_id)
    {
        $whereProductConditions = ['id' => $product_id];
        $productData = DB::table('products')->where($whereProductConditions)->get();
        return $productData;
    }


/**
* Update product price           
* @access public updateOrderNotes
* @param  array $post
* @return array $result
*/


    public function updatePriceProduct($size_array_data,$product_id)
   {
            $result = DB::table('products')
                        ->where('id','=',$product_id)
                        ->update(array('color_size_data'=>$size_array_data));
        return $result;
   }

   /**
* Order Image Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function orderImageDetail($data) {
  
        $whereOrderConditions = ['id' => $data['id'],'company_id' => $data['company_id']];
        $orderData = DB::table('orders')->where($whereOrderConditions)->get();
        return $orderData;
    }


    /**
* Order Detail           
* @access public designDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function designDetail($data) {

      
        $whereConditions = ['od.is_delete' => "1",'od.id' => $data['id']];
        $listArray = ['od.*','o.order_number','o.is_complete'];

        $designDetailData = DB::table('order_design as od')
                         ->leftJoin('orders as o','od.order_id','=', 'o.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        $combine_array['design'] = $designDetailData;
        return $combine_array;
    }


    public function getDesignPositionDetail($data)
    {

        $whereOrderPositionConditions = ['odp.design_id' => $data['id'],'odp.is_delete' => "1"];

        $listArray = ['odp.*','m.value as position_name'];

        $orderPositionData = DB::table('order_design_position as odp')
                            ->leftJoin('misc_type as m','odp.position_id','=', 'm.id')
                            ->where($whereOrderPositionConditions)
                            ->select($listArray)
                            ->get();


        $combine_array['order_design_position'] = $orderPositionData;
        $combine_array['total_pos_qnty'] = 0;
       
        if(count($combine_array['order_design_position'])>0)
        {
            $total_pos_qnty = 0;
            foreach ($combine_array['order_design_position'] as $key => $value) 
            {
                $combine_array['order_design_position'][$key]->image_1_url_photo = (!empty($value->image_1))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_1:'';
                $combine_array['order_design_position'][$key]->image_2_url_photo = (!empty($value->image_2))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_2:'';
                $combine_array['order_design_position'][$key]->image_3_url_photo = (!empty($value->image_3))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_3:'';
                $combine_array['order_design_position'][$key]->image_4_url_photo = (!empty($value->image_4))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_4:'';
                $total_pos_qnty += $value->qnty;
            }
            $combine_array['total_pos_qnty'] = $total_pos_qnty;
         }
         
        return $combine_array;
    }

    public function getAllDesigndata()
    {
        $whereConditions = ['od.status' => '1','od.is_delete' => '1','odp.is_delete' => '1'];
        $listArray = ['od.shipping_date','od.id','od.order_id','odp.position_id','od.design_name',DB::raw('group_concat(m.value) as position_name'),DB::raw('count(odp.position_id) as count_position')];
        $designData = DB::table('order_design as od')
                        ->Join('order_design_position as odp','odp.design_id','=', 'od.id')
                        ->leftJoin('misc_type as m','odp.position_id','=', 'm.id')
                        
                        ->select($listArray)
                        ->GroupBy('odp.design_id')
                        ->where($whereConditions)->get();
        $allData = array ();
        foreach($designData as $data) {
          
            $allData[$data->order_id][] = $data;
        }
        return $allData;
    }

    /**
* Order Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function orderDetailInfo($data) {
      
        $whereConditions = ['is_delete' => "1",'id' => $data['id'],'company_id' => $data['company_id']];
        $listArray = ['sales_id','is_blind','account_manager_id','price_id','company_id','name','sns_shipping'];

        $orderDetailData = DB::table('orders')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        $combine_array['order'] = $orderDetailData;
        return $combine_array;
    }

    public function getOrderByDesign($design_id)
    {
        $whereConditions = ['od.id' => $design_id];
        $listArray = ['o.*'];

        $orderData = DB::table('orders as o')
                         ->leftJoin('order_design as od','od.order_id','=', 'o.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $orderData;
    }

    public function getDesignByOrder($order_id)
    {
        $whereConditions = ['od.order_id' => $order_id,'dp.is_delete' => '1','od.is_calculate' => '1'];
        $listArray = ['dp.*'];

        $orderData = DB::table('design_product as dp')
                         ->leftJoin('order_design as od','dp.design_id','=', 'od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();
                         
        return $orderData;
    }
    
    public function getTotalQntyByProduct($design_id,$product_id)
    {
        $whereConditions = ['design_id' => $design_id,'dp.is_delete' => '1','product_id' => $product_id];

        $orderData = DB::table('purchase_detail as dp')
                         ->select(DB::raw('sum(qnty) as total_qnty'))
                         ->where($whereConditions)
                         ->get();
        
        return $orderData[0]->total_qnty;
    }
    public function getTotalQntyByOrder($data)
    {
        $whereConditions = ['od.order_id' => $data['id'], 'pd.is_delete' => '1'];
        $listArray = [DB::raw('SUM(pd.qnty) as total')];
        $qntyData = DB::table('purchase_detail as pd')
                         ->leftJoin('order_design as od','pd.design_id','=','od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $qntyData;
    }
    public function getShippedByOrder($data)
    {
        $whereConditions = ['od.order_id' => $data['id'],'is_distribute' => '1'];
        $listArray = [DB::raw('SUM(pd.qnty) as total')];
        $qntyData = DB::table('purchase_detail as pd')
                         ->leftJoin('order_design as od','pd.design_id','=','od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $qntyData[0]->total;
    }
    public function getFinishingCount($order_id)
    {
        $whereConditions = ['order_id' => $order_id, 'is_delete' => '1'];

        $orderData = DB::table('finishing')
                         ->select(DB::raw('COUNT(order_id) as total'))
                         ->where($whereConditions)
                         ->get();

        return $orderData[0]->total;
    }
    public function getDesignTotal($order_id)
    {
        $whereConditions = ['od.order_id' => $order_id];
        $listArray = [DB::raw('SUM(dp.sales_total) as total')];
        $qntyData = DB::table('order_design as od')
                         ->leftJoin('design_product as dp','dp.design_id','=','od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $qntyData[0]->total;
    }
    public function getPoByOrder($order_id,$type)
    {
      $result = DB::table('purchase_order as po')
                ->leftJoin('orders as ord','po.order_id','=','ord.id')
                ->leftJoin('client as cl','ord.client_id','=','cl.client_id')
                ->leftJoin('vendors as v','v.id','=','po.vendor_id')
                ->select('cl.client_company','v.name_company','ord.id','ord.status','po.po_id','po.po_type',DB::raw('DATE_FORMAT(po.date,"%m/%d/%Y") as date'))
                ->where('ord.status','=','1')
                ->where('ord.is_delete','=','1')
                ->where('ord.id','=',$order_id);
                if($type == 'ro')
                {
                  $result = $result->where('po.complete','=','1');
                }
                $result = $result->GroupBy('po.po_id')
                ->get();

      return $result;
    }
}