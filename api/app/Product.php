<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Product extends Model {


/**
* Product listing array           
* @access public productList
* @return array $productData
*/

    public function productList() {
        
        $whereConditions = ['products.is_delete' => '1','vendors.is_delete' => '1'];
        $listArray = ['products.vendor_id','products.id','products.description','products.vendor_sku','products.color_ids','products.size_group','products.status','vendors.name_company'];

        $vendorData = DB::table('products as products')
                         ->Join('vendors as vendors', 'products.vendor_id', '=', 'vendors.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $vendorData;
    }

/**
* Delete Product           
* @access public staffDelete
* @param  int $id
* @return array $result
*/ 

    public function productDelete($id)
    {
        if(!empty($id))
        {
            $result = DB::table('products')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }

/**
* Product Detail           
* @access public productDetail
* @param  int $productId
* @return array
*/  

    public function productDetail($productId) {

        $whereProductConditions = ['status' => '1','id' => $productId];
        $productData = DB::table('products')->where($whereProductConditions)->get();

        $combine_array['product'] = $productData;
        return $combine_array;
    }

/**
* Product Add data           
* @access public productAdd
* @param  array $data
* @return array $result
*/
    public function productAdd($data) {
        $data['product']['created_date'] = date("Y-m-d H:i:s");
        $data['product']['updated_date'] = date("Y-m-d H:i:s");
     
        $result_product = DB::table('products')->insert($data['product']);
        $productid = DB::getPdo()->lastInsertId();
        return $productid;
    }


/**
* Product Edit data           
* @access public productEdit
* @param  array $data
* @return array $result
*/  

    public function productEdit($data) {
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('products')->where('id', '=', $data['id'])->update($data);
        return $result;
    }


}
