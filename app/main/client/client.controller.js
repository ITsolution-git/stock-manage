(function () {
    'use strict';

    angular
        .module('app.client')
        
        .controller('ClientController', ClientController)
        .controller('AngularWayCtrl', AngularWayCtrl);

    /** @ngInject */
    function ClientController(ClientData, $mdDialog,$document) {
        var vm = this;

        // Data
        vm.clientData = ClientData.data;

         vm.dtOptions = {
            dom       : '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth : false,
            responsive: true
        };
        // Methods
vm.openClientDialog = openClientDialog;    
        //////////
        function openClientDialog(ev, client)
        {
            $mdDialog.show({
                controller         : 'ClientDialogController',
                controllerAs       : 'vm',
                templateUrl        : 'app/main/client/dialogs/client/client-dialog.html',
                parent             : angular.element($document.body),
                targetEvent        : ev,
                clickOutsideToClose: true,
                locals             : {
                    Client : client,
                    Clients: vm.clients,
                    event: ev
                }
            });
        }
    }
    function AngularWayCtrl($resource) {
        var vmn = this;
        $resource('i18n/data.json').query().$promise.then(function (persons) {
            vmn.persons = persons;
        });
    }
})();
