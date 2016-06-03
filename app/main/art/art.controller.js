(function () {
    'use strict';

    angular
            .module('app.art')
            .controller('ArtController', ArtController);
    /** @ngInject */
    function ArtController(ArtData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
        vm.arts = ArtData.artData.data;
        vm.screenset = ArtData.artData.data1;
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
        // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };
        vm.openRightMenu1 = function () {
            $mdSidenav('left').toggle();
        };
    }
})();
