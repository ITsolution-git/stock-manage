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

        $combine_array = array();

        $combine_array['shipping'] = $shippingData;

        return $combine_array;
    }


/**
* Shipping Note Details           
* @access public getShippingNoteDetails
* @param  int $orderId
* @return array $result
*/ 

     public function getShippingNoteDetails($id)
   {
       
        $whereConditions = ['on.order_id' => $id,'on.note_status' => '1'];
        $listArray = ['on.order_notes','on.note_id','on.created_date','u.user_name'];

        $orderNoteData = DB::table('order_notes as on')
                         ->Join('users as u', 'u.id', '=', 'on.user_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();
        return $orderNoteData;  

   }

/**
* Shipping Note Details           
* @access public getShippingDetailById
* @param  int $orderId
* @return array $result
*/


   public function getShippingDetailById($id)
   {
        $result = DB::table('order_notes')->where('note_id','=',$id)->get();
        return $result;
   }

/**
* Insert Shipping Note           
* @access public saveShippingNotes
* @param  array $post
* @return array $result
*/


public function saveShippingNotes($post)
   {
        $result = DB::table('order_notes')->insert($post);
        return $result;
   }


/**
* Delete Shipping Note           
* @access public deleteShippingNotes
* @param  array $post
* @return array $result
*/
    public function  deleteShippingNotes($id)
   {
        $result = DB::table('order_notes')
                        ->where('note_id','=',$id)
                        ->update(array('note_status'=>'0'));
        return $result;
   }


/**
* Update Shipping Note           
* @access public updateShippingNotes
* @param  array $post
* @return array $result
*/


    public function updateShippingNotes($post)
   {
            $result = DB::table('order_notes')
                        ->where('note_id','=',$post['note_id'])
                        ->update(array('order_notes'=>$post['order_notes']));
        return $result;
   }


   public function saveShippingLineData($post)
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

   }



