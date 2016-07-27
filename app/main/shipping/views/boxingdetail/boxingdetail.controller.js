(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('boxingdetailController', boxingdetailController);

    /** @ngInject */
    function boxingdetailController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;
        $scope.shipping_id = $stateParams.id;

        $scope.box_items = [];
        $scope.shipping_box_id = 0;

        $scope.getShippingBoxes = function()
        {
            $scope.box_items = [];
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;

            $http.post('api/public/shipping/getShippingBoxes',combine_array).success(function(result) {

                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;
                }
            });
        }

        $scope.getShippingBoxes();

        $scope.select_box = function(box_id)
        {
            $scope.shipping_box_id = box_id;
            $scope.box_items = $scope.shippingBoxes[box_id].boxItems;
        }

        $scope.reAllocate = function(box_id,box_item_id)
        {
            var order_main_data = {};
            var obj = {};
            obj['box_id'] =  box_id;
            order_main_data.data = angular.copy(obj);
            order_main_data.table = 'box_product_mapping';

            var condition_obj = {};
            condition_obj['id'] =  box_item_id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {
                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
                $scope.getShippingBoxes();
            });
        }
    }
})();
