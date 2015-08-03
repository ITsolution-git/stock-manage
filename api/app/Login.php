<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Login extends Model {

    /**
     * login verify function
     *
     *
     */
    public function verifylogin($username, $password) {
        $admindata = DB::table('users')->where('user_name', '=', $username)->where('password', '=', md5($password))->get();
        return $admindata;
    }


}
