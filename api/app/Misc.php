<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Misc extends Model {

/**
* Misc Save          
* @access public miscSave
* @param  array $data
* @return array $result
*/
    public function miscSave($data) {
        
        $whereConditions = ['id' => $data['id']];
        $result = DB::table('misc_type')->where($whereConditions)->update($data);
        return $result;
    }


}
