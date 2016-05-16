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
                        if(isset($data['where']['category_id']))
                        {
                            $category_id = $data['where']['category_id'];
                            $orderData = $orderData->leftJoin('product_brand_category as pbc', 'pbc.product_id', '=', 'p.id');
                            $orderData = $orderData->where('pbc.category_id' , '=', $category_id);
                        }
                        if(isset($data['where']['color_id']))
                        {
                            $color_id = $data['where']['color_id'];
                            $orderData = $orderData->leftJoin('product_color_size as pcs', 'pcs.product_id', '=', 'p.id');
                            $orderData = $orderData->where('pcs.color_id' , '=', $color_id);
                        }
                        if(isset($data['where']['size_id']))
                        {
                            $size_id = $data['where']['size_id'];
                            $orderData = $orderData->leftJoin('product_color_size as pcs', 'pcs.product_id', '=', 'p.id');
                            $orderData = $orderData->where('pcs.size_id' , '=', $size_id);
                        }
                        $orderData = $orderData->orderBy($data['paginate']['sorts']['sortBy'], $data['paginate']['sorts']['sortOrder'])
                        ->GroupBy('p.id')
                        ->skip($data['paginate']['start'])
                        ->take($data['paginate']['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        $orderData2 = DB::table('products as p')
                        ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                        ->select(DB::raw('GROUP_CONCAT(p.id) as product_id'))
                        ->where('p.vendor_id' , '=', $data['where']['vendor_id']);
                        if(isset($data['where']['search']))
                        {
                            $search = $data['where']['search'];
                            $orderData2 = $orderData2->where('p.name', 'LIKE', '%'.$search.'%');
                        }
                        if(isset($data['where']['category_id']))
                        {
                            $category_id = $data['where']['category_id'];
                            $orderData2 = $orderData2->leftJoin('product_brand_category as pbc', 'pbc.product_id', '=', 'p.id');
                            $orderData2 = $orderData2->where('pbc.category_id' , '=', $category_id);
                        }
                        if(isset($data['where']['color_id']))
                        {
                            $color_id = $data['where']['color_id'];
                            $orderData2 = $orderData2->leftJoin('product_color_size as pcs', 'pcs.product_id', '=', 'p.id');
                            $orderData2 = $orderData2->where('pcs.color_id' , '=', $color_id);
                        }
                        if(isset($data['where']['size_id']))
                        {
                            $size_id = $data['where']['size_id'];
                            $orderData2 = $orderData2->leftJoin('product_color_size as pcs', 'pcs.product_id', '=', 'p.id');
                            $orderData2 = $orderData2->where('pcs.size_id' , '=', $size_id);
                        }
                        $orderData2 = $orderData2->GroupBy('p.vendor_id')->get();
        
        $product_ids = explode(",", $orderData2[0]->product_id);

        $category_data = DB::table('product_brand_category as pbc')
                        ->leftJoin('category as c', 'c.id', '=', 'pbc.category_id')
                        ->select(DB::raw('COUNT(*) as total'),'c.category_name','pbc.category_id as category_id')
                        ->whereIn('pbc.product_id', $product_ids)
                        ->GroupBy('pbc.category_id')
                        ->get();

        $color_data = DB::table('product_color_size as pcs')
                        ->leftJoin('color as c', 'c.id', '=', 'pcs.color_id')
                        ->select(DB::raw('COUNT(*) as total'),'c.name as color_name','c.id as color_id')
                        ->whereIn('pcs.product_id', $product_ids)
                        ->GroupBy('pcs.color_id')
                        ->get();

        $size_data = DB::table('product_color_size as pcs')
                        ->leftJoin('product_size as ps', 'ps.id', '=', 'pcs.size_id')
                        ->select(DB::raw('COUNT(*) as total'),'ps.name as size_name','ps.id as size_id')
                        ->whereIn('pcs.product_id', $product_ids)
                        ->GroupBy('pcs.size_id')
                        ->get();

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
        $returnData['category_data'] = $category_data;
        $returnData['color_data'] = $color_data;
        $returnData['size_data'] = $size_data;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
    }

    public function getProductCategoryFilter($data)
    {
        /*SELECT c.category_name,pbc.category_id, COUNT(*) as total FROM product_brand_category pbc LEFT JOIN category c on c.id = pbc.category_id GROUP BY pbc.category_id ORDER BY total desc*/

        $orderData = DB::table('product_brand_category as pbc')
                    ->leftJoin('category as c', 'c.id', '=', 'pbc.category_id')
                    ->leftJoin('products as p', 'p.id', '=', 'pbc.product_id')
                    ->select('c.category_name','pbc.category_id',DB::raw('count(*) as total'))
                    ->where('p.vendor_id' , '=', $data['where']['vendor_id']);
                    if(isset($data['where']['search']))
                    {
                        $search = $data['where']['search'];
                        $orderData = $orderData->where('p.name', 'LIKE', '%'.$search.'%');
                    }
                    if(isset($data['where']['category_id']))
                    {
                        $category_id = $data['where']['category_id'];
                        $orderData = $orderData->where('pbc.category_id' , '=', $category_id);
                    }
                    $orderData = $orderData->GroupBy('pbc.category_id')
                    ->get();

        return $orderData;
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
             
            $result = DB::table('purchase_detail')->insert(['design_id'=>$post['id'],
                'size'=>$row['sizeName'],
                'price'=>$row['customerPrice'],
                'qnty'=>$row['qnty'],
                'color_id'=>$row['color_id'],
                'date'=>$post['created_date']]);
        }

         $result_design = DB::table('design_product')->insert(['design_id'=>$post['id'],
                'product_id'=>$post['product_id']]);
         return true;

    }
}
