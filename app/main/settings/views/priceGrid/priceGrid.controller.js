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
     

        var company_id = sessionService.get('company_id');
        var price_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  company_id;
        price_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/admin/price',price_list_data).success(function(result, status, headers, config) {
            $scope.price = result.data.records;                     
        });

        vm.openCreatePriceGridDialog = openCreatePriceGridDialog;
        vm.uploadCSV = uploadCSV ;
        vm.deletePriceGrid = deletePriceGrid ;

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

        function deletePriceGrid(ev, settings)
        {
            $mdDialog.show({
                controller: 'DeletePriceGridDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/deletePriceGrid/deletePriceGrid-dialog.html',
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
