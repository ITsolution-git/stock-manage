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

    public function staffList($post) {
        
        $whereConditions = ['users.is_delete' => '1','users.parent_id' => $post['cond']['company_id']];
        $listArray = ['staff.user_id','staff.id','staff.first_name','users.name','users.email','staff.last_name','staff.prime_phone_main','staff.date_start','users.status','roles.title'];

        $staffData = DB::table('users as users')
                         ->LeftJoin('staff as staff', 'users.id', '=', 'staff.user_id')
                         ->Join('roles as roles', 'users.role_id', '=', 'roles.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->where('roles.slug',"<>",'SA')
                         ->where('roles.slug',"<>",'CA')
                         ->orderBy('staff.id', 'desc')
                         ->get();

        return $staffData;
    }


/**
* Staff Edit data           
* @access public staffEdit
* @param  array $data
* @return array $result
*/  
    public function staffEdit($data) {
        unset($data['all_url_photo']);
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

    public function staffDetail($data) {

        $whereStaffConditions = ['id' => $data['id']];
        $staffData = DB::table('staff')->where($whereStaffConditions)->get();

        $whereConditions = ['status' => '1','id' => $staffData[0]->user_id];
        $listArray = ['user_name','email','password','role_id'];
       
        $userData = DB::table('users')->select($listArray)->where($whereConditions)->get();

        $combine_array = array();

        $combine_array['staff'] = $staffData;
        $combine_array['users'] = $userData;
       

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
            $result = DB::table('staff')->where('id','=',$id)->update(array("is_delete" => '0'));
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

        $whereConditions = ['type_note' => 'staff','id' => $data['note_id'],'all_id' => $data['staff_id']];
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
        $whereConditions = ['id' => $data['timeoff_id'],'staff_id' => $data['staff_id']];
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


/**
* Staff Notes Edit data           
* @access public noteEdit
* @param  array $data
* @return array $result
*/  

public function staffNotesEdit($staff_notes,$staffId) {
    
    $whereConditions = ['all_id' => $staffId,'type_note' => 'staff'];
    DB::table('notes')->where($whereConditions)->delete();

     $staff_notes_array = json_decode(json_encode($staff_notes), true);
     
           foreach($staff_notes_array as $key => $link) 
              { 

                $staff_notes_array[$key]['updated_date']  = date("Y-m-d H:i:s");
                $staff_notes_array[$key]['type_note'] = 'staff';
                $staff_notes_array[$key]['all_id'] = $staffId;
                $result_notes = DB::table('notes')->insert($staff_notes_array[$key]);

              }
        return  $staffId;
    }

/**
* Staff Time Off Edit data           
* @access public staffTimeOffEdit
* @param  array $data
* @return array $result
*/  

public function staffTimeOffEdit($timeoff_notes,$staffId) {
    
    $whereConditions = ['staff_id' => $staffId];
    DB::table('time_off')->where($whereConditions)->delete();

     $timeoff_notes_array = json_decode(json_encode($timeoff_notes), true);
     
           foreach($timeoff_notes_array as $key => $link) 
              { 
                unset($timeoff_notes_array[$key]['name']);
                unset($timeoff_notes_array[$key]['classification']);


                $timeoff_notes_array[$key]['updated_date']  = date("Y-m-d H:i:s");

                $timeoff_notes_array[$key]['date_begin']  = date("Y-m-d", strtotime($timeoff_notes_array[$key]['date_begin']));
                $timeoff_notes_array[$key]['date_end']  = date("Y-m-d", strtotime($timeoff_notes_array[$key]['date_end']));



                $timeoff_notes_array[$key]['staff_id'] = $staffId;
                $result_notes = DB::table('time_off')->insert($timeoff_notes_array[$key]);

              }
        return  $staffId;
    }


    /**
* Staff Note Timeoff         
* @access public staffNoteTimeoff
* @param  int $staffId
* @return array $combine_array
*/  

    public function staffNoteTimeoff($staffId) {

        $whereNotesConditions = ['status' => '1','is_delete' => '1','type_note' => 'staff','all_id' => $staffId];
        $listNotesArray = ['note','points','id'];
        $notesData = DB::table('notes')->select($listNotesArray)->where($whereNotesConditions)->get();


        $whereTimeoffConditions = ['staff_id' => $staffId,'time_off.is_delete' => '1','time_off.status' => '1'];
        $listArrayTimeoff = ['time_off.classification_id','time_off.id','time_off.staff_id','time_off.timerecord','time_off.applied_hours','time_off.date_begin',
                      'time_off.date_end', 'time_off.status','type.name'];

         $timeoffData = DB::table('time_off as time_off')
                         ->leftJoin('type as type','type.id','=',DB::raw("time_off.classification_id AND type.status = '1' AND type.type = 'timeoff' "))
                         ->select($listArrayTimeoff)
                         ->where($whereTimeoffConditions)
                         ->get();

        $combine_array = array();
        $combine_array['allnotes'] = $notesData;
        $combine_array['allTimeOff'] = $timeoffData;

        return $combine_array;
    }


}
