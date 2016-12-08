(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('CreatePriceGridDialogController', CreatePriceGridDialogController);

    /** @ngInject */
    function CreatePriceGridDialogController($mdDialog,$controller,$state,$scope,sessionService,$resource,$http,$stateParams,notifyService)
    {
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM')
        {
            $scope.allow_access = 1; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 0;  // THESE ROLES CAN ALLOW TO EDIT
        }


    $scope.priceDetail = function(){

           $("#ajax_loader").show();
           $http.post('api/public/admin/priceDetail',$stateParams.id).success(function(result, status, headers, config) {

            if(result.data.success == '1') {
                      
                     $scope.price = angular.copy(result.data.records[0]);

                     $scope.temp = result.data.records[0];

                     for (var i=0; i<result.data.allPriceGrid.length; i++){

                           if(result.data.allPriceGrid[i].is_per_screen_set == '1') {
                            result.data.allPriceGrid[i].is_per_screen_set = true
                           } else {
                            result.data.allPriceGrid[i].is_per_screen_set = false
                           }

                           if(result.data.allPriceGrid[i].is_gps_distrib == '1') {
                            result.data.allPriceGrid[i].is_gps_distrib = true
                           } else {
                            result.data.allPriceGrid[i].is_gps_distrib = false
                           }

                           if(result.data.allPriceGrid[i].is_gps_opt == '1') {
                            result.data.allPriceGrid[i].is_gps_opt = true
                           } else {
                            result.data.allPriceGrid[i].is_gps_opt = false
                           }

                           if(result.data.allPriceGrid[i].is_per_line == '1') {
                            result.data.allPriceGrid[i].is_per_line = true
                           } else {
                            result.data.allPriceGrid[i].is_per_line = false
                           }

                           if(result.data.allPriceGrid[i].is_per_order == '1') {
                            result.data.allPriceGrid[i].is_per_order = true
                           } else {
                            result.data.allPriceGrid[i].is_per_order = false
                           }

                        }

                      $scope.allPriceGrid = angular.copy(result.data.allPriceGrid);
                      $scope.temp_allPriceGrid = result.data.allPriceGrid;


                      $scope.allScreenPrimary = angular.copy(result.data.allScreenPrimary);
                      $scope.temp_primary = result.data.allScreenPrimary;

                      $scope.allScreenSecondary = angular.copy(result.data.allScreenSecondary);
                      $scope.temp_secondary = result.data.allScreenSecondary;

                      $scope.allEmbroidery = angular.copy(result.data.allEmbroidery);
                      $scope.temp_embro = result.data.allEmbroidery;

                      $scope.allGarment = angular.copy(result.data.allGarment);
                      $scope.temp_gar = result.data.allGarment;


                      $scope.allGarmentMackup = result.data.allGarmentMackup;
                      
                      $scope.embroswitch = result.data.embroswitch[0];
                     
                      $("#ajax_loader").hide();

                     }  else {
                     $state.go('app.settings.priceGrid');
                     }
         
            });
        }

        $scope.priceDetail();


           $scope.duplicateprimary = function (price_primary) {
            
              var combine_array_data = {};
              combine_array_data.price_id = $stateParams.id;
              combine_array_data.price_primary = price_primary;
              

              var permission = confirm("This will over right the current settings if any in the secondary price grid panel.");

              if (permission == true) {
              
                    $http.post('api/public/admin/priceGridPrimaryDuplicate',combine_array_data).success(function(result, status, headers, config) {

                    if(result.data.success == '1') {


                    $http.post('api/public/admin/priceSecondary',$stateParams.id).success(function(result, status, headers, config) {
  
                      if(result.data.success == '1') {
                               
                                $scope.allScreenSecondary = result.data.allScreenSecondary;
                               
                       }  else {
                       $state.go('app.dashboard');
                       }
                   
                    });

                     $scope.selectedIndex = 3;
                        
                     } 
           
                });

              }
          }

          $scope.duplicate = function (price,price_grid,price_primary,price_secondary,garment_mackup,garment,embroswitch,allEmbroidery) {
                          
                            var combine_array_data = {};
                            combine_array_data.price = price;
                            combine_array_data.price_grid = price_grid;
                            combine_array_data.price_primary = price_primary;
                            combine_array_data.price_secondary = price_secondary;
                            combine_array_data.garment_mackup = garment_mackup;
                            combine_array_data.garment = garment;
                            combine_array_data.embroswitch = embroswitch;
                            combine_array_data.allEmbroidery = allEmbroidery;

                            

                            var permission = confirm("Are you sure you want to duplicate this Price Grid ?");

                            if (permission == true) {
                            
                                  $http.post('api/public/admin/priceGridDuplicate',combine_array_data).success(function(result, status, headers, config) {
        
                                  if(result.data.success == '1') {
                                          window.history.back();
                                   } 
                         
                              });

                            }
                        }


            $scope.savePrice = function(price,price_grid,price_primary,price_secondary,garment_mackup,garment,embroswitch,allEmbroidery) {
                   
                    var combine_array_data = {};
                    combine_array_data.price = price;
                    combine_array_data.price_grid = price_grid;
                    combine_array_data.price_primary = price_primary;
                    combine_array_data.price_secondary = price_secondary;
                    combine_array_data.garment_mackup = garment_mackup;
                    combine_array_data.garment = garment;
                    combine_array_data.embroswitch = embroswitch;
                    combine_array_data.allEmbroidery = allEmbroidery;

                   if(price.id) {
                    $("#ajax_loader").show();
                         
                    $http.post('api/public/admin/priceEdit',combine_array_data).success(function(result, status, headers, config) {
                      $("#ajax_loader").hide();
                      if(result.data.success == '1') {

                         var data = {"status": "success", "message": "Record Updated Successfully."}
                         notifyService.notify(data.status, data.message);

                         window.history.back();
                       } 
                   
                    });
                    
                   } else {
                  
                      $state.go('app.dashboard');
                      return false;

                   }

                   };

                    $scope.allPriceGrid = [];
                    $scope.addInput = function(){
                      $scope.allPriceGrid.push({item:'', time:'', charge:'', is_gps_distrib:'', is_gps_opt:'', is_per_line:'', is_per_order:'', is_per_screen_set:''});
                    }

                    $scope.removeInput = function(index){
                        $scope.allPriceGrid.splice(index,1);
                    }


                    $scope.allScreenPrimary = [];
                    $scope.addScreenPrimary = function(){
                      $scope.allScreenPrimary.push({range_low:'', range_high:'', pricing_1c:'', pricing_2c:'', pricing_3c:'', pricing_4c:'', pricing_5c:'', pricing_6c:'',pricing_7c:'',pricing_8c:'',pricing_9c:'',pricing_10c:'',pricing_11c:'',pricing_12c:''});
                    }

                    $scope.removeScreenPrimary = function(index){
                        $scope.allScreenPrimary.splice(index,1);
                    }

                    $scope.allScreenSecondary = [];
                    $scope.addScreenSecondary = function(){
                      $scope.allScreenSecondary.push({range_low:'', range_high:'', pricing_1c:'', pricing_2c:'', pricing_3c:'', pricing_4c:'', pricing_5c:'', pricing_6c:'',pricing_7c:'',pricing_8c:'',pricing_9c:'',pricing_10c:'',pricing_11c:'',pricing_12c:''});
                    }

                    $scope.removeScreenSecondary = function(index){
                        $scope.allScreenSecondary.splice(index,1);
                    }


                     $scope.allGarment = [];
                    $scope.addGarment = function(){
                      $scope.allGarment.push({range_low:'', range_high:'', pricing_1c:'', pricing_2c:'', pricing_3c:'', pricing_4c:'', pricing_5c:'', pricing_6c:'',pricing_7c:'',pricing_8c:'',pricing_9c:'',pricing_10c:'',pricing_11c:'',pricing_12c:''});
                    }

                    $scope.removeGarment = function(index){
                        $scope.allGarment.splice(index,1);
                    }


                     $scope.allGarmentMackup = [];
                    $scope.addGarmentMackup = function(){
                      $scope.allGarmentMackup.push({range_low:'', range_high:'', percentage:''});
                    }

                    $scope.removeGarmentMackup = function(index){
                        $scope.allGarmentMackup.splice(index,1);
                    }

                      $scope.allEmbroidery = [];
                    $scope.addEmbroidery = function(){
                      $scope.allEmbroidery.push({range_low:'', range_high:'', pricing_1c:'', pricing_2c:'', pricing_3c:'', pricing_4c:'', pricing_5c:'', pricing_6c:'',pricing_7c:'',pricing_8c:'',pricing_9c:'',pricing_10c:'',pricing_11c:'',pricing_12c:''});
                    }

                    $scope.removeEmbroidery = function(index){
                        $scope.allEmbroidery.splice(index,1);
                    }

                  $scope.cancel = function() {
                         window.history.back();
                  }



                        $scope.percentagecalc = function($event,type) {
                          
                           var price_in_percentage = $event.target.value;
                           
                          
                          if(type == 'charges') {

                                if($scope.temp.discharge){
                                var discharge_value = parseFloat(parseFloat($scope.temp.discharge) + ($scope.temp.discharge * price_in_percentage) / 100).toFixed(2)
                                $scope.price.discharge = discharge_value;
                                 } 


                                 if($scope.temp.specialty){
                                    var specialty_value = parseFloat(parseFloat($scope.temp.specialty) + ($scope.temp.specialty * price_in_percentage) / 100).toFixed(2)
                                    $scope.price.specialty = specialty_value;
                                   }

                              if($scope.temp.color_matching){

                                var color_matching_value = parseFloat(parseFloat($scope.temp.color_matching) + ($scope.temp.color_matching * price_in_percentage) / 100).toFixed(2)
                                $scope.price.color_matching = color_matching_value;

                              }

                              if($scope.temp.embroidered_names){

                                var embroidered_names_value = parseFloat(parseFloat($scope.temp.embroidered_names) + ($scope.temp.embroidered_names * price_in_percentage) / 100).toFixed(2)
                                $scope.price.embroidered_names = embroidered_names_value;

                              }

                              if($scope.temp.foil){

                                var foil_value = parseFloat(parseFloat($scope.temp.foil) + ($scope.temp.foil * price_in_percentage) / 100).toFixed(2)
                                $scope.price.foil = foil_value;

                              }

                              if($scope.temp.folding){

                                var folding_value = parseFloat(parseFloat($scope.temp.folding) + ($scope.temp.folding * price_in_percentage) / 100).toFixed(2)
                                $scope.price.folding = folding_value;

                              }

                              if($scope.temp.hang_tag){

                                var hang_tag_value = parseFloat(parseFloat($scope.temp.hang_tag) + ($scope.temp.hang_tag * price_in_percentage) / 100).toFixed(2)
                                $scope.price.hang_tag = hang_tag_value;

                              }

                              if($scope.temp.ink_changes){

                                var ink_changes_value = parseFloat(parseFloat($scope.temp.ink_changes) + ($scope.temp.ink_changes * price_in_percentage) / 100).toFixed(2)
                                $scope.price.ink_changes = ink_changes_value;

                              }

                                if($scope.temp.number_on_dark){
                                var number_on_dark_value = parseFloat(parseFloat($scope.temp.number_on_dark) + ($scope.temp.number_on_dark * price_in_percentage) / 100).toFixed(2)
                                $scope.price.number_on_dark = number_on_dark_value;
                                }

                                if($scope.temp.number_on_light){
                                var number_on_light_value = parseFloat(parseFloat($scope.temp.number_on_light) + ($scope.temp.number_on_light * price_in_percentage) / 100).toFixed(2)
                                $scope.price.number_on_light = number_on_light_value;
                                }

                                 if($scope.temp.over_size){
                                var over_size_value = parseFloat(parseFloat($scope.temp.over_size) + ($scope.temp.over_size * price_in_percentage) / 100).toFixed(2)
                                $scope.price.over_size = over_size_value;
                                }

                                 if($scope.temp.over_size_screens){
                                var over_size_screens_value = parseFloat(parseFloat($scope.temp.over_size_screens) + ($scope.temp.over_size_screens * price_in_percentage) / 100).toFixed(2)
                                $scope.price.over_size_screens = over_size_screens_value;
                                }

                                if($scope.temp.poly_bagging){
                                var poly_bagging_value = parseFloat(parseFloat($scope.temp.poly_bagging) + ($scope.temp.poly_bagging * price_in_percentage) / 100).toFixed(2)
                                $scope.price.poly_bagging = poly_bagging_value;
                                }

                                if($scope.temp.press_setup){
                                var press_setup_value = parseFloat(parseFloat($scope.temp.press_setup) + ($scope.temp.press_setup * price_in_percentage) / 100).toFixed(2)
                                $scope.price.press_setup = press_setup_value;
                                }


                                if($scope.temp.printed_names){
                                var printed_names_value = parseFloat(parseFloat($scope.temp.printed_names) + ($scope.temp.printed_names * price_in_percentage) / 100).toFixed(2)
                                $scope.price.printed_names = printed_names_value;
                                }

                                if($scope.temp.screen_fees){
                                var screen_fees_value = parseFloat(parseFloat($scope.temp.screen_fees) + ($scope.temp.screen_fees * price_in_percentage) / 100).toFixed(2)
                                $scope.price.screen_fees = screen_fees_value;
                                }

                                 if($scope.temp.shipping_charge){
                                var shipping_charge_value = parseFloat(parseFloat($scope.temp.shipping_charge) + ($scope.temp.shipping_charge * price_in_percentage) / 100).toFixed(2)
                                $scope.price.shipping_charge = shipping_charge_value;
                                }

                              }  else if(type == 'chargeslist'){
                                
                                  var index = 0;
                                  angular.forEach($scope.temp_allPriceGrid, function( key, value ) {
                                   
                                       $scope.allPriceGrid[index].charge = parseFloat(parseFloat(key.charge) + (key.charge * price_in_percentage) / 100).toFixed(2);
                                       $scope.allPriceGrid[index].time =parseFloat(parseFloat(key.time) + (key.time * price_in_percentage) / 100).toFixed(2)
                                      index++;
                                    });

                              } else if(type == 'primary'){
                               
                               var index = 0;

                                  angular.forEach($scope.temp_primary, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=16; i++) {
                                      price_field = "pricing_"+i+"c";
                                      
                                      if(key[price_field] !== null) {
                                        $scope.allScreenPrimary[index][price_field] = parseFloat(parseFloat(key[price_field]) + (key[price_field] * price_in_percentage) / 100).toFixed(2);
                                       }
                                    }
                                      index++;
                                    });
                              }

                              else if(type == 'secondary'){
                               

                               var index = 0;

                                  angular.forEach($scope.temp_secondary, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=16; i++) {
                                      price_field = "pricing_"+i+"c";

                                      if(key[price_field] !== null) {
                                       $scope.allScreenSecondary[index][price_field] = parseFloat(parseFloat(key[price_field]) + (key[price_field] * price_in_percentage) / 100).toFixed(2);
                                      }
                                    }
                                      index++;
                                    });
                                  
                              } else if(type == 'embroidery'){
                               

                               var index = 0;

                                  angular.forEach($scope.temp_embro, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=12; i++) {
                                      price_field = "pricing_"+i+"c";
                                      if(key[price_field] !== null) {
                                      $scope.allEmbroidery[index][price_field] = parseFloat(parseFloat(key[price_field]) + (key[price_field] * price_in_percentage) / 100).toFixed(2);
                                      }
                                    }
                                      index++;
                                    });
                                  
                                  
                              } else if(type == 'dtogarment'){
                               

                               var index = 0;

                                  angular.forEach($scope.temp_gar, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=12; i++) {
                                      price_field = "pricing_"+i+"c";
                                      $scope.allGarment[index][price_field] = parseFloat(parseFloat(key[price_field]) + (key[price_field] * price_in_percentage) / 100).toFixed(2);
                                    }
                                      index++;
                                    });
                                  
                                  
                              }

                        }



                        $scope.amtcalc = function($event,type) {
                          
                                var price_in_amt = $event.target.value;

                                  if(!price_in_amt.length){
                                  price_in_amt = 0;
                                 }

                                if(type == 'charges') {
                                 
                                if($scope.temp.discharge){
                                  var discharge_value = parseFloat(parseFloat($scope.temp.discharge) + parseFloat(price_in_amt)).toFixed(2)
                                  $scope.price.discharge = discharge_value;
                                 } 


                                 if($scope.temp.specialty){
                                    var specialty_value = parseFloat(parseFloat($scope.temp.specialty) + parseFloat(price_in_amt)).toFixed(2)
                                    $scope.price.specialty = specialty_value;
                                   }

                              if($scope.temp.color_matching){

                                var color_matching_value = parseFloat(parseFloat($scope.temp.color_matching) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.color_matching = color_matching_value;

                              }

                              if($scope.temp.embroidered_names){

                                var embroidered_names_value = parseFloat(parseFloat($scope.temp.embroidered_names) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.embroidered_names = embroidered_names_value;

                              }

                              if($scope.temp.foil){

                                var foil_value = parseFloat(parseFloat($scope.temp.foil) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.foil = foil_value;

                              }

                              if($scope.temp.folding){

                                var folding_value = parseFloat(parseFloat($scope.temp.folding) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.folding = folding_value;

                              }

                              if($scope.temp.hang_tag){

                                var hang_tag_value = parseFloat(parseFloat($scope.temp.hang_tag) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.hang_tag = hang_tag_value;

                              }

                              if($scope.temp.ink_changes){

                                var ink_changes_value = parseFloat(parseFloat($scope.temp.ink_changes) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.ink_changes = ink_changes_value;

                              }

                                if($scope.temp.number_on_dark){
                                var number_on_dark_value = parseFloat(parseFloat($scope.temp.number_on_dark) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.number_on_dark = number_on_dark_value;
                                }

                                if($scope.temp.number_on_light){
                                var number_on_light_value = parseFloat(parseFloat($scope.temp.number_on_light) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.number_on_light = number_on_light_value;
                                }

                                 if($scope.temp.over_size){
                                var over_size_value = parseFloat(parseFloat($scope.temp.over_size) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.over_size = over_size_value;
                                }

                                 if($scope.temp.over_size_screens){
                                var over_size_screens_value = parseFloat(parseFloat($scope.temp.over_size_screens) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.over_size_screens = over_size_screens_value;
                                }

                                if($scope.temp.poly_bagging){
                                var poly_bagging_value = parseFloat(parseFloat($scope.temp.poly_bagging) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.poly_bagging = poly_bagging_value;
                                }

                                if($scope.temp.press_setup){
                                var press_setup_value = parseFloat(parseFloat($scope.temp.press_setup) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.press_setup = press_setup_value;
                                }


                                if($scope.temp.printed_names){
                                var printed_names_value = parseFloat(parseFloat($scope.temp.printed_names) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.printed_names = printed_names_value;
                                }

                                if($scope.temp.screen_fees){
                                var screen_fees_value = parseFloat(parseFloat($scope.temp.screen_fees) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.screen_fees = screen_fees_value;
                                }

                                 if($scope.temp.shipping_charge){
                                var shipping_charge_value = parseFloat(parseFloat($scope.temp.shipping_charge) + parseFloat(price_in_amt)).toFixed(2)
                                $scope.price.shipping_charge = shipping_charge_value;
                                }

                              } else if(type == 'chargeslist'){
                               

                               var index = 0;
                                  angular.forEach($scope.temp_allPriceGrid, function( key, value ) {
                                   
                                       $scope.allPriceGrid[index].charge = parseFloat(parseFloat(key.charge) + parseFloat(price_in_amt)).toFixed(2)
                                       $scope.allPriceGrid[index].time =parseFloat(parseFloat(key.time) + parseFloat(price_in_amt)).toFixed(2)
                                      index++;
                                    });
                                  

                              }

                              else if(type == 'primary'){
                               

                               var index = 0;

                                  angular.forEach($scope.temp_primary, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=16; i++) {
                                      price_field = "pricing_"+i+"c";
                                      
                                      if(key[price_field] !== null) {
                                        
                                         $scope.allScreenPrimary[index][price_field] = parseFloat(parseFloat(key[price_field]) + parseFloat(price_in_amt)).toFixed(2)
                                      }
                                      
                                    }
                                      index++;
                                    });
                                  
                              } else if(type == 'secondary'){
                               

                               var index = 0;

                                  angular.forEach($scope.temp_secondary, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=16; i++) {
                                      price_field = "pricing_"+i+"c";
                                      if(key[price_field] !== null) {
                                      $scope.allScreenSecondary[index][price_field] = parseFloat(parseFloat(key[price_field]) + parseFloat(price_in_amt)).toFixed(2)
                                      }
                                    }
                                      index++;
                                    });
                                  
                              } else if(type == 'embroidery'){
                               

                               var index = 0;
                                 
                                  angular.forEach($scope.temp_embro, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=12; i++) {
                                      price_field = "pricing_"+i+"c";
                                      
                                      if(key[price_field] !== null) {
                                       $scope.allEmbroidery[index][price_field] = parseFloat(parseFloat(key[price_field]) + parseFloat(price_in_amt)).toFixed(2)
                                      }
                                    }
                                      index++;
                                    });
                                  
                              } else if(type == 'dtogarment'){
                               

                               var index = 0;
                                 
                                  angular.forEach($scope.temp_gar, function( key, value ) {
                                   var price_field;
                                   for (var i=1; i<=12; i++) {
                                      price_field = "pricing_"+i+"c";
                                      $scope.allGarment[index][price_field] = parseFloat(parseFloat(key[price_field]) + parseFloat(price_in_amt)).toFixed(2)
                                    }
                                      index++;
                                    });
                                  
                              }

                              

                        }

    }
})();