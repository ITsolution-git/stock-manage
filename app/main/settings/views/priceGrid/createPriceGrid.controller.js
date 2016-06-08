(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('CreatePriceGridDialogController', CreatePriceGridDialogController);

    /** @ngInject */
    function CreatePriceGridDialogController($mdDialog,$controller,$state,$scope,sessionService,$resource,$http,$stateParams)
    {
       

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

    }
})();