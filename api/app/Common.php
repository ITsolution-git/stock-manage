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
    public function checkemailExist($email)
    {
    	$data = DB::table('users')->where('email','=',$email)->get();
        return $data;
    }


}
