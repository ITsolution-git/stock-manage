(function ()
{
    'use strict';

    angular
            .module('app.receiving')
            .controller('ReceivingInfoController', ReceivingInfoController);
            

    /** @ngInject */
    function ReceivingInfoController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {

    	var vm = this;
          vm.openReceivingInformationDialog = openReceivingInformationDialog;

        function openReceivingInformationDialog(ev,order_id)
        {
            $mdDialog.show({
                controller: 'InformationController',
                controllerAs: 'vm',
                templateUrl: 'app/main/receiving/dialogs/information/information.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    order_id: order_id,
                    event: ev
                },
                onRemoving : $scope.orderDetail
            });
        }
    }
    
    
})();
