(function () {
    'use strict';

    angular
            .module('app.shipping')
            .controller('shippingController', shippingController);
    /** @ngInject */
    function shippingController($q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        $scope.user_id = sessionService.get('user_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });


        var vm = this;
        vm.searchQuery = "";
        $scope.currentTab = 'wait';
        $scope.status = '';

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU' || $scope.role_slug=='SM')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }


        $scope.company_id = sessionService.get('company_id');

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'o.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'search': '',
          'status':$scope.status
        };
        
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        $scope.getStatus = function (){
            $scope.filterBy.status = $scope.status;
        };

        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params,type:'wait'};

              return $http.post('api/public/shipping/listShipping',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        $scope.getTab = function(tab)
        {
            $scope.currentTab = 'wait';
        }

         $scope.updateOrderStatus = function(name,value,id)
        {
            var order_main_data = {};

            order_main_data.table ='orders';

            $scope.name_filed = name;
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