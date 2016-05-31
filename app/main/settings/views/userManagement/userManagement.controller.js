(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserManagementController', UserManagementController);
            

    /** @ngInject */
    function UserManagementController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
        var originatorEv;
        var vm = this ;

        vm.openAddEmployeeDialog = openAddEmployeeDialog;
        vm.openEditEmployeeDialog = openEditEmployeeDialog;
        vm.resetUserPasswordDialog = resetUserPasswordDialog;
        vm.deleteEmployeeDialog = deleteEmployeeDialog;

        function openAddEmployeeDialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'AddEmployeeDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/addEmployee/addEmployee-dialog.html',
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

        function openEditEmployeeDialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'EditEmployeeDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/editEmployee/editEmployee-dialog.html',
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

        function resetUserPasswordDialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'ResetUserPasswordDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/resetUserPassword/resetUserPassword-dialog.html',
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

        function deleteEmployeeDialog(ev, settings)
        {
            $mdDialog.show({
                controller: 'DeleteEmployeeDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/deleteEmployee/deleteEmployee-dialog.html',
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

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
       
})();
