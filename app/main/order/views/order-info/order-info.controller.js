(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('OrderInfoController', OrderInfoController);

    /** @ngInject */
    function OrderInfoController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
         vm.openaddDesignDialog = openaddDesignDialog;
        //Dummy models data
        vm.orderInformation = {
            "customerPo": "######",
            "sales": "keval Baxi",
            "blind": "Yes",
            "accountManger": "Nancy McPhee",
            "mainContact": "Joshi Goodman",
            "priceGrid": "ABC Grid"
        };
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
        vm.packageInformation = [
            {"pid": "Foil", "pvalue": "4"},
            {"pid": "Over Size Screens", "pvalue": "5"},
            {"pid": "SKU", "pvalue": "2"},
            {"pid": "Poly Bagging", "pvalue": "5"},
            {"pid": "Hang Tag", "pvalue": "1"},
            {"pid": "Inside Tagging", "pvalue": "4"},
        ];
        vm.orderecap = [
            {"rid": "Screens", "rvalue": "54"},
            {"rid": "Press Setup", "rvalue": "0"},
            {"rid": "Lines Total", "rvalue": "2"},
            {"rid": "Order Total", "rvalue": "$325.00"},
            {"rid": "Tax Rate", "rvalue": ""},
            {"rid": "Tax", "rvalue": "4"},
            {"rid": "Grand Total", "rvalue": "$5,976.81"},
            {"rid": "Total Payments", "rvalue": "5"},
            {"rid": "Balance Due", "rvalue": "$2,500.98"},
        ];
        vm.designTotal = {total: "160"};
        vm.finishing = {finish: "5"};
        vm.orderInfo = {
            "separations": "",
            "Rush": "",
            "Distribution": "",
            "Digitize": "",
            "Shipping": "",
            "SetUp": "",
            "Artwork": "",
            "Discount": ""
        };
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
    }
})();
