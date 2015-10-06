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
                      'order.status','client.client_company'];

        $orderData = DB::table('order as order')
                         ->Join('client as client', 'order.client_id', '=', 'client.client_id')
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