(function ()
{
    'use strict';

    angular
        .module('app.client')
        .controller('ClientDialogController', ClientDialogController);

    /** @ngInject */
    function ClientDialogController($mdDialog,$controller,$state, Client, Clients, event,$scope,sessionService,$resource)
    {
        var vm = this;

        // Data
        vm.title = 'Edit Client';
        //vm.client = angular.copy(Client);
        vm.clients = Clients;
        vm.newClient = false;


        // Methods
        vm.addNewClient = addNewClient;
        vm.company_id = sessionService.get('company_id');
        vm.closeDialog = closeDialog;
        GetClientSelectionData();
        //////////

        /**
         * Type, Disposition, State, PriceGrid
         */
        function GetClientSelectionData()
        {

            var checkSession = $resource('api/public/client/SelectionData/'+vm.company_id,null,{
                AjaxCall : {
                       method : 'get'
                       }
            });
            checkSession.AjaxCall(null,function(Response) 
            {   
                if(Response.data.success=='1')
                {   
                    vm.AddrTypeData =Response.data.result.AddrTypeData;
                    vm.StaffList =Response.data.result.StaffList;
                    vm.ArrCleintType =Response.data.result.ArrCleintType;
                    vm.Arrdisposition  = Response.data.result.Arrdisposition;
                    vm.states_all  = Response.data.result.state;
                    $scope.AllPriceGrid=Response.data.result.AllPriceGrid;
                    $scope.approval_all = Response.data.result.approval;
                   // console.log(vm.states_all);
                }
            });
        }
        /**
         * Add new client
         */
        function addNewClient(client_data)
        {
            client_data.company_id = vm.company_id;
            var checkSession = $resource('api/public/client/addclient',null,{
                AjaxCall : {
                       method : 'post'
                       }
            });
            checkSession.AjaxCall(client_data,function(result) 
            {   
                if(result.data.success=='1')
                {   
                   $mdDialog.hide();
                   $state.go('app.client');
                   return false;
                }
                closeDialog();
                
            });
           // console.log('Client Add call');
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