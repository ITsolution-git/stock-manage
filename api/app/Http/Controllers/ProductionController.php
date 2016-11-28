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

    // GET MACHINE, SHIFT AND POSITION SCHEDULE DATA. POSITION SCHEDULE POPUP.
    public function GetShiftMachine()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']) && !empty($post['position_id']))
	    {
	    	$Position_scheduleData = $this->common->GetTableRecords('position_schedule',array('position_id'=>$post['position_id']));  // ADD POSITION SCHECULE ENTRY IF NOT AND GET THAT RECORD
	    	if(count($Position_scheduleData)==0)
	    	{	
	    		$this->common->InsertRecords('position_schedule',array('position_id'=>$post['position_id'],'is_active'=>1));
	    		$Position_scheduleData = $this->common->GetTableRecords('position_schedule',array('position_id'=>$post['position_id']));
	    	}

	    	$Position_scheduleData[0]->run_date = ($Position_scheduleData[0]->run_date=='0000-00-00')?'':date('m/d/Y',strtotime($Position_scheduleData[0]->run_date));


	    	$machine_data = $this->common->GetTableRecords('machine',array('company_id'=>$post['company_id'],'is_delete'=>1,'operation_status'=>0));  // GET MACHINE FROM COMPANU
	    	$shift_data   = $this->common->GetTableRecords('company_shift',array('company_id'=>$post['company_id'])); // GET COMPANY SHIFT
	    	

	    	$data = array("success"=>1,"message"=>GET_RECORDS,"machine_data"=>$machine_data,'shift_data'=>$shift_data,'Position_scheduleData'=>$Position_scheduleData);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }

        return response()->json(['data'=>$data]);
    }
    public function GetPositionDetails()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']) && !empty($post['position_id']))
	    {
	    	$PositionDetail= $this->production->GetPositionDetails($post['position_id'],$post['company_id']);
	    	$GarmentDetail= $this->production->GetGarmentDetail($post['position_id'],$post['company_id']);
			$data = array("success"=>1,"message"=>GET_RECORDS,"PositionDetail"=>$PositionDetail,'GarmentDetail'=>$GarmentDetail);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }

        return response()->json(['data'=>$data]);
    }

}