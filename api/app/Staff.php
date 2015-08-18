<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Staff extends Model {


/**
* Staff listing array           
* @access public staffList
* @return array $staffData
*/

    public function staffList() {
        
        $whereConditions = ['users.is_delete' => '1','staff.is_delete' => '1'];
        $listArray = ['staff.user_id','staff.id','staff.first_name','staff.last_name','staff.prime_phone_main','staff.date_start','staff.status','roles.title'];

        $staffData = DB::table('staff as staff')
                         ->Join('users as users', 'users.id', '=', 'staff.user_id')
                         ->Join('roles as roles', 'users.role_id', '=', 'roles.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $staffData;
    }

/**
* Staff Add data           
* @access public staffAdd
* @param  array $data
* @return int $staffid
*/  

     /**
     * Add Staff
     */
    public function staffAdd($data) {
    	$data['staff']['created_date'] = date("Y-m-d H:i:s");
        $data['staff']['updated_date'] = date("Y-m-d H:i:s");
        $data['users']['updated_date'] = date("Y-m-d H:i:s");
        $data['users']['updated_date'] = date("Y-m-d H:i:s");
        
        $result = DB::table('users')->insert($data['users']);
       
        $insertedid = DB::getPdo()->lastInsertId();
        
        $data['staff']['user_id'] = $insertedid;
        $result_staff = DB::table('staff')->insert($data['staff']);

         $staffid = DB::getPdo()->lastInsertId();

        return $staffid;
    }


/**
* Staff Edit data           
* @access public staffEdit
* @param  array $data
* @return array $result
*/  
    public function staffEdit($data) {

        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('staff')->where('id', '=', $data['id'])->update($data);
        return $result;
    }


/**
* Staff Detail           
* @access public staffDetail
* @param  int $staffId
* @return array $combine_array
*/  

    public function staffDetail($staffId) {

        $whereStaffConditions = ['status' => '1','id' => $staffId];
        $staffData = DB::table('staff')->where($whereStaffConditions)->get();

        $whereConditions = ['status' => '1','id' => $staffData[0]->user_id];
        $listArray = ['user_name','email','password','role_id'];
       
        $UserData = DB::table('users')->select($listArray)->where($whereConditions)->get();

        $combine_array = array();

        $combine_array['staff'] = $staffData;
        $combine_array['users'] = $UserData;

        return $combine_array;
    }

/**
* Edit User           
* @access public userEdit
* @param  array $user
* @return array $result
*/ 

    public function userEdit($user) {
        
        $user['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('users')->where('id', '=', $user['id'])->update($user);
        return $result;
    }

/**
* Delete Staff           
* @access public staffDelete
* @param  int $id
* @param  int $user_id
* @return array $result
*/ 

    public function staffDelete($id,$user_id)
    {
        if(!empty($id))
        {
            $result = DB::table('users')->where('id','=',$user_id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Staff Image Upload          
* @access public staffImageUpdate
* @param  int $insertedid
* @param  array $newfilename
* @return array $result
*/ 

     public function staffImageUpdate($insertedid,$newfilename)
    {
        if(!empty($insertedid))
        {
            
           $result =  DB::table('staff')
                        ->where('id', $insertedid)
                        ->update(['photo' => $newfilename]);
           return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Staff Related Notes          
* @access public noteList
* @param  int $staffId
* @return array $noteData
*/ 

    public function noteList($staffId) {

        $whereConditions = ['status' => '1','is_delete' => '1','type_note' => 'staff','all_id' => $staffId];
        $noteData = DB::table('notes')->where($whereConditions)->get();
        return $noteData;
    }

/**
* Note Delete          
* @access public noteDelete
* @param  int $id
* @param  int $staff_id
* @return array $result
*/

    public function noteDelete($id,$staff_id)
    {
        if(!empty($id))
        {
            $result = DB::table('notes')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Note Add          
* @access public noteAdd
* @param  array $data
* @return array $result
*/

    public function noteAdd($data) {
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('notes')->insert($data);
        return $result;
    }

/**
* Note Detail          
* @access public noteDetail
* @param  array $data
* @return array $noteData
*/

    public function noteDetail($data) {

        $whereConditions = ['status' => '1','type_note' => 'staff','id' => $data['note_id'],'all_id' => $data['staff_id']];
        $listArray = ['note','id','all_id','points'];
        $noteData = DB::table('notes')->select($listArray)->where($whereConditions)->get();
        return  $noteData;
    }

/**
* Note Edit          
* @access public noteEdit
* @param  array $data
* @return array $result
*/
    public function noteEdit($data) {

        $data['updated_date'] = date("Y-m-d H:i:s");
        $whereConditions = ['id' => $data['id'],'all_id' => $data['all_id'],'type_note' => 'staff'];
        $result = DB::table('notes')->where($whereConditions)->update($data);
        return $result;
    }

/**
* Staff Related Timeoffs          
* @access public timeoffList
* @param  int $staffId
* @return array $timeoffData
*/ 

    public function timeoffList($staffId) {
       
        $whereConditions = ['staff_id' => $staffId,'time_off.is_delete' => '1','time_off.status' => '1','type.status' => '1','type.type' => 'timeoff'];
        $listArray = ['time_off.classification_id','time_off.id','time_off.staff_id','time_off.timerecord','time_off.applied_hours','time_off.date_begin',
                      'time_off.date_end', 'time_off.status','type.name'];

         $timeoffData = DB::table('time_off as time_off')
                         ->Join('type as type', 'type.id', '=', 'time_off.classification_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $timeoffData;
    }

/**
* Timeoff Delete          
* @access public timeoffDelete
* @param  int $id
* @param  int $staff_id
* @return array $result
*/

    public function timeoffDelete($id,$staff_id)
    {
        if(!empty($id))
        {
            $result = DB::table('time_off')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Timeoff Add          
* @access public timeoffAdd
* @param  array $data
* @return array $result
*/

    public function timeoffAdd($data) {
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('time_off')->insert($data);
        return $result;
    }

/**
* Timeoff Detail          
* @access public timeoffDetail
* @param  array $data
* @return array $timeoffData
*/

    public function timeoffDetail($data) {
        $whereConditions = ['status' => '1','id' => $data['timeoff_id'],'staff_id' => $data['staff_id']];
        $listArray = ['id','staff_id','classification_id','date_begin','date_end','timerecord','applied_hours'];
        $timeoffData = DB::table('time_off')->select($listArray)->where($whereConditions)->get();
        return   $timeoffData;
    }

/**
* Timeoff Edit          
* @access public timeoffEdit
* @param  array $data
* @return array $result
*/

    public function timeoffEdit($data) {
       
        $data['updated_date'] = date("Y-m-d H:i:s");
        $whereConditions = ['id' => $data['id'],'staff_id' => $data['staff_id']];
        $result = DB::table('time_off')->where($whereConditions)->update($data);
        return $result;
    }



}
