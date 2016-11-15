<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Common;
use App\Login;

class Machine extends Model {

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
        
    public function machineList($post) {

        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
        
        $machinedata = DB::table('machine')
                        ->select(DB::raw('SQL_CALC_FOUND_ROWS *'))
                        ->where('is_delete','=','1')
                        ->where('company_id','=',$post['company_id']);
                        if($search != '')          
                        {
                            $machinedata = $machinedata->Where(function($query) use($search)
                            {
                                $query->orWhere('name_machine', 'LIKE', '%'.$search.'%');
                            });
                        }
                        $machinedata = $machinedata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();

        foreach ($machinedata as $machine) {
            if($machine->operation_status == 1) {
                $machine->operation_status = true;
            }
            else {
                $machine->operation_status = false;    
            }
        }

        $returnData['allData'] = $machinedata;
        $returnData['count'] = $count[0]->Totalcount;
        
        return $returnData;
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

    public function vendorContacts($post) {

       
               $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $admindata = DB::table('vendor_contacts')
                         ->select('*')
                         ->where('is_deleted','=','1')
                         ->where('vendor_id','=',$post['v_id']);
                 if($search != '')               
                  {
                      $admindata = $admindata->Where(function($query) use($search)
                      {
                          $query->orWhere('first_name', 'LIKE', '%'.$search.'%')
                                ->orWhere('last_name','LIKE', '%'.$search.'%')
                                ->orWhere('prime_email','LIKE', '%'.$search.'%')
                                ->orWhere('prime_phone','LIKE', '%'.$search.'%');
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
        
        return $returnData;
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


    public function SalesList($post) {

        $this->common->getDisplayNumber('sales',$post['company_id'],'company_id','id','yes');
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $admindata = DB::table('sales')
                         ->select('*',DB::raw('DATE_FORMAT(sales_created_date, "%m/%d/%Y") as sales_created_date'))
                         ->where('sales_delete','=','1')
                         ->where('company_id','=',$post['company_id']);
                 if($search != '')               
                  {
                      $admindata = $admindata->Where(function($query) use($search)
                      {
                          $query->orWhere('sales_name', 'LIKE', '%'.$search.'%')
                                ->orWhere('sales_email','LIKE', '%'.$search.'%')
                                ->orWhere('sales_phone','LIKE', '%'.$search.'%')
                                ->orWhere('display_number','=', $search)
                                ->orWhere('sales_created_date','LIKE', '%'.$search.'%');
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
        
        return $returnData;
    }

}
