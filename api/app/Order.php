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


       

        $whereClientConditions = ['status' => '1','is_delete' => '1','client_id' => $data['client_id']];
        $clientData = DB::table('client')->where($whereClientConditions)->get();

         $whereClientMainContactConditions = ['client_id' => $data['client_id']];
        $clientMainData = DB::table('client_contact')->where($whereClientMainContactConditions)->get();


/*

        $whereTimeoffConditions = ['staff_id' => $staffId,'time_off.is_delete' => '1','time_off.status' => '1','type.status' => '1','type.type' => 'timeoff'];
        $listArrayTimeoff = ['time_off.classification_id','time_off.id','time_off.staff_id','time_off.timerecord','time_off.applied_hours','time_off.date_begin',
                      'time_off.date_end', 'time_off.status','type.name'];

         $timeoffData = DB::table('time_off as time_off')
                         ->Join('type as type', 'type.id', '=', 'time_off.classification_id')
                         ->select($listArrayTimeoff)
                         ->where($whereTimeoffConditions)
                         ->get();



*/

        $combine_array = array();

        $combine_array['order'] = $orderData;
        $combine_array['client_data'] = $clientData;
        $combine_array['client_main_data'] = $clientMainData;
       

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