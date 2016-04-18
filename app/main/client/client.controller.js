(function () {
    'use strict';

    angular
            .module('app.client')
            .controller('ClientController', ClientController)
            .controller('ClientMainController', ClientMainController);
            
            //.controller('AngularWayCtrl', AngularWayCtrl);

    /** @ngInject */
    function ClientController(ClientData, $mdDialog, $document,sessionService,$resource) {
        var vm = this;
        // Data
        //console.log(sessionService.get('company_id'));
        
       // console.log(vm.clients);
         vm.dtOptions = {
            dom       : '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',

            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            bFilter: false,
//            fnRowCallback: rowCallback
        };
        vm.searchQuery = "";
        // Methods
        vm.openClientDialog = openClientDialog;
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;
        vm.getClientData = getClientData;

         vm.getClientData();
        //////////
        function getClientData()
        {
            var price_list_data = {};
            price_list_data.cond ={company_id :sessionService.get('company_id')};

            var checkSession = $resource('api/public/client/ListClient',price_list_data,{
                post : {
                       method : 'post'
                       }
            });
            checkSession.post(price_list_data,function(result) 
            {   
                if(result.data.success=='1')
                {   
                    vm.clients = result.data.records;
                }
            });
        }
        function openClientDialog(ev, client)
        {
            $mdDialog.show({
                controller: 'ClientDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/client/dialogs/client/client-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Client: client,
                    Clients: vm.clients,
                    event: ev
                }
            });
        }
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }
//        function rowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
//            console.log("aData:" + JSON.stringify(aData));
//            console.log("iDisplayIndex:" + iDisplayIndex);
//            console.log("iDisplayIndexFull:" + iDisplayIndexFull);
//            return nRow;
//        }

    }
    function AngularWayCtrl($resource) {
        var vmn = this;
        $resource('i18n/data.json').query().$promise.then(function (persons) {
            vmn.persons = persons;
        });
    }
    function ClientMainController($state)
    {
        //console.log($state.current.name);
        if($state.current.name == 'app.client')
        {
            $state.go('app.client.list');
        }
    }


})();
