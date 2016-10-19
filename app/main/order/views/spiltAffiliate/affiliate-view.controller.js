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


         // change display number to design Id for fetching the order data
          var design_data = {};
           design_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           design_data.table ='order_design';
          
          $http.post('api/public/common/GetTableRecords',design_data).success(function(result) {
            
              
              if(result.data.success == '1') 
              {
                 
                  $scope.design_id = result.data.records[0].id;
                  $scope.design_display_number = $stateParams.id;
                  $scope.designDetail();
                  $scope.designPosition();
                  $scope.designProductData();
                             

              } else {
                $state.go('app.order');
              }
          });



       $scope.designDetail = function(){
         $("#ajax_loader").show();
        var combine_array_id = {};
            combine_array_id.id = $scope.design_id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                $("#ajax_loader").hide();
                if(result.data.success == '1') {
                     
                    $scope.order_id = result.data.records[0].order_id;

                    $scope.designInforamtion = result.data.records[0];

                } else {
                    $state.go('app.order');
                }
                
            });
        }

        $scope.designProductData = function(){
            $("#ajax_loader").show();
            var combine_array_id = {};
            combine_array_id.id = $scope.design_id;
            
            $http.post('api/public/product/designProduct',combine_array_id).success(function(result, status, headers, config) {
                
                $("#ajax_loader").hide();
                if(result.data.success == '1') {
                    $scope.productData = result.data.productData;
                }
                else{
                    $scope.productData = [];                    
                }
            });
        }

       $scope.designPosition = function(){

        var combine_array_id = {};
            combine_array_id.id = $scope.design_id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.total_pos_qnty = 0;
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    $scope.order_design_position = result.data.order_design_position;
                    $scope.total_pos_qnty = result.data.total_pos_qnty;
                }
                else{
                    $scope.order_design_position = [];
                    $scope.total_pos_qnty = 0;
                }
            });
        }

       

        var vm = this;
        //Dummy models data
    }
})();
