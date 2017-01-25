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
        $scope.selectedSizes = [];
        $scope.product = [];
        $scope.location = '';
        $scope.address_id = 0;
        $scope.page = 1;
        $scope.all_address_selcted = '1';

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

        var state_data = {};
        state_data.table ='state';

        $http.post('api/public/common/GetTableRecords',state_data).success(function(result) {

            if(result.data.success == '1')
            {
                $scope.states_all =result.data.records;
            }
            else
            {
                $scope.states_all = [];
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
            combine_array_id.page = $scope.page;
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
                $scope.pagination = result.data.pagination;
            });
        }

        $scope.getProductByAddress = function(address_id)
        {
            $scope.all_address_selcted = 0;
            angular.forEach($scope.distributionData, function(value, key){
                if(value.id != address_id)
                value.is_selected = 0;
            });

            angular.forEach($scope.orderProducts, function(value, key){
                value.selected = false;
            });

            $scope.address_id = address_id;
            $scope.distributionData[address_id].is_selected = 1;
            $scope.addressProducts = [];
            $scope.location = $scope.distributionData[$scope.address_id].description;
            $scope.shippingType = $scope.distributionData[$scope.address_id].shippingType;
            $scope.shippingMethod = $scope.distributionData[$scope.address_id].shippingMethod;

            $scope.selectedSizes = [];
        }

        $scope.returnFunction = function()
        {
            $state.reload();
        }

        var vm = this;
        vm.openaddExistingLocatioDilog = openaddExistingLocatioDilog;

        var vm = this;

        function openaddExistingLocatioDilog(ev)
        {
            $mdDialog.show({
                controller: 'ExistingLocationController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/distribution/existinglocation.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    order_id: $scope.order_id,
                    client_id: $scope.order.client_id,
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
                    $scope.shippingMethod = [];
                }
            });
        }

        $scope.updateShippingType = function(id)
        {
            var stype_main_data = {};
            var condition_obj = {};

            stype_main_data.table ='order_shipping_address_mapping';
            condition_obj['id'] =  $scope.distributionData[$scope.address_id].order_adress_id;

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

            smethod_main_data.table ='order_shipping_address_mapping';
            condition_obj['id'] =  $scope.distributionData[$scope.address_id].order_adress_id;

            $scope.name_filed = 'shipping_method_id';
            var obj = {};
            obj[$scope.name_filed] =  id;
            smethod_main_data.data = angular.copy(obj);
            
            smethod_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',smethod_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }
        $scope.allocateDistQty = function(key,productArr)
        {
            if(productArr.distributed_qnty == '')
            {
                notifyService.notify('error', 'Please enter valid quantity');
                $scope.addressProducts[key].distributed_qnty = productArr.old_distributed_qnty;
                return false;
            }

            /*var remaining_qnty = parseInt(productArr.remaining_qnty) + parseInt(productArr.old_distributed_qnty);

            if(parseInt(productArr.distributed_qnty) > parseInt(remaining_qnty))
            {
                $scope.addressProducts[key].distributed_qnty = productArr.old_distributed_qnty;
                notifyService.notify('error', 'You cannot allocate more than '+remaining_qnty+' quantity');
                return false;
            }*/

            //$("#ajax_loader").show();

            var combine_array = {};
            combine_array.product = productArr;
            if(productArr.address_id)
            {
                combine_array.address_id = productArr.address_id;
            }
            else
            {
                combine_array.address_id = $scope.address_id;
            }
            combine_array.order_id = $scope.order_id;
            combine_array.company_id = sessionService.get('company_id');

            $http.post('api/public/shipping/addProductToShip',combine_array).success(function(result, status, headers, config) {
                
                if(result.data.success == '1') {

                    angular.forEach($scope.orderProducts, function(value, key){
                        if(productArr.id == value.id) {
                            value.distributed_qnty = result.data.distributed_qnty;
                            value.remaining_qnty = result.data.remaining_qnty;
                        }
                    });
                    $scope.total_order_qty = result.data.total_order_qty;
                    $scope.total_shipped_qnty = result.data.total_shipped_qnty;
                    $scope.distributionData[combine_array.address_id].addressTotalProducts =  result.data.addressTotalProducts;
                }
                else
                {
                    $scope.addressProducts[key].distributed_qnty = productArr.old_distributed_qnty;
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
            });
        }

        $scope.openInsertPopup = function(path,ev,table)
        {
            var insert_params = {client_id:$scope.order.client_id,order_id:$scope.order_id};
            sessionService.openAddPopup($scope,path,insert_params,table);
        }

        $scope.getPage = function(param)
        {
            if(param == 'forward')
            {
                if($scope.pagination.page < $scope.pagination.size)
                {
                    $scope.page = $scope.page + 1;
                    $scope.getDistributionDetail();
                }
            }
            else
            {
                if($scope.page > 1)
                {
                    $scope.page = $scope.page - 1;
                    $scope.getDistributionDetail();
                }
            }
        }
        $scope.toggle = function (item, list, key, product) {

            var idx = list.indexOf(item);

            if (idx > -1) {
                $scope.selectedSizes.splice(idx, 1);
                if($scope.selectedSizes.length > 0)
                {
                    for(var i = 0; i < $scope.addressProducts.length; i++) {
                        var obj = $scope.addressProducts[i];

                        if(item === $scope.addressProducts[i].id) {
                            $scope.addressProducts.splice(i, 1);
                        }
                    }
                }
                else
                {
                    $scope.addressProducts = [];
                }
            }
            else {
                $scope.selectedSizes.push(item);

                var combine_array = {};
                if($scope.address_id > 0)
                {
                    combine_array.address_id = $scope.address_id;    
                }
                combine_array.order_id = $scope.order_id;
                combine_array.id = item;
                combine_array.product = product;

                $http.post('api/public/distribution/getSizeBySelect',combine_array).success(function(result, status, headers, config) {
                    
                    if(result.data.success == '1') {
                        if(result.data.products.length == 1)
                        {
                            $scope.addressProducts.push(result.data.products[0]);
                        }
                        else
                        {
                            angular.forEach(result.data.products, function(value, key){
                                $scope.addressProducts.push(value);
                            });
                        }
                        $scope.addressProducts.sort();
                    }
                    else
                    {
                        if($scope.address_id > 0)
                        {
                            $scope.addressProducts.push(result.data.products);
                            $scope.addressProducts.sort();
                        }
                    }
                });
            }
        };
        $scope.getAllLocation = function()
        {
            $scope.all_address_selcted = 1;
            $scope.addressProducts = [];
            $scope.location == '';
            $scope.address_id = '0';

            angular.forEach($scope.distributionData, function(value, key){
                value.is_selected = 0;
            });

            angular.forEach($scope.orderProducts, function(value, key){
                value.selected = false;
            });

            $state.reload();
        }
    }
})();
