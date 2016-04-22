(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('OrderDialogController', OrderDialogController);
/** @ngInject */
    function OrderDialogController($scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {
        var companyData = {};
            companyData.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1'};
            companyData.table ='client';

                $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
        
                        if(result.data.success == '1') 
                        {
                            $scope.allCompany =result.data.records;
                        } 
                        else
                        {
                            $scope.allCompany=[];
                        }
                });


                 $scope.save = function (orderData) {
 
          
                   if(orderData == undefined) {

                      var data = {"status": "error", "message": "Company and Job Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(orderData.name == undefined) {

                      var data = {"status": "error", "message": "Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(orderData.client_id == undefined) {

                      var data = {"status": "error", "message": "Company should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    }

              var combine_array_id = {};
             
              combine_array_id.orderData = orderData;
              combine_array_id.company_id = sessionService.get('company_id');
              combine_array_id.login_id = sessionService.get('user_id');

              $http.post('api/public/order/addOrder',combine_array_id).success(function(result) 
                {
                    $mdDialog.hide();
                    $state.go('app.order.order-info',{id: result.data.id});
                    return false;
                    
                });
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    }
})();