<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Login extends Model {

    
     /**
    * Login Verify function           
    * @access public verifylogin
    * @param  string $username
    * @param  string $password
    * @return array $admindata
    */
    public function verifylogin($username, $password) {
        $admindata = DB::table('users as usr')
                    ->select('usr.id','usr.email','usr.status','usr.name','r.title','r.slug')
        			 ->leftjoin('roles as r','r.id', '=' ,'usr.role_id')
        			 ->where('usr.user_name', '=', $username)
        			 ->where('usr.password', '=', md5($password))
        			 ->where('usr.is_delete','=','1')
        			 ->get();
        return $admindata;
    }

    /**
    * Login time with userId          
    * @access public user_id
    * @return Last Inserted Id
    */
    public function loginRecord($user_id)
    {
        $result = DB::table('login_record')->insert(['user_id'=>$user_id,'login_time'=>date('Y-m-d H:i:s')]);
        $insertedid = DB::getPdo()->lastInsertId();

        return $insertedid;
    }

    /**
    * Update login record Update logout timeStamp           
    * @access public loginRecordUpdate
    * @param  string $loginid
    */
    public function loginRecordUpdate($loginid)
    {
        $result = DB::table('login_record')->where('login_id','=',$loginid)->update(array('logout_time'=>date('Y-m-d H:i:s')));
    }
}
