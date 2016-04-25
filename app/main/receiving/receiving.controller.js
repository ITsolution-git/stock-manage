(function () {
    'use strict';

    angular
            .module('app.receiving')
            .controller('ReceivingController', ReceivingController);

    /** @ngInject */
    function ReceivingController(ReceivingData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;
         vm.searchQuery = "";

        // Data
        vm.receiving = ReceivingData.data;
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