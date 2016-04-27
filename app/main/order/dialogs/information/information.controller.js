(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('InformationController', InformationController);

    /** @ngInject */
    function InformationController(order_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {
        $scope.orderDetailInfo = function(order_id){

            var combine_array_id = {};
            combine_array_id.id = order_id;
            combine_array_id.company_id = sessionService.get('company_id');
            
            $http.post('api/public/order/orderDetailInfo',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.order_data = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                   $scope.allGrid = result.data.price_grid;
                   $scope.staffList =result.data.staff;
                   $scope.brandCoList =result.data.brandCo;
                }
               
            });

          }

     $scope.orderDetailInfo(order_id);

      $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.saveOrderInfo = function (orderDataDetail) {
         
                var order_data = {};
                order_data.table ='orders'
                order_data.orderDataDetail =orderDataDetail
                order_data.cond ={id:order_id}
            
                $http.post('api/public/order/editOrder',order_data).success(function(result) {
                     $mdDialog.hide();
                     var data = {"status": "success", "message": "Data Updated Successfully."}
                     notifyService.notify(data.status, data.message);
                });


            
           
        };
    }
})();