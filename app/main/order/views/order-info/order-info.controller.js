(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('OrderInfoController', OrderInfoController);

    /** @ngInject */
    function OrderInfoController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope)
    {


            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            
            

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                }
                else {
                    $state.go('order.list');
                }
                $("#ajax_loader").hide();
            });
       
        var vm = this;
         vm.openaddDesignDialog = openaddDesignDialog;

        /* vm.orderDetails = OrderDataDetail.data.records;
         console.log(vm.orderDetails);*/

          vm.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;

        vm.designs = [
            {"id": "1", "designName": "Spring Shirts", "total": "70", "totalcolor": "3", "status": "In Producation xx/xx/xxxx", "statusValue": "60"},
            {"id": "2", "designName": "Spring Shirts 2", "total": "25", "totalcolor": "2", "status": "In Producation xx/xx/xxxx", "statusValue": "60"},
            {"id": "3", "designName": "Hat FAN", "total": "20", "totalcolor": "63", "status": "In Producation xx/xx/xxxx", "statusValue": "60"}
        ];
        vm.purchases = [
            {"poid": "27", "potype": "Purchase Order", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"poid": "28", "potype": "Purchase Order", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
        vm.receives = [
            {"roid": "27", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"roid": "28", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
        vm.affiliateOrders = [
            {"Company": "Company Name", "units": "150", "designs": "1"},
            {"Company": "Company Name", "units": "10,000", "designs": "2"},
        ];
       
       
        vm.designTotal = {total: "160"};
        vm.finishing = {finish: "5"};
        
        vm.shipping = {
            "productshipped": "800",
            "Total": "100",
        };
        vm.distrbution = {
            "location": "231",
        };
        vm.note = {
            "notes": "5",
        };
        vm.artwork = {
            "approved": "Approved"

        };
        vm.ordertotal = {
            "orderline": "$500",
            "ordercharges": "$500",
            "ordersales": "$500"
        };
        
//        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
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
    }
})();
