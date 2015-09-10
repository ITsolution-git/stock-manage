<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Misc extends Model {


/**
* Misc value 1 listing array           
* @access public value1List
* @return array $priceData
*/

    public function value1List() {
        
        $whereConditions = ['is_delete' => '1'];
        $listArray = ['id','approval','misc_art_type_id','misc_boxing_type','misc_charge_apply','misc_color_group','misc_direct_garment','misc_Direct_garment_sz','misc_disposition','misc_graphic_size','misc_level','misc_yesno','misc_po_status','misc_address_type'];

        $value1Data = DB::table('misc_value1')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $value1Data;
    }

}
