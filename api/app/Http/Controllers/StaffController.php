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

    public function add() {
 
         $data = Input::all();
         
          $result = $this->staff->StaffAdd($data);

          if (count($result) > 0) {

            $response = array('success' => 1, 'message' => "Record insert successfully",'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

      //  $username = $data['username'];
       // $password = $data['password'];
        
       // $result = $this->login->verifylogin($username, $password);
    }

}
