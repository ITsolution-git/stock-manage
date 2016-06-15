(function () {
    'use strict';

    angular
            .module('app.misc')
            .controller('miscController', miscController);
    /** @ngInject */
    function miscController(miscData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
        vm.ows = miscData.miscDetail.data;
        vm.oip = miscData.miscDetail.data1;
    }
})();
