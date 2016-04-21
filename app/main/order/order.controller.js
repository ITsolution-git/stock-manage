(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController);


    /** @ngInject */

    function OrderController(OrderData,OrderUserData,OrderCompanyData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService) {
        var vm = this;

        // Data

        vm.salesCheck = OrderUserData.data.records;
        vm.companyCheck = OrderCompanyData.data.records;

        vm.shipDate;
        vm.shipdate = false;
        //3. create date
        vm.createDate;
        vm.createdate = false;
        vm.companyfilter = false;
        vm.searchOrder;
        vm.ordersId = [{"id": "27"}, {"id": "35"}, {"id": "12"}];
        vm.orderfilter = false;
        vm.rangeFrom;
        vm.rangeTo;
        vm.datefilter = false;

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { console.log(123); };


        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
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

            angular.forEach(vm.salesCheck, function(check){
                if (check.name == true){
                    $scope.sellerArray.push(check.id);
                }
            })
            if($scope.sellerArray.length > 0)
            {
                flag = false;
                $scope.filterBy.seller = angular.copy($scope.sellerArray);
            }

            $scope.clientArray = [];
            angular.forEach(vm.companyCheck, function(company){
                if (company.client_company == true){
                    $scope.clientArray.push(company.client_id);
                }
            })
            if($scope.clientArray.length > 0)
            {
                flag = false;
                $scope.filterBy.client = angular.copy($scope.clientArray);
            }

            if(vm.createDate != '' && vm.createDate != undefined)
            {
                flag = false;
                $scope.filterBy.created_date = get_formated_date(vm.createDate);
            }
            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }

        $scope.search = function () {
            $scope.reloadCallback();
        };

        $scope.getResource = function (params, paramsObj, search) {
            $scope.params = params;
            $scope.paramsObj = paramsObj;
 
            var orderData = {};
            orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

              return $http.post('api/public/order/listOrder',orderData).success(function(response) {
                var header = response.header;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }
        $scope.removeItem = function (item) {
          $http.post('table-delete-row.json', {
            'name': item.name
          }).then(function (response) {
            $scope.reloadCallback();
          })
        };

        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        
        vm.searchQuery = "";

        // Methods
        vm.openOrderDialog = openOrderDialog;
        vm.openaddDesignDialog = openaddDesignDialog;
        vm.dtInstanceCB = dtInstanceCB;
//        vm.searchTable = searchTable;


        // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };

        function openOrderDialog(ev, order)
        {
            $mdDialog                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                .show({
                controller: 'OrderDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/order/order-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
        function openaddDesignDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
        function openaddSplitAffiliateDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddSplitAffiliateController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
       
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
            jQuery('.dev-rdetail').on('click', function () {
                var $tr = $(this).closest('tr');
                var row = datatableObj.row($tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    $tr.removeClass('shown');
                } else {
                    var rowHtml=$tr.find("div.dev-rdetail-data").html();
                    row.child(rowHtml).show();
                    $tr.addClass('shown').next('tr').addClass('table-desc').children('td').addClass('collpas');
                }
            });
        }
    }



})();
