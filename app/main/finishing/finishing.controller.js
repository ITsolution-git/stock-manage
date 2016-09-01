(function () {
    'use strict';

    angular
            .module('app.finishing')
            .controller('FinishingController', FinishingController);

    /** @ngInject */
    function FinishingController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        var vm = this;
         vm.searchQuery = "";

         this.condition = '';

        this.conditions = ('Yes No').split(' ').map(function (state) { return { abbrev: state }; });
        
        vm.editFinishing = editFinishing ;
        
        /*$scope.getFinishingData = function()
        {
            var finish_list_data = {};
            var condition_obj = {};
            condition_obj['company_id'] =  sessionService.get('company_id');
            finish_list_data.cond = angular.copy(condition_obj);

            $http.post('api/public/finishing/listFinishing',finish_list_data).success(function(result)
            {
                $scope.orders = result.data.records;
            });
        }

        $scope.getFinishingData();*/

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
        };
         $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        /**
         * Returns the formatted date 
         * @returns {date}
         */
        function get_formated_date(unixdate)
        {
            var date = ("0" + unixdate.getDate()).slice(-2);
            var month = unixdate.getMonth() + 1;
            month = ("0" + month).slice(-2);
            var year = unixdate.getFullYear();

            var new_date = year + "-" + month + "-" + date;
            return new_date;
        }

        $scope.filterOrders = function(){
            
            var flag = true;
            $scope.filterBy.seller = '';
            $scope.filterBy.client = '';
            $scope.filterBy.created_date = '';
            $scope.filterBy.temp = '';
            $scope.sellerArray = [];

            angular.forEach(vm.salesCheckModal, function(check){
                    $scope.sellerArray.push(check.id);
            })
            if($scope.sellerArray.length > 0)
            {
                flag = false;
                $scope.filterBy.seller = angular.copy($scope.sellerArray);
            }

            $scope.clientArray = [];
            angular.forEach(vm.companyCheckModal, function(company){
                    $scope.clientArray.push(company.id);
            })
            if($scope.clientArray.length > 0)
            {
                flag = false;
                $scope.filterBy.client = angular.copy($scope.clientArray);
            }

            if(vm.createDate != '' && vm.createDate != undefined && vm.createDate != false)
            {
                flag = false;
                $scope.filterBy.created_date = vm.createDate;
            }
            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }

        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

              return $http.post('api/public/finishing/listFinishing',orderData).success(function(response) {
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

        function editFinishing(ev, finishing)
        {
            $mdDialog.show({
                controller: 'EditFinishingDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/finishing/dialogs/editFinishing/editFinishing-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Finishing: finishing,
                    event: ev
                }
            });
        }

        // Data
        //vm.receiving = ReceivingData.data;
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
        
        $scope.changeStatus = function(finishing)
        {
            var finishing_data = {};
            finishing_data.data = {
                                    'status' : finishing.status
                                };
            finishing_data.cond = {};
            finishing_data['table'] ='finishing';
            finishing_data.cond['id'] = finishing.id;
            $http.post('api/public/common/UpdateTableRecords',finishing_data).success(function(result) {
                var data = {"status": "success", "message": "Record updated successfully"}
                notifyService.notify(data.status, data.message);
            });
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