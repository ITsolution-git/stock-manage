<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Product;
use App\Common;
use App\Api;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class ProductController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Product $product,Common $common,Api $api) {

        $this->product = $product;
        $this->common = $common;
        $this->api = $api;
       
    }

/**
* Product Listing controller        
* @access public index
* @return json data
*/


/**
* Product Listing controller        
* @access public index
* @return json data
*/


/** 
 * @SWG\Definition(
 *      definition="productList",
 *      type="object",
 *     
 *      @SWG\Property(
 *          property="cond",
 *          type="object",
 *          required={"company_id"},
 *          @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *         )
 *
 *      )
 *  )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/admin/product",
 *  summary = "Product Listing",
 *  tags={"Product"},
 *  description = "Product Listing",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Product Listing",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/productList")
 *  ),
 *  @SWG\Response(response=200, description="Product Listing"),
 *  @SWG\Response(response="default", description="Product Listing"),
 * )
 */

    public function index() {
 $post = Input::all();
        $result = $this->product->productList($post);
       
       
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        
        return response()->json(["data" => $response]);
    }

/**
* Product Delete controller      
* @access public delete
* @param  array $post
* @return json data
*/
    public function delete()
    {
        $post = Input::all();
      
        if(!empty($post[0]))
        {

            $getData = $this->product->productDelete($post[0]);
            if($getData)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

/**
* Product Detail controller      
* @access public detail
* @param  array $data
* @return json data
*/
    public function detail() {
 
         $data = Input::all();
         
          $result = $this->product->productDetail($data);


          $result['product'][0]->all_url_photo = UPLOAD_PATH.$data['company_id'].'/product/'.$result["product"][0]->id.'/'.$result['product'][0]->photo;

           if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result['product']);
            } else {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result['product']);
            }
        
        return response()->json(["data" => $response]);

    }

/**
* Making the directory and given path     
* @access public create_dir
* @param  string $dir_path
*/

