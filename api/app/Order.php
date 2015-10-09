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

        $orderData = DB::table('order as order')
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
        $orderData = DB::table('order')->where($whereOrderConditions)->get();



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
        $combine_array['order_position'] = $orderPositionData;
        $combine_array['client_data'] = $clientData;
        $combine_array['client_main_data'] = $clientMainData;
        $combine_array['order_line'] = $orderLineData;
       

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
       
        $whereConditions = ['on.order_id' => '1','on.note_status' => '1'];
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



    public function deleteOrder($id)
    {
        if(!empty($id))
        {
                $result = DB::table('order')->where('id','=',$id)->update(array("is_delete" => '0'));
                return $result;
        }
        else
        {
                return false;
        }
    }
	
}