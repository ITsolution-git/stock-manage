<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Staff;
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

       
        $data['staff'] = array('last_name' => isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : '',
            'first_name' => isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : '',
            'middle_name' => isset($_REQUEST['middle_name']) ? $_REQUEST['middle_name'] : '',
            'start_date' => isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '',
            'prime_phone_main' => $_REQUEST['prime_phone_main'] ? $_REQUEST['prime_phone_main'] : '',
            
            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : '',
            'commision_base' => isset($_REQUEST['commision_base']) ? $_REQUEST['commision_base'] : '',
            'commission_sub' => isset($_REQUEST['commission_sub']) ? $_REQUEST['commission_sub'] : '',
            'prime_address_city' => isset($_REQUEST['prime_address_city']) ? $_REQUEST['prime_address_city'] : '',
            'prime_address_state' => isset($_REQUEST['prime_address_state']) ? $_REQUEST['prime_address_state'] : '',
            'prime_address_zip' => isset($_REQUEST['prime_address_zip']) ? $_REQUEST['prime_address_zip'] : '',
            'prime_address1' => isset($_REQUEST['staff_type']) ? $_REQUEST['staff_type'] : '',
            'prime_address1' => isset($_REQUEST['prime_address1']) ? $_REQUEST['prime_address1'] : '',
            'prime_address2' => isset($_REQUEST['prime_address2']) ? $_REQUEST['prime_address2'] : '',
            'birthday' => isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : '',
            'date_start' => isset($_REQUEST['date_start']) ? $_REQUEST['date_start'] : '',
            'date_end' => isset($_REQUEST['date_end']) ? $_REQUEST['date_end'] : '',
            'level' => isset($_REQUEST['level']) ? $_REQUEST['level'] : '',
            'notes' => isset($_REQUEST['notes']) ? $_REQUEST['notes'] : '',
            'second_mail' => isset($_REQUEST['second_mail']) ? $_REQUEST['second_mail'] : '',
            'emergency_contact_name' => isset($_REQUEST['emergency_contact_name']) ? $_REQUEST['emergency_contact_name'] : '',
            'emergency_contact_relation' => isset($_REQUEST['emergency_contact_relation']) ? $_REQUEST['emergency_contact_relation'] : ''

        );




        $data['users'] = array('user_name' => isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : '',
            'email' => isset($_REQUEST['email']) ? $_REQUEST['email'] : '',
            'password' => isset($_REQUEST['password']) ? $_REQUEST['password'] : '',
            'role_id' => isset($_REQUEST['role_id']) ? $_REQUEST['role_id'] : ''
        );


                foreach($data['staff'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['staff'][$key]); 
                    } 
                } 

                 foreach($data['users'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['users'][$key]); 
                    } 
                } 




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

          $insertedid = $this->staff->staffAdd($data);

          if ($insertedid && $_FILES) {

                if (!$_FILES['image']['error'] && isset($insertedid)) {

                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = FILEUPLOAD . "staff/" . $insertedid;
                    StaffController::create_dir($uploaddir);
                    
                    $newfilename = "staff_main_" . $insertedid . "." . $extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->staff->staffImageUpdate($insertedid,$newfilename);
                    }
                }

            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
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
 

         
          $data['staff'] = array('id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : '',
            'last_name' => isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : '',
            'first_name' => isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : '',
            'middle_name' => isset($_REQUEST['middle_name']) ? $_REQUEST['middle_name'] : '',
            'start_date' => isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '',
            'prime_phone_main' => $_REQUEST['prime_phone_main'] ? $_REQUEST['prime_phone_main'] : '',
            'user_id' => $_REQUEST['user_id'] ? $_REQUEST['user_id'] : '',
            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : '',
            'commision_base' => isset($_REQUEST['commision_base']) ? $_REQUEST['commision_base'] : '',
            'commission_sub' => isset($_REQUEST['commission_sub']) ? $_REQUEST['commission_sub'] : '',
            'prime_address_city' => isset($_REQUEST['prime_address_city']) ? $_REQUEST['prime_address_city'] : '',
            'prime_address_state' => isset($_REQUEST['prime_address_state']) ? $_REQUEST['prime_address_state'] : '',
            'prime_address_zip' => isset($_REQUEST['prime_address_zip']) ? $_REQUEST['prime_address_zip'] : '',
            'prime_address1' => isset($_REQUEST['staff_type']) ? $_REQUEST['staff_type'] : '',
            'prime_address1' => isset($_REQUEST['prime_address1']) ? $_REQUEST['prime_address1'] : '',
            'prime_address2' => isset($_REQUEST['prime_address2']) ? $_REQUEST['prime_address2'] : '',
            'birthday' => isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : '',
            'date_start' => isset($_REQUEST['date_start']) ? $_REQUEST['date_start'] : '',
            'date_end' => isset($_REQUEST['date_end']) ? $_REQUEST['date_end'] : '',
            'level' => isset($_REQUEST['level']) ? $_REQUEST['level'] : '',
            'notes' => isset($_REQUEST['notes']) ? $_REQUEST['notes'] : '',
            'second_mail' => isset($_REQUEST['second_mail']) ? $_REQUEST['second_mail'] : '',
            'emergency_contact_name' => isset($_REQUEST['emergency_contact_name']) ? $_REQUEST['emergency_contact_name'] : '',
            'emergency_contact_relation' => isset($_REQUEST['emergency_contact_relation']) ? $_REQUEST['emergency_contact_relation'] : ''

        );




        $data['users'] = array('user_name' => isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : '',
            'email' => isset($_REQUEST['email']) ? $_REQUEST['email'] : '',
            'password' => isset($_REQUEST['password']) ? $_REQUEST['password'] : '',
            'role_id' => isset($_REQUEST['role_id']) ? $_REQUEST['role_id'] : ''
        );


                foreach($data['staff'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['staff'][$key]); 
                    } 
                } 

                 foreach($data['users'] as $key => $link) 
                { 

                    if($link == '') 
                    { 
                        unset($data['users'][$key]); 
                    } 
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
          
          

          $data['users']['password'] = md5($data['users']['password']);
          $data['users']['name'] = $data['staff']['first_name'].' '.$data['staff']['last_name'];

          
          $result = $this->staff->staffEdit($data['staff']);
          $resultUsers = $this->staff->userEdit($data['users']);

          if (count($result) > 0) {


            if ($_FILES) {

                if (!$_FILES['image']['error'] && isset($data['staff']['id'])) {

                     array_map('unlink', glob(FILEUPLOAD . "staff/" . $data['staff']['id']."/*"));

                    $filename = $_FILES['image']['name'];
                    $info = new SplFileInfo($filename);
                    $extention = $info->getExtension();
                    $uploaddir = FILEUPLOAD . "staff/" . $data['staff']['id'];
                    StaffController::create_dir($uploaddir);
                    
                    $newfilename = "staff_main_" . $data['staff']['id'] . "." . $extention;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploaddir . "/" . $newfilename)) {
                       
                       $result = $this->staff->staffImageUpdate($data['staff']['id'],$newfilename);
                    }
                }

            
        } 

         $response = array('success' => 1, 'message' => UPDATE_RECORD,'records' => $result);
        


           
        } else {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => '');
        }
        

