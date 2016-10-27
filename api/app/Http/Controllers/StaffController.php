<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Staff;
use App\Common;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class StaffController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Staff $staff,Common $common) {

        parent::__construct();
        $this->staff = $staff;
        $this->common = $common;
       
    }

/**
* Staff Listing controller        
* @access public index
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="staffList",
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
 *  path = "/api/public/admin/staff",
 *  summary = "Staff Listing",
 *  tags={"Staff"},
 *  description = "Staff Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Staff Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/staffList")
 *  ),
 *  @SWG\Response(response=200, description="Staff Listing"),
 *  @SWG\Response(response="default", description="Staff Listing"),
 * )
 */

    public function index() {
         $post = Input::all();
        $result = $this->staff->staffList($post);
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }



/**
* Staff Edit controller      
* @access public edit
* @param  array $data
* @return json data
*/
    public function edit() {
 
         

$data = Input::all();


 $email = $this->common->checkemailExist($data['users']['email'],$data['staff']['user_id']);
            if(count($email)>0)
            {
                $message = "Email Exists";
                $success = 2;
                $data = array("success"=>$success,"message"=>$message,"id"=>0);
                return response()->json(['data'=>$data]);
            }


         $data['users']['id'] = $data['staff']['user_id'];


          if(isset($data['staff']['date_start'])) {
             $data['staff']['date_start'] = date("Y-m-d", strtotime($data['staff']['date_start']));
          }

          if(isset($data['staff']['date_end'])) {
             $data['staff']['date_end'] = date("Y-m-d", strtotime($data['staff']['date_end']));
          }

          if(isset($data['staff']['birthday'])) {
             $data['staff']['birthday'] = date("Y-m-d", strtotime($data['staff']['birthday']));
          }
          
          
          if($data['users']['password'] == ''){
           unset($data['users']['password']);
          }  else {
            $data['users']['password'] = md5($data['users']['password']);
          }
          
          $data['users']['name'] = $data['staff']['first_name'].' '.$data['staff']['last_name'];

          $result = $this->staff->staffEdit($data['staff']);
          $resultUsers = $this->staff->userEdit($data['users']);

          if (count($result) > 0) {

         $response = array('success' => 1, 'message' => UPDATE_RECORD,'records' => $result);
        
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
        }
        

return response()->json(["data" => $response]);
    }

/**
* Making the directory and given path     
* @access public create_dir
* @param  string $dir_path
*/

public function create_dir($dir_path) {

        if (!file_exists($dir_path)) {
            mkdir($dir_path, 0777, true);
        } else {
         exec("chmod $dir_path 0777");
           // chmod($dir_path, 0777);
        }
    }


/**
* Staff Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function detail() {
 
         $data = Input::all();
         

          $result = $this->staff->staffDetail($data);
          

          $result['staff'][0]->all_url_photo = UPLOAD_PATH.$data['company_id'].'/staff/'.$result["staff"][0]->id.'/'.$result['staff'][0]->photo;

       
           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['staff'],'users_records' => $result['users']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['staff'],'users_records' => $result['users']);
        }
        
        return response()->json(["data" => $response]);

    }

/**
* Staff Delete controller      
* @access public detail
* @param  array $post
* @return json data
*/
    public function delete()
    {
        $post = Input::all();
       
        if(!empty($post['staff_id']) && !empty($post['user_id']))
        {
            $getData = $this->staff->staffDelete($post['staff_id'],$post['user_id']);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }


/**
* Notes Listing of particular staff Controller       
* @access public note
* @param  array $data
* @return json data
*/

    public function note() {
        $data = Input::all();
        
        $result = $this->staff->noteList($data[0]);
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }

/**
* Note Delete Controller       
* @access public notedelete
* @param  array $post
* @return json data
*/

    public function notedelete()
    {
        $post = Input::all();
      
        if(!empty($post['note_id']) && !empty($post['staff_id']))
        {
            $getData = $this->staff->noteDelete($post['note_id'],$post['staff_id']);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

/**
* Note Add Controller       
* @access public noteAdd
* @param  array $data
* @return json data
*/
    public function noteAdd() {

        $data = Input::all();

          $result = $this->staff->noteAdd($data);
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

/**
* Note Detail Controller       
* @access public notedetail
* @param  array $data
* @return json data
*/

    public function notedetail() {
 
         $data = Input::all();
         

          $result = $this->staff->noteDetail($data);
          

           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }

/**
* Note Edit Controller       
* @access public noteEdit
* @param  array $data
* @return json data
*/

    public function noteEdit() {

       
         $data = Input::all();
         
          $result = $this->staff->noteEdit($data);
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

/**
* Timeoff Listing of particular staff Controller       
* @access public timeoff
* @param  array $data
* @return json data
*/

    public function timeoff() {
        $data = Input::all();
        
        $result = $this->staff->timeoffList($data[0]);
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }

/**
* Timeoff Delete Controller       
* @access public timeoffDelete
* @param  array $post
* @return json data
*/
    public function timeoffDelete()
    {
        $post = Input::all();
      
        if(!empty($post['timeoff_id']) && !empty($post['staff_id']))
        {
            $getData = $this->staff->timeoffDelete($post['timeoff_id'],$post['staff_id']);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

/**
* Timeoff Add Controller       
* @access public timeoffAdd
* @param  array $data
* @return json data
*/

    public function timeoffAdd() {

        $data = Input::all();

          $result = $this->staff->timeoffAdd($data);
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

/**
* Timeoff Detail Controller       
* @access public timeoffdetail
* @param  array $data
* @return json data
*/

    public function timeoffdetail() {
 
         $data = Input::all();
         
        $result = $this->staff->timeoffDetail($data);
          
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }

/**
* Timeoff Edit Controller       
* @access public noteEdit
* @param  array $data
* @return json data
*/

    public function timeoffEdit() {

         $data = Input::all();

         if(isset($data['date_begin'])) {
             $data['date_begin'] = date("Y-m-d", strtotime($data['date_begin']));
          }

          if(isset($data['date_end'])) {
             $data['date_end'] = date("Y-m-d", strtotime($data['date_end']));
          }

          $result = $this->staff->timeoffEdit($data);
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);
    }

/**
* Staff Note/Timeoff controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function staffNoteTimeoff() {
 
        $data = Input::all();
        $result = $this->staff->staffNoteTimeoff($data);
                 
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'allnotes' => $result['allnotes'],'allTimeOff' => $result['allTimeOff']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'allnotes' => $result['allnotes'],'allTimeOff' => $result['allTimeOff']);
        }
        
        return response()->json(["data" => $response]);

    }

}
