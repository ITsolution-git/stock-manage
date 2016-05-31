(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('DeletePriceGridDialogController', DeletePriceGridDialogController);

    /** @ngInject */
    function DeletePriceGridDialogController($mdDialog,$controller,$state, event,$scope,sessionService,$resource)
    {
        var vm = this;

        //////////

        
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