return response()->json(["data" => $response]);
    }

    
public function create_dir($dir_path) {

        if (!file_exists($dir_path)) {
            mkdir($dir_path, 0777, true);
        } else {
            chmod($dir_path, 0777);
        }
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
          

          $result['staff'][0]->all_url_photo = UPLOAD_PATH.'staff/'.$result["staff"][0]->id.'/'.$result['staff'][0]->photo;

         // if blank birthdaye is enter then we will not calculate date
         if($result['staff'][0]->birthday == '0000-00-00 00:00:00') {

             unset($result['staff'][0]->birthday);

         } else {

            $result['staff'][0]->birthday = date("d-F-Y", strtotime($result['staff'][0]->birthday));
         }

         if($result['staff'][0]->date_start == '0000-00-00 00:00:00') {

              unset($result['staff'][0]->date_start);

         } else {

            $result['staff'][0]->date_start = date("d-F-Y", strtotime($result['staff'][0]->date_start));
         }

         if($result['staff'][0]->date_end == '0000-00-00 00:00:00') {

              unset($result['staff'][0]->date_end);

         } else {

            $result['staff'][0]->date_end = date("d-F-Y", strtotime($result['staff'][0]->date_end));
         }


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


    /**
     * Notes of the staff.
     *
     * @param  
     * @return Data Response
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
     * change the is_delete status of the users and delete
     *
     * @param  user_id,id
     * @return Data Response
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
     * Notes Add.
     *
     * @param  all note  data in post
     * @return Data Response
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
     * Note Detail
     *
     * @param  staff detail page
     * @return Data Response
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
     * Notes Edit.
     *
     * @param  all note of the Note data in post
     * @return Data Response
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
     * Time Off of the staff.
     *
     * @param  
     * @return Data Response
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
     * change the is_delete status of the users and delete
     *
     * @param  user_id,id
     * @return Data Response
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
     * Notes Add.
     *
     * @param  all note  data in post
     * @return Data Response
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
     * Note Detail
     *
     * @param  staff detail page
     * @return Data Response
     */

    public function timeoffdetail() {
 
         $data = Input::all();
         

          $result = $this->staff->timeoffDetail($data);
          

          if($result[0]->date_begin == '0000-00-00 00:00:00') {

             unset($result[0]->date_begin);

         } else {

            $result[0]->date_begin = date("d-F-Y", strtotime($result[0]->date_begin));
         }

         if($result[0]->date_end == '0000-00-00 00:00:00') {

              unset($result[0]->date_end);

         } else {

            $result[0]->date_end = date("d-F-Y", strtotime($result[0]->date_end));
         }


        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);

    }

    /**
     * Notes Edit.
     *
     * @param  all note of the Note data in post
     * @return Data Response
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



}
