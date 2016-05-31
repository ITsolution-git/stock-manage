(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('IntegrationsController', IntegrationsController);
            

    /** @ngInject */
    function IntegrationsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
            
            var originatorEv;
            var vm = this ;

            vm.ssActivewearDialog = ssActivewearDialog ;
            vm.authorizeNet = authorizeNet ;
            vm.upsDialog = upsDialog ;

            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

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
            
            function ssActivewearDialog(ev, settings)
            {
                $mdDialog.show({
                    controller: 'SSActivewearDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/ssActivewear/ssActivewear-dialog.html',
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

            function authorizeNet(ev, settings)
            {
                $mdDialog.show({
                    controller: 'AuthorizeNetDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/authorizeNet/authorizeNet-dialog.html',
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

            function upsDialog(ev, settings)
            {
                $mdDialog.show({
                    controller: 'UpsDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/ups/ups-dialog.html',
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
