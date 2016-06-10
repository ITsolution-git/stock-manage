(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('AffiliateViewController', AffiliateViewController);

    /** @ngInject */


    function AffiliateViewController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
        $scope.productSearch = '';
        $scope.vendor_id = 0;
        $scope.company_id = sessionService.get('company_id');

       $scope.designDetail = function(){
         $("#ajax_loader").show();
        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    
                     $("#ajax_loader").hide();
                    result.data.records[0].hands_date = new Date(result.data.records[0].hands_date);
                    result.data.records[0].shipping_date = new Date(result.data.records[0].shipping_date);
                    result.data.records[0].start_date = new Date(result.data.records[0].start_date);
                    $scope.order_id = result.data.records[0].order_id;

                    $scope.designInforamtion = result.data.records[0];

                } else {
                    $state.go('app.order');
                }
                
            });
        }

        $scope.designProductData = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/product/designProduct',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                  
                    $scope.designProduct = result.data.records;
                    $scope.productData = result.data.productData.product[0];
                    $scope.colorName = result.data.colorName;
                    $scope.colorId = result.data.colorId;
                    $scope.is_supply = result.data.is_supply;
                    $scope.calculate_data = result.data.calculate_data[0];
                    $scope.productData.product_image_view = "https://www.ssactivewear.com/"+$scope.productData.product_image;


                } else {
                    
                }
                
            });
        }

       $scope.designPosition = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    $scope.order_design_position = result.data.order_design_position;

                }
                
            });
        }

        $scope.designDetail();
        $scope.designPosition();
        $scope.designProductData();

        var vm = this;
        //Dummy models data
    }
})();
