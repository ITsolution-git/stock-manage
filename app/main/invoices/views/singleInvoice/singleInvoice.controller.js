(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('singleInvoiceController', singleInvoiceController);
    /** @ngInject */
    function singleInvoiceController(singleInvoiceData, purchaseOrderData, $document, $window, $timeout, $mdDialog,$scope) {
        var vm = this;
        vm.linktopay = linktopay;
        $scope.siData = singleInvoiceData.data;
        $scope.pmntHistry = purchaseOrderData.data;
        // JS FOR MODAL LINK TO PAY
        function linktopay(ev, settings) {
            $mdDialog.show({
                controller: 'linktoPayController',
                controllerAs: 'vm',
                templateUrl: 'app/main/invoices/dialogs/linktopay/linktopay-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev
                }
            });
        }
    }
})();
