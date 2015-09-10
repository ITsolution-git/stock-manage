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
* Price Listing controller        
* @access public value1
* @return json data
*/

    public function value1() {
 
        $result = $this->misc->value1List();
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }



}
