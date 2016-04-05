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
        //Dummy models data
        vm.designs = [
            {"id": "1", "designName": "Spring Shirts", "total": "70", "totalcolor": "3", "status": "In Producation xx/xx/xxxx"},
            {"id": "2", "designName": "Spring Shirts 2", "total": "25", "totalcolor": "2", "status": "In Producation xx/xx/xxxx"},
            {"id": "3", "designName": "Hat FAN", "total": "20", "totalcolor": "63", "status": "In Producation xx/xx/xxxx"}
        ];
        vm.purchases = [
            {"poid": "27", "potype": "Purchase Order", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"poid": "28", "potype": "Purchase Order", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"}

        ];




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