(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionController', DistributionController);

    /** @ngInject */
    function DistributionController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        $scope.orderDetail = function(){
            $("#ajax_loader").show();
            
            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.order_id = $stateParams.id;
            

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
            combine_array_id.id = $stateParams.id;
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
            combine_array_id.order_id = $stateParams.id;

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

        $scope.orderDetail();
        $scope.designDetail();
        $scope.getDistProductAddress();

        var vm = this;
        vm.openaddAddressDialog = openaddAddressDialog;
        vm.distributionDistributed = {
            "productshipped": "800",
            "Total": "100",
        };
        vm.distributionLocation = {
            "location": "231",
        };
        vm.distProducts = [
            {productName: "Product Name 1", jobName: "Job Name1", job: "#", totalAllocated: "0/120", buttn: "Distributed"},
            {productName: "Product Name 2", jobName: "Job Name1.1", job: "#", totalAllocated: "0/120", buttn: "Distributed"},
            {productName: "Product Name 3", jobName: "Job Name1.2", job: "#", totalAllocated: "80/120", buttn: "Edit"}
        ]

                ;
        vm.distlocations = [
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"},
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"},
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"}
        ]

                ;
        vm.distInfo = {
            customerPO: "######",
            sales: "Keval Baxi",
            blind: "Yes",
            accountManager: "Nancy McPhee",
            mainContact: "Joshi Goodman",
            priceGrid: "ABC Grid",
        };
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
                onRemoving : $scope.reloadPage
            });
        }

        //Dummy models data


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
                }
            });
        }

        function createDistribution(ev,action,product_id,product_name)
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
                    product_id: product_id,
                    order_id: $scope.order_id,
                    client_id: $scope.order.client_id,
                    product_name: product_name,
                    event: ev
                }
            });
        }


        vm.productSearch = null;

    }
})();
