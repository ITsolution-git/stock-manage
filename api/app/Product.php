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

        $whereProductConditions = ['p.id' => $data['id']];
        $listArray = ['p.*','v.name_company'];


        $productData = DB::table('products as p')
                        ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                        ->select($listArray)
                        ->where($whereProductConditions)->get();

        $combine_array['product'] = $productData;
        return $combine_array;
    }




    public function getVendorProducts($data)
    {
        $product_id_array=array();

        if(isset($data['where']['search']))
        {
            $search = $data['where']['search'];
            $sql = DB::table('products')
                        ->select(DB::raw('GROUP_CONCAT(id) as products'))
                        ->where('name', 'LIKE', '%'.$search.'%')
                        ->where('vendor_id' , '=', $data['where']['vendor_id'])
                        ->get();
            if(count($sql)>0)
            {
                $product_id_array = explode(",",$sql[0]->products);
            }
        }
        if(isset($data['where']['category_id']) && !empty($data['where']['category_id']))
        {
            $category_id_array = $data['where']['category_id'];
            foreach($category_id_array as $key=>$val)
            {
               $sql = DB::table('product_brand_category as pbc')
                        ->select(DB::raw('GROUP_CONCAT(pbc.product_id) as products'),'pbc.category_id','c.category_name')
                        ->leftJoin('category as c', 'c.id', '=', 'pbc.category_id')
                        ->whereIn('pbc.product_id' ,$product_id_array )
                        ->where('pbc.category_id','=',$val)
                        ->get();

                if(count($sql)>0)
                {
                   $product_id_array = explode(",",$sql[0]->products);
                }
            }
        }
        if(isset($data['where']['color_id']) && !empty($data['where']['color_id']))
        {
            $color_id_array = $data['where']['color_id'];
            foreach($color_id_array as $key=>$val)
            {
               $sql = DB::table('product_color_size as pcs')
                        ->select(DB::raw('GROUP_CONCAT(pcs.product_id) as products'),'pcs.color_id','c.name as color_name')
                        ->leftJoin('color as c', 'c.id', '=', 'pcs.color_id')
                        ->whereIn('pcs.product_id' ,$product_id_array )
                        ->where('pcs.color_id','=',$val)
                        ->get();

                if(count($sql)>0)
                {
                   $product_id_array = explode(",",$sql[0]->products);
                }
            }
        }
        if(isset($data['where']['size_id']) && !empty($data['where']['size_id']))
        {
            $size_id_array = $data['where']['size_id'];
            foreach($size_id_array as $key=>$val)
            {
               $sql = DB::table('product_color_size as pcs')
                        ->select(DB::raw('GROUP_CONCAT(pcs.product_id) as products'),'pcs.size_id','c.name as color_name')
                        ->leftJoin('color as c', 'c.id', '=', 'pcs.size_id')
                        ->whereIn('pcs.product_id' ,$product_id_array )
                        ->where('pcs.size_id','=',$val)
                        ->get();

                if(count($sql)>0)
                {
                   $product_id_array = explode(",",$sql[0]->products);
                }
            }
        }


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
                        ->where('p.vendor_id' , '=', $data['where']['vendor_id'])
                        ->whereIn('p.id',$product_id_array)
                        ->orderBy($data['paginate']['sorts']['sortBy'], $data['paginate']['sorts']['sortOrder'])
                        ->GroupBy('p.id')
                        ->skip($data['paginate']['start'])
                        ->take($data['paginate']['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );


        $category_data = DB::table('product_brand_category as pbc')
                        ->leftJoin('category as c', 'c.id', '=', 'pbc.category_id')
                        ->select(DB::raw('COUNT(pbc.category_id) as total'),'c.category_name','pbc.category_id as category_id')
                        ->whereIn('pbc.product_id',$product_id_array)
                        ->GroupBy('pbc.category_id')
                        ->get();

        $color_data = DB::table('product_color_size as pcs')
                        ->leftJoin('color as c', 'c.id', '=', 'pcs.color_id')
                        ->select(DB::raw('COUNT(DISTINCT pcs.product_id) as total'),'c.name as color_name','c.id as color_id')
                        ->whereIn('pcs.product_id', $product_id_array)
                        ->GroupBy('pcs.color_id')
                        ->get();

        $size_data = DB::table('product_color_size as pcs')
                        ->leftJoin('product_size as ps', 'ps.id', '=', 'pcs.size_id')
                        ->select(DB::raw('COUNT(DISTINCT pcs.product_id) as total'),'ps.name as size_name','ps.id as size_id')
                        ->whereIn('pcs.product_id', $product_id_array)
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

          if(isset($post['is_supply'])) {
            $insert_array = array('design_id' => $post['id'],'product_id'=>$post['product_id'],'is_supply' => $post['is_supply']);
          } else {
            $insert_array = array('design_id'=>$post['id'],'product_id'=>$post['product_id']);
          }

         // if($post['record_delete'] == 0) {
            $result_design = DB::table('design_product')->insert($insert_array);
         // }
       
        foreach($post['productData'] as $row) {

             if(isset($row['sku'])) {

                $insert_purchase_array = array('design_id'=>$post['id'],
                    'size'=>$row['sizeName'],
                    'sku'=>$row['sku'],
                    'price'=>$row['customerPrice'],
                    'qnty'=>$row['qnty'],
                    'color_id'=>$row['color_id'],
                    'date'=>$post['created_date']);

             } else {

                $insert_purchase_array = array('design_id'=>$post['id'],
                    'size'=>$row['sizeName'],
                    'sku'=>0,
                    'price'=>0,
                    'qnty'=>$row['qnty'],
                    'color_id'=>$row['color_id'],
                    'date'=>$post['created_date']);
             }

            $result = DB::table('purchase_detail')->insert($insert_purchase_array);
        }

        
         
         return true;

    }


/**
* Order Detail           
* @access public designDetail
* @param  int $orderId and $clientId
* @return array $combine_array
*/  

    public function designProduct($data) {

     
        $whereConditions = ['pd.is_delete' => "1",'dp.is_delete' => "1",'dp.design_id' => $data['id']];
        $listArray = ['dp.*','pd.*','c.name as colorName'];

        $designDetailData = DB::table('design_product as dp')
                         
                         ->leftJoin('purchase_detail as pd','dp.design_id','=', 'pd.design_id')
                         ->leftJoin('color as c','pd.color_id','=', 'c.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        
        $combine_array['design_product'] = $designDetailData;
        
        if($designDetailData) {
            $combine_array['product_id'] = $designDetailData[0]->product_id;
            $combine_array['design_id'] = $designDetailData[0]->design_id;
            $combine_array['colorName'] = $designDetailData[0]->colorName;
            $combine_array['colorId'] = $designDetailData[0]->color_id;
            $combine_array['is_supply'] = $designDetailData[0]->is_supply;
        }

        return $combine_array;
    }

    public function getPurchaseDetail($designId) {

    $whereConditions = ['is_delete' => "1",'design_id' => $designId];

    $result = DB::table('purchase_detail')->where($whereConditions)->get();

        
        $purchaseDetail = array();
        foreach ($result as $key=>$alldata){
          
                 $purchaseDetail[$alldata->size] = $alldata->qnty;
          }
        
        return $purchaseDetail;

    }

     public function getPurchaseDetailColor($designId) {

    $whereConditions = ['is_delete' => "1",'design_id' => $designId];
    $result = DB::table('purchase_detail')->where($whereConditions)->get();

        
        $purchaseDetail = array();
        foreach ($result as $key=>$alldata){
          
                 $purchaseDetail[$alldata->color_id][$alldata->size] = $alldata->qnty;
          }
        
        return $purchaseDetail;

    }


    public function getCustomProduct($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
       

        $whereConditions = ['product.is_delete' => "1",'product.company_id' => $post['company_id']];

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS product.*')];

        $productData = DB::table('products as product')
                         ->select($listArray)
                         ->where($whereConditions);
                        
                        if($search != '')
                        {
                          $productData = $productData->Where(function($query) use($search)
                          {
                              $query->orWhere('product.name', 'LIKE', '%'.$search.'%');
                              $query->orWhere('product.id', '=', $search);
                          });
                        }
                        
                        $productData = $productData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        $returnData = array();
        $returnData['allData'] = $productData;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
    }

    public function getProductDetailColorSize($post)
    {
       
        $whereConditions = ['p.product_id' => $post['id'],'p.status' => '1','p.is_delete' => '1','c.status' => '1','c.is_delete' => '1','pz.status' => '1','pz.is_delete' => '1'];
        $listArray = ['p.id','p.product_id','p.color_id','p.size_id','c.name as color','pz.name as sizeName'];

        $productColorSizeData = DB::table('product_color_size as p')
                         ->leftJoin('color as c', 'c.id', '=', 'p.color_id')
                         ->leftJoin('product_size as pz', 'pz.id', '=', 'p.size_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $whereProductData = ['id' => $post['id']];
        $productName = DB::table('products')->where($whereProductData)->get();

        $allDetail = array();
        if($post['design_id'] != 0) {
            $allDetail = $this->getPurchaseDetailColor($post['design_id']);
        }
       
       
        $all_array = array();

        foreach ($productColorSizeData as $key=>$alldata){
         $alldata->qnty =  0;

            if (!empty($allDetail)) {
                     
                    if(isset($allDetail[$alldata->color_id][$alldata->sizeName])){
                        $alldata->qnty = $allDetail[$alldata->color_id][$alldata->sizeName];
                    }
            } 
         
          $all_array[$alldata->color_id]['color_name'] = $alldata->color;
          $all_array[$alldata->color_id]['size_data'][] = $alldata;
        }


        $combine_array['productColorSizeData'] = $all_array;
        $combine_array['product_name'] = $productName[0]->name;
        $combine_array['product_id'] = $productName[0]->id;
        $combine_array['product_description'] = $productName[0]->description;

        return $combine_array;
    }


    public function addcolorsize($post)
    {
        if(!empty($post['product_id']))
        {

            if($post['color_id'] == 0) {
                 $result_color = DB::table('color')->insert([
                'company_id'=>$post['company_id'],
                'is_sns'=>0]);
                 $colorId = DB::getPdo()->lastInsertId();
            } else {
                $colorId = $post['color_id'];
            }


             $result_size = DB::table('product_size')->insert([
            'company_id'=>$post['company_id'],
            'is_sns'=>0]);
             $sizeId = DB::getPdo()->lastInsertId();

             $result_color_size = DB::table('product_color_size')->insert([
            'product_id'=>$post['product_id'],
            'color_id'=>$colorId,
            'size_id'=>$sizeId]);
             return true;
        }
        else
        {
            return false;
        }
    }

}
