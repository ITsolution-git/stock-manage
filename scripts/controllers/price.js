
app.controller('priceListCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
  AuthService.AccessService('FM');
   $("#ajax_loader").show();
  $http.get('api/public/admin/price').success(function(result, status, headers, config) {

                                  $scope.price = result.data.records;
                                  $scope.pagination = AllConstant.pagination;
                                  $("#ajax_loader").hide();

                                  var init;

                                  $scope.searchKeywords = '';
                                  $scope.filteredPrice = [];
                                  $scope.row = '';
                                  $scope.select = function (page) {
                                      var end, start;
                                      start = (page - 1) * $scope.numPerPage;
                                      end = start + $scope.numPerPage;
                                      return $scope.currentPagePrice = $scope.filteredPrice.slice(start, end);
                                  };
                                  $scope.onFilterChange = function () {
                                      $scope.select(1);
                                      $scope.currentPage = 1;
                                      return $scope.row = '';
                                  };
                                  $scope.onNumPerPageChange = function () {
                                      $scope.select(1);
                                      return $scope.currentPage = 1;
                                  };
                                  $scope.onOrderChange = function () {
                                      $scope.select(1);
                                      return $scope.currentPage = 1;
                                  };
                                  $scope.search = function () {
                                      $scope.filteredPrice = $filter('filter')($scope.price, $scope.searchKeywords);
                                      return $scope.onFilterChange();
                                  };
                                  $scope.order = function (rowName) {
                                      if ($scope.row === rowName) {
                                          return;
                                      }
                                      $scope.row = rowName;
                                      $scope.filteredPrice = $filter('orderBy')($scope.price, rowName);
                                      return $scope.onOrderChange();
                                  };
                                  $scope.numPerPageOpt = [10, 20, 50, 100];
                                  $scope.numPerPage = 10;
                                  $scope.currentPage = 1;
                                  $scope.currentPagePrice = [];

                                  init = function () {
                                      $scope.search();

                                      return $scope.select($scope.currentPage);
                                  };
                                  return init();
                         
                          });

                         $scope.delete = function (price_id) {
                          
                            var permission = confirm(AllConstant.deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/priceDelete',price_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('setting.price');
                                            $("#price_"+price_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } 

}]);

app.controller('priceAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
   
AuthService.AccessService('FM');
                        $scope.percentagecalc = function($event) {
                          
                                var price_in_percentage = $event.target.value;

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

                        }



                        $scope.amtcalc = function($event) {
                          
                                var price_in_amt = $event.target.value;
                                 
                                 if(!price_in_amt.length){
                                  price_in_amt = 0;
                                 }
                                 
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

                        }



                    if($stateParams.id) {
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

                                      
                                      $scope.allPriceGrid = result.data.allPriceGrid;
                                      $scope.allScreenPrimary = result.data.allScreenPrimary;
                                      $scope.allScreenSecondary = result.data.allScreenSecondary;
                                      $scope.allGarmentMackup = result.data.allGarmentMackup;
                                      $scope.allGarment = result.data.allGarment;
                                      $scope.embroswitch = result.data.embroswitch[0];
                                      $scope.allEmbroidery = result.data.allEmbroidery;
                                      
                                      $("#ajax_loader").hide();

                                     
                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

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
                               
                          $http.post('api/public/admin/priceEdit',combine_array_data).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {


                                   $state.go('setting.price');
                                    return false;
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                          /* $http.post('api/public/admin/priceAdd',combine_array_data).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                   $state.go('setting.price');
                                    return false;
                                   
                             } 
                         
                          });*/
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

                            

                            var permission = confirm("Are you sure you want to duplicate this record ?");

                            if (permission == true) {
                            
                                  $http.post('api/public/admin/priceGridDuplicate',combine_array_data).success(function(result, status, headers, config) {
        
                                  if(result.data.success == '1') {

                                         $state.go('setting.price');
                                          return false;
                                         
                                   } 
                         
                              });

                            }
                        } 


                        $scope.duplicateprimary = function (price_id,price_primary) {
                          
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

                                         $("ul.nav-tabs li").removeClass("active"); 
                                         $( "ul li:nth-child(2)").addClass( "active" );

                                        
                                         
                                   } 
                         
                              });

                            }
                        } 


                       

}]);