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

            vm.uploadCSV = uploadCSV ;

            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

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
