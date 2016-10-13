(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentController', shipmentController);

    /** @ngInject */
    function shipmentController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        $scope.role_slug = sessionService.get('role_slug');
        
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        if($stateParams.id == '' || $scope.allow_access == '0'){
            $state.go('app.shipping');
            return false;
        }

        
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
                  $state.go('app.shipping');
                     return false;
            }
        });

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                  $scope.miscData = result.data.records;
        });

        $scope.updateShippingAll = function(name,value,id)
        {
            
            var order_main_data = {};

            if(name == 'max_pack')
            {
                order_main_data.table ='purchase_detail';
            }
            else
            {
                order_main_data.table ='shipping';
            }


            if(name == 'approval_id')
            {
                order_main_data.table ='orders';
            }

            $scope.name_filed = name;
            var obj = {};
            obj[$scope.name_filed] =  value;

            if(name == 'shipping_type_id')
            {
               obj['shipping_method'] =  '';
            }
            order_main_data.data = angular.copy(obj);


            var condition_obj = {};
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

             if(name == 'shipping_type_id')
                {
                     $scope.shipping.shipping_method = '';
                }

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }

        $scope.UpdateShippingDate = function(name,value,id)
        {
            if(value != '')
            {
                value = new Date(value);
            }
            var order_main_data = {};
            order_main_data.table ='shipping';
            $scope.name_filed = name;
            
            var obj = {};
            obj[$scope.name_filed] =  value;
            order_main_data.data = angular.copy(obj);
            var condition_obj = {};
            
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }

        $scope.box_shipment = function(shipping_items)
        {
            
            if($scope.shipping.shipping_type_id == 0 || $scope.shipping.shipping_type_id == '')
            {
                var data = {"status": "error", "message": "Please select any shipping method."}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(shipping_items.length == 0){
                $("#ajax_loader").hide();
                    var data = {"status": "error", "message": "There are no items for boxing."}
                    notifyService.notify(data.status, data.message);
                    return false;
            }
            $("#ajax_loader").show();
            $http.post('api/public/shipping/CreateBoxShipment',shipping_items).success(function(result) {

                if(result.data.success == '1') {
                    var data = {"status": "success", "message": "Boxes created Successfully."}
                    notifyService.notify(data.status, data.message);
                }
                else
                {
                    var data = {"status": "info", "message": "Delete all boxes in the boxes tab to rebox shipment."}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
                $state.go('app.shipping.boxingdetail',{id: $stateParams.id});
            });
                      
        }
    }
})();
