(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionProductController', DistributionProductController);

    /** @ngInject */
    function DistributionProductController(Addresses,action,product_id,order_id,client_id,product_name,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        $scope.Addresses = Addresses;
        $scope.product_name = product_name;

        $scope.searchQuery = '';

        var combine_array_id = {};
        combine_array_id.product_id = product_id;

        $http.post('api/public/distribution/getDistSizeByProduct',combine_array_id).success(function(result) {
            
            if(result.success == '1') {
               $scope.products = result.products;
            }
        });

        var combine_array_id = {};
        combine_array_id.product_id = product_id;
        combine_array_id.order_id = order_id;
        combine_array_id.client_id = client_id;

        $http.post('api/public/distribution/getDistAddress',combine_array_id).success(function(result) {
            
            if(result.success == '1') {
               $scope.addresses = result.addresses;
               $scope.selected_addresses = result.selected_addresses;
            }
        });

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        vm.dtInstanceCB = dtInstanceCB;
        vm.openAddProductDialog = openAddProductDialog;
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
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
        vm.productSearch = null;
        
        $scope.toggle = function (item, list) {
            var idx = list.indexOf(item);
            if (idx > -1) {
              list.splice(idx, 1);
            }
            else {
              list.push(item);
            }
        };
        $scope.exists = function (item, list) {
            return list.indexOf(item) > -1;
        };

        $scope.allocate = function()
        {
            console.log($scope.selected_addresses);
            if($scope.selected_addresses.length == 0)
            {
                var data = {"status": "error", "message": "Please select atleast one address to distribute"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            var combine_array = {};
            combine_array.product_id = product_id;
            combine_array.order_id = order_id;
            combine_array.client_id = client_id;
            combine_array.address_ids = $scope.selected_addresses;
            combine_array.products = $scope.products;
            combine_array.action = action;

            $http.post('api/public/distribution/addEditDistribute',combine_array).success(function(result) {
                if(result.success == 1) {
                    var data = {"status": "success", "message": result.message}
                    notifyService.notify(data.status, data.message);
                    $mdDialog.hide();
                }
                else {
                    var data = {"status": "error", "message": result.message}
                    notifyService.notify(data.status, data.message);
                }
            });
        }

        $scope.cancel = function()
        {
            $mdDialog.hide();
        }

        $scope.filterAddress = function()
        {
            var combine_array = {};
            combine_array.product_id = product_id;
            combine_array.order_id = order_id;
            combine_array.client_id = client_id;
            combine_array.search = $scope.searchQuery;

            $http.post('api/public/distribution/getDistAddress',combine_array).success(function(result) {
                if(result.success == '1') {
                   $scope.addresses = result.addresses;
                }
            });
        }
    }
})();
