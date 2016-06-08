(function () {
    'use strict';

    angular
            .module('app.shipping')
            .controller('shippingController', shippingController);
    /** @ngInject */
    function shippingController(shippingData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
        vm.ows = shippingData.shippingDetail.data;
        vm.oip = shippingData.shippingDetail.data1;
    }
})();
