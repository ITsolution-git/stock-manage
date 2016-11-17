<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use DateTime;

class Order extends Model {

  public function __construct(Common $common) 
  {
      $this->common = $common;
  }
	
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

        $this->common->getDisplayNumber('orders',$post['company_id'],'company_id','id','yes');

        $whereConditions = ['order.is_delete' => '1','order.company_id' => $post['company_id'],'order.parent_order_id' => '0'];

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS order.client_id,order.id,order.display_number,order.name,order.created_date,order.approved_date,order.date_shipped,
                      order.status,order.approval_id,client.client_company,misc_type.value as approval,sales.sales_name,users.name as account_manager')];

        $orderData = DB::table('orders as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
                         ->leftJoin('sales as sales','order.sales_id','=', 'sales.id')
                         ->leftJoin('users as users','order.account_manager_id','=', 'users.id')
                         ->leftJoin('misc_type as misc_type','order.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
                         ->select($listArray)
                         ->where($whereConditions);
                        
                        if($search != '')
                        {
                          $orderData = $orderData->Where(function($query) use($search)
                          {
                              $query->orWhere('order.name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('sales.sales_name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('order.display_number', 'LIKE', '%'.$search.'%')
                                    ->orWhere('users.name', 'LIKE', '%'.$search.'%')
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
                        if(isset($post['filter']['order_status']) && $post['filter']['order_status'] != '')
                        {
                            $orderData = $orderData->where('order.approval_id', $post['filter']['order_status']);
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
        
        $listArray = ['order.*','order.name as order_name','client.client_company','misc_type.value as approval','sales.sales_name',
                      'users.name','cc.first_name as client_first_name','i.id as invoice_id',
                      'cc.last_name as client_last_name','price_grid.name as price_grid_name','a.approval as art_approval'];

        $orderDetailData = DB::table('orders as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
                         ->leftJoin('sales as sales','order.sales_id','=', 'sales.id')
                         ->leftJoin('users as users','order.account_manager_id','=', 'users.id')
                         ->leftJoin('invoice as i','order.id','=', 'i.order_id')
                         ->leftJoin('art as a','order.id','=', 'a.order_id')
                         ->leftJoin('client_contact as cc','order.contact_main_id','=',DB::raw("cc.id"));
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
                         ->GroupBy('order.id')
                         ->get();

        $combine_array = array();
        $combine_array['order'] = $orderDetailData;
        return $combine_array;
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

    /**
* Order Detail           
* @access public designDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function designDetail($data) {

      
        $whereConditions = ['od.is_delete' => "1",'od.id' => $data['id']];
        $listArray = ['od.*','o.order_number','o.is_complete','o.price_id','o.display_number as order_display_number','o.affiliate_display_number'];

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

        $listArray = ['odp.*','m.value as position_name','mt.value as placement_type_name','odp.position_id as duplicate_position_id','o.price_id','p.foil',
                      'p.number_on_dark','p.number_on_dark','p.over_size_screens','p.ink_changes','p.number_on_light','p.discharge','p.discharge','p.specialty','p.press_setup','p.screen_fees'];

        $orderPositionData = DB::table('order_design_position as odp')
                            ->leftJoin('misc_type as m','odp.position_id','=', 'm.id')
                            ->leftJoin('misc_type as mt','odp.placement_type','=', 'mt.id')
                            ->leftJoin('order_design as od','odp.design_id','=', 'od.id')
                            ->leftJoin('orders as o','od.order_id','=', 'o.id')
                            ->leftJoin('price_grid as p','o.price_id','=', 'p.id')
                            ->where($whereOrderPositionConditions)
                            ->select($listArray)
                            ->get();


        $combine_array['order_design_position'] = $orderPositionData;
        $combine_array['total_pos_qnty'] = 0;
       
        if(count($combine_array['order_design_position'])>0)
        {
            $total_pos_qnty = 0;
            $total_screen_fees = 0;
            foreach ($combine_array['order_design_position'] as $key => $value) 
            {

               
               $combine_array['order_design_position'][$key]->total_price = ($value->foil_qnty * $value->foil) + ($value->number_on_dark_qnty * $value->number_on_dark) +($value->oversize_screens_qnty * $value->over_size_screens) +($value->ink_charge_qnty * $value->ink_changes) + ($value->number_on_light_qnty * $value->number_on_light) + ($value->discharge_qnty * $value->discharge) + ($value->speciality_qnty * $value->specialty) + ($value->press_setup_qnty * $value->press_setup);
               $combine_array['order_design_position'][$key]->total_price = round($combine_array['order_design_position'][$key]->total_price, 2);
               $combine_array['order_design_position'][$key]->position_header_name = $value->position_name;
               $combine_array['order_design_position'][$key]->qnty_header_name = $value->qnty;
               $combine_array['order_design_position'][$key]->stitch_header_name = $value->color_stitch_count;
               $combine_array['order_design_position'][$key]->placement_header_name = $value->placement_type_name;

                 /*$combine_array['order_design_position'][$key]->position_header_name = $value->position_name.'-'.$value->qnty;

                 if($value->color_stitch_count != ''){
                  $combine_array['order_design_position'][$key]->position_header_name .= '-'.$value->color_stitch_count;
                 }

                 if($value->placement_type_name != ''){
                   $combine_array['order_design_position'][$key]->position_header_name .= '-'.$value->placement_type_name;
                 }*/
                 
                $combine_array['order_design_position'][$key]->image_1_url_photo = (!empty($value->image_1))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_1:'';
                $combine_array['order_design_position'][$key]->image_2_url_photo = (!empty($value->image_2))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_2:'';
                $combine_array['order_design_position'][$key]->image_3_url_photo = (!empty($value->image_3))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_3:'';
                $combine_array['order_design_position'][$key]->image_4_url_photo = (!empty($value->image_4))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_4:'';
                
                $total_pos_qnty += $value->qnty;
                $combine_array['order_design_position'][$key]->total_screen_fees = $value->screen_fees_qnty * $value->screen_fees;
                $total_screen_fees += $value->screen_fees_qnty * $value->screen_fees;

                $value->position_image = '';
                
                if($combine_array['order_design_position'][$key]->image_1_url_photo != '')
                {
                  $value->position_image = $combine_array['order_design_position'][$key]->image_1_url_photo;
                }
                else if($combine_array['order_design_position'][$key]->image_2_url_photo != '')
                {
                  $value->position_image = $combine_array['order_design_position'][$key]->image_2_url_photo;
                }
                else if($combine_array['order_design_position'][$key]->image_3_url_photo != '')
                {
                  $value->position_image = $combine_array['order_design_position'][$key]->image_3_url_photo;
                }
                else if($combine_array['order_design_position'][$key]->image_4_url_photo != '')
                {
                  $value->position_image = $combine_array['order_design_position'][$key]->image_4_url_photo;
                } else {
                    $value->position_image = NOIMAGE;
                }
            }
            $combine_array['total_pos_qnty'] = $total_pos_qnty;
            $combine_array['total_screen_fees'] = $total_screen_fees;
         }
         
        return $combine_array;
    }

    public function getAllDesigndata()
    {
        $whereConditions = ['od.status' => '1','od.is_delete' => '1','odp.is_delete' => '1'];
        $listArray = ['od.shipping_date','od.id','od.order_id','odp.position_id','od.design_name','od.display_number as design_display_number',DB::raw('group_concat(m.value) as position_name'),DB::raw('count(odp.position_id) as count_position')];
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
        $listArray = ['sales_id','is_blind','account_manager_id','price_id','company_id','name','sns_shipping','date_start','date_shipped','in_hands_by','approval_id','client_id','contact_main_id','custom_po'];

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
        $whereConditions = ['pam.order_id' => $data['id']];
        $listArray = [DB::raw('SUM(pas.distributed_qnty) as total')];
        $qntyData = DB::table('product_address_mapping as pam')
                         ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
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
        $whereConditions = ['od.order_id' => $order_id,'od.is_delete' => '1','dp.is_delete' => '1'];
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
                ->select('cl.client_company','v.name_company','ord.id','ord.status','po.po_id','po.display_number','po.po_type',DB::raw('DATE_FORMAT(po.date,"%m/%d/%Y") as date'))
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

    public function checkDuplicatePositions($design_id,$position)
    {
     
       $whereConditions = ['status' => '1','is_delete' => '1','design_id' => $design_id];
       $listArray = ['position_id'];
       $designData = DB::table('order_design_position')
                        ->select($listArray)
                        ->where($whereConditions)->get();
                     
      $position_array = array();
      $duplicate =  0;
      foreach($designData as $datanew) {
          if($datanew->position_id == $position) {
             $duplicate =  1;
          }
      }
        return $duplicate;
    }

    public function getOrderNotes($order_id)
    {
        $whereConditions = ['od.order_id' => $order_id,'od.is_delete' => '1','odp.is_delete' => '1'];


        $orderData = DB::table('order_design as od')
                        ->leftJoin('order_design_position as odp','od.id','=','odp.design_id')
                        ->select(DB::raw('COUNT(odp.note) as total'))
                        ->where($whereConditions)
                        ->where('odp.note','!=','')
                        ->get();

        return $orderData[0]->total;
    }


    public function getOrderNoteDetail($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

    $listArray = [DB::raw('SQL_CALC_FOUND_ROWS odp.note,odp.id')];

    $result = DB::table('order_design as od')
            ->leftJoin('order_design_position as odp','od.id','=','odp.design_id')
            ->leftJoin('orders as o','o.id','=','od.order_id')
          ->select($listArray)
          ->where('od.is_delete','=','1')
          ->where('odp.is_delete','=','1')
          ->where('odp.note','!=','')
          ->where('o.company_id','=',$post['company_id'])
          ->where('o.parent_order_id','=',0)
          ->where('o.display_number','=',$post['display_number']);

          if($search != '')               
                    {
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('odp.note', 'LIKE', '%'.$search.'%');
                      });
                    }
                 $result = $result->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
         ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
    
   
    $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;   
    //echo "<pre>"; print_r($result); die();
    return $returnData;
  }

    public function getTotalPackingCharge($order_id)
    {
        $whereConditions = ['od.order_id' => $order_id,'od.is_delete' => '1'];
        $listArray = [DB::raw('SUM(dp.extra_charges) as total')];
        $qntyData = DB::table('order_design as od')
                         ->leftJoin('design_product as dp','dp.design_id','=','od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $qntyData[0]->total;
    }


   


         public function orderInfoData($company_id,$order_id) {
      
            $whereConditions = ['is_delete' => "1",'id' => $order_id,'company_id' => $company_id];
            $orderDetailData = DB::table('orders')
                             ->select('*')
                             ->where($whereConditions)
                             ->get();

           return $orderDetailData;
      }


       public function GetOrderDetailAll($orderId) {

     
        
        $where = ['o.id' => $orderId,'o.is_delete' => '1','od.is_delete' => '1','dp.is_delete' => '1','pcs.is_delete' => '1','p.is_delete' => '1','v.is_delete' => '1'];

        $listArray = ['p.id','p.name as product_name','dp.sales_total','dp.total_line_charge','dp.is_supply','dp.is_calculate','v.name_company',
                        'c.name as color_name','dp.id as design_product_id','p.vendor_id'];

        $productData = DB::table('orders as o')
                         ->leftJoin('order_design as od', 'o.id', '=', 'od.order_id')
                         ->leftJoin('design_product as dp', 'od.id', '=', 'dp.design_id')
                         ->leftJoin('products as p', 'dp.product_id', '=', 'p.id')
                         ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                         ->leftJoin('purchase_detail as pcs', 'dp.id', '=', 'pcs.design_product_id')
                         ->leftJoin('color as c', 'pcs.color_id', '=', 'c.id')
                         ->select($listArray)
                         ->where($where)
                         ->GroupBy('dp.id')
                         ->orderBy('dp.id','desc')
                         ->get();

        $combine_array = array();

        if(!empty($productData) && $productData[0]->id > 0)
        {
            
            foreach ($productData as  $key=>$product) {

                $sizeData = DB::table('purchase_detail as pd')
                                     ->where('pd.design_product_id','=',$product->design_product_id)
                                     ->get();
                $product->sizeData = $sizeData;


                $total_qnty = DB::table('purchase_detail')
                                     ->select(DB::raw('SUM(qnty) as total_qnty'))
                                     ->where('design_product_id','=',$product->design_product_id)
                                     ->get();

                $product->total_qnty = $total_qnty[0]->total_qnty; 
               
                $combine_array[] = $product;
            }
        }
        else
        {
            return array();
        }

        return $combine_array;
    }


     public function orderChargeData($orderId) {

        $where = ['o.id' => $orderId,'o.is_delete' => '1','od.is_delete' => '1','odp.is_delete' => '1'];


        $productData = DB::table('orders as o')
                         ->leftJoin('order_design as od', 'o.id', '=', 'od.order_id')
                         ->leftJoin('order_design_position as odp', 'od.id', '=', 'odp.design_id')
                          ->select(DB::raw('sum(foil_qnty) as foil_qnty'),DB::raw('sum(number_on_dark_qnty) as number_on_dark_qnty'),DB::raw('sum(oversize_screens_qnty) as oversize_screens_qnty'),DB::raw('sum(ink_charge_qnty) as ink_charge_qnty'),DB::raw('sum(number_on_light_qnty) as number_on_light_qnty'),DB::raw('sum(discharge_qnty) as discharge_qnty'),DB::raw('sum(speciality_qnty) as speciality_qnty'))
                         ->where($where)
                         ->get();

        return $productData;
        
    }


    public function GetAllClientsLowerCase($post)
  {
      
       $listArray = ['client_id','client_company'];
      $whereConditions = ['is_delete' => "1",'company_id' => $post['company_id']];
      $orderDetailData = DB::table('client')
         ->select($listArray)
         ->where($whereConditions)
         ->get();
   
        foreach ($orderDetailData as $key=>$alldata){
          $newData[$key]['client_company'] = strtolower($alldata->client_company);
          $newData[$key]['client_id'] = $alldata->client_id;
        }

         return $newData;

  }

    public function getApprovalOrders($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
          $search = $post['filter']['name'];
        }

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS o.id as order_id,o.display_number,o.created_date,SUM(dp.sales_total) as sales_total,u.name,o.order_sns_status,o.sns_shipping,o.order_number,o.updated_date')];

        $where = ['v.name_company' => 'S&S Vendor','o.company_id' => $post['company_id'],'o.parent_order_id' => 0];
        $orderData = DB::table('orders as o')
                          ->leftJoin('order_design as od', 'o.id', '=', 'od.order_id')
                          ->leftJoin('design_product as dp', 'od.id', '=', 'dp.design_id')
                          ->leftJoin('products as p', 'dp.product_id', '=', 'p.id')
                          ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                          ->leftJoin('users as u', 'u.id', '=', 'o.approved_by')
                          ->select($listArray)
                          ->where($where);
                          if($search != '')
                          {
                            $orderData = $orderData->Where(function($query) use($search)
                            {
                                $query->orWhere('o.display_number', 'LIKE', '%'.$search.'%')
                                      ->orWhere('o.order_number', 'LIKE', '%'.$search.'%')
                                      ->orWhere('u.name', 'LIKE', '%'.$search.'%');
                            });
                          }
                          if($post['type'] == 'denied')
                          {
                            $orderData = $orderData->where('o.order_sns_status','=','denied');
                          }
                          if($post['type'] == 'approved')
                          {
                            $orderData = $orderData->where('o.order_number','!=','');
                          }
                          if($post['type'] == 'pending')
                          {
                            $orderData = $orderData->where('o.order_number','=','')
                                          ->where('o.order_sns_status','=','');
                          }
                          $orderData = $orderData->GroupBy('o.id')
                          ->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
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
}