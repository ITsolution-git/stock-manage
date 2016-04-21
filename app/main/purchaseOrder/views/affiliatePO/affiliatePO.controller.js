(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('AffiliatePOController', AffiliatePOController);

    /** @ngInject */
    function AffiliatePOController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        vm.orderInformationPO = {
            "customerPo": "######",
            "sales": "keval Baxi",
            "blind": "Yes",
            "accountManger": "Nancy McPhee",
            "mainContact": "Joshi Goodman",
            "priceGrid": "ABC Grid"
        };
        //Dummy models data
        vm.designsAffilatePO = [
            {"id": "1", "designName": "Spring Shirts", "total": "70", "totalcolor": "3", "status": "In Producation xx/xx/xxxx", "statusValue": "60"},
            {"id": "2", "designName": "Spring Shirts 2", "total": "25", "totalcolor": "2", "status": "In Producation xx/xx/xxxx", "statusValue": "60"},
            {"id": "3", "designName": "Hat FAN", "total": "20", "totalcolor": "63", "status": "In Producation xx/xx/xxxx", "statusValue": "60"}
        ];
        vm.purchases = [
            {"poid": "1", "potype": "1", "clientName": "1"},
            {"poid": "2", "potype": "2", "clientName": "2"},
        ];
        vm.receives = [
            {"roid": "1", "clientName": "1", "vendor": "1"},
            {"roid": "2", "clientName": "2", "vendor": "2"},
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
        vm.affiliatePO = {
            total: "160",
            finish: "5",
            "productshipped": "800",
            "Total": "1000",
            "location": "231",
            "notes": "5",
            "approved": "Approved"
        }
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
    }
})();
