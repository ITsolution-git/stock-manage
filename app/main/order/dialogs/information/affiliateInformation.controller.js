(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AffiliateInformationController', AffiliateInformationController);

    /** @ngInject */
    function AffiliateInformationController(order_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
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
                   $scope.contact_main =result.data.contact_main;
                  
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

        $scope.orderDetailInfo(order_id);

        $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.calculateAll = function(order_id,company_id)
        {
            $("#ajax_loader").show();
            $http.get('api/public/order/calculateAll/'+order_id+'/'+company_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.orderDetailInfo(order_id);
            });
        }

        $scope.saveOrderInfo = function (orderDataDetail) {
        
            var order_data = {};
            order_data.table ='orders'
            order_data.orderDataDetail =orderDataDetail
            order_data.cond ={id:order_id}

            $http.post('api/public/order/editOrder',order_data).success(function(result) {
                $mdDialog.hide();
                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
                $scope.calculateAll(order_id,sessionService.get('company_id'));
            });
        };
    }
})();