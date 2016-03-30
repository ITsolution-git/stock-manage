(function ()
{
    'use strict';

    angular
        .module('app.client')
        .controller('ClientDialogController', ClientDialogController);

    /** @ngInject */
    function ClientDialogController($mdDialog, Client, Clients, event)
    {
        var vm = this;

        // Data
        vm.title = 'Edit Client';
        vm.client = angular.copy(Client);
        vm.clients = Clients;
        vm.newClient = false;



        // Methods
        vm.addNewClient = addNewClient;

        vm.closeDialog = closeDialog;

        //////////

        /**
         * Add new task
         */
        function addNewClient()
        {
            vm.clients.unshift(vm.client);

            closeDialog();
        }

     

        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();