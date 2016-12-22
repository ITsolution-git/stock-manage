<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Production;
use App\Common;
use App\Client;
use DB;
use Request;

class ProductionController extends Controller { 

	public function __construct(Production $production,Common $common, Client $client) 
 	{
 		parent::__construct();
        $this->production = $production;
        $this->common = $common;
        $this->client = $client;
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
	    	$Position_scheduleData[0]->rush_job = ($Position_scheduleData[0]->rush_job=='1')?true:false;


	    	$machine_data = $this->common->GetTableRecords('machine',array('company_id'=>$post['company_id'],'is_delete'=>1,'operation_status'=>0));  // GET MACHINE FROM COMPANU
	    	$shift_data   = $this->common->GetTableRecords('company_shift',array('company_id'=>$post['company_id'],'is_delete'=>1)); // GET COMPANY SHIFT
	    	

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

    public function GetFilterData()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']))
	    {
	    	$filter=array('cond');
	    	$filter['cond']['company_id'] = $post['company_id'];
	    	$clients = $this->client->getClientFilterData($filter);  // GET CLIENT FROM COMPANy
	    	$production_type   = $this->common->GetTableRecords('misc_type',array('company_id'=>$post['company_id'],'type'=>'placement_type','is_delete'=>1),array('value'=>'')); // GET COMPANY PRODUCTION TYPE
	    	foreach ($production_type as $key => $value) {
	    		$value->label=$value->value;
	    	}
			$data = array("success"=>1,"message"=>GET_RECORDS,"clients"=>$clients,'production_type'=>$production_type);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }
	    return response()->json(['data'=>$data]);
    }

    public function SchedualBoardData()
    {
    	$post = Input::all();
    	//echo "<pre>"; print_r($post); echo "</pre>"; die();
    	if(!empty($post['company_id']))
	    {
	    	$run_date = (!empty($post['run_date']))? date('Y-m-d',strtotime($post['run_date'])):date('Y-m-d');
	    	$SchedualBoardData = $this->production->SchedualBoardData($post['company_id'],$run_date);

	    	$prev_date = date('Y-m-d', strtotime('-1 day', strtotime($run_date)));
	    	$next_date = date('Y-m-d', strtotime('+1 day', strtotime($run_date)));
	    	$current_date = date('F j, Y',strtotime($run_date));
	    	if(count($SchedualBoardData)>0)
	    	{
	    		$success=1;
	    		$message =GET_RECORDS;
	    	}
	    	else
	    	{
				$success=2;
	    		$message =NO_RECORDS;
	    	}
	    	$data = array("success"=>$success,"message"=>$message,'SchedualBoardData'=>$SchedualBoardData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }
	    return response()->json(['data'=>$data]);

    }
    
    public function SchedualBoardweekData()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']))
	    {
	    	$run_date = (!empty($post['run_date']))? date('Y-m-d',strtotime($post['run_date'])):date('Y-m-d');
	    	
	    	$week_start = date('Y-m-d',strtotime("monday this week",strtotime($run_date)));
	    	$week_end = date('Y-m-d',strtotime("sunday this week",strtotime($run_date)));
	    	
	    	$start_day = date('d',strtotime($week_start));
	    	$end_day = date('d',strtotime($week_end));
	    	$month = date('F',strtotime($run_date));
	    	$year = date('Y',strtotime($run_date));

	    	$SchedualBoardweekData = $this->production->SchedualBoardweekData($post['company_id'],$week_start,$week_end);
	    	$prev_date = date('Y-m-d', strtotime('-2 day', strtotime($week_start)));
	    	$next_date = date('Y-m-d', strtotime('+2 day', strtotime($week_end)));
	    	$current_date = $month." ".$start_day."-".$end_day.", ".$year;

	    	if(count($SchedualBoardweekData)>0)
	    	{
	    		$success=1;
	    		$message =GET_RECORDS;
	    	}
	    	else
	    	{
				$success=2;
	    		$message =NO_RECORDS;
	    	}
	    	$data = array("success"=>$success,"message"=>$message,'SchedualBoardweekData'=>$SchedualBoardweekData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }
	    return response()->json(['data'=>$data]);

    }
    public function SchedualBoardMachineData()
    {
    	$post = Input::all();
    	//echo "<pre>"; print_r($post); echo "</pre>"; die();
    	if(!empty($post['company_id']))
	    {
	    	$machine_id = (!empty($post['machine_id']))?$post['machine_id']:'';
	    	$run_date = (!empty($post['run_date']))? date('Y-m-d',strtotime($post['run_date'])):date('Y-m-d');
	    	$SchedualBoardMachineData = $this->production->SchedualBoardMachineData($post['company_id'],$run_date,$machine_id);

	    	$prev_date = date('Y-m-d', strtotime('-1 day', strtotime($run_date)));
	    	$next_date = date('Y-m-d', strtotime('+1 day', strtotime($run_date)));
	    	$current_date = date('F j, Y',strtotime($run_date));
	    	if(count($SchedualBoardMachineData)>0)
	    	{
	    		$success=1;
	    		$message =GET_RECORDS;
	    	}
	    	else
	    	{
				$success=2;
	    		$message =NO_RECORDS;
	    	}
	    	$data = array("success"=>$success,"message"=>$message,'SchedualBoardMachineData'=>$SchedualBoardMachineData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
	    }
	    else
	    {
	    	$data = array("success"=>0,"message"=>MISSING_PARAMS);
	    }
	    return response()->json(['data'=>$data]);
    }

    public function GetSchedulePositionDetail() // POPUP OF POSITION
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

    public function SaveSchedulePosition()
    {
    	$post = Input::all();
    	if(!empty($post['company_id']) && !empty($post['id']))
	    {
	    	//$post['rush_job']
	    	$post['run_date'] = date('Y-m-d',strtotime($post['run_date']));
	    	$machine_data = $this->common->UpdateTableRecords('position_schedule',array('id'=>$post['id']),array('machine_id'=>$post['machine_id'],'shift_id'=>$post['shift_id'],'run_date'=>$post['run_date'],'rush_job'=>$post['rush_job']));  
	    	$data = array("success"=>'success',"message"=>UPDATE_RECORD);
	    }
	    else
	    {
	    	$data = array("success"=>'error',"message"=>MISSING_PARAMS);
	    }

        return response()->json(['data'=>$data]);
    }
    public function GetRuntimeData($position_id)
    {
    	$per_screen = 0.167; // 10 MINUTES
    	$getRunspeed = $this->production->getRunspeed($position_id);
    	$getOrderImpression = $this->production->getOrderImpression($position_id);
    	$getPositioncolors = $this->production->getPositioncolors($position_id);
    	$iph = $this->common->GetTableRecords('position_schedule',array('position_id'=>$post['position_id']));

    	echo "<br> Runspeed at ->".$getRunspeed*100;
    	echo "<br> Order Size  ->".$getOrderImpression;
    	echo "<br> Per screen  ->".$per_screen;
    	echo "<br> Colors      ->".$getPositioncolors;

    	//$getIPH = $this->production->getIPH($position_id);
    }

}