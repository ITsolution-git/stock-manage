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

        $scope.address_id = 0;
        $scope.shipping_id = 0;
        $scope.productSearch = '';

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
            
            $http.post('api/public/shipping/shipOrder',combine_array).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                    $scope.unshippedProducts = result.data.unshippedProducts;

                   /*if($scope.unshippedProducts.length == '0')
                   {
                        var UpdateArray = {};
                        UpdateArray.table ='orders';
                        UpdateArray.data = {shipping_status:3};
                        UpdateArray.cond = {id:$scope.order_id};

                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                        {
                            
                        });
                   }*/

                   if($scope.address_id > 0)
                   {
                        var addr_arr = {};
                        addr_arr.id = $scope.address_id;
                        addr_arr.shipping_id = $scope.shipping_id;
                        $scope.getProductByAddress(addr_arr);
                        $scope.getShippingAddress();
                   }
                }
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

        var state_data = {};
        state_data.table ='state';

        $http.post('api/public/common/GetTableRecords',state_data).success(function(result) {

            if(result.data.success == '1') 
            {
                $scope.states_all  = result.data.records;
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
                   $scope.getShippingAddress();
                   $scope.shipOrder();
                } else {
                    $state.go('app.shipping');
                }
            });    
        }
        
        $scope.getProductByAddress = function(address)
        {
            $scope.address_id = address.id;
            $scope.shipping_id = address.shipping_id;

            if($scope.shipping_id == undefined)
            {
                $scope.shipping_id = 0;
                $scope.assignedItems = [];
            }
            $("#ajax_loader").show();

/*            if($scope.shipping_id > 0)
            {*/
                var combine_array = {};
                combine_array.address_id = $scope.address_id;
                combine_array.order_id = $scope.order_id;
                
                $http.post('api/public/shipping/getProductByAddress',combine_array).success(function(result, status, headers, config) {
                    
                    if(result.data.success == '1') {
                        $("#ajax_loader").hide();
                        $scope.assignedItems = result.data.products;
                    }
                });
//            }
        }

        $scope.getShippingAddress = function()
        {
            var combine_array = {};
            combine_array.client_id = $scope.order.client_id;
            combine_array.id = $scope.order.id;
            combine_array.search = $scope.productSearch;
            combine_array.address_id = $scope.address_id;
            $("#ajax_loader").show();
            
            $http.post('api/public/shipping/getShippingAddress',combine_array).success(function(result, status, headers, config) {
                
                if(result.data.success == '1') {
                    $scope.assignAddresses = result.data.assignAddresses;
                    $scope.unAssignAddresses = result.data.unAssignAddresses;
                    $scope.shipping_id = result.data.shipping_id;
                }
                $("#ajax_loader").hide();
            });
        }

        $scope.updateShipping = function(productArr)
        {
            if($scope.address_id == 0)
            {
                var data = {"status": "error", "message": "Please select address"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(productArr.distributed_qnty > 0)
            {
                /*var UpdateArray = {};
                UpdateArray.table ='orders';
                UpdateArray.data = {shipping_status:2};
                UpdateArray.cond = {id:$scope.order_id};

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                {
                    
                });*/

                $("#ajax_loader").show();

                var combine_array = {};
                combine_array.product = productArr;
                combine_array.address_id = $scope.address_id;
                combine_array.order_id = $scope.order_id;
                combine_array.company_id = sessionService.get('company_id');

                $http.post('api/public/shipping/addProductToShip',combine_array).success(function(result, status, headers, config) {
                    
                    if(result.data.success == '1') {
                        $scope.shipOrder();
                    }
                    $("#ajax_loader").hide();
                });
            }
            else
            {
                var data = {"status": "error", "message": "Please enter valid qnty"}
                notifyService.notify(data.status, data.message);
                return false;
            }
        }

        $scope.addAllProducts = function()
        {
            if($scope.address_id == 0)
            {
                var data = {"status": "error", "message": "Please select address"}
                notifyService.notify(data.status, data.message);
                return false;
            }

            var combine_array = {};
            combine_array.products = $scope.unshippedProducts;
            combine_array.address_id = $scope.address_id;
            combine_array.order_id = $scope.order_id;
            combine_array.company_id = sessionService.get('company_id');
            $("#ajax_loader").show();

            $http.post('api/public/shipping/addAllProductToShip',combine_array).success(function(result, status, headers, config) {
                
                if(result.data.success == '1') {
                    $scope.shipOrder();
                }
                else
                {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
            });
        }

        $scope.shippingDetails = function()
        {
            if($scope.address_id == 0 || $scope.address_id == undefined)
            {
                var data = {"status": "error", "message": "Please select address"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            else if($scope.shipping_id == 0 || $scope.shipping_id == undefined)
            {
                var data = {"status": "error", "message": "Please select allocated address"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            else if($scope.assignedItems.length == 0)
            {
                var data = {"status": "error", "message": "Please assign product to address"}
                notifyService.notify(data.status, data.message);
                return false;   
            }
            else
            {
                $state.go('app.shipping.shipmentdetails',{id: $scope.shipping_id});
            }
        }

        $scope.returnFunction = function(){
            $state.reload();
        }

        // DYNAMIC POPUP FOR INSERT RECORDS
        $scope.openInsertPopup = function(path,ev,table)
        {
            console.log($scope.order.client_id);
            var insert_params = {client_id:$scope.order.client_id};
            sessionService.openAddPopup($scope,path,insert_params,table);
        }
    }
})();
