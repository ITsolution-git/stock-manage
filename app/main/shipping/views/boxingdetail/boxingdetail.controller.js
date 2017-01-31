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

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        if($scope.shipping_id == '' || $scope.allow_access == '0') {
            $state.go('app.shipping');
            return false;
        }

        $scope.box_items = [];
        $scope.shipping_box_id = 0;
        $scope.page = 1;
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
            combine_array.type = 'box';
            combine_array.page = $scope.page;
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
                $scope.pagination = result.data.pagination;
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

        $scope.getPage = function(param)
        {
            if(param == 'forward')
            {
                if($scope.pagination.page < $scope.pagination.size)
                {
                    $scope.page = $scope.page + 1;
                    $scope.shipOrder();
                }
            }
            else
            {
                if($scope.page > 1)
                {
                    $scope.page = $scope.page - 1;
                    $scope.shipOrder();
                }
            }
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

        /*$scope.shippingDetails = function()
        {
            var ship_data = {};
            ship_data['table'] ='shipping';
            ship_data.cond = {'id' : $scope.shipping_id};

            $http.post('api/public/common/GetTableRecords',ship_data).success(function(result) {
                if(result.data.success == 1)
                {
                    $scope.shipping =result.data.records[0];
                }
            });
        }

        $scope.getShippingBoxes = function()
        {
            $("#ajax_loader").show();
            $scope.box_items = [];
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;
            combine_array.company_id = sessionService.get('company_id');

            $http.post('api/public/shipping/getShippingBoxes',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;
                    $scope.total_box_qnty =result.data.total_box_qnty;
                    $scope.boxType =result.data.boxType;

                    if($scope.shipping_box_id > 0)
                    {
                        $scope.select_box($scope.shipping_box_id);
                    }
                } else {
                     $state.go('app.shipping');
                     return false;
                }
            });
        }

        var allData = {};
        allData.table ='shipping';
        allData.cond ={display_number:$stateParams.id,company_id:sessionService.get('company_id')}

        $http.post('api/public/common/GetTableRecords',allData).success(function(result)
        {   
            if(result.data.success=='1')
            {   
                $scope.shipping_id = result.data.records[0].id;
                $scope.shippingDetails();
                $scope.getShippingBoxes();
            }
        });*/

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
                /*var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);*/
                $scope.getShippingBoxes();
            });
        }

        $scope.changeBoxType = function(id,box_setting_id)
        {
            var order_main_data = {};
            var obj = {};
            obj['box_setting_id'] =  box_setting_id;
            order_main_data.data = angular.copy(obj);
            order_main_data.table = 'shipping_box';

            var condition_obj = {};
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {
                /*var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);*/
            });
        }

        $scope.select_box = function(box_id)
        {
            $scope.shipping_box_id = box_id;
            $scope.box_items = $scope.shippingBoxes[box_id].boxItems;
        }

        $scope.update_box_qty = function(box)
        {
            if(box.md == '' || box.md == undefined)
            {
                box.md = 0;            
            }
            if(box.spoil == '' || box.spoil == undefined)
            {
                box.spoil = 0;       
            }
            if(parseInt(box.spoil) > parseInt(box.actual))
            {
                var data = {"status": "error", "message": "Spoil can not be greater than actual."}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(parseInt(box.md) > parseInt(box.actual))
            {
                var data = {"status": "error", "message": "MD can not be greater than actual."}
                notifyService.notify(data.status, data.message);
                return false;
            }


            var combine = parseInt(box.md) + parseInt(box.spoil);
            box.actual =  parseInt(box.boxed_qnty) - parseInt(combine);

            var ship_data = {};
            ship_data['table'] ='shipping_box';
            ship_data.data = {'actual':box.actual, 'md':box.md, 'spoil':box.spoil};
            ship_data.cond = {'id' : box.box_id};

            $("#ajax_loader").show();
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
            if($scope.shipping.tracking_number != '')
            {
                var data = {"status": "error", "message": "Label is already generated you can't delete boxes"}
                notifyService.notify(data.status, data.message);
                return false;
            }
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
