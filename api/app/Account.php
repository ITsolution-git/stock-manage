<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Account extends Model {

    /**
     * login verify function
     *
     *
     */
    public function GetCompanyData() {
        $admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
        				 ->select('usr.name','usr.user_name','usr.email','usr.remember_token','usr.status','rol.title')
        				 ->get();
        return $admindata;
    }
    public function InsertCompanyData($post)
    {
    	$result = DB::table('users')->insert($post);
    	return $result;
    }


}
