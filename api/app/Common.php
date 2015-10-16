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


    /**
* Get All Misc type
* @access public getAllMiscDataWithoutBlank
* @return array $Misc
*/

    public function getAllMiscDataWithoutBlank() {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1'];
        $MiscData = DB::table('misc_type')->where($whereMiscConditions)->get();

        $allData = array ();
        foreach($MiscData as $data) {
           
           if($data->value != ''){
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


        $whereConditions = ['misc.status' => '1','misc.is_delete' => '1','staff.is_delete' => '1','misc.type' => 'staff_type'];
        $listArray = ['staff.id','staff.first_name','staff.last_name'];

        $staffData = DB::table('staff as staff')
                         ->Join('misc_type as misc', 'staff.staff_type', '=', 'misc.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $staffData;
    }


    public function getBrandCordinator()
    {
       


        $whereConditions = ['users.status' => '1','users.is_delete' => '1','roles.slug' => 'BC'];
        $listArray = ['users.id','users.name'];

        $brandCordinatorData = DB::table('users as users')
                         ->Join('roles as roles', 'users.role_id', '=', 'roles.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $brandCordinatorData;
    }


    public function InsertRecords($table,$records)
    {
        $result = DB::table($table)->insert($records);

        $id = DB::getPdo()->lastInsertId();

        return $id;
    }
    public function GetTableRecords($table,$cond,$notcond)
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

        if(count($notcond)>0)
        {
            foreach ($notcond as $key => $value) 
            {
                
                    $result =$result ->where($key,'!=',$value);
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

     public function DeleteTableRecords($table,$cond)
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
        $result=$result->delete();
        return $result;
    }

 /**
* Get All Placement data
* @access public getAllPlacementData
* @return array $Misc
*/

    public function getAllPlacementData() {
        

        $listArray = ['placement.misc_id','placement.misc_value','placement.id','misc_type.value as position'];

        $wherePlacementConditions = ['placement.status' => '1','placement.is_delete' => '1'];
        $placementData = DB::table('placement')
        ->leftJoin('misc_type as misc_type', 'placement.misc_id', '=', 'misc_type.id')
        ->select($listArray)
        ->where($wherePlacementConditions)->get();          
        return $placementData;
    }

      /**
* Get All Misc type
* @access public getMiscData
* @return array $Misc
*/

    public function getMiscData() {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1','type'=>'position'];
        $miscData = DB::table('misc_type')->select('id','value')->where($whereMiscConditions)->where('value','!=','')->get();       
        return $miscData;
    }

}
