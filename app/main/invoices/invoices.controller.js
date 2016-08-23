(function () {
    'use strict';

    angular
            .module('app.invoices')
            .controller('invoiceController', invoiceController);
    /** @ngInject */
    function invoiceController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
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

        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }
        vm.openMenu = function ($mdOpenMenu, ev) {
            //originatorEv = ev;
            $mdOpenMenu(ev);
        };

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'search': ''
        };
        
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        
        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

              return $http.post('api/public/invoice/listInvoice',orderData).success(function(response) {
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
    }
})();
