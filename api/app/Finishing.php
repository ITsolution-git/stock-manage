<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Finishing extends Model {

	
	public function getFinishingdata()
	{
        
        $listArray = ['o.id','f.qty','fc.category_name', DB::raw('CONCAT(c.pl_firstname," ",c.pl_lastname) as client_name'),'o.job_name',
                      'o.status','f.note','f.category_id','c.client_id'];

        $query = DB::table('orders as o')
                         ->leftJoin('finishing as f', 'o.id', '=', 'f.order_id')
                         ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                         ->leftJoin('finishing_category as fc', 'f.category_id', '=', 'fc.id')
                         ->select($listArray);

        if(!empty($data))
        {
            $query->where('o.created_date', '=', $data['order_date']);
        }
        
        $finishingData = $query->get();

        return $finishingData;	
	}


/**
* Finishing Detail           
* @access public finishingDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function finishingDetail($data) {

         

        $whereFinishingConditions = ['id' => $data['id']];
        $finishingData = DB::table('orders')->where($whereFinishingConditions)->get();



        $whereFinishingPositionConditions = ['order_id' => $data['id']];
        $finishingPositionData = DB::table('order_positions')->where($whereFinishingPositionConditions)->get();


        $whereFinishingLineConditions = ['order_id' => $data['id']];
        $finishingLineData = DB::table('order_orderlines')->where($whereFinishingLineConditions)->get();



       

        $whereClientConditions = ['status' => '1','is_delete' => '1','client_id' => $data['client_id']];
        $clientData = DB::table('client')->where($whereClientConditions)->get();

         $whereClientMainContactConditions = ['client_id' => $data['client_id']];
        $clientMainData = DB::table('client_contact')->where($whereClientMainContactConditions)->get();



        $combine_array = array();

        $combine_array['finishing'] = $finishingData;
        $combine_array['finishing_position'] = $finishingPositionData;
        $combine_array['client_data'] = $clientData;
        $combine_array['client_main_data'] = $clientMainData;
        $combine_array['finishing_line'] = $finishingLineData;
       

        return $combine_array;
    }


/**
* Finishing Note Details           
* @access public getFinishingNoteDetails
* @param  int $orderId
* @return array $result
*/ 

     public function getFinishingNoteDetails($id)
   {
       
        $whereConditions = ['on.order_id' => '1','on.note_status' => '1'];
        $listArray = ['on.order_notes','on.note_id','on.created_date','u.user_name'];

        $finishingNoteData = DB::table('order_notes as on')
                         ->Join('users as u', 'u.id', '=', 'on.user_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();
        return $finishingNoteData;  

   }

/**
* Finishing Note Details           
* @access public getFinishingDetailById
* @param  int $orderId
* @return array $result
*/


   public function getFinishingDetailById($id)
   {
        $result = DB::table('order_notes')->where('note_id','=',$id)->get();
        return $result;
   }

/**
* Insert Finishing Note           
* @access public saveFinishingNotes
* @param  array $post
* @return array $result
*/


public function saveFinishingNotes($post)
   {
        $result = DB::table('order_notes')->insert($post);
        return $result;
   }


/**
* Delete Finishing Note           
* @access public deleteFinishingNotes
* @param  array $post
* @return array $result
*/
    public function  deleteFinishingNotes($id)
   {
        $result = DB::table('order_notes')
                        ->where('note_id','=',$id)
                        ->update(array('note_status'=>'0'));
        return $result;
   }


/**
* Update Finishing Note           
* @access public updateFinishingNotes
* @param  array $post
* @return array $result
*/


    public function updateFinishingNotes($post)
   {
            $result = DB::table('order_notes')
                        ->where('note_id','=',$post['note_id'])
                        ->update(array('order_notes'=>$post['order_notes']));
        return $result;
   }



    public function deleteFinishing($id)
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
	
}