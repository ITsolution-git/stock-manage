<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Finishing extends Model {

    
    public function getFinishingdata($company_id)
    {
        $listArray = ['o.id as order_id','f.id','f.qty','fc.category_name', DB::raw('CONCAT(c.pl_firstname," ",c.pl_lastname) as client_name'),'o.job_name',
                      'f.status','f.note','f.category_id','c.client_id','f.time','f.start_time','f.end_time','f.est'];

        $query = DB::table('orders as o')
                        ->leftJoin('finishing as f', 'o.id', '=', 'f.order_id')
                        ->leftJoin('client as c', 'o.client_id', '=', 'c.client_id')
                        ->leftJoin('finishing_category as fc', 'f.category_id', '=', 'fc.id')
                        ->select($listArray)
                        ->where('f.is_delete', '!=', '1')
                        ->where('o.company_id', '=', $company_id);
        
        $finishingData = $query->get();

        return $finishingData;  
    }

    public function addFinishing($data)
    {
        $result = DB::table('finishing')->insert($data);
        
        return $result;
    }

    public function updateFinishing($data)
    {
        $result = DB::table($data['table'])
                    ->where($data['where'])
                    ->update($data['field']);
        
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

    public function getFinishingDetailById($id)
    {
        $result = DB::table('finishing')->where('id','=',$id)->get();
        return $result;
    }
    
}