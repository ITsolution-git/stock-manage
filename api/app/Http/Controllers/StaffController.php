<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Staff;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use DB;
use Image;
use Request;

class StaffController extends Controller {  
    

/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Staff $staff) {

        $this->staff = $staff;
       
    }

    /**
     * Check login functionality.
     *
     * @param  
     * @return Data Response
     */

    public function index() {
 
        $result = $this->staff->staffList();
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

     /**
     * Staff Add.
     *
     * @param  all staff data in post
     * @return Data Response
     */

    public function add() {
 
         $data = Input::all();
         
         if(isset($data['staff']['date_start'])) {
            $data['staff']['date_start'] = date("Y-m-d", strtotime($data['staff']['date_start']));
         }

         if(isset($data['staff']['birthday'])) {
            $data['staff']['birthday'] = date("Y-m-d", strtotime($data['staff']['birthday']));
         }

         if(isset($data['staff']['date_end'])) {
            $data['staff']['date_end'] = date("Y-m-d", strtotime($data['staff']['date_start']));
         }
        

          $data['users']['password'] = md5($data['users']['password']);
          $data['users']['name'] = $data['staff']['first_name'].' '.$data['staff']['last_name'];

          $result = $this->staff->staffAdd($data);

          if (count($result) > 0) {

            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

    /**
     * Staff Edit.
     *
     * @param  all staff data in post
     * @return Data Response
     */

    public function edit() {
 
         $data = Input::all();

         $data['users']['id'] = $data['staff']['user_id'];


          
          $data['staff']['date_start'] = date("Y-m-d", strtotime($data['staff']['date_start']));
          $data['staff']['birthday'] = date("Y-m-d", strtotime($data['staff']['birthday']));
          $data['staff']['date_end'] = date("Y-m-d", strtotime($data['staff']['date_end']));

          $data['users']['password'] = md5($data['users']['password']);
          $data['users']['name'] = $data['staff']['first_name'].' '.$data['staff']['last_name'];

          $result = $this->staff->staffEdit($data['staff']);
          $resultUsers = $this->staff->userEdit($data['users']);

          if (count($result) > 0) {

            $response = array('success' => 1, 'message' => UPDATE_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

    


     /**
     * Staff Add.
     *
     * @param  staff detail page
     * @return Data Response
     */

    public function detail() {
 
         $data = Input::all();
         

          $result = $this->staff->staffDetail($data);
          

          $result['staff'][0]->date_start = date("d-F-Y", strtotime($result['staff'][0]->date_start));
          $result['staff'][0]->birthday = date("d-F-Y", strtotime($result['staff'][0]->birthday));
          $result['staff'][0]->date_end = date("d-F-Y", strtotime($result['staff'][0]->date_end));


           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['staff'],'users_records' => $result['users']);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['staff'],'users_records' => $result['users']);
        }
        
        return response()->json(["data" => $response]);

    }

     /**
     * change the is_delete status of the users and delete
     *
     * @param  user_id,id
     * @return Data Response
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




}
