(function () {
    'use strict';

    angular
            .module('app.finishing')
            .controller('FinishingController', FinishingController);

    /** @ngInject */
    function FinishingController($q, $mdDialog, $document, $mdSidenav,$state,  DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;
         vm.searchQuery = "";

         this.condition = '';
         this.condition1 = '';

        this.conditions = ('Yes No').split(' ').map(function (state) { return { abbrev: state }; });
        this.conditions1 = ('Yes No').split(' ').map(function (state) { return { abbrev1: state }; });
        // Data
        //vm.receiving = ReceivingData.data;
        //Datatable
        /*vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };*/
        // Methods
       /*vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }*/
    }
})();