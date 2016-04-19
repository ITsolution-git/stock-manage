(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController)
            .controller('OrderDialogController', OrderDialogController);


    /** @ngInject */

    function OrderController(OrderData,OrderUserData,OrderCompanyData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService) {
        var vm = this;

        // Data

        vm.salesCheck = OrderUserData.data.records;
        vm.companyCheck = OrderCompanyData.data.records;

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { console.log(123); };


        $scope.filterBy = {
          'search': '',
          'seller': '',
          'client': ''
        };

        $scope.filterOrders = function(){
            $scope.sellerArray = [];
            angular.forEach(vm.salesCheck, function(check){
                if (check.name == true){
                    $scope.sellerArray.push(check.id);
                }
            })
            if($scope.sellerArray.length > 0)
            {
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
                $scope.filterBy.client = angular.copy($scope.clientArray);
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
        //1. sales rep
      //  vm.salesCheck = [{"v": true, "name": "Nick Santo"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Nick Santo"}, {"v": false, "name": "Nick Santo"}, ];
        //2. ship date
        vm.shipDate;
        vm.shipdate = false;
        //3. create date
        vm.createDate;
        vm.createdate = false;
        //4. company
      //  vm.companyCheck = [{"v": false, "name": "Checkbox 1"}, {"v": true, "name": "Checkbox 2"}, {"v": false, "name": "Checkbox 3"}, {"v": false, "name": "Checkbox 4"}, {"v": false, "name": "Checkbox 5"}, {"v": false, "name": "Checkbox 6"}, {"v": false, "name": "Checkbox 7"}, ];
        vm.companyfilter = false;
        //5. order id
        vm.searchOrder;
        vm.ordersId = [{"id": "27"}, {"id": "35"}, {"id": "12"}];
        vm.orderfilter = false;
        //6. data range
        vm.rangeFrom;
        vm.rangeTo;
        vm.datefilter = false;

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


    function OrderDialogController($scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService) {


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
                    } else if(orderData.job_name == undefined) {

                      var data = {"status": "error", "message": "Job Name should not be blank"}
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
