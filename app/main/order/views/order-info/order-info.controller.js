(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('OrderInfoController', OrderInfoController);

    /** @ngInject */
    function OrderInfoController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        $scope.orderDetail = function(){
            $("#ajax_loader").show();
            
            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.order_id = $stateParams.id;
            

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                } else {
                    $state.go('app.order');
                }
            });
          }

        $scope.designDetail = function(){

            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/order/designListing',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.designs = result.data.records.all_design;
                   $scope.total_unit = result.data.records.total_unit;
                }
                else {
                    $scope.designs = [];
                    $scope.total_unit = 0;
                }

                if($scope.total_unit == undefined)
                {
                    $scope.total_unit = 0;            
                }
            });
        }

        $scope.listAffiliate = function(){

            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/affiliate/getAffiliateData',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.affiliateList = result.data.affiliateList;
                }
                else {
                    $scope.affiliateList = [];
                }
            });
        }

        $scope.orderDetail();
        $scope.designDetail();
        $scope.listAffiliate();

        $scope.checkDesign = function()
        {
            if($scope.designs.length == 0)
            {
                var data = {"status": "error", "message": "Please add design to split order"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            /*else if($scope.designs[0].size_data.length == 0)
            {
                var data = {"status": "error", "message": "Please add product to split order"}
                notifyService.notify(data.status, data.message);
                return false;
            }*/
            else
            {
                $state.go('app.order.spiltAffiliate',{id: $scope.order_id});
            }
        }
       
        var vm = this;
        vm.openaddDesignDialog = openaddDesignDialog;

        /* vm.orderDetails = OrderDataDetail.data.records;
         console.log(vm.orderDetails);*/

        vm.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
        vm.openinformationDialog = openinformationDialog;
        vm.openApproveOrderDialog = openApproveOrderDialog;
      
        vm.purchases = [
            {"poid": "27", "potype": "Purchase Order", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"poid": "28", "potype": "Purchase Order", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
        vm.receives = [
            {"roid": "27", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"roid": "28", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
      
      
        vm.note = {
            "notes": "5",
        };
        vm.artwork = {
            "approved": "Approved"

        };
       
//        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            scrollY:171
        };
        vm.dtOptionsPurchase = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            scrollY:103
        };
        vm.assign_item = assign_item;
        function assign_item(item,item_name,item_charge,item_id){
            
            var item_array = {'item':item,'item_name':item_name,'item_charge':item_charge,'item_id':item_id,'order_id':$scope.order_id,'company_id':sessionService.get('company_id')};

            $http.post('api/public/order/addRemoveToFinishing',item_array).success(function(result) {

                if(result.data.success == '1') {
                    $scope.orderDetail();
                }
                else {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);               
                }
                
            });
        }
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.dtInstanceCB = dtInstanceCB;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
         function openaddDesignDialog(ev, event_id)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    event_id: event_id,
                    event: ev
                 },
                 onRemoving : $scope.designDetail
            });
        }


        function openinformationDialog(ev,order_id)
        {
            $mdDialog.show({
                controller: 'InformationController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/information/information.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    order_id: order_id,
                    event: ev
                },
                onRemoving : $scope.orderDetail
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
        function openApproveOrderDialog(ev, settings) {
            $mdDialog.show({
                controller: 'approveOrderDiallogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/approveorder/approveorder.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev
                }
            });
        }
        $scope.updateOrderCharge = function(column_name,id,value,table_name,match_condition)
        {
            var position_main_data = {};
            position_main_data.table =table_name;
            $scope.name_filed = column_name;
          
            var obj = {};
            obj[$scope.name_filed] =  value;
            position_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[match_condition] =  id;
            position_main_data.cond = angular.copy(condition_obj);
            position_main_data.company_id = sessionService.get('company_id');

            $http.post('api/public/order/updateOrderCharge',position_main_data).success(function(result) {
                $scope.orderDetail();
            });
            /*$http.post('api/public/common/UpdateTableRecords',position_main_data).success(function(result) {
            });*/
        }


        $http.post('api/public/common/getCompanyDetail',sessionService.get('company_id')).success(function(result) {
            if(result.data.success == '1') 
            {
                $scope.allCompanyDetail =result.data.records;
                $scope.oversize = $scope.allCompanyDetail[0].oversize_value;
            } 
            else
            {
                $scope.allCompanyDetail=[];
            }
        });
        
        var timeoutID = window.setTimeout(function zx() {
            var $table = $('table.scrol.table3'),
            $bodyCells = $table.find('tbody tr:first').children(),
            colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 200);
        //Received
        var timeoutID = window.setTimeout(function yx() {
            var $table = $('table.scrol.table4'),
            $bodyCells = $table.find('tbody tr:first').children(),
            colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 300);
        //Affiliate
        var timeoutID = window.setTimeout(function yz() {
            var $table = $('table.scrol.table5'),
                    $bodyCells = $table.find('tbody tr:first').children(),
                    colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 340);

        $scope.printPdf=function()
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/order/savePDF';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var input_order = document.createElement('input');
            input_order.name = 'order';
            input_order.setAttribute('value', JSON.stringify($scope.order));
            form.appendChild(input_order);

            var input_company_detail = document.createElement('input');
            input_company_detail.name = 'company_detail';
            input_company_detail.setAttribute('value', JSON.stringify($scope.allCompanyDetail));
            form.appendChild(input_company_detail);

            document.body.appendChild(form);
            form.submit();  
        };

        $scope.openEmailPopup = function (ev) {
    
            $mdDialog.show({
                controller: 'openEmailController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/views/order-info/send-email.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    client_id: $scope.order.client_id,
                    order_id: $stateParams.id,
                    event: ev
                  }
            });
        };

        $scope.createPO = function() {
            var condition_obj = {};
            condition_obj.order_id = $scope.order_id;
            condition_obj.company_id = sessionService.get('company_id');

            $http.post('api/public/purchase/createPO',condition_obj).success(function(result) {
                
            });
        }



         $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);
            UpdateArray.order_id = $scope.order_id;
            UpdateArray.company_id = sessionService.get('company_id');

            var permission = confirm(AllConstant.deleteMessage);
                if (permission == true)
                {

                $http.post('api/public/order/deleteOrderCommon',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success','Record Deleted Successfully.');
                        $scope.designDetail();
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                   });
                 }
        } 
    }
})();
