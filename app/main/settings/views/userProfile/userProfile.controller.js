(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserProfileController', UserProfileController);

    /** @ngInject */


    function UserProfileController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
      var vm = this;

        vm.openChangePasswordialog = openChangePasswordialog;

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

        function openChangePasswordialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'ChangePasswordDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/changePassword/changePassword-dialog.html',
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
