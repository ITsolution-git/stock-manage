(function ()
{
    'use strict';

    angular
        .module('app.art')
        .controller('generateArtController', generateArtController);

    /** @ngInject */
    function generateArtController($mdDialog,$controller,event,$scope,sessionService,$resource)
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