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
                  
                   //$scope.minDate = new Date(result.data.records[0].date_start);
                   //$scope.minShipDate = new Date(result.data.records[0].date_shipped);
                }
               
            });
          }

        var companyData = {};
        companyData.company_id =sessionService.get('company_id');
        companyData.table = 'client';
        companyData.cond = {'company_id':sessionService.get('company_id'),'is_delete':1};

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

          var misc_list_data = {};
          var condition_obj = {};
          condition_obj['company_id'] =  sessionService.get('company_id');
          misc_list_data.cond = angular.copy(condition_obj);

          $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                    $scope.miscData = result.data.records;
          });


       /*    $scope.updateStartDate = function(){
            $scope.minDate = new Date($scope.order_data.date_start);
            $scope.minShipDate = new Date($scope.order_data.date_start);
          }

          $scope.updateshipDate = function(){
            $scope.minShipDate = new Date($scope.order_data.date_shipped);
          }
*/


     $scope.orderDetailInfo(order_id);

      $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.saveOrderInfo = function (orderDataDetail) {
         

         var date_shipped;
         var date_start ;
         var in_hands_by;


        if(orderDataDetail.date_shipped != '') {
            date_shipped = new Date(orderDataDetail.date_shipped);
        }

        if(orderDataDetail.date_start != '') {
            date_start = new Date(orderDataDetail.date_start);
        }

        if(orderDataDetail.in_hands_by != '') {
            in_hands_by = new Date(orderDataDetail.in_hands_by);
        }
          
      
              
        if(date_shipped < date_start) {
           var data = {"status": "error", "message": "Shipped Date must be greater then Start Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }

        if(in_hands_by < date_shipped) {
           var data = {"status": "error", "message": "Hands Date must be greater then Shipped Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }

        if(in_hands_by < date_start) {
           var data = {"status": "error", "message": "Hands Date must be greater then Start Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }


        
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