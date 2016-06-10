(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('approveOrderDiallogController', approveOrderDiallogController);

    /** @ngInject */
    function approveOrderDiallogController($mdDialog,$controller,event,$scope,sessionService,$resource)
    {
        var vm = this;
        vm.title = 'Order Approved';
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();