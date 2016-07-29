(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentOverviewController', shipmentOverviewController);

    /** @ngInject */
    function shipmentOverviewController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        $scope.shipping_id = $stateParams.id;

        $scope.getShippingOverview = function()
        {
            $("#ajax_loader").show();
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;

            $http.post('api/public/shipping/getShippingOverview',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;
                    $scope.shippingItems =result.data.shippingItems;
                    $scope.shipping =result.data.records[0];

                    if($scope.shipping.boxing_type == '0') {
                        $scope.shipping.boxing_type = 'Retail';
                    }
                    if($scope.shipping.boxing_type == '1') {
                        $scope.shipping.boxing_type = 'Standard';
                    }
                    if($scope.shipping.boxing_type == '1') {
                        $scope.shipping.boxing_type = 'USPS';
                    }
                    if($scope.shipping.boxing_type == '2') {
                        $scope.shipping.boxing_type = 'Fedex';
                    }
                }
            });
        }
        $scope.getShippingOverview();
    }
})();
