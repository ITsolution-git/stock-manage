<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use App\Category;
use App\Order;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\FinishingQueue;
use DB;

use Request;

class FinishingQueueController extends Controller { 

    public function __construct(FinishingQueue $finishingQueue, Category $category, Common $common, Order $order)
    {
        parent::__construct();
        $this->finishingQueue = $finishingQueue;
        $this->category = $category;
        $this->common = $common;
        $this->order = $order;
    }

  
/** 
 * @SWG\Definition(
 *      definition="finishingQueueList",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/finishingQueue/listFinishing",
 *  summary = "FinishingQueue Listing",
 *  tags={"FinishingQueue"},
 *  description = "FinishingQueue Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="FinishingQueue Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/finishingQueueList")
 *  ),
 *  @SWG\Response(response=200, description="FinishingQueue Listing"),
 *  @SWG\Response(response="default", description="FinishingQueue Listing"),
 * )
 */
    public function listFinishingQueue()
    {
        //$post = Input::all();
        //$data = $this->finishingQueue->getFinishingdata($post);

        $post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];
        $post['type'] = $post_all['cond']['type'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'o.display_number';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'o.display_number';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->finishingQueue->getFinishingdata($post);

        /*foreach ($result['allData'] as $data) {
            $inner_data = $this->finishingQueue->getFinishingByOrder($data->order_id);

            foreach ($inner_data as $row) {
                if($row->start_time != '00:00:00') {
                    //$row->start_time = date('h:i A', strtotime($row->start_time));
                    $start_time = explode(":", $row->start_time);
                    $ampm = $start_time[0] >= 12 ? 'PM' : 'AM';
                    $row->start_time = $row->start_time ." ". $ampm;
                }
                else {
                    $row->start_time = '';   
                }
                if($row->end_time != '00:00:00') {
                    //$row->end_time = date('h:i A', strtotime($row->end_time));
                    $end_time = explode(":", $row->end_time);
                    $ampm = $end_time[0] >= 12 ? 'PM' : 'AM';
                    $row->end_time = $row->end_time ." ". $ampm;
                }
                else {
                    $row->end_time = '';
                }
                if($row->est == '00:00:00') {
                    $row->est = '';
                }
            }

            $data->order_finishing = $inner_data;
        }*/

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'o.name', 'name' => 'Order Name'),
                        1=>array('key' => 'c.client_company', 'name' => 'Client'),
                        2=>array('key' => 'fc.item', 'name' => 'Finishing Type'),
                        3=>array('key' => 'o.due_date', 'name' => 'Due Date'),
                        4=>array('key' => 'o.in_hands_by', 'name' => 'In Hands Date'),
                        5=>array('key' => '', 'name' => '', 'sortable' => false)
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success'=>$success);
        return response()->json($data);
    }

    public function GetShiftMachine()
    {
        $post = Input::all();
        if(!empty($post['company_id']) && !empty($post['finishing_id']))
        {
            $Position_scheduleData = $this->common->GetTableRecords('finishing_schedule',array('finishing_id'=>$post['finishing_id']));  // ADD FINISHING SCHECULE ENTRY IF NOT AND GET THAT RECORD
            if(count($Position_scheduleData)==0)
            {   
                $this->common->InsertRecords('finishing_schedule',array('finishing_id'=>$post['finishing_id'],'is_active'=>1));
                $Position_scheduleData = $this->common->GetTableRecords('finishing_schedule',array('finishing_id'=>$post['finishing_id']));
            }

            $Position_scheduleData[0]->run_date = ($Position_scheduleData[0]->run_date=='0000-00-00')?'':date('m/d/Y',strtotime($Position_scheduleData[0]->run_date));


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

    public function FinishingBoardData()
    {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die();
        if(!empty($post['company_id']))
        {
            $run_date = (!empty($post['run_date']))? date('Y-m-d',strtotime($post['run_date'])):date('Y-m-d');
            $FinishingBoardData = $this->finishingQueue->FinishingBoardData($post['company_id'],$run_date);

            $prev_date = date('Y-m-d', strtotime('-1 day', strtotime($run_date)));
            $next_date = date('Y-m-d', strtotime('+1 day', strtotime($run_date)));
            $current_date = date('F j, Y',strtotime($run_date));
            if(count($FinishingBoardData)>0)
            {
                $success=1;
                $message =GET_RECORDS;
            }
            else
            {
                $success=2;
                $message =NO_RECORDS;
            }
            $data = array("success"=>$success,"message"=>$message,'FinishingBoardData'=>$FinishingBoardData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);

    }
    
    public function FinishingBoardweekData()
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

            $FinishingBoardweekData = $this->finishingQueue->FinishingBoardweekData($post['company_id'],$week_start,$week_end);
            $prev_date = date('Y-m-d', strtotime('-2 day', strtotime($week_start)));
            $next_date = date('Y-m-d', strtotime('+2 day', strtotime($week_end)));
            $current_date = $month." ".$start_day."-".$end_day.", ".$year;

            if(count($FinishingBoardweekData)>0)
            {
                $success=1;
                $message =GET_RECORDS;
            }
            else
            {
                $success=2;
                $message =NO_RECORDS;
            }
            $data = array("success"=>$success,"message"=>$message,'FinishingBoardweekData'=>$FinishingBoardweekData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);

    }
    public function FinishingBoardMachineData()
    {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die();
        if(!empty($post['company_id']))
        {
            $machine_id = (!empty($post['machine_id']))?$post['machine_id']:'';
            $run_date = (!empty($post['run_date']))? date('Y-m-d',strtotime($post['run_date'])):date('Y-m-d');
            $FinishingBoardMachineData = $this->finishingQueue->FinishingBoardMachineData($post['company_id'],$run_date,$machine_id);

            $prev_date = date('Y-m-d', strtotime('-1 day', strtotime($run_date)));
            $next_date = date('Y-m-d', strtotime('+1 day', strtotime($run_date)));
            $current_date = date('F j, Y',strtotime($run_date));
            if(count($FinishingBoardMachineData)>0)
            {
                $success=1;
                $message =GET_RECORDS;
            }
            else
            {
                $success=2;
                $message =NO_RECORDS;
            }
            $data = array("success"=>$success,"message"=>$message,'FinishingBoardMachineData'=>$FinishingBoardMachineData,'prev_date'=>$prev_date,'next_date'=>$next_date,'current_date'=>$current_date);
        }
        else
        {
            $data = array("success"=>0,"message"=>MISSING_PARAMS);
        }
        return response()->json(['data'=>$data]);
    }
}