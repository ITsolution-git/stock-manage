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





}
