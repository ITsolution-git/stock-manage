(function () {
    'use strict';

    angular
            .module('app.client')
            .controller('ClientController', ClientController)
            .controller('ClientMainController', ClientMainController);
            
            //.controller('AngularWayCtrl', AngularWayCtrl);

    /** @ngInject */
    function ClientController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
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
        $scope.company_id = sessionService.get('company_id');

        //////////

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'client.client_id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };
/*        $scope.search = function () {
            $scope.reloadCallback();
        };*/
        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
       $scope.getResource = function (params, paramsObj, search)
        {
            $scope.params = params;
            $scope.paramsObj = paramsObj;

            var price_list_data = {};
            price_list_data.cond ={company_id :$scope.company_id,params:$scope.params};

            return $http.post('api/public/client/ListClient',price_list_data).success(function(response) {
              
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
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
                clickOutsideToClose: false,
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
        if($state.current.name == 'app.client')
        {
            $state.go('app.client.list');
        }
    }


})();
