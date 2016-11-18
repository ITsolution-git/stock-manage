<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Production;
use App\Common;
use DB;
use Request;

class ProductionController extends Controller { 

	public function __construct(Production $production,Common $common) 
 	{
 		parent::__construct();
        $this->production = $production;
        $this->common = $common;
    }

}