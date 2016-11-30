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
                $scope.designDetail();
                $scope.getDistProductAddress();
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
                $scope.states_all  = result.data.records;
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

        $scope.designDetail = function(){

            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/order/designListing',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.designs = result.data.records.all_design;
                   $scope.total_unit = result.data.records.total_unit;
                }
                else {
                    $scope.designs = [];
                    $scope.total_unit = 0;
                }

                if($scope.total_unit == undefined)
                {
                    $scope.total_unit = 0;            
                }
            });
        }

        $scope.getDistProductAddress = function(){

            var combine_array_id = {};
            combine_array_id.order_id = $scope.order_id;

            $http.post('api/public/distribution/getDistProductAddress',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.success == '1') {
                   $scope.products = result.products;
                   $scope.distribution_address = result.distribution_address;
                }
                else {
                    $scope.products = [];
                    $scope.distribution_address = [];
                }
            });
        }

        $scope.returnFunction = function()
        {
            $state.reload();
        }

        var vm = this;
        vm.openaddAddressDialog = openaddAddressDialog;
        vm.openAddProductDialog = openAddProductDialog;
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

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        vm.dtInstanceCB = dtInstanceCB;
        vm.openAddProductDialog = openAddProductDialog;
        vm.createDistribution = createDistribution;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }

        function openAddProductDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddProductController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addProduct/addProduct.html',
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

        function createDistribution(ev,action,product_array)
        {
            $mdDialog.show({
                controller: 'DistributionProductController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/views/distributionProduct/distributionProduct.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Addresses: $scope.distribution_address,
                    action: action,
                    order_id: $scope.order_id,
                    client_id: $scope.order.client_id,
                    product_arr: product_array,
                    event: ev
                },
                onRemoving : $scope.returnFunction
            });
        }
        $scope.openInsertPopup = function(path,ev,table)
        {
            var insert_params = {client_id:$scope.order.client_id};
            sessionService.openAddPopup($scope,path,insert_params,table);
        }
        vm.productSearch = null;
    }
})();
