(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionController', DistributionController);

    /** @ngInject */
    function DistributionController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
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

        $scope.shippingType = [];
        $scope.shippingMethod = [];
        $scope.addressProducts = [];
        $scope.location = '';
        $scope.address_id = 0;

        // change display number to order Id for fetching the order data
        var order_data = {};
        order_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
        order_data.table ='orders';

        $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {

            if(result.data.success == '1')
            {
                $scope.vendorRecord =result.data.records;
                $scope.order_id = result.data.records[0].id;

                $scope.orderDetail();
                $scope.getDistributionDetail();
            }
            else
            {
                $state.go('app.order');
            }
        });


        $scope.orderDetail = function(){
            $("#ajax_loader").show();

            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.order_id = $scope.order_id;


            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                } else {
                    $state.go('app.order');
                }
            });
        }

        $scope.getDistributionDetail = function(){

            var combine_array_id = {};
            combine_array_id.order_id = $scope.order_id;
            $scope.controls = [];

            $http.post('api/public/distribution/getDistributionDetail',combine_array_id).success(function(result, status, headers, config) {

                if(result.data.success == '1') {
                    $scope.controls = [{}];
                    $scope.distributionData = result.data.distributionData;

                    if($scope.address_id > 0)
                    {
                        $scope.getProductByAddress($scope.address_id);
                    }
                }
                else {
                    $scope.distributionData = [];
                }
                $scope.total_order_qty = result.data.total_order_qty;
                $scope.total_shipped_qnty = result.data.total_shipped_qnty;
                $scope.orderProducts = result.data.orderProducts;
            });
        }

        $scope.getProductByAddress = function(address_id)
        {
            $scope.address_id = address_id;
            $scope.addressProducts = $scope.distributionData[$scope.address_id].products;
            $scope.distributionData[$scope.address_id].is_selected = 1;
            $scope.location = $scope.distributionData[$scope.address_id].description;
            $scope.shippingType = $scope.distributionData[$scope.address_id].shippingType;
            $scope.shippingMethod = $scope.distributionData[$scope.address_id].shippingMethod;
        }

        $scope.returnFunction = function()
        {
            $state.reload();
        }

        var vm = this;
        vm.openaddAddressDialog = openaddAddressDialog;

        var vm = this;

        function openaddAddressDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddAddressController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addAddress/addAddress.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Orders: $scope.order,
                    event: ev
                },
                onRemoving : $scope.returnFunction
            });
        }

        $scope.getShippingMethod = function(id)
        {
            var shipping_method_data = {};
            shipping_method_data.cond ={shipping_type_id:id};
            shipping_method_data.table ='shipping_method';

            $http.post('api/public/common/GetTableRecords',shipping_method_data).success(function(result) {

                if(result.data.success == '1')
                {
                    $scope.shippingMethod = $scope.vendorRecord =result.data.records;
                }
                else
                {
                    $scope.shippingMethod
                }
            });
        }

        $scope.updateShippingType = function(id)
        {
            var stype_main_data = {};
            var condition_obj = {};

            if($scope.distributionData[$scope.address_id].shipping_id > 0)
            {
                stype_main_data.table ='shipping';
                condition_obj['id'] =  $scope.distributionData[$scope.address_id].shipping_id;
            }
            else
            {
                stype_main_data.table ='order_shipping_address_mapping';
                condition_obj['id'] =  $scope.distributionData[$scope.address_id].order_adress_id;
            }

            $scope.name_filed = 'shipping_type_id';
            var obj = {};
            obj[$scope.name_filed] =  id;
            stype_main_data.data = angular.copy(obj);
            
            stype_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',stype_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
                $scope.getShippingMethod(id);
            });
        }

        $scope.updateShippingMethod = function(id)
        {
            var smethod_main_data = {};
            var condition_obj = {};

            if($scope.distributionData[$scope.address_id].shipping_id > 0)
            {
                smethod_main_data.table ='shipping';
                condition_obj['id'] =  $scope.distributionData[$scope.address_id].shipping_id;
            }
            else
            {
                smethod_main_data.table ='order_shipping_address_mapping';
                condition_obj['id'] =  $scope.distributionData[$scope.address_id].order_adress_id;
            }

            $scope.name_filed = 'shipping_method';
            var obj = {};
            obj[$scope.name_filed] =  id;
            smethod_main_data.data = angular.copy(obj);
            
            smethod_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',smethod_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }
        $scope.allocateDistQty = function(productArr)
        {
            if($scope.address_id == 0)
            {
                var data = {"status": "error", "message": "Please select address"}
                notifyService.notify(data.status, data.message);
                return false;
            }

            //$("#ajax_loader").show();

            var combine_array = {};
            combine_array.product = productArr;
            combine_array.address_id = $scope.address_id;
            combine_array.order_id = $scope.order_id;
            combine_array.company_id = sessionService.get('company_id');

            $http.post('api/public/shipping/addProductToShip',combine_array).success(function(result, status, headers, config) {
                
                if(result.data.success == '1') {
                    $scope.getDistributionDetail();
                }
                else
                {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
            });
        }
    }
})();
