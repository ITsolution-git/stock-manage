<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use DateTime;

class Company extends Model {


        public function __construct( Common $common)
          {
        $this->common = $common;
        }
    /**
     * login verify function
     *
     *
     */
    public function GetCompanyData() {
        $admindata = DB::table('users as usr')
        				 ->Join('roles as rol', 'usr.role_id', '=', 'rol.id')
        				 ->select('usr.name','usr.user_name','usr.email','usr.remember_token','usr.status','rol.title','usr.id','usr.phone')
        				 ->where('usr.is_delete','=','1')
                 ->where('rol.slug','=','CA')
                 ->orderBy('usr.id', 'desc')
                 ->get();
        return $admindata;
    }
    public function InsertCompanyData($post)
    {

      //echo "<pre>"; print_r($post); echo "</pre>"; die;
   
    	$result = DB::table('users')->insert(array('name'=>$post['name'],'email'=>$post['email'],'password'=>$post['password'],'oversize_value' => '0.50','role_id'=>$post['role_id'],'created_date'=>date('Y-m-d')));
       $user_array = $post;
        unset($post['email']);
        unset($post['password']);
        unset($post['name']);
        unset($post['role_id']);
        unset($post['parent_id']);
        unset($post['created_date']); 
        unset($post['status']);
        
     $companyid = DB::getPdo()->lastInsertId();
        $post['company_id'] = $companyid ;




        $result_company_detail = DB::table('staff')->insert($post);

/// default price grid code start 

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
                $link['name'] = $link['name'].' '.$user_array['name'];
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
               
          }

      $result_price_charge = DB::table('price_grid_charges')->insert($priceDataChargeArray);
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
               
               
          }

     $result_price_primary = DB::table('price_screen_primary')->insert($priceDataPrimaryArray);
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
              
               
          }
 $result_price_secondary = DB::table('price_screen_secondary')->insert($priceDataSecondaryArray);
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
              
          }

        $result_price_markup = DB::table('price_garment_mackup')->insert($priceDataGmArray);
               

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
          }
          $result_dg = DB::table('price_direct_garment')->insert($priceDataDgArray);

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
              
          }
           $result_embro = DB::table('embroidery_switch_count')->insert($priceDataEsArray);
           $switchId = DB::getPdo()->lastInsertId();

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
              
          }
           $result_screeen_embro = DB::table('price_screen_embroidery')->insert($priceDataEmbroArray);
               

// code end for price  grid Embroidery


/// default price grid code end  


// Code for Default Misc data Start
           
        $whereConditionsMisc = ['is_delete' => '1','company_id' => 0];
        $miscData = DB::table('misc_type')
                         ->select('*')
                         ->where($whereConditionsMisc)
                         ->get();
 
        $miscDataArray = json_decode(json_encode($miscData), true);
        
        foreach($miscDataArray as $Misckey => $misclink) 
        { 
                unset($miscDataArray[$Misckey]['id']);
               $miscDataArray[$Misckey]['company_id'] = $companyid;       
         }

        $result_misc = DB::table('misc_type')->insert($miscDataArray);

        $make_folder = $this->makefolder($companyid);  

        
