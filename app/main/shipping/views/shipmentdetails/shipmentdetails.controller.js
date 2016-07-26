(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentController', shipmentController);

    /** @ngInject */
    function shipmentController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        var combine_array_id = {};
        combine_array_id.shipping_id = $stateParams.id;
        combine_array_id.company_id = sessionService.get('company_id');

        $http.post('api/public/shipping/shippingDetail',combine_array_id).success(function(result, status, headers, config) {
            if(result.data.success == '1') {
                $scope.shippingItems = result.data.shippingItems;
                $scope.shipping_type = result.data.shipping_type;
                $scope.shipping = result.data.records[0];
            }
            else {
                $scope.shippingItems = [];
            }
        });
    }
})();
