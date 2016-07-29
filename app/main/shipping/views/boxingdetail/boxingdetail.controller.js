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
            $("#ajax_loader").show();
            $scope.box_items = [];
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;

            $http.post('api/public/shipping/getShippingBoxes',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;

                    if($scope.shipping_box_id > 0)
                    {
                        $scope.select_box($scope.shipping_box_id);
                    }
                }
            });
        }

        $scope.getShippingBoxes();

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

        $scope.select_box = function(box_id)
        {
            $scope.shipping_box_id = box_id;
            $scope.box_items = $scope.shippingBoxes[box_id].boxItems;
        }

        $scope.update_box_qty = function(box)
        {
            $("#ajax_loader").show();
            
            if(box.md == '' || box.md == undefined)
            {
                box.md = 0;            
            }
            if(box.spoil == '' || box.spoil == undefined)
            {
                box.spoil = 0;       
            }

            var combine = parseInt(box.md) + parseInt(box.spoil);
            box.actual =  parseInt(box.boxed_qnty) - parseInt(combine);

            var ship_data = {};
            ship_data['table'] ='shipping_box';
            ship_data.data = {'actual':box.actual, 'md':box.md, 'spoil':box.spoil};
            ship_data.cond = {'id' : box.box_id};

            $http.post('api/public/common/UpdateTableRecords',ship_data).success(function(result) {
                if(result.data.success == 1)
                {
                    $scope.getShippingBoxes();
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.delete_box = function(id)
        {
            $scope.shipping_box_id = 0;
            $("#ajax_loader").show();
            $http.post('api/public/shipping/DeleteBox',id).success(function(result) {
                $scope.getShippingBoxes();
                var data = {"status": "success", "message": "Data Deleted Successfully."}
                notifyService.notify(data.status, data.message);
                $("#ajax_loader").hide();
            });
        }
    }
})();
