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

        $whereConditions = ['order.is_delete' => "1",'order.company_id' => $post['company_id']];

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
                         ->leftJoin('client_contact as cc','order.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
                         ->leftJoin('price_grid as price_grid','order.price_id','=', 'price_grid.id')
                         ->leftJoin('misc_type as misc_type','order.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$data['company_id']))
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        $combine_array['order'] = $orderDetailData;
        return $combine_array;
    }

    public function getOrderLineDetail($data)
    {
        $listArray = ['o.*','p.description as product_description','p.name as product_name','c.name as color_name'];
        $whereOrderLineConditions = ['order_id' => $data['id']];
        $orderLineData = DB::table('order_orderlines as o')
                        ->leftJoin('products as p','o.product_id','=','p.id')
                        ->leftJoin('color as c','o.color_id','=','c.id')
                        ->select($listArray)
                        ->where($whereOrderLineConditions)->get();

        $combine_array['order_line_data'] = $orderLineData;

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
* Order Note Details           
* @access public getOrderNoteDetails
* @param  int $orderId
* @return array $result
*/ 

     public function getOrderNoteDetails($id)
   {
       
        $whereConditions = ['on.order_id' => $id,'on.note_status' => '1'];
        $listArray = ['on.order_notes','on.note_id',DB::raw('DATE_FORMAT(on.created_date, "%m/%d/%Y") as created_date'),'u.user_name'];

        $orderNoteData = DB::table('order_notes as on')
                         ->Join('users as u', 'u.id', '=', 'on.user_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();
        return $orderNoteData;  

   }

/**
* Order Note Details           
* @access public getOrderDetailById
* @param  int $orderId
* @return array $result
*/


   public function getOrderDetailById($id)
   {
        $result = DB::table('order_notes')->where('note_id','=',$id)->get();
        return $result;
   }

/**
* Insert Order Note           
* @access public saveOrderNotes
* @param  array $post
* @return array $result
*/


public function saveOrderNotes($post)
   {
        $result = DB::table('order_notes')->insert($post);
        return $result;
   }


/**
* Delete Order Note           
* @access public deleteOrderNotes
* @param  array $post
* @return array $result
*/
    public function  deleteOrderNotes($id)
   {
        $result = DB::table('order_notes')
                        ->where('note_id','=',$id)
                        ->update(array('note_status'=>'0'));
        return $result;
   }


/**
* Update Order Note           
* @access public updateOrderNotes
* @param  array $post
* @return array $result
*/


    public function updateOrderNotes($post)
   {
            $result = DB::table('order_notes')
                        ->where('note_id','=',$post['note_id'])
                        ->update(array('order_notes'=>$post['order_notes']));
        return $result;
   }


   public function saveOrderLineData($post)
   {

        $result = DB::table('order_orderlines')->insert(['order_id'=>$post['order_id'],
            'size_group_id'=>$post['size_group_id'],
            'product_id'=>$post['product_id'],
            'vendor_id'=>$post['vendor_id'],
            'color_id'=>$post['color_id'],
            'client_supplied'=>$post['client_supplied'],
            'qnty'=>$post['qnty'],
            'markup'=>$post['markup'],
            'override'=>$post['override'],
            'peritem'=>$post['peritem']]);

        $insertedid = DB::getPdo()->lastInsertId();


        $post['created_date'] = date("Y-m-d H:i:s");

       


        foreach($post['items'] as $row) {
             
            $result123 = DB::table('purchase_detail')->insert(['orderline_id'=>$insertedid,
                'size'=>$row['size'],
                'order_id'=>$post['order_id'],
                'qnty'=>$row['qnty'],
                'date'=>$post['created_date']]);

           $insertednewid = DB::getPdo()->lastInsertId();

            $distribution_detail = DB::table('distribution_detail')->insert(['orderline_id'=>$insertedid,
                'size'=>$row['size'],
                'order_id'=>$post['order_id'],
                'qnty'=>$row['qnty'],
                'date'=>$post['created_date']]);

        }

        return $insertedid; 

   }



public function updateOrderLineData($post)
{
    
    $result = DB::table('order_orderlines')
                    ->where('id','=',$post['id'])
                    ->update(array('order_id'=>$post['order_id'],
                                    'size_group_id'=>$post['size_group_id'],
                                    'product_id'=>$post['product_id'],
                                    'vendor_id'=>$post['vendor_id'],
                                    'color_id'=>$post['color_id'],
                                    'client_supplied'=>$post['client_supplied'],
                                    'qnty'=>$post['qnty'],
                                    'avg_garment_cost'=>$post['avg_garment_cost'],
                                    'avg_garment_price'=>$post['avg_garment_price'],
                                    'print_charges'=>$post['print_charges'],
                                    'markup'=>$post['markup'],
                                    'markup_default'=>$post['markup_default'],
                                    'override'=>$post['override'],
                                    'peritem'=>$post['peritem'],
                                    'os'=>$post['os'],
                                    'per_line_total'=>$post['per_line_total'],
                                    'override_diff'=>$post['override_diff'])
                            );


    $post['created_date'] = date("Y-m-d H:i:s");

   

    foreach($post['items'] as $row) {
         
        $result123 = DB::table('purchase_detail')
                    ->where('id','=',$row['id'])
                    ->update(array( 'size'=>$row['size'],
                                    'qnty'=>$row['qnty'],
                                    'date'=>$post['created_date'])
                            );

      $distribution_detail = DB::table('distribution_detail')
                    ->where('id','=',$row['id'])
                    ->update(array( 'size'=>$row['size'],
                                    'qnty'=>$row['qnty'],
                                    'date'=>$post['created_date'])
                            );

    } 

}



    public function deleteOrder($id)
    {
        if(!empty($id))
        {
                $result = DB::table('orders')->where('id','=',$id)->update(array("is_delete" => '0'));
                return $result;
        }
        else
        {
                return false;
        }
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
    * Save button data          
    * @access public getOrderDetailById
    * @param  int $order_id
    * @return array $result
    */

    public function saveButtonData($post)
    {


       if($post['textdata'] == 'po' || $post['textdata'] == 'sg') {

      $whereConditions = ['order_id' => $post['order_id'],'status' => '1','is_delete' => '1'];
      $orderLineData = DB::table('order_orderlines')->where($whereConditions)->get();
      

                $vendor_array = array();
                foreach ($orderLineData as $key=>$alldataOrderLine){

                   if(!in_array($alldataOrderLine->vendor_id,$vendor_array)) {

                      array_push($vendor_array, $alldataOrderLine->vendor_id); 
                      $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],'vendor_id'=>$alldataOrderLine->vendor_id,
                      'po_type'=>$post['textdata'],
                      'date'=>$post['created_date']]);
                      
                   }

                 }

                
       

            $whereConditions = ['po.order_id' => $post['order_id'],'ood.order_id' => $post['order_id']];
            $listArray = ['po.po_id','pd.size','pd.qnty','pd.id','ood.vendor_id','po.vendor_id as po_vendorid'];
            $orderData = DB::table('purchase_detail as pd')
                             ->Join('purchase_order as po', 'pd.order_id', '=', 'po.order_id')
                             ->Join('order_orderlines as ood', 'ood.id', '=', 'pd.orderline_id')

                             ->select($listArray)
                             ->where($whereConditions)
                             ->where('pd.size', '<>','')
                             ->where('pd.qnty', '<>','0')
                             ->get();
                            
              $vendor_data_array = array();
                             
              foreach ($orderData as $key=>$alldata){

              if($alldata->vendor_id == $alldata->po_vendorid){
                $vendor_data_array[$alldata->vendor_id][] = $alldata;
              }
                    
              }

              foreach ($vendor_data_array as $key=>$alldataNew){
                foreach ($alldataNew as $key=>$alldataSaveNew){
                 $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$alldataSaveNew->po_id,
                'line_id'=>$alldataSaveNew->id,
                'date'=>$post['created_date']]);
               }
             }

       } elseif ($post['textdata'] == 'cp') {
         
           $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],
                    'po_type'=>$post['textdata'],
                    'date'=>$post['created_date']]);
               $insertedpoid = DB::getPdo()->lastInsertId();
              
               $whereConditions = ['op.order_id' => $post['order_id'],'op.status' => '1'];
               $orderData = DB::table('order_positions as op')->leftJoin('misc_type as m','m.id','=','op.placement_type')->select("op.id",'m.slug')->where($whereConditions)->get();
                 for($i=0; $i<4; $i++)
                 {
                    DB::table('purchase_placement')->insert(['po_id'=>$insertedpoid]); // 4 placements
                 }

                 foreach ($orderData as $key=>$alldata){
                    
                    
                      if($alldata->slug == 43) {
                     $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$insertedpoid,
                    'line_id'=>$alldata->id,
                    'date'=>$post['created_date']]);
                     
                   }
                 }
       } elseif ($post['textdata'] == 'ce') {
         
           $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],
                    'po_type'=>$post['textdata'],
                    'date'=>$post['created_date']]);
               $insertedpoid = DB::getPdo()->lastInsertId();
              
                $whereConditions = ['op.order_id' => $post['order_id'],'op.status' => '1'];
               $orderData = DB::table('order_positions as op')->leftJoin('misc_type as m','m.id','=','op.placement_type')->select("op.id",'m.slug')->where($whereConditions)->get();

                 for($i=0; $i<4; $i++)
                 {
                     DB::table('purchase_placement')->insert(['po_id'=>$insertedpoid]);// 4 placements
                 }

                 foreach ($orderData as $key=>$alldata){
                     if($alldata->slug == 45) {
                     $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$insertedpoid,
                    'line_id'=>$alldata->id,
                    'date'=>$post['created_date']]);

                   

                   }
                 }
       }

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


    /**
* Order Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function POorderDetail($data) {

        $wherePoConditions = ['order_id' => $data['id']];
        $listPoArray = ['po_id','order_id','vendor_id','vendor_contact_id','po_type','shipt_block','vendor_charge','order_total',DB::raw('DATE_FORMAT(ship_date, "%m/%d/%Y") as ship_date'),
                      DB::raw('DATE_FORMAT(hand_date, "%m/%d/%Y") as hand_date'),DB::raw('DATE_FORMAT(arrival_date, "%m/%d/%Y") as arrival_date'),
                      DB::raw('DATE_FORMAT(expected_date, "%m/%d/%Y") as expected_date'),DB::raw('DATE_FORMAT(created_for_date, "%m/%d/%Y") as created_for_date'),
                      DB::raw('DATE_FORMAT(vendor_arrival_date, "%m/%d/%Y") as vendor_arrival_date'),DB::raw('DATE_FORMAT(vendor_deadline, "%m/%d/%Y") as vendor_deadline'),
                      'vendor_party_bill','ship_to','vendor_instruction','receive_note',DB::raw('DATE_FORMAT(date, "%m/%d/%Y") as date'),'complete'];
        $orderPOData = DB::table('purchase_order')->select($listPoArray)->where($wherePoConditions)->get();
        $combine_array['order_po_data'] = $orderPOData;
        return $combine_array;
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

    public function getTaskList($order_id)
    {
        $listArray = ['ot.id','ot.order_name','ot.due_date','ot.time','ot.status','ot.type','ot.note','ot.date_added','t.task_name','r.result_name'];
        $whereOrderTaskConditions = ['ot.order_id' => $order_id];
        $orderTaskData = DB::table('order_tasks as ot')
                        ->Join('task as t', 't.id','=','ot.task_id')
                        ->Join('result as r', 'r.id','=','ot.result_id')
                        ->select($listArray)
                        ->where($whereOrderTaskConditions)->get();

        return $orderTaskData;
    }

    /**
    * Save button data          
    * @access public getOrderDetailById
    * @param  int $po_id
    * @return array $result
    */

    public function poDuplicate($post)
    {

      $whereConditions = ['po_id' => $post['po_id']];
      $orderPoData = DB::table('purchase_order')->where($whereConditions)->get();


      $result = DB::table('purchase_order')->insert(['order_id'=>$orderPoData[0]->order_id,'vendor_id'=>$orderPoData[0]->vendor_id,
      'po_type'=>$orderPoData[0]->po_type,
      'vendor_contact_id'=>$orderPoData[0]->vendor_contact_id,
      'shipt_block'=>$orderPoData[0]->shipt_block,
      'vendor_charge'=>$orderPoData[0]->vendor_charge,
      'order_total'=>$orderPoData[0]->order_total,
      'ship_date'=>$orderPoData[0]->ship_date,
      'hand_date'=>$orderPoData[0]->hand_date,
      'arrival_date'=>$orderPoData[0]->arrival_date,
      'expected_date'=>$orderPoData[0]->expected_date,
      'created_for_date'=>$orderPoData[0]->created_for_date,
      'vendor_arrival_date'=>$orderPoData[0]->vendor_arrival_date,
      'vendor_deadline'=>$orderPoData[0]->vendor_deadline,
      'vendor_party_bill'=>$orderPoData[0]->vendor_party_bill,
      'ship_to'=>$orderPoData[0]->ship_to,
      'vendor_instruction'=>$orderPoData[0]->vendor_instruction,
      'complete'=>$orderPoData[0]->complete,
      'date'=>$post['created_date']]);
      
       $insertedpoid = DB::getPdo()->lastInsertId();


               $whereConditionsline = ['po_id' => $post['po_id']];
               $orderLineData = DB::table('purchase_order_line')->where($whereConditionsline)->get();

                 foreach ($orderLineData as $key=>$alldata){
                    
                     $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$insertedpoid,
                    'line_id'=>$alldata->line_id,
                    'qnty_ordered'=>$alldata->qnty_ordered,
                    'unit_price'=>$alldata->unit_price,
                    'line_total'=>$alldata->line_total,
                    'short'=>$alldata->short,
                    'over'=>$alldata->over,
                    'total_qnty'=>$alldata->total_qnty,
                    'location'=>$alldata->location,
                    'instruction'=>$alldata->instruction,
                    'date'=>$post['created_date']]);
                  
                 }
    }

    public function getTaskDetail($id)
    {
        $listArray = ['ot.*','t.task_name','r.result_name'];
        $whereOrderTaskConditions = ['ot.id' => $id];
        $orderTaskData = DB::table('order_tasks as ot')
                        ->Join('task as t', 't.id','=','ot.task_id')
                        ->Join('result as r', 'r.id','=','ot.result_id')
                        ->select($listArray)
                        ->where($whereOrderTaskConditions)->get();
        return $orderTaskData;
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
        

        $listArray = ['od.*','cf.name as front_color_name','bci.name as back_color_name','sr.name as side_right_color_name',
                        'sl.name as side_left_color_name','tc.name as top_color_name','bc.name as bottom_color_name'];

        $designDetailData = DB::table('order_design as od')
                         
                         ->leftJoin('color as cf','od.front_color_id','=', 'cf.id')
                         ->leftJoin('color as bci','od.back_color_id','=', 'bci.id')
                         ->leftJoin('color as sr','od.side_right_color_id','=', 'sr.id')
                         ->leftJoin('color as sl','od.side_left_color_id','=', 'sl.id')
                         ->leftJoin('color as tc','od.top_color_id','=', 'tc.id')
                         ->leftJoin('color as bc','od.bottom_color_id','=', 'bc.id')
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

       
        if(count($combine_array['order_design_position'])>0)
        {
            foreach ($combine_array['order_design_position'] as $key => $value) 
            {
                $combine_array['order_design_position'][$key]->image_1_url_photo = (!empty($value->image_1))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_1:'';
                $combine_array['order_design_position'][$key]->image_2_url_photo = (!empty($value->image_2))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_2:'';
                $combine_array['order_design_position'][$key]->image_3_url_photo = (!empty($value->image_3))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_3:'';
                $combine_array['order_design_position'][$key]->image_4_url_photo = (!empty($value->image_4))?UPLOAD_PATH.$data['company_id'].'/order_design_position/'.$value->id."/".$value->image_4:'';

            }
         }   

        return $combine_array;
    }

    public function getAllDesigndata()
    {
        $whereConditions = ['od.status' => '1','od.is_delete' => '1','odp.is_delete' => '1'];
        $listArray = ['p.name as product_name','od.shipping_date','od.id','od.order_id','odp.position_id','od.design_name',DB::raw('group_concat(m.value) as position_name'),DB::raw('count(odp.position_id) as count_position')];
        $designData = DB::table('order_design as od')
                        ->Join('order_design_position as odp','odp.design_id','=', 'od.id')
                        ->leftJoin('misc_type as m','odp.position_id','=', 'm.id')
                        ->leftJoin('design_product as dp','odp.design_id','=',DB::raw("dp.design_id AND dp.is_delete = '1'"))
                        ->leftJoin('products as p','dp.product_id','=', 'p.id')
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
        $listArray = ['sales_id','is_blind','account_manager_id','price_id','company_id'];

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
        $whereConditions = ['od.order_id' => $order_id];
        $listArray = ['dp.*'];

        $orderData = DB::table('design_product as dp')
                         ->leftJoin('order_design as od','dp.design_id','=', 'od.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $orderData;
    }
    
    public function getTotalQntyByDesign($design_id)
    {
        $whereConditions = ['design_id' => $design_id];

        $orderData = DB::table('purchase_detail as dp')
                         ->select(DB::raw('sum(qnty) as total_qnty'))
                         ->where($whereConditions)
                         ->get();
        
        return $orderData[0]->total_qnty;
    }
}