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
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        $scope.address_id = 0;
        $scope.shipping_id = 0;
        $scope.productSearch = '';
        $scope.selectedSizes = [];
        $scope.all_selected = 0;

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

        $scope.assignedItems = [];

        $scope.shipOrder = function()
        {
            var combine_array = {};
            combine_array.order_id = $scope.order_id;
            $("#ajax_loader").show();

            $http.post('api/public/shipping/getShippingOrdersDetail',combine_array).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $scope.assignAddresses = result.data.shippingData;
                    $scope.no_of_locations = result.data.shippingData.length;
                }
                else
                {
                    $scope.shippingData = [];
                    $scope.no_of_locations = '0';
                }
                $scope.total_order_qty = result.data.total_order_qty;
                $scope.undistributed_qty = result.data.undistributed_qty;
                $("#ajax_loader").hide();
            });
        }

        $scope.updateShippingType = function(id,order_adress_id,key)
        {
            var stype_main_data = {};
            var condition_obj = {};

            stype_main_data.table ='order_shipping_address_mapping';
            condition_obj['id'] = order_adress_id;

            $scope.name_filed = 'shipping_type_id';
            var obj = {};
            obj[$scope.name_filed] =  id;
            stype_main_data.data = angular.copy(obj);
            
            stype_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',stype_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
                $scope.getShippingMethod(id,key);
            });
        }

        $scope.updateShippingMethod = function(id,order_adress_id)
        {
            var smethod_main_data = {};
            var condition_obj = {};

            smethod_main_data.table ='order_shipping_address_mapping';
            condition_obj['id'] = order_adress_id;

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

        $scope.getShippingMethod = function(id,key)
        {
            var shipping_method_data = {};
            shipping_method_data.cond ={shipping_type_id:id};
            shipping_method_data.table ='shipping_method';

            $http.post('api/public/common/GetTableRecords',shipping_method_data).success(function(result) {

                if(result.data.success == '1')
                {
                    $scope.assignAddresses[key].shippingMethod = result.data.records;
                }
                else
                {
                    $scope.assignAddresses[key].shippingMethod = [];
                }
            });
        }

        $scope.updateShippingAll = function(name,value,id)
        {
            var order_main_data = {};

            order_main_data.table ='shipping';

            if(name == 'date_shipped') {
                value = new Date(value);
            }

            $scope.name_filed = angular.copy(name);
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

        var vm = this;
        vm.openaddDesignDialog = openaddDesignDialog;

        function openaddDesignDialog(ev,shipping)
        {
            $mdDialog.show({
                controller: 'EditShippingController',
                controllerAs: $scope,
                templateUrl: 'app/main/shipping/dialogs/orderwaitship/editshipping.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    shipping: shipping,
                    event: ev
                 },
                 onRemoving : $scope.returnFunction
            });
        }


        var allData = {};
        allData.table ='orders';
        allData.cond ={display_number:$stateParams.id,company_id:sessionService.get('company_id')}

        $http.post('api/public/common/GetTableRecords',allData).success(function(result)
        {
            if(result.data.success=='1')
            {
                $scope.order_id = result.data.records[0].id;
                $scope.display_number = result.data.records[0].display_number;
                $scope.getDetail();
            }
        });

        $scope.getDetail = function()
        {
            var combine_array_id = {};
            combine_array_id.company_id = sessionService.get('company_id');
            combine_array_id.id = $scope.order_id;
            $("#ajax_loader").show();

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                   //$scope.getShippingAddress();
                   $scope.shipOrder();
                } else {
                    $state.go('app.shipping');
                }
            });
        }

        $scope.returnFunction = function(){
            $state.reload();
        }

        // DYNAMIC POPUP FOR INSERT RECORDS
        $scope.openInsertPopup = function(path,ev,table)
        {
            var insert_params = {client_id:$scope.order.client_id,order_id:$scope.order_id};
            sessionService.openAddPopup($scope,path,insert_params,table);
        }

        $scope.toggle = function (item, list, key, flag) {

            var idx = list.indexOf(item);

            if (flag == true) {
                $scope.selectedSizes.splice(idx, 1);
                $scope.all_selected = 0;
                var UpdateArray = {};        // INSERT RECORD ARRAY
                UpdateArray.data = {'selected':'0'};
                UpdateArray.table ='shipping';
                UpdateArray.cond = {'id':item}
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                });
            }
            else {
                $scope.selectedSizes.push(item);
                var UpdateArray = {};        // INSERT RECORD ARRAY
                UpdateArray.data = {'selected':'1'};
                UpdateArray.table ='shipping';
                UpdateArray.cond = {'id':item}
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                });
            }
        };

        $scope.selectAll = function()
        {
            angular.forEach($scope.assignAddresses, function(value, key){
                value.selected = true;
            });
            var UpdateArray = {};        // INSERT RECORD ARRAY
            UpdateArray.data = {'selected':'1'};
            UpdateArray.table ='shipping';
            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                $scope.all_selected = 1;
            });
        }

        $scope.next = function()
        {
            console.log($scope.selectedSizes);
            if($scope.all_selected != '0' || $scope.selectedSizes.length > 0)
            {
                $state.go('app.shipping.boxingdetail',{id: $scope.display_number});
            }
            else
            {
                var data = {"status": "error", "message": "Please select at least one address to go next."}
                notifyService.notify(data.status, data.message);
            }
        }
    }
})();
