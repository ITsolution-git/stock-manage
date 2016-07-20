(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('orderWaitController', orderWaitController);

    /** @ngInject */
    function orderWaitController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        var combine_array = {};
        combine_array.order_id = $stateParams.id;
        $scope.order_id = $stateParams.id;
        $scope.address_id = 0;

        $scope.assignedItems = [];

        $http.post('api/public/shipping/shipOrder',combine_array).success(function(result, status, headers, config) {
            if(result.data.success == '1') {
                $("#ajax_loader").hide();
               $scope.unshippedProducts = result.data.unshippedProducts;
               $scope.assignAddresses = result.data.assignAddresses;
               $scope.unAssignAddresses = result.data.unAssignAddresses;

               if($scope.address_id > 0)
               {
                    $scope.getProductByAddress($scope.address_id);
               }
            }
        });

        $scope.getProductByAddress = function(address_id)
        {
            $scope.address_id = address_id;

            var combine_array = {};
            combine_array.address_id = address_id;
            combine_array.order_id = $scope.order_id;
            
            $http.post('api/public/shipping/getProductByAddress',combine_array).success(function(result, status, headers, config) {
                
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                    $scope.assignedItems = result.data.products;
                }
            });
        }
    }
})();
