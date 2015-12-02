<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Order extends Model {

	
	public function getOrderdata()
	{

	$whereConditions = ['order.is_delete' => '1'];
        $listArray = ['order.client_id','order.id','order.job_name','order.created_date','order.in_hands_date','order.approved_date','order.needs_garment',
                      'order.in_art_done','order.third_party_from','order.in_production','order.in_finish_done','order.ship_by',
                      'order.status','order.f_approval','client.client_company','misc_type.value as approval'];

        $orderData = DB::table('orders as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
                         ->leftJoin('misc_type as misc_type', 'order.f_approval', '=', 'misc_type.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();
        return $orderData;	
	}


/**
* Order Detail           
* @access public orderDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function orderDetail($data) {

        $whereOrderConditions = ['id' => $data['id']];
        $orderData = DB::table('orders')->where($whereOrderConditions)->get();

        $whereOrderPositionConditions = ['order_id' => $data['id']];
        $orderPositionData = DB::table('order_positions')->where($whereOrderPositionConditions)->get();

        $whereOrderLineConditions = ['order_id' => $data['id']];
        $orderLineData = DB::table('order_orderlines')->where($whereOrderLineConditions)->get();

        $whereClientConditions = ['status' => '1','is_delete' => '1','client_id' => $data['client_id']];
        $clientData = DB::table('client')->where($whereClientConditions)->get();

        $whereClientMainContactConditions = ['client_id' => $data['client_id']];
        $clientMainData = DB::table('client_contact')->where($whereClientMainContactConditions)->get();

        $combine_array = array();

        $combine_array['order'] = $orderData;


        foreach ($orderPositionData as $key=>$alldata){

            if($alldata->placementvalue){
                 $orderPositionData[$key]->placementvalue = explode(',', $alldata->placementvalue);
            }

            if($alldata->sizegroupvalue){
                $orderPositionData[$key]->sizegroupvalue = explode(',', $alldata->sizegroupvalue);
            }
           
            
        }


        
        $combine_array['order_position'] = $orderPositionData;
        $combine_array['client_data'] = $clientData;
        $combine_array['client_main_data'] = $clientMainData;
        $combine_array['order_line_data'] = $orderLineData;
       

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
        $listArray = ['on.order_notes','on.note_id','on.created_date','u.user_name'];

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

         

        } 

   }



public function updateOrderLineData($post)
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

      
         

    } 

}


public function savePO($post)
   {
    

              $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],
                    'po_type'=>$post['textdata'],
                    'date'=>$post['created_date']]);
               $insertedpoid = DB::getPdo()->lastInsertId();

              
               $whereConditions = ['order_id' => $post['order_id'],'status' => '1'];

               $orderData = DB::table('purchase_detail')->where($whereConditions)->get();

                 foreach ($orderData as $key=>$alldata){
                    
                     if(!empty($alldata->size) && $alldata->qnty != 0){
                     $resultnew = DB::table('purchase_order_line')->insert(['po_id'=>$insertedpoid,
                    'line_id'=>$alldata->id,
                    'date'=>$post['created_date']]);
                     }

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
        
        $result = DB::table('purchase_order')->where('order_id','=',$post['order_id'])->get();
        $array_count = count($result);

        if($array_count == 0) {

          $result = DB::table('purchase_order')->insert(['order_id'=>$post['order_id'],
            $post['data']=>1,
            'date'=>$post['created_date']]);

        } else {

          $result123 = DB::table('purchase_order')
                    ->where('order_id','=',$post['order_id'])
                    ->update(array( $post['data']=>1)
                            );
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
	
}