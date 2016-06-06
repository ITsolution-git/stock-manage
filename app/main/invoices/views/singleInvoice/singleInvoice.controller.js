(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('singleInvoiceController', singleInvoiceController);
    /** @ngInject */
    function singleInvoiceController($document, $window, $timeout, $mdDialog) {
        var vm = this;
    }
})();
