<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Vendor extends Model {

/**
* Vendor listing array           
* @access public vendorList
* @return array $staffData
*/

    public function vendorList() {
        
        $whereConditions = ['status' => '1','is_delete' => '1'];
        $vendorData = DB::table('vendors')->where($whereConditions)->get();
        return $vendorData;
    }

/**
* Delete Vendor           
* @access public vendorDelete
* @param  int $id
* @return array $result
*/ 

    public function vendorDelete($id)
    {
        if(!empty($id))
        {
            $result = DB::table('vendors')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Vendor Add data           
* @access public staffAdd
* @param  array $data
* @return int $vendorid
*/  

     /**
     * Add Staff
     */
    public function vendorAdd($data) {
        $data['vendor']['created_date'] = date("Y-m-d H:i:s");
        $data['vendor']['updated_date'] = date("Y-m-d H:i:s");
        //$data['users']['updated_date'] = date("Y-m-d H:i:s");
       // $data['users']['updated_date'] = date("Y-m-d H:i:s");
        
      //  $result = DB::table('users')->insert($data['users']);
       
      //  $insertedid = DB::getPdo()->lastInsertId();
        
      //  $data['staff']['user_id'] = $insertedid;
        $result_vendor = DB::table('vendors')->insert($data['vendor']);

         $vendorid = DB::getPdo()->lastInsertId();

        return  $vendorid;
    }

/**
* Vendor Image Upload          
* @access public vendorImageUpdate
* @param  int $insertedid
* @param  array $newfilename
* @return array $result
*/ 

     public function vendorImageUpdate($insertedid,$newfilename)
    {
        if(!empty($insertedid))
        {
            
           $result =  DB::table('vendors')
                        ->where('id', $insertedid)
                        ->update(['photo' => $newfilename]);
           return $result;
        }
        else
        {
            return false;
        }
    }


/**
* Vendor Detail           
* @access public vendorDetail
* @param  int $vendorId
* @return array $combine_array
*/  

    public function vendorDetail($vendorId) {

        $whereVendorConditions = ['status' => '1','id' => $vendorId];
        $vendorData = DB::table('vendors')->where($whereVendorConditions)->get();

    /*$whereConditions = ['status' => '1','id' => $staffData[0]->user_id];
    $listArray = ['user_name','email','password','role_id'];
   
    $UserData = DB::table('users')->select($listArray)->where($whereConditions)->get();*/

        $combine_array = array();

        $combine_array['vendor'] = $vendorData;
       // $combine_array['users'] = $UserData;

        return $combine_array;
    }


}
