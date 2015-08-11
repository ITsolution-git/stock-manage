<?php

namespace App\Http\Controllers;




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
 
        $result = $this->staff->StaffList();
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => "Data fetch successfully",'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => "Wrong Credential",'records' => $result);
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
         
        
         $data['password'] = md5($data['password']);

          $data['date_start'] = date("Y-m-d", strtotime($data['date_start']));
          $data['birthday'] = date("Y-m-d", strtotime($data['birthday']));
          $data['date_end'] = date("Y-m-d", strtotime($data['date_end']));

          $result = $this->staff->StaffAdd($data);

          if (count($result) > 0) {

            $response = array('success' => 1, 'message' => "Record insert successfully",'records' => $result);
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
 $data['password'] = md5($data['password']);
          $data['date_start'] = date("Y-m-d", strtotime($data['date_start']));
          $data['birthday'] = date("Y-m-d", strtotime($data['birthday']));
          $data['date_end'] = date("Y-m-d", strtotime($data['date_end']));

          $result = $this->staff->StaffEdit($data);

          if (count($result) > 0) {

            $response = array('success' => 1, 'message' => "Record Updated successfully",'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }

    /**
     * All types related to staff.
     *
     * @param  
     * @return Data Response
     */

    public function type() {
 
        $result = $this->staff->TypeList('staff');

       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => "Data fetch successfully",'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => "No records Found",'records' => $result);
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
         
          $result = $this->staff->StaffDetail($data);
          
          $result[0]->date_start = date("d-F-Y", strtotime($result[0]->date_start));
          $result[0]->birthday = date("d-F-Y", strtotime($result[0]->birthday));
          $result[0]->date_end = date("d-F-Y", strtotime($result[0]->date_end));


           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => "Data fetch successfully",'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => "User eithe not exists or Inactive",'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }




}