public function create_dir($dir_path) {

        if (!file_exists($dir_path)) {
            mkdir($dir_path, 0777, true);
        } else {
           exec("chmod $dir_path 0777");
        }
    }

    public function getProductByVendor()
    {
        $post_all = Input::all();
        //print_r($post_all);exit;
        $records = array();

        $post = $post_all['cond']['params'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = 15;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'p.name';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'p.name';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $whereData = array();
        
        $whereData['vendor_id'] = $post['filter']['vendor_id'];

        if($post['filter']['vendor_id'] != '')
        {
            $whereData['search'] = $post['filter']['search'];
        }
        if(isset($post['filter']['category_id']) && !empty($post['filter']['category_id']))
        {
            $whereData['category_id'] = $post['filter']['category_id'];
        }
        if(isset($post['filter']['color_id']) && !empty($post['filter']['color_id']))
        {
            $whereData['color_id'] = $post['filter']['color_id'];
        }
        if(isset($post['filter']['size_id']) && !empty($post['filter']['size_id']))
        {
            $whereData['size_id'] = $post['filter']['size_id'];
        }

        $data['where'] = $whereData;
        $data['paginate'] = $post;
        $data['fields'] = array();
        $header = array();
        
        $result = $this->product->getVendorProducts($data);
        $count = (empty($result['count']))?'1':$result['count'];

        if(empty($result['count']))
        {
            $success = 0;
        }
        else
        {
            $success = 1;
        }

        //$count = $result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $count);

        
        if (count($result) > 0) {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } else {
           
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }


        $data = array('header'=>$header,'rows' => $result['allData'],'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'category_filter' => $result['category_data'],'color_filter' => $result['color_data'],'size_filter' => $result['size_data'],'success'=>$success);
        return  response()->json($data);
    }

    public function productDetailData() {
 
        $data = Input::all();
        $result_api = $this->api->getApiCredential($data['company_id'],'api.sns','ss_detail');
       
       // print_r($result_api[0]->password);exit;
        $credential = $result_api[0]->username.":".$result_api[0]->password;
 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.ssactivewear.com/v2/products/?style=".$data['product_id']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERPWD,$credential);
        $result = curl_exec($curl);
        curl_close($curl);

       $all_data = json_decode($result);
       

       $allDetail = array();
       if($data['design_id'] != 0) {
        $allDetail = $this->product->getPurchaseDetail($data['design_id']);
       }

        foreach($all_data as $key => $data) {
             
           
            $color_data = $this->common->getColorId($data->colorName);
            
           if($key == 0) {
             $productAllData['colorSelection'] = $data->colorName;
           }

            $productAllData['colorData'][$data->colorName]['sizes'][$key]['color_id'] = $color_data[0]->id;

            if(count($allDetail) > 0) {
                
                if(isset($allDetail[$data->sizeName])){
                     $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)$allDetail[$data->sizeName];
                }
               
            } else {
                $productAllData['colorData'][$data->colorName]['sizes'][$key]['qnty'] = (int)0;
            }
            
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['sizeName'] = $data->sizeName;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['sku'] = $data->sku;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['caseQty'] = $data->caseQty;
            $productAllData['colorData'][$data->colorName]['colorSwatchImage'] = $data->colorSwatchImage;
            $productAllData['colorData'][$data->colorName]['colorSwatchTextColor'] = $data->colorSwatchTextColor;
            $productAllData['colorData'][$data->colorName]['sizes'][$key]['customerPrice'] = $data->customerPrice;
            $productAllData['colorData'][$data->colorName]['colorFrontImage'] = $data->colorFrontImage;
            $productAllData['colorData'][$data->colorName]['colorSideImage'] = $data->colorSideImage;
            $productAllData['colorData'][$data->colorName]['colorBackImage'] = $data->colorBackImage;
            $productAllData['colorData'][$data->colorName]['colorName'] = $data->colorName;
        }
       
        return response()->json(["data" => $productAllData]);
        

    }
    

    public function getCustomProduct() {

       $post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];
        $post['company_id'] = $post_all['cond']['company_id'];

        if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }

        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'product.id';
        }

        $sort_by = $post['sorts']['sortBy'] ? $post['sorts']['sortBy'] : 'product.id';
        $sort_order = $post['sorts']['sortOrder'] ? $post['sorts']['sortOrder'] : 'desc';

        $result = $this->product->getCustomProduct($post);
        

        $records = $result['allData'];
        $success = (empty($result['count']))?'0':1;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => 7,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'product.id', 'name' => 'ID'),
                        1=>array('key' => 'product.name', 'name' => 'Name')
                        
                        );


            $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$sort_by,'sortOrder' => $sort_order,'success' => $success);
        return  response()->json($data);


    }

    public function addProduct() {

        $post = Input::all();
        $post['created_date']=date('Y-m-d');
        $record_delete = $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['id']));
        $post['record_delete']=$record_delete;
        $result = $this->product->addProduct($post);
        $message = INSERT_RECORD;
        $success = 1;
       
        
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

     public function designProduct() {
 
        $data = Input::all();
        $result = $this->product->designProduct($data);

     
        if(empty($result['design_product']))
        {

           $response = array(
                                'success' => 0, 
                                'message' => NO_RECORDS
                                ); 
           return response()->json(["data" => $response]);
        }

       if($result['product_id']) {

         $productArray = ['id' => $result['product_id']];
         $result_product = $this->product->productDetail($productArray);
        
       }
       
       
            $response = array(
                                'success' => 1, 
                                'message' => GET_RECORDS,
                                'records' => $result['design_product'],
                                'productData' => $result_product,
                                'colorName' => $result['colorName']
                                );
        
        return response()->json(["data" => $response]);

    }

     public function deleteAddProduct()
    {
        $post = Input::all();
       
        if(!empty($post['id']))
        {
            $record_data = $this->common->DeleteTableRecords('purchase_detail',array('design_id' => $post['id']));
            $record_delete = $this->common->DeleteTableRecords('design_product',array('design_id' => $post['id']));
            if($record_data)
            {
                $message = DELETE_RECORD;
                $success = 1;
            }
            else
            {
                $message = MISSING_PARAMS;
                $success = 0;
            }
        }
        else
        {
            $message = MISSING_PARAMS;
            $success = 0;
        }
        $data = array("success"=>$success,"message"=>$message);
        return response()->json(['data'=>$data]);

    }

    public function uploadCSV()
    {
        $post = Input::all();
       
       if(isset($post["file"])){

        $filename=$_FILES["file"]["tmp_name"];



         if($_FILES["file"]["size"] > 0)
         {
            $file = fopen($filename, "r");
            $k=1;
            $product_arr = array();
            

            while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
            {
               $product_arr['product_name'] = $emapData[0];
                
                /*$sql = "SELECT id FROM brand WHERE brand_name = '".$emapData[2]."'";
                $query = mysql_query($sql);
                if(mysql_num_rows($query) > 0)
                {
                    while ($branddata = mysql_fetch_array($query)) {

                        $brand_id = $branddata['id'];
                    }
                }
                else
                {
                    $brand_query = "INSERT INTO brand SET brand_name = '".$emapData[2]."',brand_image = '".$emapData[12]."' ";
                    mysql_query($brand_query);
                    $brand_id = mysql_insert_id();
                }

                $sub_query = "INSERT INTO products SET id = '".$emapData[0]."',brand_id = '".$brand_id."',name = '".mysql_real_escape_string($emapData[4])."',description = '".$emapData[5]."',
                                    product_image = '".$emapData[13]."' ";
                //mysql_query($sub_query);

                if($emapData[7] != '')
                {
                    $category_data = explode(',', $emapData[7]);

                    foreach ($category_data as $category_id) {
                        $map_query = "INSERT INTO product_brand_category SET product_id = '".$emapData[0]."',category_id = '".$category_id."' ";
                        mysql_query($map_query);
                    }
                }
                $k++;*/
            }
            fclose($file);
            echo "complete";
            exit;
        }
    }    


      

    }
    
}