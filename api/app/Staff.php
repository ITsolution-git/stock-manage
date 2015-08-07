<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Staff extends Model {

    /**
     * login verify function
     *
     *
     */
    public function StaffList() {
        $staffdata = DB::table('staff')->where('status','=','1')->get();
        return $staffdata;
    }

    public function StaffAdd($data) {
    	$data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        

        $result = DB::table('staff')->insert($data);
        return $result;
    }


}
