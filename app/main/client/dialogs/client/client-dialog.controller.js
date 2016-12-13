(function ()
{
    'use strict';

    angular
        .module('app.client')
        .controller('ClientDialogController', ClientDialogController);

    /** @ngInject */
    function ClientDialogController($mdDialog,$controller,$state, Client, Clients, event,$scope,sessionService,$resource,notifyService)
    {
        var vm = this;

        // Data
        vm.title = 'Edit Client';
        //vm.client = angular.copy(Client);
        vm.clients = Clients;
        vm.newClient = false;
        $scope.client={};


        // Methods
        vm.addNewClient = addNewClient;
        vm.company_id = sessionService.get('company_id');
        vm.closeDialog = closeDialog;
        GetClientSelectionData();
        //////////
        $scope.Gapi_options = { // GOOGLE ADDRESS API OPTIONS
            componentRestrictions: { country: 'US' }
        };
        $scope.Gapi_address = { // GOOGLE ADDRESS API PARAMETERS
            name: '',
            place: '',
            components: {
              placeId: '',
              streetNumber: '', 
              street: '',
              city: '',
              state: '',
              countryCode: '',
              country: '',
              postCode: '',
              district: '',
              location: {
                lat: '',
                long: ''
                }
            }
        };
        $scope.GetAPIData = function (apidata)
        {
           // console.log(123); return false;
            $scope.client.pl_address = angular.isUndefined(apidata.streetNumber)?'':apidata.streetNumber+", ";
            $scope.client.pl_address = angular.isUndefined(apidata.street)?$scope.client.pl_address:$scope.client.pl_address+apidata.street;
            $scope.client.pl_city = angular.isUndefined(apidata.city)?'':apidata.city;
            for(var i=0; i<$scope.states_all.length; i++)
            {
                if($scope.states_all[i].code == apidata.state)
                {
                    $scope.client.state_id = angular.isUndefined($scope.states_all[i].id)?'':$scope.states_all[i].id;
                    $scope.client.pl_state = angular.isUndefined($scope.states_all[i].id)?'':$scope.states_all[i].id;
                }
            }
            $scope.client.pl_pincode = angular.isUndefined(apidata.postCode)?'':apidata.postCode;
        }
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
                    $scope.states_all = Response.data.result.state;;
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
                } else {

                  var data = {"status": "error", "message": "Company Name already exists!"}
                  notifyService.notify(data.status, data.message);
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