<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Staff extends Model {

    /**
     * Staff listing
     */
    public function StaffList() {
        $staffData = DB::table('staff')->where('status','=','1')->get();
        return $staffData;
    }

     /**
     * Add Staff
     */
    public function StaffAdd($data) {
    	$data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        

        $result = DB::table('staff')->insert($data);
        return $result;
    }


    /**
     * Edit Staff
     */
    public function StaffEdit($data) {
        
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('staff')->where('id', '=', $data['id'])->update($data);
        return $result;
    }





    

    /**
     * Type listing
     */
    public function TypeList($type) {
        $typeData = DB::table('type')->where('status','=','1')->where('type','=',$type)->get();
        return $typeData;
    }

    /**
     * Staff Detail
     */
    public function staffDetail($staffId) {
        $staffData = DB::table('staff')->where('status','=','1')->where('id','=',$staffId)->get();

        return $staffData;
    }


}
