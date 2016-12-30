<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Price extends Model {


/**
* Price listing array           
* @access public priceList
* @return array $priceData
*/

    public function priceList($post) {
        
        $whereConditions = ['is_delete' => '1','company_id' => $post['cond']['company_id']];
        $listArray = ['id','name','status'];

        $priceData = DB::table('price_grid')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->orderBy('id', 'desc')
                         ->get();

        return $priceData;
    }

/**
* Delete Price           
* @access public priceDelete
* @param  int $id
* @return array $result
*/ 

    public function priceDelete($id)
    {
        if(!empty($id))
        {
            $result = DB::table('price_grid')->where('id','=',$id)->update(array("is_delete" => '0'));
            return $result;
        }
        else
        {
            return false;
        }
    }


/**
* Price Detail           
* @access public priceDetail
* @param  int $priceId
* @return array $combine_array
*/  

    public function priceDetail($priceId) {

        $wherePriceConditions = ['id' => $priceId];
        $priceData = DB::table('price_grid')->where($wherePriceConditions)->get();


        $whereConditions = ['price_id' => $priceId];
        $listArray = ['item','time','charge','is_gps_distrib','is_gps_opt','is_per_line','is_per_order','is_per_screen_set'];
        $priceCharge = DB::table('price_grid_charges')->select($listArray)->where($whereConditions)->get();


        $whereConditionsScreenPrimary = ['price_id' => $priceId];
        $listArrayPrimary = ['range_high','range_low','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c','pricing_11c','pricing_12c','pricing_13c','pricing_14c','pricing_15c','pricing_16c'];
        $priceScreenPrimary = DB::table('price_screen_primary')->select($listArrayPrimary)->where($whereConditionsScreenPrimary)->get();


        $whereConditionsScreenSecondary = ['price_id' => $priceId];
        $listArraySecondary = ['range_high','range_low','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c','pricing_11c','pricing_12c','pricing_13c','pricing_14c','pricing_15c','pricing_16c'];
        $priceScreenSecondary = DB::table('price_screen_secondary')->select($listArraySecondary)->where($whereConditionsScreenSecondary)->get();


        $whereConditionsGarmentMackup = ['price_id' => $priceId];
        $listArrayGarmentMackup = ['range_high','range_low','percentage'];
        $priceGarmentMackup = DB::table('price_garment_mackup')->select($listArrayGarmentMackup)->where($whereConditionsGarmentMackup)->get();


        $whereConditionsAllGarment = ['price_id' => $priceId];
        $listArrayAllGarment = ['range_high','range_low','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c','pricing_11c','pricing_12c'];
        $priceAllGarment = DB::table('price_direct_garment')->select($listArrayAllGarment)->where($whereConditionsAllGarment)->get();


        $whereConditionsembroSwitch = ['price_id' => $priceId];
        $listArrayAllEmbroSwitch = ['id','range_high_1','range_low_1','range_high_2','range_low_2','range_high_3','range_low_3','range_high_4','range_low_4','range_high_5','range_low_5','range_high_6','range_low_6','range_high_7','range_low_7','range_high_8','range_low_8','range_high_9','range_low_9','range_high_10','range_low_10','range_high_11','range_low_11','range_high_12','range_low_12'];
        $priceAllEmbroSwitch = DB::table('embroidery_switch_count')->select($listArrayAllEmbroSwitch)->where($whereConditionsembroSwitch)->get();


        $priceAllEmbro = array();
        if(!empty($priceAllEmbroSwitch)){
        $whereConditionsAllEmbro = ['price_id' => $priceId,'embroidery_switch_id' => $priceAllEmbroSwitch[0]->id];
        $listArrayAllEmbro = ['range_high','range_low','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c','pricing_11c','pricing_12c'];
        $priceAllEmbro = DB::table('price_screen_embroidery')->select($listArrayAllEmbro)->where($whereConditionsAllEmbro)->get();
         }

        $combine_array['price'] = $priceData;
        $combine_array['allPriceGrid'] = $priceCharge;
        $combine_array['allScreenPrimary'] = $priceScreenPrimary;
        $combine_array['allScreenSecondary'] = $priceScreenSecondary;
        $combine_array['allGarmentMackup'] = $priceGarmentMackup;
        $combine_array['allGarment'] = $priceAllGarment;
        $combine_array['embroswitch'] = $priceAllEmbroSwitch;
        $combine_array['allEmbroidery'] = $priceAllEmbro;
        return $combine_array;
    }

/**
* Price Add          
* @access public priceAdd
* @param  array $data
* @return array $result
*/

    public function priceAdd($data,$priceData,$priceScreenPrimary,$priceScreenSecondary,$priceGarmentMackup,$priceDirectGarment,$priceEmbroSwitch,$price_embro) {
        
        
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('price_grid')->insert($data);

        $priceid = DB::getPdo()->lastInsertId();

           foreach($priceData as $key => $link) 
              { 
                $priceData[$key]['price_id'] = $priceid;
                $result_price = DB::table('price_grid_charges')->insert($priceData[$key]);
              }

             foreach($priceScreenPrimary as $keyprimary => $linkprimary) 
              { 
                $priceScreenPrimary[$keyprimary]['price_id'] = $priceid;
                $result_primary = DB::table('price_screen_primary')->insert($priceScreenPrimary[$keyprimary]);
              }

              foreach($priceScreenSecondary as $keysecondary => $linksecondary) 
              { 
                $priceScreenSecondary[$keysecondary]['price_id'] = $priceid;
                $result_secondary = DB::table('price_screen_secondary')->insert($priceScreenSecondary[$keysecondary]);
              }

               foreach($priceGarmentMackup as $keygarmack => $linkgarmack) 
              { 
                $priceGarmentMackup[$keygarmack]['price_id'] = $priceid;
                $result_garment_mackup = DB::table('price_garment_mackup')->insert($priceGarmentMackup[$keygarmack]);
              }

              foreach($priceDirectGarment as $keydgarm => $linkdgarm) 
              { 
                $priceDirectGarment[$keydgarm]['price_id'] = $priceid;
                $result_direct_garment = DB::table('price_direct_garment')->insert($priceDirectGarment[$keydgarm]);
              }

              
                $priceEmbroSwitch['price_id'] = $priceid;
                $result_embro_switch = DB::table('embroidery_switch_count')->insert($priceEmbroSwitch);
              

                $switchId = DB::getPdo()->lastInsertId();

               foreach($price_embro as $key => $link) 
              { 
                $price_embro[$key]['price_id'] = $priceid;
                $price_embro[$key]['embroidery_switch_id'] = $switchId;
                $result_direct_garment = DB::table('price_screen_embroidery')->insert($price_embro[$key]);
              }




        return $priceid;
    }

/**
* Price Edit          
* @access public priceEdit
* @param  array $data
* @return array $result
*/
    public function priceEdit($data) {

        $data['updated_date'] = date("Y-m-d H:i:s");
        $whereConditions = ['id' => $data['id']];
        $result = DB::table('price_grid')->where($whereConditions)->update($data);
        return $result;
    }

/**
* Price charges data           
* @access public priceChargesEdit
* @param  array $data
* @return array $result
*/  

public function priceChargesEdit($priceData,$priceId) {
    
    DB::table('price_grid_charges')->where('price_id', '=', $priceId)->delete();
     
           foreach($priceData as $key => $link) 
              { 
                $priceData[$key]['price_id'] = $priceId;
                $result_price = DB::table('price_grid_charges')->insert($priceData[$key]);
              }
        return  $priceId;
    }


/**
* Price charges Primary data           
* @access public priceChargesPrimaryEdit
* @param  array $data
* @return array $result
*/  

public function priceChargesPrimaryEdit($price_primary,$priceId) {
    
    DB::table('price_screen_primary')->where('price_id', '=', $priceId)->delete();
     
           foreach($price_primary as $key => $link) 
              { 
                $price_primary[$key]['price_id'] = $priceId;
                $result_price_primary = DB::table('price_screen_primary')->insert($price_primary[$key]);
              }
        return  $priceId;
    }

/**
* Price charges Secondary data           
* @access public priceChargesSecondaryEdit
* @param  array $data
* @return array $result
*/  

public function priceChargesSecondaryEdit($price_secondary,$priceId) {
    
    DB::table('price_screen_secondary')->where('price_id', '=', $priceId)->delete();
     
           foreach($price_secondary as $key => $link) 
              { 
                $price_secondary[$key]['price_id'] = $priceId;
                $result_price_secondary = DB::table('price_screen_secondary')->insert($price_secondary[$key]);
              }
        return  $priceId;
    }


/**
* Price charges Garment Mackup data           
* @access public priceGarmentMackupEdit
* @param  array $data
* @return array $result
*/  

public function priceGarmentMackupEdit($garment_mackup,$priceId) {
    
    DB::table('price_garment_mackup')->where('price_id', '=', $priceId)->delete();
     
           foreach($garment_mackup as $key => $link) 
              { 
                $garment_mackup[$key]['price_id'] = $priceId;
                $result_garment_markup = DB::table('price_garment_mackup')->insert($garment_mackup[$key]);
              }
        return  $priceId;
    }

/**
* Price charges Direct Garment data           
* @access public priceDirectGarmentEdit
* @param  array $data
* @return array $result
*/  

public function priceDirectGarmentEdit($direct_garment,$priceId) {
    
    DB::table('price_direct_garment')->where('price_id', '=', $priceId)->delete();
     
           foreach($direct_garment as $key => $link) 
              { 
                $direct_garment[$key]['price_id'] = $priceId;
                $result_direct_garment = DB::table('price_direct_garment')->insert($direct_garment[$key]);
              }
        return  $priceId;
    }



/**
* Price charges Embro Switch data           
* @access public priceEmbroSwitchEdit
* @param  array $data
* @return array $result
*/  

public function priceEmbroSwitchEdit($embro_switch,$priceId) {
    
    DB::table('embroidery_switch_count')->where('price_id', '=', $priceId)->delete();
     
    $embro_switch['price_id'] = $priceId;
    $result_embro_switch = DB::table('embroidery_switch_count')->insert($embro_switch);
              
    return  $priceId;
    }


/**
* Price charges Embroidery data           
* @access public priceEmbroEdit
* @param  array $data
* @return array $result
*/  

public function priceEmbroEdit($price_embro,$priceId,$switchId) {
    
    DB::table('price_screen_embroidery')->where('price_id', '=', $priceId)->where('embroidery_switch_id', '=', $switchId)->delete();
     
           foreach($price_embro as $key => $link) 
              { 
                $price_embro[$key]['price_id'] = $priceId;
                $price_embro[$key]['embroidery_switch_id'] = $switchId;
                $result_direct_garment = DB::table('price_screen_embroidery')->insert($price_embro[$key]);
              }
        return  $priceId;
    }


    /**
* Price Add          
* @access public priceDuplicate
* @param  array $data
* @return array $result
*/

    public function priceGridDuplicate($data,$priceData,$priceScreenPrimary,$priceScreenSecondary,$priceGarmentMackup,$priceDirectGarment,$priceEmbroSwitch,$price_embro) {
        
        unset($data['id']);
       

        $data['name'] = $data['name'].' Copy';
        $data['created_date'] = date("Y-m-d H:i:s");
        $data['updated_date'] = date("Y-m-d H:i:s");
        $result = DB::table('price_grid')->insert($data);

        $priceid = DB::getPdo()->lastInsertId();

           foreach($priceData as $key => $link) 
              { 
                $priceData[$key]['price_id'] = $priceid;
                $result_price = DB::table('price_grid_charges')->insert($priceData[$key]);
              }

             foreach($priceScreenPrimary as $keyprimary => $linkprimary) 
              { 
                $priceScreenPrimary[$keyprimary]['price_id'] = $priceid;
                $result_primary = DB::table('price_screen_primary')->insert($priceScreenPrimary[$keyprimary]);
              }

              foreach($priceScreenSecondary as $keysecondary => $linksecondary) 
              { 
                $priceScreenSecondary[$keysecondary]['price_id'] = $priceid;
                $result_secondary = DB::table('price_screen_secondary')->insert($priceScreenSecondary[$keysecondary]);
              }

               foreach($priceGarmentMackup as $keygarmack => $linkgarmack) 
              { 
                $priceGarmentMackup[$keygarmack]['price_id'] = $priceid;
                $result_garment_mackup = DB::table('price_garment_mackup')->insert($priceGarmentMackup[$keygarmack]);
              }

              foreach($priceDirectGarment as $keydgarm => $linkdgarm) 
              { 
                $priceDirectGarment[$keydgarm]['price_id'] = $priceid;
                $result_direct_garment = DB::table('price_direct_garment')->insert($priceDirectGarment[$keydgarm]);
              }

              
                unset($priceEmbroSwitch['id']);

                $priceEmbroSwitch['price_id'] = $priceid;
                $result_embro_switch = DB::table('embroidery_switch_count')->insert($priceEmbroSwitch);
              

                $switchId = DB::getPdo()->lastInsertId();

               foreach($price_embro as $key => $link) 
              { 
                $price_embro[$key]['price_id'] = $priceid;
                $price_embro[$key]['embroidery_switch_id'] = $switchId;
                $result_direct_garment = DB::table('price_screen_embroidery')->insert($price_embro[$key]);
              }




        return $priceid;
    }


    /**
* Price Secondary           
* @access public priceSecondary
* @param  int $priceId
* @return array $combine_array
*/  

    public function priceSecondary($priceId) {

        $whereConditionsScreenSecondary = ['price_id' => $priceId];
        $listArraySecondary = ['range_high','range_low','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c','pricing_11c','pricing_12c','pricing_13c','pricing_14c','pricing_15c','pricing_16c'];
        $priceScreenSecondary = DB::table('price_screen_secondary')->select($listArraySecondary)->where($whereConditionsScreenSecondary)->get();

        $combine_array['allScreenSecondary'] = $priceScreenSecondary;
       
        return $combine_array;
    }


/**
* Placement Save          
* @access public placementSave
* @param  array $data
* @return array $result
*/
    public function placementSave($data) {
        
         
        $data[$data['columnname']] = $data['updatedcolumn'];
        unset($data['columnname']);
        unset($data['updatedcolumn']);
        
        $whereConditions = ['id' => $data['id']];
        $result = DB::table('placement')->where($whereConditions)->update($data);
        return $result;
    }



    public function priceDetailExcel($priceId) {

        $wherePriceConditions = ['id' => $priceId];
        $listArrayData = ['name as Name','login_id'];
        $priceData = DB::table('price_grid')->select($listArrayData)->where($wherePriceConditions)->get();


        $wherePriceConditions = ['id' => $priceId];
        $listArrayDatanew = [DB::raw("discharge as 'Discharge',foil as 'Foil',number_on_dark as 'Number on Dark',poly_bagging as 'Poly Bagging',specialty as 'Speciality',folding as 'Folding',number_on_light as 'Number on Light',press_setup as 'Press Setup',color_matching as 'Color Matching',hang_tag as 'Hang Tag',over_size as 'Oversize',printed_names as 'Printed Name',embroidered_names as 'Embroidered Names',ink_changes as 'Ink Charges',over_size_screens as 'Oversize Screens',screen_fees as 'Screen Fees',shipping_charge as 'Shipping Charge'")];
        $priceDataCharges = DB::table('price_grid')->select($listArrayDatanew)->where($wherePriceConditions)->get();
        


        $whereConditions = ['price_id' => $priceId];
        $listArray = [DB::raw("item as 'Item',charge as 'Charge',time as 'Time',is_per_line as 'Available to per Line',is_per_order as 'Available to per Order',is_per_screen_set as 'Available to per Screen Set'")];
        $priceCharge = DB::table('price_grid_charges')->select($listArray)->where($whereConditions)->get();


        $whereConditionsScreenPrimary = ['price_id' => $priceId];
        $listArrayPrimary = [DB::raw("range_low as 'Low Range',range_high as 'High Range',pricing_1c,pricing_2c,pricing_3c,pricing_4c,pricing_5c,pricing_6c,pricing_7c,pricing_8c,pricing_9c,pricing_10c,pricing_11c,pricing_12c,pricing_13c,pricing_14c,pricing_15c,pricing_16c")];
        $priceScreenPrimary = DB::table('price_screen_primary')->select($listArrayPrimary)->where($whereConditionsScreenPrimary)->get();


        $whereConditionsScreenSecondary = ['price_id' => $priceId];
        $listArraySecondary = [DB::raw("range_low as 'Low Range',range_high as 'High Range',pricing_1c,pricing_2c,pricing_3c,pricing_4c,pricing_5c,pricing_6c,pricing_7c,pricing_8c,pricing_9c,pricing_10c,pricing_11c,pricing_12c,pricing_13c,pricing_14c,pricing_15c,pricing_16c")];
        $priceScreenSecondary = DB::table('price_screen_secondary')->select($listArraySecondary)->where($whereConditionsScreenSecondary)->get();


        $whereConditionsGarmentMackup = ['price_id' => $priceId];
        $listArrayGarmentMackup = [DB::raw("range_low as 'Range Low',range_high as 'Range High',percentage as 'Percentage'")];
        $priceGarmentMackup = DB::table('price_garment_mackup')->select($listArrayGarmentMackup)->where($whereConditionsGarmentMackup)->get();


        $whereConditionsAllGarment = ['price_id' => $priceId];
        $listArrayAllGarment = [DB::raw("range_low as 'Range Low',range_high as 'Range High',pricing_1c as 'Light 4*4',pricing_2c as 'Dark 4*4',pricing_3c as 'Light 6*6',pricing_4c as 'Dark 6*6',pricing_5c as 'Light 10*10',pricing_6c as 'Dark 10*10',pricing_7c as 'Light 12*12',pricing_8c as 'Dark 12*12'")];
        $priceAllGarment = DB::table('price_direct_garment')->select($listArrayAllGarment)->where($whereConditionsAllGarment)->get();


        $whereConditionsembroSwitch = ['price_id' => $priceId];
        $listArrayAllEmbroSwitch = ['id','range_low_1','range_low_2','range_low_3','range_low_4','range_low_5','range_low_6','range_low_7','range_low_8','range_low_9','range_high_1','range_high_2','range_high_3','range_high_4','range_high_5','range_high_6','range_high_7','range_high_8','range_high_9'];
        $priceAllEmbroSwitch = DB::table('embroidery_switch_count')->select($listArrayAllEmbroSwitch)->where($whereConditionsembroSwitch)->get();
        

        $priceAllEmbro = array();
        if(!empty($priceAllEmbroSwitch)){
        $whereConditionsAllEmbro = ['price_id' => $priceId,'embroidery_switch_id' => $priceAllEmbroSwitch[0]->id];
        $listArrayAllEmbro = ['range_low','range_high','pricing_1c','pricing_2c','pricing_3c','pricing_4c','pricing_5c','pricing_6c','pricing_7c','pricing_8c','pricing_9c','pricing_10c'];
        $priceAllEmbro = DB::table('price_screen_embroidery')->select($listArrayAllEmbro)->where($whereConditionsAllEmbro)->get();
         }

        $combine_array['price'] = $priceData;
        $combine_array['charges'] = $priceDataCharges;
        $combine_array['allPriceGrid'] = $priceCharge;
        $combine_array['allScreenPrimary'] = $priceScreenPrimary;
        $combine_array['allScreenSecondary'] = $priceScreenSecondary;
        $combine_array['allGarmentMackup'] = $priceGarmentMackup;
        $combine_array['allGarment'] = $priceAllGarment;
        $combine_array['embroswitch'] = $priceAllEmbroSwitch;
        $combine_array['allEmbroidery'] = $priceAllEmbro;
        return $combine_array;
    }




}
