<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Affiliate;
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

class AffiliateController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Affiliate $affiliate,Common $common) {
        $this->affiliate = $affiliate;
        $this->common = $common;
    }

    public function getAffiliateDetail()
    {
        $post = Input::all();
        $affiliate_data = $this->common->GetTableRecords('affiliates',array('company_id' => $post['cond']['company_id']),array());
        print_r($affiliate_data);exit;
        $design_data = $this->common->GetTableRecords('order_design',array('order_id' => $post['cond']['order_id']),array());

        $design_detail = array();

        foreach ($design_data as $design) {
            print_r($design);exit;
        }
    }
}