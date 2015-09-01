<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Common extends Model {

/**
* Get admin roles controller      
* @access public getAdminRoles
* @return array $roles
*/
    public function getAdminRoles() {
        $roles = DB::table('roles')->where('slug','<>','SA')->get();
        return $roles;
    }
    public function checkemailExist($email)
    {
    	$data = DB::table('users')->where('email','=',$email)->get();
        return $data;
    }

/**
* Get type list controller      
* @access public TypeList
* @param  int $type
* @return array $typeData
*/

    public function TypeList($type) {
        $typeData = DB::table('type')->where('status','=','1')->where('type','=',$type)->get();
        return $typeData;
    }

/**
* Get staff roles,input params [7,8] 7 = SuperAdmin,8=Facility Manager
* @access public getStaffRoles
* @return array $staffRoles
*/

    public function getStaffRoles() {
        
        $staffRoles = DB::table('roles')->whereNotIn('id', [7,8])->get();

        return $staffRoles;
    }

/**
* Get All Vendors
* @access public getAllVendors
* @return array $staffRoles
*/

    public function getAllVendors() {
        
        $whereVendorConditions = ['status' => '1','is_delete' => '1'];
        $vendorData = DB::table('vendors')->where($whereVendorConditions)->get();
        return $vendorData;
    }
}
