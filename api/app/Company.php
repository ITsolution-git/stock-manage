<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use App\Login;
use Mail;
use DateTime;

class Company extends Model {


        public function __construct( Common $common, Login $login)
          {
        $this->common = $common;
        $this->login = $login;
        }
    /**
     * login verify function
     *
     *
     */
    public function GetCompanyData($post) {
       $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $admindata = DB::table('users as usr')
                 ->Join('roles as rol', 'usr.role_id', '=', 'rol.id')
                 ->select(DB::raw('SQL_CALC_FOUND_ROWS usr.name,usr.created_date,usr.user_name,usr.email,usr.remember_token,usr.status,rol.title,usr.id,usr.phone'))
                 ->where('usr.is_delete','=','1')
                 ->where('rol.slug','=','CA')
                 ->where('usr.parent_id','=','1');
                 if($search != '')               
                  {
                      $admindata = $admindata->Where(function($query) use($search)
                      {
                          $query->orWhere('usr.name', 'LIKE', '%'.$search.'%')
                                ->orWhere('usr.email','LIKE', '%'.$search.'%');
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
        
        if($count[0]->Totalcount>0)
        {
          foreach ($admindata as $key=>$value) 
          {
            $admindata[$key]->created_date =date('m/d/Y',strtotime($value->created_date)) ;
          }
        }
        
        return $returnData;

    }
    public function InsertCompanyData($post)
    {
 
      //echo "<pre>"; print_r($post); echo "</pre>"; die;
      $string = $this->login->getString(6);
      $result = DB::table('users')->insert(array('name'=>$post['name'],'parent_id'=>$post['parent_id'],'email'=>$post['email'],'password'=>md5($string),'role_id'=>$post['role_id'],'created_date'=>date('Y-m-d')));
       $user_array = $post;
      
      $post['prime_address1']       = !empty($post['prime_address1'])?$post['prime_address1']:'';
      $post['prime_address_street'] = !empty($post['prime_address_street'])?$post['prime_address_street']:'';
      $post['prime_address_city']   = !empty($post['prime_address_city'])?$post['prime_address_city']:'';
      $post['prime_address_state']  = !empty($post['prime_address_state'])?$post['prime_address_state']:'';
      $post['prime_address_zip']    = !empty($post['prime_address_zip'])?$post['prime_address_zip']:'';
      $post['prime_phone_main']     = !empty($post['prime_phone_main'])?$post['prime_phone_main']:'';
      $post['url']                 = !empty($post['url'])?$post['url']:'';


        $post['oversize_value'] = 0.50;
        $companyid = DB::getPdo()->lastInsertId();
        
        $post['company_id'] = $companyid ;


        $result_company_detail = DB::table('staff')->insert(array('user_id'=>$companyid,'oversize_value'=>OVERSIZE_VALUE,'tax_rate'=>TAX_RATE,
                  'created_date'=>date('Y-m-d'),
                  'prime_address1'=>$post['prime_address1'],
                  'prime_address_street'=>$post['prime_address_street'],
                  'prime_address_city'=>$post['prime_address_city'],
                  'prime_address_state'=>$post['prime_address_state'],
                  'prime_address_zip'=>$post['prime_address_zip'],
                  'prime_phone_main'=>$post['prime_phone_main'],
                  'url'=>$post['url'])
                );

        // SEND MAIL TO COMPANY WITH PASSWORD
        $email = $post['email'];
        Mail::send('emails.newcompany', ['password' =>$string,'user'=>$post['name'],'email'=>$email], function($message) use ($email) 
                {
                    $message->to($email, 'New Stokkup Account')->subject('New Account for Stokkup');
                });
        
               
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
         $dir_path_purchase = base_path() . "/public/uploads/". $companyid.'/purchase';
         $dir_path_custom_image= base_path() . "/public/uploads/". $companyid.'/custom_image';

          $old_umask = umask(0);

            if (!file_exists($dir_path_pdf)) {
           
              mkdir($dir_path_pdf, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }

            if (!file_exists($dir_path_custom_image)) {
           
              mkdir($dir_path_custom_image, 0777);
            
            } else {
                exec("chmod $dir_path 0777");
            }
            
            if (!file_exists($dir_path_purchase)) {
           
            mkdir($dir_path_purchase, 0777);
            
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
        				 ->select('usr.name','usr.user_name','usr.email','usr.password','usr.profile_photo','usr.remember_token','usr.status','usr.id','usr.role_id','usr.phone','st.first_name','st.last_name','st.prime_address1','st.prime_address_city','st.prime_address_state','st.prime_address_country','st.prime_address_zip','st.url','st.photo','st.user_id','st.oversize_value','st.tax_rate','st.cron_runtime','st.id as staff_id','st.prime_phone_main','st.gross_year','state.name as state_name')
        				 ->where($whereConditions)
        				 ->get();
        return $admindata;
    }
    public function SaveCompanyData($post)
    {

        $make_folder = $this->makefolder($post['id']);
        
        $new_post = array('prime_address1'=>$post['prime_address1'],'prime_address_city'=>$post['prime_address_city'],'prime_address_state'=>$post['prime_address_state'],'prime_phone_main'=>$post['prime_phone_main'],'prime_address_street'=>$post['prime_address_street'],'prime_address_zip'=>$post['prime_address_zip'],'url'=>$post['url']);


        $result = DB::table('users')->where('id','=',$post['id'])->update(array('name'=>$post['name'],'email'=>$post['email']));

        $result_address = DB::table('staff')->where('user_id','=',$post['id'])->update($new_post);
        return $result;

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

    public function getApiDetail($api_id,$table,$company_id)
    {
        $result = DB::table('api_link_table as alt')
            ->select('fd.*')
            ->Join($table.' as fd','fd.link_id','=','alt.id')
            ->where("alt.company_id","=",$company_id)
            ->where("alt.api_id","=",$api_id)
            ->get();
        return $result;
    }

    public function getColors($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $colordata = DB::table('color as cl')
                 ->select(DB::raw('SQL_CALC_FOUND_ROWS *'))
                 ->where('cl.is_delete','=','1')
                 ->where('cl.is_sns','=','0');
                 if($search != '')               
                  {
                      $colordata = $colordata->Where('cl.name', 'LIKE', '%'.$search.'%');
                  }
                 $colordata = $colordata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $colordata;
        $returnData['count'] = $count[0]->Totalcount;

        return $returnData;
    }
    public function getSizes($post)
    {
      $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $colordata = DB::table('product_size as pz')
                 ->select(DB::raw('SQL_CALC_FOUND_ROWS *'))
                 ->where('pz.is_delete','=','1')
                 ->where('pz.is_sns','=','0');
                 if($search != '')               
                  {
                      $colordata = $colordata->Where('pz.name', 'LIKE', '%'.$search.'%');
                  }
                 $colordata = $colordata->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
       
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $colordata;
        $returnData['count'] = $count[0]->Totalcount;

        return $returnData;
    }
    public function getCompanyAddress($company_id)
    {
       $result = DB::table('company_address as ca')
                    ->leftJoin('state as st','st.id','=','ca.state')
                    ->select('st.name as state_name','ca.*')
                    ->where('ca.company_id','=',$company_id)
                    ->where('ca.is_deleted','=','1')
                    ->get();
        return $result;   
    }       
    
    public function getQBAPI($company_id)
    {
       $result = DB::table('api_link_table as alt')
           ->select('qd.*')
           ->Join('quickbook_detail as qd','qd.link_id','=','alt.id')
           ->where("alt.company_id","=",$company_id)
           ->where("alt.api_id","=",QUICKBOOK_ID)
           ->get();
       return $result;
   }

}
