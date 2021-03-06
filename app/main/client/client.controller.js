(function () {
    'use strict';

    angular
            .module('app.client')
            .controller('ClientController', ClientController)
            .controller('ClientMainController', ClientMainController);
            
            //.controller('AngularWayCtrl', AngularWayCtrl);

    /** @ngInject */
    function ClientController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) {

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
        var originatorEv;
         vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        vm.openClientDialog = openClientDialog;
       
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;
        $scope.company_id = sessionService.get('company_id');
        $scope.user_id = sessionService.get('user_id');
        $scope.role_slug = sessionService.get('role_slug');

        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='FM' || $scope.role_slug=='PU' || $scope.role_slug=='AT' || $scope.role_slug=='SM')
        {
            $scope.allow_access = 1;  // THESE ROLE CAN ALLOW TO EDIT
        }
        else
        {
            $scope.allow_access = 0; // THESE ROLE CAN ALLOW TO EDIT, JUST CAN VIEW
        }

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
            $("#ajax_loader").show();
            $scope.params = params;
            $scope.paramsObj = paramsObj;

            var price_list_data = {};
            price_list_data.cond ={company_id :$scope.company_id,params:$scope.params};

            return $http.post('api/public/client/ListClient',price_list_data).success(function(response) {
              
                var header = response.header;
                $scope.success = response.success;
                $("#ajax_loader").hide();
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
            if($scope.allow_access==0){ notifyService.notify('error',AllConstant.NO_ACCESS); return false;}
            
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



    $scope.delete_client = function (ev,client_id)
        {
           
            var UpdateArray = {};
            UpdateArray.table ='client';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {client_id: client_id};
            
            
            var permission = confirm(AllConstant.deleteMessage);

            if (permission == true) 
            {
                $("#ajax_loader").show();
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', result.data.message);
                        $scope.reloadCallback();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
            }
        }




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
