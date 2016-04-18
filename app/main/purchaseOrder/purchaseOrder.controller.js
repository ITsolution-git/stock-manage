(function () {
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('PurchaseOrderController', PurchaseOrderController);

    /** @ngInject */
    function PurchaseOrderController(PurchaseOrderData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;

        // Data
        vm.purchaseOrders = PurchaseOrderData.data;
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }
    }
})();