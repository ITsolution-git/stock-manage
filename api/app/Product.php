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

    public function productList($post) {
        
        $whereConditions = ['products.is_delete' => '1','vendors.is_delete' => '1','vendors.company_id' => $post['cond']['company_id']];
        $listArray = ['products.name','products.vendor_id','products.id','products.description','products.vendor_sku','products.color_ids','products.size_group','products.status','vendors.name_company'];

        $vendorData = DB::table('products as products')
                         ->Join('vendors as vendors', 'products.vendor_id', '=', 'vendors.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->orderBy('products.id', 'desc')
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

    public function productDetail($data) {

        $whereProductConditions = ['id' => $data['id']];
        $productData = DB::table('products')->where($whereProductConditions)->get();

        $combine_array['product'] = $productData;
        return $combine_array;
    }




    public function getVendorProducts($data)
    {
        if(empty($data['fields']))
        {
            $listArray = [DB::raw('SQL_CALC_FOUND_ROWS p.id,p.name,p.product_image,p.description,v.name_company as vendor_name')];
        }
        else
        {
            $listArray = [DB::raw('SQL_CALC_FOUND_ROWS p.name as product_name,p.product_image,p.description,v.name_company as vendor_name')];
        }
        

        $orderData = DB::table('products as p')
                        ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                        ->select($listArray)
                        ->where('p.vendor_id' , '=', $data['where']['vendor_id']);
                        if(isset($data['where']['search']))
                        {
                            $search = $data['where']['search'];
                            $orderData = $orderData->where('p.name', 'LIKE', '%'.$search.'%');
                        }
                        $orderData = $orderData->orderBy($data['paginate']['sorts']['sortBy'], $data['paginate']['sorts']['sortOrder'])
                        ->skip($data['paginate']['start'])
                        ->take($data['paginate']['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        if(!empty($data['fields']))
        {
            $temp='';
            foreach ($orderData as $key => $value) {
                $temp .= $value->product_name.",";
            }
            $temp = substr($temp,0,-1);
            
            $ret_array = array();
            $ret_array[0]['product_name']=$temp;
        }
        else
        {
            $ret_array = $orderData;
        }

        $returnData = array();
        $returnData['allData'] = $ret_array;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
    }

    public function GetProductColor($data)
    {
        $listArray = ['color_size_data'];
        $orderData = DB::table('products')
                        ->select($listArray)
                        ->where($data)
                        ->get();
        return $orderData;
    }

    public function GetColorDeail($data)
    {
        $orderData = DB::table('color')
                        ->where($data)
                        ->get();
        return $orderData;
    }


/**
* Product Image Upload          
* @access public productImageUpdate
* @param  int $insertedid
* @param  array $newfilename
* @return array $result
*/ 

     public function productImageUpdate($insertedid,$newfilename)
    {
        if(!empty($insertedid))
        {
            
           $result =  DB::table('products')
                        ->where('id', $insertedid)
                        ->update(['photo' => $newfilename]);
           return $result;
        }
        else
        {
            return false;
        }
    }

    public function addProduct($post) {
       
        foreach($post['productData'] as $row) {
             
            $resul = DB::table('purchase_detail')->insert(['design_id'=>$post['id'],
                'size'=>$row['sizeName'],
                'price'=>$row['customerPrice'],
                'qnty'=>$row['qnty'],
                'color_id'=>$row['color_id'],
                'date'=>$post['created_date']]);

        }
        return true;
      
    }

}
