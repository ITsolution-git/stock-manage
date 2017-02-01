(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('ExistingLocationController', ExistingLocationController);

    /** @ngInject */
    function ExistingLocationController(order_id,client_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {
        $scope.client_id = client_id;
        $scope.order_id = order_id;

        $scope.allOrderAddress = function (order_id) {

            var addressData = {};
            addressData.order_id = order_id;

            $http.post('api/public/order/allOrderAddress',addressData).success(function(result)
            {   
                if(result.data.success=='1')
                {   
                    $scope.addressModel = result.data.records;
                    $scope.addressModelOld = angular.copy(result.data.records);
                }
                else
                {
                    $scope.addressModel = [];
                    $scope.addressModelOld = [];
                }     
            });
        };

        $scope.selectedItemChange = function (client_id,company_change) {

            if(company_change == 0)
            {
                if(client_id != $scope.client_id)
                {
                    $scope.addressChecksettings = {externalIdProp: myCustomPropertyForTheObjectCompany()}
                    
                    function myCustomPropertyForTheObjectCompany(){
                        $scope.addressModel = [];
                    }

                    for (var i = 0; i < $scope.addressModel.length; i++) {              
                        $scope.addressModel[i].id = null;
                    }
                } else {
                    $scope.allOrderAddress($scope.order_id);
                }
            }
            var clientData = {};
            clientData.client_id =client_id;

            $http.post('api/public/order/GetAllClientsAddress',clientData).success(function(result)
            {   
                if(result.data.success=='1')
                {   
                    $scope.allAddressData = result.data.records;
                } else {
                    $scope.allAddressData = [];
                }
            });
        };
        
        $scope.allOrderAddress(order_id);
        $scope.selectedItemChange(client_id,1);

        $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.saveOrderInfo = function()
        {
            var order_data = {};
            order_data.table ='orders'
            order_data.addressModel =$scope.addressModel
            order_data.addressModelOld = $scope.addressModelOld;
            order_data.action = 'address';
            order_data.cond ={id:order_id}
        
            $http.post('api/public/order/editOrder',order_data).success(function(result) {

                if(result.data.success == '1')
                {
                    var data = {"status": "success", "message": "Data Updated Successfully."}
                    notifyService.notify(data.status, data.message);
                    $mdDialog.hide();
                }
                else
                {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                }
            });
        }
    }
})();