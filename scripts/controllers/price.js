
app.controller('priceListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant) {
  
  $http.get('api/public/admin/price').success(function(result, status, headers, config) {

                                  $scope.price = result.data.records;
                                  $scope.pagination = AllConstant.pagination;

                         
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

app.controller('priceAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant','$filter', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant,$filter) {
   
                    if($stateParams.id) {

                           $http.post('api/public/admin/priceDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                      
                                     $scope.price = result.data.records[0];

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