<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Company extends Model {

    /**
     * login verify function
     *
     *
     */
    public function GetCompanyData() {
        $admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                         
        				 ->select('usr.name','usr.user_name','usr.email','usr.remember_token','usr.status','rol.title','usr.id')
        				 ->where('usr.is_delete','=','1')
                         ->where('rol.slug','=','CA')
                         ->get();
        return $admindata;
    }
    public function InsertCompanyData($post)
    {

     
        if(!isset($post['company_logo'])) {
            $post['company_logo'] = '';
        }
        $new_post = array('address'=>$post['address'],'city'=>$post['city'],'state'=>$post['state'],'country'=>$post['country'],'zip'=>$post['zip'],'url'=>$post['url'],'company_logo'=>$post['company_logo']);
        
        unset($post['address']);
        unset($post['city']);
        unset($post['state']);
        unset($post['country']);
        unset($post['zip']);
        unset($post['url']);
        unset($post['company_logo']);

    	$result = DB::table('users')->insert($post);
        $companyid = DB::getPdo()->lastInsertId();

       

        $companyid_array = array('company_id'=>$companyid);
        $result_array = array_merge((array)$new_post, (array)$companyid_array); 



        $result_company_detail = DB::table('company_detail')->insert($result_array);


/// Add default price grid code start 

       $whereConditions = ['is_delete' => '1','company_id' => 0];
       
        $priceData = DB::table('price_grid')
                         ->select('*')
                         ->where($whereConditions)
                         ->get();
 
        $priceDataArray = json_decode(json_encode($priceData), true);
     
        foreach($priceDataArray as $key => $link) 
        { 
              
                $old_price_id = $link['id'];
                unset($link['id']);
                $link['name'] = $link['name'].' '.$post['user_name'];
                $link['created_date'] = date("Y-m-d H:i:s");
                $link['updated_date'] = date("Y-m-d H:i:s");
                $link['company_id'] = $companyid;
                $result = DB::table('price_grid')->insert($link);
                $priceid = DB::getPdo()->lastInsertId();
         }


// code start for price grid charge
        $wherePriceGridCharge = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataCharge = DB::table('price_grid_charges')
                         ->select('*')
                         ->where($wherePriceGridCharge)
                         ->get();

         $priceDataChargeArray = json_decode(json_encode($priceDataCharge), true);
        
          foreach($priceDataChargeArray as $keyPrice => $linkArray) 
          { 
               unset($priceDataChargeArray[$keyPrice]['id']);
               $priceDataChargeArray[$keyPrice]['price_id'] = $priceid;
               $priceDataChargeArray[$keyPrice]['created_date'] = date("Y-m-d H:i:s");
               $priceDataChargeArray[$keyPrice]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_grid_charges')->insert($priceDataChargeArray[$keyPrice]);
               
          }

// code end for price grid charge 

// code start for price grid Primary
        $wherePriceGridPrimary = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataPrimary = DB::table('price_screen_primary')
                         ->select('*')
                         ->where($wherePriceGridPrimary)
                         ->get();

         $priceDataPrimaryArray = json_decode(json_encode($priceDataPrimary), true);
        
          foreach($priceDataPrimaryArray as $keyPrmary => $linkArray) 
          { 
               unset($priceDataPrimaryArray[$keyPrmary]['id']);
               $priceDataPrimaryArray[$keyPrmary]['price_id'] = $priceid;
               $priceDataPrimaryArray[$keyPrmary]['created_date'] = date("Y-m-d H:i:s");
               $priceDataPrimaryArray[$keyPrmary]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_screen_primary')->insert($priceDataPrimaryArray[$keyPrmary]);
               
          }

// code end for price grid Primary 


// code start for price grid Secondary
        $wherePriceGridSecondary = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataSecondary = DB::table('price_screen_secondary')
                         ->select('*')
                         ->where($wherePriceGridSecondary)
                         ->get();

         $priceDataSecondaryArray = json_decode(json_encode($priceDataSecondary), true);
        
          foreach($priceDataSecondaryArray as $keySecondary => $linkArray) 
          { 
               unset($priceDataSecondaryArray[$keySecondary]['id']);
               $priceDataSecondaryArray[$keySecondary]['price_id'] = $priceid;
               $priceDataSecondaryArray[$keySecondary]['created_date'] = date("Y-m-d H:i:s");
               $priceDataSecondaryArray[$keySecondary]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_screen_secondary')->insert($priceDataSecondaryArray[$keySecondary]);
               
          }

// code end for price grid Secondary

// code start for price grid garment markup
        $wherePriceGridGm = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataGm = DB::table('price_garment_mackup')
                         ->select('*')
                         ->where($wherePriceGridGm)
                         ->get();

         $priceDataGmArray = json_decode(json_encode($priceDataGm), true);
        
          foreach($priceDataGmArray as $keyGm => $linkArray) 
          { 
               unset($priceDataGmArray[$keyGm]['id']);
               $priceDataGmArray[$keyGm]['price_id'] = $priceid;
               $priceDataGmArray[$keyGm]['created_date'] = date("Y-m-d H:i:s");
               $priceDataGmArray[$keyGm]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_garment_mackup')->insert($priceDataGmArray[$keyGm]);
               
          }

// code end for price garment markup

// code start for price grid direct garment
        $wherePriceGridDg = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataDg = DB::table('price_direct_garment')
                         ->select('*')
                         ->where($wherePriceGridDg)
                         ->get();

         $priceDataDgArray = json_decode(json_encode($priceDataDg), true);
        
          foreach($priceDataDgArray as $keyDg => $linkArray) 
          { 
               unset($priceDataDgArray[$keyDg]['id']);
               $priceDataDgArray[$keyDg]['price_id'] = $priceid;
               $priceDataDgArray[$keyDg]['created_date'] = date("Y-m-d H:i:s");
               $priceDataDgArray[$keyDg]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_direct_garment')->insert($priceDataDgArray[$keyDg]);
               
          }

// code end for price direct garment

// code start for price grid Embroidery switch count
        $wherePriceGridEs = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataEs = DB::table('embroidery_switch_count')
                         ->select('*')
                         ->where($wherePriceGridEs)
                         ->get();

         $priceDataEsArray = json_decode(json_encode($priceDataEs), true);
        
          foreach($priceDataEsArray as $keyEs => $linkArray) 
          { 
               unset($priceDataEsArray[$keyEs]['id']);
               $priceDataEsArray[$keyEs]['price_id'] = $priceid;
               $priceDataEsArray[$keyEs]['created_date'] = date("Y-m-d H:i:s");
               $priceDataEsArray[$keyEs]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('embroidery_switch_count')->insert($priceDataEsArray[$keyEs]);
               $switchId = DB::getPdo()->lastInsertId();
          }

// code end for price  Embroidery switch count


// code start for price grid Embroidery
        $wherePriceGridEmbro = ['is_delete' => '1','price_id' => $old_price_id];
       
        $priceDataEmbro = DB::table('price_screen_embroidery')
                         ->select('*')
                         ->where($wherePriceGridEmbro)
                         ->get();

         $priceDataEmbroArray = json_decode(json_encode($priceDataEmbro), true);
        
          foreach($priceDataEmbroArray as $keyEmbro => $linkArray) 
          { 
               unset($priceDataEmbroArray[$keyEmbro]['id']);
               $priceDataEmbroArray[$keyEmbro]['price_id'] = $priceid;
               $priceDataEmbroArray[$keyEmbro]['embroidery_switch_id'] = $switchId;
               $priceDataEmbroArray[$keyEmbro]['created_date'] = date("Y-m-d H:i:s");
               $priceDataEmbroArray[$keyEmbro]['updated_date'] = date("Y-m-d H:i:s");
               $result_price = DB::table('price_screen_embroidery')->insert($priceDataEmbroArray[$keyEmbro]);
               
          }

// code end for price  grid Embroidery


/// Add default price grid code end          

    	return $result;
    }
    public function GetCompanybyId($id,$company_id)
    {
        $whereConditions = ['usr.id' => $id,'usr.is_delete' => '1','usr.role_id' => $company_id];
    	$admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                         ->leftJoin('company_detail as com', 'usr.id', '=', 'com.company_id')
        				 ->select('usr.name','usr.user_name','usr.email','usr.password','usr.remember_token','usr.status','usr.id','usr.role_id','com.address','com.city','com.state','com.country','com.zip','com.url','com.company_logo')
        				 ->where($whereConditions)
        				 ->get();
        return $admindata;
    }
    public function SaveCompanyData($post)
    {


        $new_post = array('address'=>$post['address'],'city'=>$post['city'],'state'=>$post['state'],'country'=>$post['country'],'zip'=>$post['zip'],'url'=>$post['url'],'company_logo'=>$post['company_logo']);
    	
        unset($post['address']);
        unset($post['city']);
        unset($post['state']);
        unset($post['country']);
        unset($post['zip']);
        unset($post['url']);
        unset($post['company_logo']);
        
        if(isset($post['company_url_photo'])){
         unset($post['company_url_photo']);
        }

         
        if(!empty($post['id']))
    	{
    		$result = DB::table('users')->where('id','=',$post['id'])->update($post);
            $result_address = DB::table('company_detail')->where('company_id','=',$post['id'])->update($new_post);
    		return $result;

           
    	}
    	else
    	{
    		return 0;
    	}
    }
    public function DeleteCompanyData($id)
    {
    	if(!empty($id))
    	{
    		$result = DB::table('users')->where('id','=',$id)->update(array("is_delete" => '0'));
    		$result = DB::table('users')->where('parent_id','=',$id)->update(array("is_delete" => '0'));
            $result = DB::table('company_detail')->where('company_id','=',$id)->update(array("is_delete" => '0'));
    		return 1;
    	}
    	else
    	{
    		return false;
    	}
    }


}
