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
        

        $staffData = DB::table('staff as staff')
                         ->Join('users as users', 'users.id', '=', 'staff.user_id')
                         ->Join('roles as roles', 'users.role_id', '=', 'roles.id')
                         ->select('staff.user_id','staff.id','staff.first_name','staff.last_name','staff.prime_phone_main','staff.date_start','staff.status','roles.title')
                         ->where('users.is_delete','=','1')
                         ->where('staff.is_delete','=','1')
                         ->get();

        return $staffData;
    }

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
     * Edit Staff
     */
    public function staffEdit($data) {
        

        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('staff')->where('id', '=', $data['id'])->update($data);
        return $result;
    }




    /**
     * Staff Detail
     */
    public function staffDetail($staffId) {
        $staffData = DB::table('staff')->where('status','=','1')->where('id','=',$staffId)->get();
       
        $UserData = DB::table('users')->select('user_name','email','password','role_id')->where('status','=','1')->where('id','=',$staffData[0]->user_id)->get();

        $combine_array = array();

        $combine_array['staff'] = $staffData;
        $combine_array['users'] = $UserData;

        return $combine_array;
    }

     /**
     * Edit Staff
     */
    public function userEdit($user) {
        
        $user['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('users')->where('id', '=', $user['id'])->update($user);
        return $result;
    }

     /**
     * Delete Staff
     */

    public function staffDelete($id,$user_id)
    {
        if(!empty($id))
        {
            $result = DB::table('users')->where('id','=',$user_id)->update(array("is_delete" => '0'));
          //  $result = DB::table('staff')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

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
     * Staff Detail
     */
    public function noteList($staffId) {
        $noteData = DB::table('notes')->where('status','=','1')->where('type_note','=','staff')->where('all_id','=',$staffId)->get();
        return $noteData;
    }

     /**
     * Delete Staff
     */

    public function noteDelete($id,$staff_id)
    {
        if(!empty($id))
        {
            $result = DB::table('notes')->where('id','=',$id)->update(array("is_delete" => '0'));
          //  $result = DB::table('staff')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }


    /**
     * Add Note
     */
    public function noteAdd($data) {
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('notes')->insert($data);
        return $result;
    }

     /**
     * Note Detail
     */
    public function noteDetail($data) {
       
       
        $noteData = DB::table('notes')->select('note','id','all_id','points')->where('status','=','1')->where('type_note','=','staff')->where('id','=',$data['note_id'])->where('all_id','=',$data['staff_id'])->get();
        return  $noteData;
    }

    /**
     * Edit Staff
     */
    public function noteEdit($data) {
        
        
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('notes')->where('id', '=', $data['id'])->where('all_id', '=', $data['all_id'])->where('type_note', '=','staff')->update($data);
        return $result;
    }


}
