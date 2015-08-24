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
    public function vendorAdd($data,$vendor_contact) {
    
   
     $vendor_contact_array = json_decode(json_encode( $vendor_contact), true);
     

        $data['vendor']['created_date'] = date("Y-m-d H:i:s");
        $data['vendor']['updated_date'] = date("Y-m-d H:i:s");

        $result_vendor = DB::table('vendors')->insert($data['vendor']);

         $vendorid = DB::getPdo()->lastInsertId();

           foreach($vendor_contact_array as $key => $link) 
              { 
                
                $vendor_contact_array[$key]['vendor_id'] = $vendorid;
                $result_vendor = DB::table('vendor_contacts')->insert($vendor_contact_array[$key]);
              }


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


    $whereConditions = ['vendor_id' => $vendorId];
    $listArray = ['first_name','last_name','position','prime_email','prime_phone'];
   
    $UserData = DB::table('vendor_contacts')->select($listArray)->where($whereConditions)->get();

        $combine_array = array();

        $combine_array['vendor'] = $vendorData;
        $combine_array['allContacts'] = $UserData;

        return $combine_array;
    }


/**
* Vendor Edit data           
* @access public vendorEdit
* @param  array $data
* @return array $result
*/  
    public function vendorEdit($data) {

        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('vendors')->where('id', '=', $data['id'])->update($data);
        return $result;
    }

 /**
* Vendor Contact Edit data           
* @access public vendorEdit
* @param  array $data
* @return array $result
*/  

public function vendorContactEdit($vendor_contact,$vendorId) {
    
    DB::table('vendor_contacts')->where('vendor_id', '=', $vendorId)->delete();

     $vendor_contact_array = json_decode(json_encode($vendor_contact), true);
     
           foreach($vendor_contact_array as $key => $link) 
              { 
                
                $vendor_contact_array[$key]['vendor_id'] = $vendorId;
                $result_vendor = DB::table('vendor_contacts')->insert($vendor_contact_array[$key]);
              }
        return  $vendorId;
    }


}
