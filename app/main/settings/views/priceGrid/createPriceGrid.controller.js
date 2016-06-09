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

    }
})();