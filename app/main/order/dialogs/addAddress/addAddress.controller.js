(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddAddressController', AddAddressController);

    /** @ngInject */
    function AddAddressController(Orders,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        $scope.client_id = Orders.client_id;
        $scope.addAddress={
            "description":"",
            "attn":"",
            "address":"",
            "address2":"",
            "city":"",
            "zipcode":"",
            "phone":"",
            "state":"",
            "client_id":$scope.client_id,
            "country":"USA"
        };

        var state = {};
        state.table ='state';

        $http.post('api/public/common/GetTableRecords',state).success(function(result) 
        {   
            if(result.data.success=='1')
            {   
                $scope.states_all = result.data.records;
            }
        });

        $scope.save = function()
        {
            if($scope.addAddress.description == '')
            {
                notifyService.notify('error','Please enter description');
                return false;
            }
            if($scope.addAddress.address == '')
            {
                notifyService.notify('error','Please enter address');
                return false;
            }

            var InserArray = {}; // INSERT RECORD ARRAY
            InserArray.cond = {client_id:$scope.client_id};
            InserArray.data = $scope.addAddress;
            InserArray.table = 'client_distaddress';
            // INSERT API CALL
            $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
            {
                notifyService.notify('success','Record added successfully');
                $mdDialog.hide();
            });

        }
        
        $scope.closeDialog = function()
        {
            $mdDialog.hide();
        }
    }
})();