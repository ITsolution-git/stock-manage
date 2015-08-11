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


}