public function updateShippingLineData($post)
{
    
    $result = DB::table('order_orderlines')
                    ->where('id','=',$post['id'])
                    ->update(array('size_group_id'=>$post['size_group_id'],
                                    'product_id'=>$post['product_id'],
                                    'vendor_id'=>$post['vendor_id'],
                                    'color_id'=>$post['color_id'],
                                    'client_supplied'=>$post['client_supplied'],
                                    'qnty'=>$post['qnty'],
                                    'markup'=>$post['markup'],
                                    'override'=>$post['override'],
                                    'per_line_total'=>$post['per_line_total'],
                                    'peritem'=>$post['peritem'])
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



    public function deleteShipping($id)
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
    * Shipping line Details           
    * @access public getShippingDetailById
    * @param  int $orderline_id
    * @return array $result
    */

    public function getShippingLineItemById($id)
    {
        $result = DB::table('purchase_detail')->where('orderline_id','=',$id)->get();
        return $result;
    }

    /**
    * Save button data          
    * @access public getShippingDetailById
    * @param  int $order_id
    * @return array $result
    */

    public function saveButtonData($post)
    {


       if($post['textdata'] == 'po' || $post['textdata'] == 'sg') {

      $whereConditions = ['order_id' => $post['order_id'],'status' => '1','is_delete' => '1'];
      $orderLineData = DB::table('order_orderlines')->where($whereConditions)->get();
      

                $vendor_array = array();
                foreach ($orderLineData as $key=>$alldataShippingLine){

                   if(!in_array($alldataShippingLine->vendor_id,$vendor_array)) {

                      array_push($vendor_array, $alldataShippingLine->vendor_id); 
                      $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],'vendor_id'=>$alldataShippingLine->vendor_id,
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
              
               $whereConditions = ['order_id' => $post['order_id'],'status' => '1'];
               $orderData = DB::table('order_positions')->where($whereConditions)->get();

                 foreach ($orderData as $key=>$alldata){
                    
                    
                      if($alldata->placement_type == 43) {
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
              
               $whereConditions = ['order_id' => $post['order_id'],'status' => '1'];
               $orderData = DB::table('order_positions')->where($whereConditions)->get();

                 foreach ($orderData as $key=>$alldata){
                     if($alldata->placement_type == 45) {
                     $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$insertedpoid,
                    'line_id'=>$alldata->id,
                    'date'=>$post['created_date']]);
                   }
                 }
       }

    }

    /**
    * Shipping item Details           
    * @access public getShippingItemById
    * @param  int $item_id
    * @return array $result
    */

    public function getShippingItemById($id)
    {
        $whereConditions = ['price_id' => $id,'is_per_order' => '1'];
        $result = DB::table('price_grid_charges')->where($whereConditions)->get();
        return $result;
    }

    /**
    * Shipping item Details           
    * @access public getItemsByShipping
    * @param  int $item_id
    * @return array $result
    */

    public function getItemsByShipping($id)
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
* Shipping Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function POorderDetail($data) {

        $wherePoConditions = ['order_id' => $data['id']];
        $orderPOData = DB::table('purchase_order')->where($wherePoConditions)->get();
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
        $listArray = ['pd.id','ol.product_id','ol.vendor_id','ol.color_id','ol.size_group_id','pd.size','pd.qnty','mt.value as size_group_name','mt2.value as color_name','p.name','v.main_contact_person'];

        $orderData = DB::table('orders as order')
                        ->select($listArray)
                        ->leftJoin('order_orderlines as ol', 'order.id', '=', 'ol.order_id')
                        ->leftJoin('distribution_detail as pd', 'ol.id', '=', 'pd.orderline_id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('products as p','p.id','=','ol.product_id')
                        ->leftJoin('vendors as v','v.id','=','ol.vendor_id')
                        ->leftJoin('misc_type as mt2','mt2.id','=','ol.color_id')
                        ->where($data)
                        ->where('pd.qnty','!=','')
                        ->get();
        return $orderData;  
    }

    public function getDistributedAddress($data)
    {
        $orderData = DB::table('client_distaddress as cd')
                        ->leftJoin('item_address_mapping as ia', 'cd.id', '=', 'ia.address_id')
                        ->where($data)
                        ->GroupBy('ia.address_id')
                        ->get();
        return $orderData;  
    }

    public function getDistributedItems($data)
    {
        $listArray = ['pd.id','ol.product_id','ol.vendor_id','ol.color_id','ol.size_group_id','pd.size','pd.qnty','mt.value as size_group_name','mt2.value as color_name','p.name','v.main_contact_person'];

        $orderData = DB::table('orders as order')
                        ->select($listArray)
                        ->leftJoin('order_orderlines as ol', 'order.id', '=', 'ol.order_id')
                        ->leftJoin('distribution_detail as pd', 'ol.id', '=', 'pd.orderline_id')
                        ->leftJoin('misc_type as mt','mt.id','=','ol.size_group_id')
                        ->leftJoin('products as p','p.id','=','ol.product_id')
                        ->leftJoin('vendors as v','v.id','=','ol.vendor_id')
                        ->leftJoin('misc_type as mt2','mt2.id','=','ol.color_id')
                        ->leftJoin('item_address_mapping as ia', 'pd.id', '=', 'ia.item_id')
                        ->where($data)
                        ->get();
        return $orderData;
    }

    public function getTaskList($order_id)
    {
        $listArray = ['ot.id','ot.order_name','ot.due_date','ot.time','ot.status','ot.type','ot.note','ot.date_added','t.task_name','r.result_name'];
        $whereShippingTaskConditions = ['ot.order_id' => $order_id];
        $orderTaskData = DB::table('order_tasks as ot')
                        ->Join('task as t', 't.id','=','ot.task_id')
                        ->Join('result as r', 'r.id','=','ot.result_id')
                        ->select($listArray)
                        ->where($whereShippingTaskConditions)->get();

        return $orderTaskData;
    }

    /**
    * Save button data          
    * @access public getShippingDetailById
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
        $whereShippingTaskConditions = ['ot.id' => $id];
        $orderTaskData = DB::table('order_tasks as ot')
                        ->Join('task as t', 't.id','=','ot.task_id')
                        ->Join('result as r', 'r.id','=','ot.result_id')
                        ->select($listArray)
                        ->where($whereShippingTaskConditions)->get();
        return $orderTaskData;
    }
}