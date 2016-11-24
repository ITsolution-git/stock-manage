(function () {
    'use strict';

    angular
            .module('app.finishingQueue')
            .controller('FinishingQueueController', FinishingQueueController);

    /** @ngInject */
    function FinishingQueueController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        
        var vm = this;
        vm.searchQuery = "";

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.currentTab = 'all';

        this.condition = '';

        this.conditions = ('Yes No').split(' ').map(function (state) { return { abbrev: state }; });
        
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

              return $http.post('api/public/finishingQueue/listFinishingQueue',orderData).success(function(response) {
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

        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

        $scope.getTab = function(tab)
        {
            $scope.currentTab = 'all';
        }
    }
})();