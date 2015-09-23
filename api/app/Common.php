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

/**
* Get All Misc type
* @access public getAllMiscData
* @return array $Misc
*/

    public function getAllMiscData() {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1'];
        $MiscData = DB::table('misc_type')->where($whereMiscConditions)->get();

        $allData = array ();
        foreach($MiscData as $data) {
           
           if($data->value == ''){
            $data->value = '-'; 
            $allData[$data->type][] = $data;
           } else {
            $allData[$data->type][] = $data;
           }
        }
        return $allData;
    }

    public function GetMicType($type)
    {
        $whereVendorConditions = ['status' => '1','is_delete' => '1','type'=>$type];
        $misc_type = DB::table('misc_type')->where($whereVendorConditions)->where('value','!=','')->get();
        return $misc_type;
    }
    public function getStaffList()
    {
        $whereConditions = ['is_delete' => '1'];
        $stafflist = DB::table('staff')->select('id','first_name','last_name')->where($whereConditions)->get();
        return $stafflist;

    }
    public function InsertRecords($table,$records)
    {
        $result = DB::table($table)->insert($records);
        return $result;
    }
    public function GetTableRecords($table,$cond)
    {
        $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->get();
        return $result;
    }
    public function UpdateTableRecords($table,$cond,$data)
    {
         $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->update($data);
        return $result;
    }
}
