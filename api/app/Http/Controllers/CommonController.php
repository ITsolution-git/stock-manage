<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use DB;

use Request;
// Common Controller for default data
class CommonController extends Controller {  


	public function __construct(Common $common) 
 	{
        $this->common = $common;

    }

	public function getAdminRoles()
	{
		$listRoels = $this->common->getAdminRoles();

		$success = count($listRoels);
		$message  = ($success>0)? 'Get Records.':NO_RECORDS;

		$data = array("records" => $listRoels,"success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);

	}


}