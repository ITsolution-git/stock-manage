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

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
       
})();