// Code for Default Misc data End

    	return $companyid ;
    }

    public function makeFolder($companyid){

       $dir_path = base_path() . "/public/uploads/" . $companyid;

        
          $old_umask = umask(0);

           if (!file_exists($dir_path)) {
           
            mkdir($dir_path, 0777);

            } else {
                exec("chmod $dir_path 0777");
            }

         
          umask($old_umask);

         $dir_path_art = base_path() . "/public/uploads/" . $companyid.'/art'; 
         $dir_path_client = base_path() . "/public/uploads/" . $companyid.'/client';
         $dir_path_company = base_path() . "/public/uploads/" . $companyid.'/company';
         $dir_path_document = base_path() . "/public/uploads/" . $companyid.'/document';
         $dir_path_order = base_path() . "/public/uploads/" . $companyid.'/order';
         $dir_path_product = base_path() . "/public/uploads/" . $companyid.'/product';
         $dir_path_staff = base_path() . "/public/uploads/" . $companyid.'/staff';
         $dir_path_tax = base_path() . "/public/uploads/" . $companyid.'/tax'; 
         $dir_path_vendor = base_path() . "/public/uploads/". $companyid.'/vendor'; 
         $dir_path_pdf = base_path() . "/public/uploads/". $companyid.'/pdf'; 

          $old_umask = umask(0);

            if (!file_exists($dir_path_pdf)) {
           
              mkdir($dir_path_pdf, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

           if (!file_exists($dir_path_art)) {
           
            mkdir($dir_path_art, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_client)) {
           
            mkdir($dir_path_client, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_company)) {
           
            mkdir($dir_path_company, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_document)) {
           
            mkdir($dir_path_document, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

             if (!file_exists($dir_path_order)) {
           
            mkdir($dir_path_order, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

             if (!file_exists($dir_path_product)) {
           
            mkdir($dir_path_product, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_staff)) {
           
            mkdir($dir_path_staff, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_tax)) {
           
            mkdir($dir_path_tax, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_vendor)) {
           
            mkdir($dir_path_vendor, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

          umask($old_umask);
          return 1;


    }
    public function GetCompanybyId($id,$company_id)
    { 
      $whereConditions = ['usr.id' => $id,'usr.is_delete' => '1'];
    	$admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                 ->leftJoin('staff as st', 'usr.id', '=', 'st.user_id')
                 ->leftJoin('state as state', 'state.id', '=', 'st.prime_address_state')
        				 ->select('usr.name','usr.user_name','usr.email','usr.password','usr.remember_token','usr.status','usr.id','usr.role_id','usr.phone','st.prime_address1','st.prime_address_city','st.prime_address_state','st.prime_address_country','st.prime_address_zip','st.url','st.photo','st.user_id','st.oversize_value','st.tax_rate','st.id as staff_id','st.prime_phone_main','state.name as state_name')
        				 ->where($whereConditions)
        				 ->get();
        return $admindata;
    }
    public function SaveCompanyData($post)
    {

        $make_folder = $this->makefolder($post['id']);
        
        $new_post = array('prime_address1'=>$post['prime_address1'],'prime_address_city'=>$post['prime_address_city'],'prime_address_state'=>$post['prime_address_state'],'prime_address_country'=>$post['prime_address_country'],'prime_address_zip'=>$post['prime_address_zip'],'url'=>$post['url']);
    	
        unset($post['prime_address1']);
        unset($post['prime_address_city']);
        unset($post['prime_address_state']);
        unset($post['prime_address_country']);
        unset($post['prime_address_zip']);
        unset($post['url']);
        unset($post['photo']);
        unset($post['user_id']);

/*        if(isset($post['oversize_value']))
        {*/
            $new_post['oversize_value']=$post['oversize_value'];
            unset($post['oversize_value']);
/*        }

        if(isset($post['tax_rate']))
        {*/
            $new_post['tax_rate']=$post['tax_rate'];
            unset($post['tax_rate']);
        //}

            
        if(isset($post['company_url_photo']))
        {
            unset($post['company_url_photo']);
        }
        if(!empty($post['id']))
    	  {
        		$result = DB::table('users')->where('id','=',$post['id'])->update($post);

            $result_address = DB::table('staff')->where('user_id','=',$post['id'])->update($new_post);
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
      		DB::table('users')->where('id','=',$id)->update(array("is_delete" => '0'));
      		DB::table('users')->where('parent_id','=',$id)->update(array("is_delete" => '0'));
          DB::table('staff')->where('user_id','=',$id)->update(array("is_delete" => '0'));
      		return 1;
    	}
    	else
    	{
    		  return false;
    	}
    }
    public function getCompanyInfo($company_id)
    {
       $company_data = $this->common->GetTableRecords('company_info',array('user_id' => $company_id),array());
       if(count($company_data)==0)
       {
          $this->common->InsertRecords('company_info',array('user_id'=>$company_id));
       }
        $result = DB::table('users as u')
                  ->select('ci.*')
                  ->leftJoin('company_info as ci','ci.user_id','=','u.id')
                  ->where("u.id","=",$company_id)
                  ->get();
        return $result;
    }
    public function getAffiliate($company_id,$affilite_id)
    {
       $result = DB::table('affiliates as af')
                  ->select('af.*','pg.name as price_grid','pg.id as price_id')
                  ->leftJoin('price_grid as pg','pg.id','=','af.price_grid')
                  ->where("af.company_id","=",$company_id);
            
            if(!empty($affilite_id))
              {
                $result=  $result->where('af.id','=',$affilite_id);
              }
              $result = $result->get();

              if(count($result)>0)
              {
                  foreach ($result as $key => $value)
                  {
                        $result[$key]->screen_print=!empty($value->screen_print)? true:false;
                        $result[$key]->embroidery=!empty($value->embroidery)? true:false;
                        $result[$key]->packing=!empty($value->packing)? true:false;
                        $result[$key]->shipping=!empty($value->shipping)? true:false;
                        $result[$key]->art_work=!empty($value->art_work)? true:false;
                  }

              }
              
        return $result;
    }
    public function addAffilite($post)
    {
      $result = DB::table('affiliates')->insert($post);
      return $result;
    }
    public function UpdateAffilite($post,$id)
    {
       $result = DB::table('affiliates')->where('id',"=",$id)->update($post);
       return $result;
    }
     public function getAuthorizeAPI($company_id)
    {
        $result = DB::table('api_link_table as alt')
            ->select('ad.*')
            ->Join('authorize_detail as ad','ad.link_id','=','alt.id')
            ->where("alt.company_id","=",$company_id)
            ->where("alt.api_id","=",AUTHORIZED_ID)
            ->get();
        return $result;
    }
    public function InsertAuthorizeAPI($company_id)
    {
      $result  = DB::table('api_link_table')->insert(array("api_id"=>AUTHORIZED_ID,"company_id"=>$company_id));
      $link_id = DB::getPdo()->lastInsertId();
      $result  = DB::table('authorize_detail')->insert(array("link_id"=>$link_id));

      return $result;
    }
     public function getUpsAPI($company_id)
    {
        $result = DB::table('api_link_table as alt')
            ->select('ad.*')
            ->Join('ups_detail as ad','ad.link_id','=','alt.id')
            ->where("alt.company_id","=",$company_id)
            ->where("alt.api_id","=",UPS_ID)
            ->get();
        return $result;
    }
    public function InsertUpsAPI($company_id)
    {
      $result  = DB::table('api_link_table')->insert(array("api_id"=>UPS_ID,"company_id"=>$company_id));
      $link_id = DB::getPdo()->lastInsertId();
      $result  = DB::table('ups_detail')->insert(array("link_id"=>$link_id));

      return $result;
    }
     public function getSnsAPI($company_id)
    {
        $result = DB::table('api_link_table as alt')
            ->select('ad.*')
            ->Join('ss_detail as ad','ad.link_id','=','alt.id')
            ->where("alt.company_id","=",$company_id)
            ->where("alt.api_id","=",SNS_ID)
            ->get();
        return $result;
    }
    public function InsertSnsAPI($company_id)
    {
      $result  = DB::table('api_link_table')->insert(array("api_id"=>SNS_ID,"company_id"=>$company_id));
      $link_id = DB::getPdo()->lastInsertId();
      $result  = DB::table('ss_detail')->insert(array("link_id"=>$link_id));

      return $result;
    }
    
    
    

}
