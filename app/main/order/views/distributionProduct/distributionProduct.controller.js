(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionProductController', DistributionProductController);

    /** @ngInject */
    function DistributionProductController(Addresses,action,product_arr,order_id,client_id,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        var order_data = {};
        order_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
        order_data.table ='orders';
      
        $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
            if(result.data.success == '1') 
            {
                $scope.order_id = result.data.records[0].id;
                $scope.orderDetail();
            } 
            else
            {
                $state.go('app.order');
            }
      });

        $scope.Addresses = Addresses;
        $scope.product_name = product_arr.product_name;

        $scope.searchQuery = '';
        $scope.address_id = 0;
        $scope.product_id = product_arr.product_id;
        $scope.design_product_id = product_arr.design_product_id;
        $scope.products = [];

        $scope.orderDetail = function(){
            $("#ajax_loader").show();
            
            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');
            

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

        var combine_array_id = {};
        combine_array_id.product_id = $scope.product_id;
        combine_array_id.order_id = order_id;

/*        $http.post('api/public/distribution/getDistSizeByProduct',combine_array_id).success(function(result) {
            
            if(result.success == '1') {
               $scope.products = result.products;
            }
        });*/

        $scope.getDistributionDetail = function()
        {
            $("#ajax_loader").show();
            var combine_array_id = {};
            combine_array_id.product_id = $scope.product_id;
            combine_array_id.order_id = order_id;
            combine_array_id.client_id = client_id;
            combine_array_id.design_product_id = $scope.design_product_id;

            $http.post('api/public/distribution/getDistAddress',combine_array_id).success(function(result) {
                
                $("#ajax_loader").hide();
                if(result.success == '1') {
                   $scope.addresses = result.addresses;
                   $scope.selected_addresses = result.selected_addresses;

                   if($scope.address_id > 0)
                   {
                        $scope.getProductByAddress($scope.address_id);
                   }
               }
            });
        }

        $scope.getDistributionDetail();

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        vm.dtInstanceCB = dtInstanceCB;
        vm.openaddAddressDialog = openaddAddressDialog;
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

        $scope.getProductByAddress = function(address_id)
        {
            $scope.address_id = address_id;
            $scope.products = $scope.addresses[$scope.address_id].sizeArr;
        }

        $scope.allocate = function()
        {
            if($scope.address_id == 0)
            {
                var data = {"status": "error", "message": "Please select atleast one address to distribute"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            var combine_array = {};
            combine_array.product_id = $scope.product_id;
            combine_array.order_id = order_id;
            combine_array.client_id = client_id;
            combine_array.company_id = sessionService.get('company_id')
//            combine_array.address_ids = $scope.selected_addresses;
            combine_array.address_id = $scope.address_id;
            combine_array.products = $scope.products;
            combine_array.action = action;

            $("#ajax_loader").show();
            $http.post('api/public/distribution/addEditDistribute',combine_array).success(function(result) {
                
                $("#ajax_loader").hide();
                if(result.success == 1) {
                    var data = {"status": "success", "message": result.message}
                    notifyService.notify(data.status, data.message);
                    $scope.getDistributionDetail();
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
            combine_array.product_id = $scope.product_id;
            combine_array.order_id = order_id;
            combine_array.client_id = client_id;
            combine_array.search = $scope.searchQuery;
            combine_array.design_product_id = $scope.design_product_id;

            $http.post('api/public/distribution/getDistAddress',combine_array).success(function(result) {
                if(result.success == '1') {
                   $scope.addresses = result.addresses;
                }
            });
        }
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
    }
})();