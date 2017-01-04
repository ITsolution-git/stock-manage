<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Common;
use App\Login;

class Box extends Model {

/**
* Vendor listing array           
* @access public vendorList
* @return array $staffData
*/

    public function __construct( Common $common, Login $login)
    {
        $this->common = $common;
        $this->login = $login;
    }
        
    public function boxList($post) {

        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        
        $boxdata = DB::table('box_setting')
                        ->select(DB::raw('SQL_CALC_FOUND_ROWS *'))
                        ->where('is_delete','=','1')
                        ->where('company_id','=',$post['company_id']);
                        if($search != '')          
                        {
                            $boxdata = $boxdata->Where(function($query) use($search)
                            {
                                $query->orWhere('box_type', 'LIKE', '%'.$search.'%');
                                $query->orWhere('length', 'LIKE', '%'.$search.'%');
                                $query->orWhere('width', 'LIKE', '%'.$search.'%');
                                $query->orWhere('height', 'LIKE', '%'.$search.'%');
                                $query->orWhere('weight', 'LIKE', '%'.$search.'%');
                            });
                        }
                        $boxdata = $boxdata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();

        $returnData['allData'] = $boxdata;
        $returnData['count'] = $count[0]->Totalcount;
        
        return $returnData;
    }
}