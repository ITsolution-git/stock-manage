(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController);


    /** @ngInject */

    function OrderController($q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='SU' || $scope.role_slug=='AT')
        {
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }


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


        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        var approval_data = {};
        approval_data ={company_id :sessionService.get('company_id'),type:'approval','is_delete':1,'status':1};
      
        $http.post('api/public/common/GetMiscApprovalData',approval_data).success(function(result) {

            if(result.data.success == '1') 
            {
                vm.statusCheckData =result.data.records;
            } 
        });

        vm.salesCheckModal = [];
        vm.createDate;
        vm.createdate = false;
        vm.companyfilter = false;
        vm.orderStatusfilter = false;
         //4. company
        vm.companyCheckModal = [];
        vm.statusCheckModal = [];
        //vm.companyCheckData = [{id: 1, "label": "Checkbox 1"}, {id: 2, "label": "Checkbox 2"}, {id: 3, "label": "Checkbox 3"}, {id: 4, "label": "Checkbox 4"}, {id: 5,  "label": "Checkbox 5"}, {id: 6, "label": "Checkbox 6"}, {id: 7, "label": "Checkbox 7"}, ];
        vm.searchOrder;
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

            vm.shipDate = vm.createDate = vm.rangeFrom = vm.rangeTo = false;
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
            vm.statusChecksettings = {externalIdProp: myCustomPropertyForTheObjectStatus()}
            function myCustomPropertyForTheObjectStatus(){
                vm.statusCheckModal = [];
            }
            for (var i = 0; i < this.salesCheckModal.length; i++) {
               this.salesCheckModal[i].id = null;
            }
            for (var i = 0; i < vm.companyCheckModal.length; i++) {
                vm.companyCheckModal[i].id = null;

            }
            for (var i = 0; i < vm.statusCheckModal.length; i++) {
                vm.statusCheckModal[i].id = null;

            }   
            vm.shipDate = vm.createDate = vm.rangeFrom = vm.rangeTo = null;
            this.searchOrder = null;
            jQuery('.dateFilter').prop("value", " ");

            console.log(vm.statusCheckModal);

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
          'created_date': '',
          'status':''
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
            $scope.filterBy.status = '';
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

            $scope.orderStatusArray = [];
            angular.forEach(vm.statusCheckModal, function(status){
                    $scope.orderStatusArray.push(status.id);
            })
            if($scope.orderStatusArray.length > 0)
            {
                flag = false;
                $scope.filterBy.status = angular.copy($scope.orderStatusArray);
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
