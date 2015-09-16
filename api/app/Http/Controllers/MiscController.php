<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Misc;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class MiscController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Misc $misc) {

        $this->misc = $misc;
       
    }

/**
* Misc save data       
* @access public miscSave
* @param  array $data
* @return json data
*/

    public function miscSave() {

          $data = Input::all();
          $result = $this->misc->miscSave($data);
         
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => INSERT_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }





}
