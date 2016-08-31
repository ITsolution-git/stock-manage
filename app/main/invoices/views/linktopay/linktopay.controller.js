(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('linktoPayController', linktoPayController);

    /** @ngInject */


    function linktoPayController123($window, $timeout,$filter,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
      var vm = this;
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        /**
         * Close dialog
         */
        function closeDialog() {
            $mdDialog.hide();
        }
                
    }
})();
