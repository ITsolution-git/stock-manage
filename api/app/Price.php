<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Price extends Model {


/**
* Price listing array           
* @access public priceList
* @return array $priceData
*/

    public function priceList() {
        
        $whereConditions = ['is_delete' => '1'];
        $listArray = ['id','name','status'];

        $priceData = DB::table('price_grid')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $priceData;
    }

/**
* Delete Price           
* @access public priceDelete
* @param  int $id
* @return array $result
*/ 

    public function priceDelete($id)
    {
        if(!empty($id))
        {
            $result = DB::table('price_grid')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }


/**
* Price Detail           
* @access public priceDetail
* @param  int $priceId
* @return array $combine_array
*/  

    public function priceDetail($priceId) {

        $whereStaffConditions = ['id' => $priceId];
        $priceData = DB::table('price_grid')->where($whereStaffConditions)->get();
        $combine_array['price'] = $priceData;
        return $combine_array;
    }

/**
* Price Add          
* @access public priceAdd
* @param  array $data
* @return array $result
*/

    public function priceAdd($data) {
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('price_grid')->insert($data);
        return $result;
    }

/**
* Price Edit          
* @access public priceEdit
* @param  array $data
* @return array $result
*/
    public function priceEdit($data) {

        $data['updated_date'] = date("Y-m-d H:i:s");
        $whereConditions = ['id' => $data['id']];
        $result = DB::table('price_grid')->where($whereConditions)->update($data);
        return $result;
    }


}
