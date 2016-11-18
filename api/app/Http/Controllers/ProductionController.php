<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Production;
use App\Common;
use DB;
use Request;

class ProductionController extends Controller { 

	public function __construct(Production $production,Common $common) 
 	{
 		parent::__construct();
        $this->production = $production;
        $this->common = $common;
    }
    public function GetShiftMachine()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']))
	    {
	    	$machine_data = $this->common->GetTableRecords('machine',array('company_id'=>$post['company_id'],'is_delete'=>1,'operation_status'=>0));
	    	$shift_data   = $this->common->GetTableRecords('company_info',array('user_id'=>$post['company_id']));
	    	$data = array("success"=>1,"message"=>GET_RECORDS,"machine_data"=>$machine_data,'shift_data'=>$shift_data);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }

        return response()->json(['data'=>$data]);
    }

}