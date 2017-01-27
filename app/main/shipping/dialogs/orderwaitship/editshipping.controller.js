(function ()
{
    'use strict';

    angular
        .module('app.shipping')
        .controller('EditShippingController', EditShippingController);

    /** @ngInject */
    function EditShippingController(shipping,$mdDialog,$controller,$state,event,$scope,sessionService,$resource,$http,notifyService)
    {
        var vm = this;

        $scope.address = shipping;

        $scope.cancel = function () {
            $mdDialog.hide();
        };

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
                    $scope.address.shippingMethod = result.data.records;
                }
                else
                {
                    $scope.address.shippingMethod = [];
                }
            });
        }

        $scope.updateShipping = function()
        {
            var shipping_data = {};

            shipping_data.table ='shipping';

            if($scope.address.date_shipped != '') {
                $scope.address.date_shipped = new Date($scope.address.date_shipped);
            }


            shipping_data.data = {'shipping_type_id':$scope.address.shipping_type_id};

            var condition_obj = {};
            condition_obj['id'] =  $scope.address.id;
            shipping_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',shipping_data).success(function(result) {
                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
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
    }
})();