(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('AffiliateController', AffiliateController);
            

    /** @ngInject */
    function AffiliateController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
            
            var originatorEv;
            var vm = this ;

            vm.deleteAffiliate = deleteAffiliate ;
            vm.uploadCSV = uploadCSV ;
            vm.addAffiliate = addAffiliate ;
            vm.editAffiliate = editAffiliate ;

            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

            function deleteAffiliate(ev, settings)
            {
                $mdDialog.show({
                    controller: 'DeleteAffiliateDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/deleteAffiliate/deleteAffiliate-dialog.html',
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

            function addAffiliate(ev, settings)
            {
                $mdDialog.show({
                    controller: 'AddAffiliateDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/addAffiliate/addAffiliate-dialog.html',
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

            function editAffiliate(ev, settings)
            {
                $mdDialog.show({
                    controller: 'UploadCSVDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/editAffiliate/editAffiliate-dialog.html',
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
