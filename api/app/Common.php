<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Common extends Model {

    /**
     * login verify function
     *
     *
     */
    public function getAdminRoles() {
        $roles = DB::table('roles')->get();
        return $roles;
    }

    /**
     * Type listing
     */
    public function TypeList($type) {
        $typeData = DB::table('type')->where('status','=','1')->where('type','=',$type)->get();
        return $typeData;
    }

     /**
     * Get staff roles
     * input params [7,8] 7 = SuperAdmin,8=Facility Manager
     * all staff related roles
     */
    public function getStaffRoles() {
        
        $staffRoles = DB::table('roles')->whereNotIn('id', [7,8])->get();

        return $staffRoles;
    }


}
