(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('PriceGridController', PriceGridController);
            

    /** @ngInject */
    function PriceGridController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
        var originatorEv;
        var vm = this ;
        /*vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };*/

        vm.openCreatePriceGridDialog = openCreatePriceGridDialog;
        vm.uploadCSV = uploadCSV ;

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

        function openCreatePriceGridDialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'CreatePriceGridDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/createPriceGrid/createPriceGrid-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Settings: settings,
                    Settings: vm.settings,
                    event: ev
                }
            });
        }

        function uploadCSV(ev, settings)
        {
            $mdDialog.show({
                controller: 'UploadCSVDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/uploadCSV/uploadCSV-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Settings: settings,
                    Settings: vm.settings,
                    event: ev
                }
            });
        }

    }
    
})();
