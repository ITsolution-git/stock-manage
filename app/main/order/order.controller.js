(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController);


    /** @ngInject */

    function OrderController($q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService) {
        var vm = this;
        vm.resetFilter = resetFilter;
        vm.showDatePicker = showDatePicker;
        $scope.role = sessionService.get('role_slug');
        // Data

        var company_id = sessionService.get('company_id');
        

           $http.get('api/public/common/getStaffList/'+company_id).success(function(result, status, headers, config) 
              {
                  if(result.data.success == '1') 
                  {
                      vm.salesCheckData = result.data.records;
                  } 
                  
              });

       
//1. sales rep
        vm.salesCheckModal = [];
       
        //3. create date
        vm.createDate;
        vm.createdate = false;
        vm.companyfilter = false;
         //4. company
        vm.companyCheckModal = [];
        //vm.companyCheckData = [{id: 1, "label": "Checkbox 1"}, {id: 2, "label": "Checkbox 2"}, {id: 3, "label": "Checkbox 3"}, {id: 4, "label": "Checkbox 4"}, {id: 5,  "label": "Checkbox 5"}, {id: 6, "label": "Checkbox 6"}, {id: 7, "label": "Checkbox 7"}, ];
        vm.searchOrder;
        vm.ordersId = [{"id": "27"}, {"id": "35"}, {"id": "12"}];
        vm.orderfilter = false;
        vm.rangeFrom;
        vm.rangeTo;
        vm.datefilter = false;

        var company_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        company_list_data.cond = angular.copy(condition_obj);
        
        $http.post('api/public/client/getClientFilterData',company_list_data).success(function(Listdata) {
            vm.companyCheckData = Listdata.data.records;
        });

        function resetFilter() {

/*            for (var i = 0; i < this.salesCheckData.length; i++) {
                this.salesCheckData[i].label = false;
            }
            for (var i = 0; i < this.companyCheckData.length; i++) {
                this.companyCheckData[i].label = false;
            }
*/            vm.shipDate = vm.createDate = vm.rangeFrom = vm.rangeTo = false;
            this.searchOrder = null;
            jQuery('.dateFilter').prop("value", " ");
           
            vm.companyChecksettings = {externalIdProp: myCustomPropertyForTheObject()}
            function myCustomPropertyForTheObject(){
                vm.companyCheckModal = [];
            }
            vm.salesChecksettings = {externalIdProp: myCustomPropertyForTheObjectSale()}
            function myCustomPropertyForTheObjectSale(){
                vm.salesCheckModal = [];
            }
            for (var i = 0; i < this.salesCheckModal.length; i++) {
               this.salesCheckModal[i].id = null;
            }
            for (var i = 0; i < vm.companyCheckModal.length; i++) {
                vm.companyCheckModal[i].id = null;

            }   
            vm.shipDate = vm.createDate = vm.rangeFrom = vm.rangeTo = null;
            this.searchOrder = null;
            jQuery('.dateFilter').prop("value", " ");

            $scope.filterOrders();
        }

/*        jQuery('.dateFilter').keydown(function() {
          //code to not allow any changes to be made to input field
          return false;
        });*/

         function showDatePicker(ev) {
            $mdpDatePicker(vm.createDate, {
                targetEvent: ev
            }).then(function (selectedDate) {
                vm.createDate = selectedDate;
            });
            $mdpDatePicker(vm.shipDate, {
                targetEvent: ev
            }).then(function (selectedDate) {
                vm.shipDate = selectedDate;
            });
            $mdpDatePicker(vm.rangeFrom, {
                targetEvent: ev
            }).then(function (selectedDate) {
                vm.rangeFrom = selectedDate;
            });
            $mdpDatePicker(vm.rangeTo, {
                targetEvent: ev
            }).then(function (selectedDate) {
                vm.rangeTo = selectedDate;
            });
            
        };
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

        /*$scope.search = function () {
            $scope.reloadCallback();
        };*/

        $scope.getResource = function (params, paramsObj, search) {
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

              return $http.post('api/public/order/listOrder',orderData).success(function(response) {
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
            // if($('.md-sidenav-right').hasClass("md-closed")){
            // $('body').addClass('filtershow');
            // }
            // if(!$('.md-sidenav-right').hasClass("md-closed")){
            // $('body').removeClass('filtershow');
            // }
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
//            jQuery('.dev-rdetail').on('click', function () {
//                var $tr = $(this).closest('tr');
//                var row = datatableObj.row($tr);
//
//                if (row.child.isShown()) {
//                    row.child.hide();
//                    $tr.removeClass('shown');
//                } else {
//                    var rowHtml=$tr.find("div.dev-rdetail-data").html();
//                    row.child(rowHtml).show();
//                    $tr.addClass('shown').next('tr').addClass('table-desc').children('td').addClass('collpas');
//                }
//            });
        }
    }



})();
