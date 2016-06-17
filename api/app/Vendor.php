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

    public function vendorList($post) {
        
               $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $admindata = DB::table('vendors')
                         ->select(DB::raw('SQL_CALC_FOUND_ROWS *'))
                         ->where('is_delete','=','1')
                         ->where('company_id','=',$post['company_id']);
                 if($search != '')               
                  {
                      $admindata = $admindata->Where(function($query) use($search)
                      {
                          $query->orWhere('name_company', 'LIKE', '%'.$search.'%')
                                ->orWhere('email','LIKE', '%'.$search.'%');
                      });
                  }
                 $admindata = $admindata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $admindata;
        $returnData['count'] = $count[0]->Totalcount;
        
/*        if($count[0]->Totalcount>0)
        {
          foreach ($admindata as $key=>$value) 
          {
            $admindata[$key]->created_date =date('m/d/Y',strtotime($value->created_date)) ;
          }
        }
        */
        return $returnData;

        $whereConditions = ['status' => '1','is_delete' => '1','company_id' => $post['cond']['company_id']];
        $vendorData = DB::table('vendors')->where($whereConditions)->orderBy('id', 'desc')->get();
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
* Vendor Detail           
* @access public vendorDetail
* @param  int $vendorId
* @return array $combine_array
*/  

    public function vendorDetail($data) {

       
        $whereVendorConditions = ['id' => $data['id'],'company_id' => $data['company_id']];
        $vendorData = DB::table('vendors')->where($whereVendorConditions)->get();

        
    $whereConditions = ['vendor_id' => $data['id']];
    $listArray = ['first_name','last_name','role_id','prime_email','prime_phone','id'];
   
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
        unset($data['all_url_photo']);
        $result = DB::table('vendors')->where('id', '=', $data['id'])->update($data);
        return $result;
    }


    /**
* all products of particular vendor           
* @access public vendorDetail
* @param  int $vendorId
* @return array $productData
*/  

    public function productVendor($data) {
        $listArray = ['id','name','description'];
        $whereVendorConditions = ['vendor_id' => $data['id']];
        $productData = DB::table('products')->select($listArray)->where($whereVendorConditions)->get();
        return $productData;
    }




}
