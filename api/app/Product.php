<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Art;
class Product extends Model {


/**
* Product listing array           
* @access public productList
* @return array $productData
*/
    public function __construct(Art $art) 
    {
        $this->art = $art;
    }

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
        $search = '';

        //DB::statement('SET GLOBAL group_concat_max_len = 1000000');
        DB::statement('SET group_concat_max_len = 1000000');

        if(isset($data['where']['search']))
        {
            $search = $data['where']['search'];
        }

        $sql = DB::table('products as p')
                ->select(DB::raw('GROUP_CONCAT(id) as products'))
                ->leftJoin('client_product_supplied','p.id','=',DB::raw("client_product_supplied.product_id AND client_product_supplied.client_id = ".$data['where']['client_id']));

        if($data['where']['vendor_id'] == 1)
        {
            if($search != '')
            {
                $sql = $sql->orWhere(function($query) use($search)
                {
                    $query->orWhere('p.name', 'LIKE', '%'.$search.'%');
                    $query->orWhere('p.brand_name', 'LIKE', '%'.$search.'%');
                    $query->orWhere('p.id','=',$search);
                });
            }
        }
        else
        {
            if($search != '')
            {
                $sql = $sql->where('name', 'LIKE', '%'.$search.'%');
            }
        }

        $sql = $sql->where('vendor_id' , '=', $data['where']['vendor_id'])
                ->where('client_product_supplied.product_id','=',NULL)
                ->get();

        if(count($sql)>0)
        {
            $product_id_array = explode(",",$sql[0]->products);
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
            $listArray = [DB::raw('SQL_CALC_FOUND_ROWS p.id,p.name,p.brand_name,p.product_image,p.description,v.name_company as vendor_name,p.vendor_id,c.color_front_image,c.id as color_id')];
        }
        else
        {
            $listArray = [DB::raw('SQL_CALC_FOUND_ROWS p.name as product_name,p.brand_name,p.product_image,p.description,v.name_company as vendor_name,p.vendor_id,c.color_front_image,c.id as color_id')];
        }
        

        $orderData = DB::table('products as p')
                        ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                        ->leftJoin('product_color_size as pcs', 'p.id', '=', 'pcs.product_id')
                        ->leftJoin('color as c', 'pcs.color_id', '=', 'c.id')
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

         if(!isset($post['warehouse']))
            {
                $post['warehouse'] = '';
            }

/*        if(isset($post['is_supply'])) {
            $insert_array = array('design_id' => $post['id'],'product_id'=>$post['product_id'],'is_supply' => $post['is_supply'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));
        }
        else
        {
            $insert_array = array('design_id'=>$post['id'],'product_id'=>$post['product_id'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));
        }*/

        $insert_array = array('design_id' => $post['id'],'product_id'=>$post['product_id'],'is_supply' => $post['is_supply'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));

        if($post['action'] == 'Add') {
            $design_product_id = DB::table('design_product')->insertGetId($insert_array);
        }
        else
        {
            $insert_design = DB::table('design_product')
                            ->where('design_id', $post['id'])
                            ->where('product_id', $post['product_id'])
                            ->update($insert_array);

            //$design_product_id = $post['design_product_id'];
            $result_design = DB::table('design_product')
                            ->where('design_id', $post['id'])
                            ->where('product_id', $post['product_id'])
                            ->get();
            $design_product_id = $result_design[0]->id;
        }


       
    $delete = DB::table('purchase_detail')->where('design_product_id','=',$design_product_id)->delete();
 
