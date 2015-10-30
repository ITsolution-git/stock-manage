<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Finishing extends Model {

	
	public function getFinishingdata()
	{
        
        $listArray = ['o.id as order_id','f.id','f.qty','fc.category_name', DB::raw('CONCAT(c.pl_firstname," ",c.pl_lastname) as client_name'),'o.job_name',
                      'o.status','f.note','f.category_id','c.client_id'];

        $query = DB::table('orders as o')
                        ->leftJoin('finishing as f', 'o.id', '=', 'f.order_id')
                        ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                        ->leftJoin('finishing_category as fc', 'f.category_id', '=', 'fc.id')
                        ->select($listArray)
                        ->where('f.is_delete', '!=', '1');
        
        $finishingData = $query->get();

        return $finishingData;	
	}

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
                $result = DB::table('finishing')->where('id','=',$id)->update(array("is_delete" => '1'));
                return $result;
        }
        else
        {
                return false;
        }
    }
	
}