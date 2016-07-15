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

        var combine_array_id = {};
        combine_array_id.order_id = $stateParams.id;
        $scope.order_id = $stateParams.id;

        $http.post('api/public/shipping/shipOrder',combine_array_id).success(function(result, status, headers, config) {
            if(result.data.success == '1') {
                $("#ajax_loader").hide();
               $scope.order = result.data.records[0];
               $scope.order_items = result.data.order_item;
            }
        });

        //Dummy models data
        vm.orderItems = [
            {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             }];
        vm.addresses = [
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"}
        ];
        vm.selectedOrderItems = [
            {"sizeGroup": "Mens", "product": "12345", "size": "M", "color": "Black"}
        ];
    }
})();
