(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('UpsDialogController', UpsDialogController);

    /** @ngInject */
    function UpsDialogController($mdDialog,$controller,$state,  event,$scope,sessionService,$resource)
    {
        var vm = this;

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