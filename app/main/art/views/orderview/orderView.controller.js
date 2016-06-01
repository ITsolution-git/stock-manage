(function ()
{
    'use strict';

    angular
            .module('app.art')
            .controller('orderViewController', orderViewController);

    /** @ngInject */
    function orderViewController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        //Dummy models data
        vm.companyPOinfo = {
            "pOType": "Purchase Order",
            "orderNumber": "12345",
            "vendor": "American Apparel",
            "mainContact": "Joshi Goodman",
            "dateCreated": "xx/xx/xxxx",
            "shipDate": "xx/xx/xxxx",
            "receiveDate": "xx/xx/xxxx"
        };
        vm.companyPOProduct = [
            {"item": "Spring Shirts", "sKU": "####", "sizeGroup": "Mens", "size": "M", "color": "Blue", "ordered": "0/100", "unitPrice": "$0.00"},
            {"item": "Spring Shirts 2", "sKU": "####", "sizeGroup": "Womens", "size": "L", "color": "Purple", "ordered": "0/100", "unitPrice": "$0.00"},
            {"item": "Hat FN", "sKU": "####", "sizeGroup": "Infants", "size": "XL", "color": "Green", "ordered": "0/100", "unitPrice": "$0.00"}
        ];

        vm.designTotal = {total: "160"};
        vm.finishing = {finish: "5"};

        vm.distrbution = {
            "location": "231",
        };
        vm.notePo = {
            "notes": "5",
        };
        vm.dropshipPo = {
            "data": "Lorem posem text that gives all the details on the purchase order and its dropship instructions"
        };
        vm.webPo = {
            "link": "www.link.com"
        };
        vm.purchaseOrderPO = {
            "shipChage": "$500",
            "totalAmount": "$500",
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
