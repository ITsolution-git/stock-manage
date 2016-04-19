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
        vm.client={
            companyName:"",
            type:"",
            streetAddr:"",
            suit:"",
            city:"",
            state:"",
            zipCode:"",
            billingEmail:"",
            companyPhone:"",
            companyWebURL:"",
            disposition:"",
            firstName:"",
            lastName:"",
            phone:"",
            email:"",
        };
        vm.states=[
            {abbrev:"State-1"},  
            {abbrev:"State-2"}  
        ];
        vm.clientTypes=[
            {abbrev:"Type-1"},  
            {abbrev:"Type-2"}  
        ];
        vm.dispositions=[
            {abbrev:"Disposition-1"},  
            {abbrev:"Disposition-2"}  
        ];


        // Methods
        vm.addNewClient = addNewClient;

        vm.closeDialog = closeDialog;

        //////////

        /**
         * Add new client
         */
        function addNewClient(client_data)
        {
            vm.clients.unshift(vm.client);
            console.log('Client Add call');
            //closeDialog();
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