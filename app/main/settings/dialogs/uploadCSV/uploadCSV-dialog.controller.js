(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('UploadCSVDialogController', UploadCSVDialogController);

    /** @ngInject */
    function UploadCSVDialogController($mdDialog,$controller,$state, event,$scope,sessionService,$resource)
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