(function () {
    'use strict';

    angular
            .module('app.receiving')
            .controller('ReceivingController', ReceivingController);

    /** @ngInject */
    function ReceivingController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
        
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        $scope.company_id = sessionService.get('company_id');


        /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'po.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'receive_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        
      $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.company_id = $scope.company_id;
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

            $("#ajax_loader").show();     
           return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.success  = result.success;
                if(result.success=='1')
                {
                    
                    return {
                      'rows': result.rows,
                      'header': result.header,
                      'pagination': result.pagination,
                      'sortBy': result.sortBy,
                      'sortOrder': result.sortOrder
                    }

                }
                else
                {
                    notifyService.notify('error',result.message);
                }
                
            });
        }


        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });


        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
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