    foreach($post['productData'] as $key_color=>$row_color) 
    {
        foreach ($row_color['sizes'] as $key_size => $row) 
        {
            $sku = 0;
            if(isset($row['sku'])) {
                $sku = $row['sku'];
            }

            if(!isset($row['qnty']))
            {
                $row['qnty'] = 0;
            }
            
            if(isset($row['customerPrice'])) {
                $price = $row['customerPrice'];
            }
            
            if(isset($row['customer_price'])) {
                $price = $row['customer_price'];
            }
            
            if($row['qnty'] > 0) 
            {
                $insert_purchase_array = array('design_id'=>$post['id'],
                'design_product_id'=>$design_product_id,
                'product_id'=>$post['product_id'],
                'size'=>$row['sizeName'],
                'sku'=>$sku,
                'price'=>$price,
                'qnty'=>$row['qnty'],
                'remaining_qnty'=>$row['qnty'],
                'color_id'=>$row['color_id'],
                'date'=>$post['created_date']);
            $result = DB::table('purchase_detail')->insert($insert_purchase_array);
            }
        }
    }
    return true;
}


    public function addProductCustom($post) {
         if(!isset($post['warehouse']))
            {
                $post['warehouse'] = '';
            }
/*        if(isset($post['is_supply'])) {
            $insert_array = array('design_id' => $post['id'],'product_id'=>$post['product_id'],'is_supply' => $post['is_supply'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));
        }
        else
        {
            $insert_array = array('design_id'=>$post['id'],'product_id'=>$post['product_id'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));
        }*/
        $insert_array = array('design_id' => $post['id'],'product_id'=>$post['product_id'],'is_supply' => $post['is_supply'],'warehouse'=>$post['warehouse'],'date_added' => date('Y-m-d h:i:sa'));
        if($post['action'] == 'Add') {
            $design_product_id = DB::table('design_product')->insertGetId($insert_array);
        }
        else
        {
            $insert_design = DB::table('design_product')
                            ->where('design_id', $post['id'])
                            ->where('product_id', $post['product_id'])
                            ->where('is_delete', '1')
                            ->update($insert_array);
            //$design_product_id = $post['design_product_id'];
            $result_design = DB::table('design_product')
                            ->where('design_id', $post['id'])
                            ->where('product_id', $post['product_id'])
                            ->where('is_delete', '1')
                            ->get();
            $design_product_id = $result_design[0]->id;
        }
       
        $delete = DB::table('purchase_detail')->where('design_product_id','=',$design_product_id)->delete();
        foreach($post['productData'] as $row) {
            $sku = 0;
            if(isset($row['sku'])) {
                $sku = $row['sku'];
            }
            if(!isset($row['qnty']))
            {
                $row['qnty'] = 0;
            }
            
            if(isset($row['customerPrice'])) {
                $price = $row['customerPrice'];
            }
            
            if(isset($row['customer_price'])) {
                $price = $row['customer_price'];
            }
            
            if($row['qnty'] > 0) {
                  $insert_purchase_array = array('design_id'=>$post['id'],
                'design_product_id'=>$design_product_id,
                'product_id'=>$post['product_id'],
                'size'=>$row['sizeName'],
                'sku'=>$sku,
                'price'=>$price,
                'qnty'=>$row['qnty'],
                'color_id'=>$row['color_id'],
                'date'=>$post['created_date']);
            $result = DB::table('purchase_detail')->insert($insert_purchase_array);
            
            }
            
              
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

     
        $where = ['od.id' => $data['id'],'dp.is_delete' => '1'];

        $listArray = ['p.id','p.brand_name','p.name as product_name','p.description','p.product_image','dp.avg_garment_cost','dp.avg_garment_price','dp.print_charges','dp.markup',
                        'dp.markup_default','dp.override','dp.override_diff','dp.sales_total','dp.total_line_charge','dp.is_supply','dp.is_calculate','v.name_company',
                        'c.name as color_name','dp.id as design_product_id','c.id as color_id','p.vendor_id','dp.design_id','p.company_id','od.order_id','dp.size_group_id','dp.warehouse','c.color_front_image'];

        $productData = DB::table('order_design as od')
                         ->leftJoin('design_product as dp', 'od.id', '=', 'dp.design_id')
                         ->leftJoin('products as p', 'dp.product_id', '=', 'p.id')
                         ->leftJoin('vendors as v', 'p.vendor_id', '=', 'v.id')
                         ->leftJoin('purchase_detail as pcs', 'dp.id', '=', 'pcs.design_product_id')
                         ->leftJoin('color as c', 'pcs.color_id', '=', 'c.id')
                         ->select($listArray)
                         ->where($where)
                         ->GroupBy('dp.product_id')
                         ->orderBy('dp.id','desc')
                         ->get();

        $combine_array = array();

        if(!empty($productData) && $productData[0]->id > 0)
        {
            $total_price = 0;
            $total_product = 0;
            foreach ($productData as $product) {

                $find = 'supplied';
                $product->supplied = 0;

                $product_name = strtolower($product->product_name);

                if (strpos($product_name,$find) !== false) {
                    $product->supplied = 1;
                }

                $sizeData = DB::table('purchase_detail as pd')
                                     ->where('pd.design_product_id','=',$product->design_product_id)
                                     ->select('c.name as color_name','pd.*')
                                     ->leftJoin('color as c', 'pd.color_id', '=', 'c.id')
                                     ->get();
                

                $product->total_qnty = 0;
                $product->total_price = 0;
                $product->sizeData = array();
                foreach ($sizeData as $size) {
                    $product->total_price += $size->qnty * $size->price;
                    $product->total_qnty += $size->qnty;
                    $product->sizeData[$size->color_name][] = $size;
                }
                $product->total_price = round($product->total_price,2);

                $total_price += $product->total_price;
                $total_product += $product->total_qnty;

                if($product->vendor_id >1){
                   // $product->product_image_view = UPLOAD_PATH.$product->company_id."/products/".$product->id."/".$product->product_image;
                    $product->product_image_view = UPLOAD_PATH.$product->company_id."/custom_image/".$product->color_id."/".$product->color_front_image;
                } else {
                    $product->product_image_view = "https://www.ssactivewear.com/".$product->product_image;
                }
                
                $combine_array['productData'][$product->id] = $product;

                $whereConditions = ['order_id' => $product->order_id,'design_id' => $product->design_id,'product_id'=>$product->id];
                $items = DB::table('order_item_mapping')->where($whereConditions)->get();

                $order = DB::table('orders')->where('id','=',$product->order_id)->get();

                $whereConditions = ['price_id' => $order[0]->price_id,'is_per_order' => '1'];
                $order_items = DB::table('price_grid_charges')->where($whereConditions)->get();

                $product_finishing_data = array();
                foreach ($order_items as $order_item)
                {
                    $i = 0;
                    foreach ($items as $item)
                    {
                        if($item->item_id == $order_item->id)
                        {
                            $i = 1;
                        }
                    }
                    
                    if($i == 1)
                    {
                        $order_item->selected = '1';
                        $product->order_items[] = $order_item;
                    }
                    else
                    {
                        $order_item->selected = '0';
                        $product->order_items[] = $order_item;
                    }
                }
            }
            $combine_array['total_product'] = $total_product;
            $combine_array['total_price'] = $total_price;
        }
        else
        {
            return array();
        }

        return $combine_array;
    }

    public function getPurchaseDetail($design_product_id) {

    $whereConditions = ['is_delete' => "1",'design_product_id' => $design_product_id];

    $result = DB::table('purchase_detail')->where($whereConditions)->get();

        
        $purchaseDetail = array();
        foreach ($result as $key=>$alldata){
          
                 $purchaseDetail[$alldata->color_id][$alldata->size] = $alldata->qnty;
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
       

        $whereConditions = ['product.is_delete' => "1",'v.is_delete' => '1','v.company_id' => $post['company_id']];

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS product.*,v.name_company')];

        $productData = DB::table('products as product')
                         ->leftJoin('vendors as v', 'v.id', '=', 'product.vendor_id')
                         ->select($listArray)
                         ->where($whereConditions);
                        
                        if($search != '')
                        {
                          $productData = $productData->Where(function($query) use($search)
                          {
                              $query->orWhere('product.name', 'LIKE', '%'.$search.'%');
                              $query->orWhere('product.id', '=', $search);
                               $query->orWhere('v.name_company', 'LIKE', '%'.$search.'%');

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
        $listArray = ['p.id','p.product_id','p.customer_price','p.color_id','p.size_id','c.name as color','pz.name as sizeName','c.color_front_image','c.id as color_id'];

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
                        $alldata->qnty = (int)$allDetail[$alldata->color_id][$alldata->sizeName];
                    }
            } 
         
          $all_array[$alldata->color_id]['color_name'] = $alldata->color;
          $all_array[$alldata->color_id]['id'] = $alldata->id;
          $all_array[$alldata->color_id]['color_front_image'] = $alldata->color_front_image;
          $all_array[$alldata->color_id]['color_front_image_url_photo'] = (!empty($alldata->color_front_image))?UPLOAD_PATH.$post['company_id'].'/custom_image/'.$alldata->color_id."/".$alldata->color_front_image:'';
          $all_array[$alldata->color_id]['size_data'][] = $alldata;
        }


        $combine_array['product_image_url'] = UPLOAD_PATH.$post['company_id']."/products/".$productName[0]->id."/".$productName[0]->product_image;
        $combine_array['productColorSizeData'] = $all_array;
        $combine_array['vendor_id'] = $productName[0]->vendor_id;
        $combine_array['product_name'] = $productName[0]->name;
        $combine_array['product_id'] = $productName[0]->id;
        $combine_array['product_description'] = $productName[0]->description;
        $combine_array['product_image'] = $productName[0]->product_image;
        


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

    public function getProductCountByVendor($vendor_id)
    {
        $count = DB::table('products')
                         ->select(DB::raw('COUNT(id) as total'))
                         ->where('vendor_id','=',$vendor_id)
                         ->get();
        
        return $count[0]->total;
    }

    public function getVendorByProductCount($company_id)
    {
        $listArray = ['v.id','v.name_company',''];
        $vendorData = DB::table('vendors as v')
                        ->leftJoin('products as p', 'p.vendor_id', '=', 'v.id')
                        ->select(DB::raw('COUNT(DISTINCT p.id) as total'),'v.id','v.name_company')
                        ->where('v.company_id' , '=', $company_id)
                        ->where('v.is_delete' , '=', '1')
                        ->where('p.is_delete' , '=', '1')
                        ->orWhere('v.company_id' , '=', '0')
                        ->GroupBy('v.id')
                        ->get();

        return $vendorData;
    }

    public function getAffiliateDesignProduct($design_id)
    {
        $whereConditions = ['dp.design_id' => $design_id,'dp.assign_to_affiliate' => '0'];
        $productData = DB::table('products as p')
                         ->leftJoin('design_product as dp', 'p.id', '=', 'dp.product_id')
                         ->select('p.name as product_name','dp.id as design_product_id')
                         ->where($whereConditions)
                         ->get();
        
        return $productData;
    }

    public function getSnsProductDetail($id) {

     
        $where = ['od.order_id' => $id,'dp.is_delete' => '1','pcs.sku' => '!=0','pcs.is_distribute' => '0','pcs.qnty' => '>0'];
      
        $listArray = ['pcs.*','dp.warehouse'];

        $productData = DB::table('order_design as od')
                         ->leftJoin('design_product as dp', 'od.id', '=', 'dp.design_id')
                         ->leftJoin('purchase_detail as pcs', 'dp.id', '=', 'pcs.design_product_id')
                         ->select($listArray)
                         ->where('od.order_id','=',$id)
                         ->where('dp.is_delete','=','1')
                         ->where('pcs.sku','!=','0')
                         ->where('pcs.is_distribute','=','0')
                         ->where('pcs.qnty','>','0')
                         ->get();
        
        return $productData;
    }

    public function productListDownload($company_id) {
        
        $whereConditions = ['products.is_delete' => '1','vendors.is_delete' => '1','vendors.company_id' => $company_id];
        $listArray = ['products.name as PRODUCT NAME','products.description as DESCRIPTION','vendors.name_company as VENDOR','c.name as COLOR','pz.name as SIZE','pcs.customer_price as PRICE'];

        $vendorData = DB::table('products as products')
                         ->leftJoin('vendors as vendors', 'products.vendor_id', '=', 'vendors.id')
                         ->leftJoin('product_color_size as pcs', 'products.id', '=', 'pcs.product_id')
                         ->leftJoin('color as c', 'c.id', '=', 'pcs.color_id')
                         ->leftJoin('product_size as pz', 'pz.id', '=', 'pcs.size_id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->orderBy('products.id', 'desc')
                         ->get();

        return $vendorData;
    }
}
