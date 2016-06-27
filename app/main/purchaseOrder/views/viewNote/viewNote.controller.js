(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('ViewNoteController', ViewNoteController);

    /** @ngInject */
    function ViewNoteController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
         //vm.openaddNoteDialog = openaddNoteDialog;
         vm.openeditNoteDialog = openeditNoteDialog;
         $scope.company_id = sessionService.get('company_id');
         $scope.po_id = $stateParams.id;

        //Dummy models data
     
                /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'note.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'purchase_notes'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        


       $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.po_id = $scope.po_id;
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

            $("#ajax_loader").show();     
           return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.success  = result.success;
                if(result.success=='1')
                {
                    return {
                      'rows': result.rows,
                      'header': result.header,
                      'pagination': result.pagination,
                      'sortBy': result.sortBy,
                      'sortOrder': result.sortOrder
                    }
                }
                else
                {
                    notifyService.notify('error',result.message);
                }
                
            });
        }

        $scope.openaddNoteDialog = function(ev, flag,note_data)
        {
            $mdDialog.show({
                controller: function($scope,params){
                    $scope.params = params;
                    $scope.note_data  = note_data;
                    //console.log($scope.note_data);
                    $scope.addNote = function (notes) 
                    {
                    var InserArray = {}; // INSERT RECORD ARRAY

                    InserArray.data = notes;
                    InserArray.data.po_id = $scope.params.po_id;
                    InserArray.data.note_date = AllConstant.currentdate;
                    InserArray.table ='purchase_notes';            

                    // INSERT API CALL
                    $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
                    {   
                        if(Response.data.success=='1')
                        {
                            notifyService.notify('success',Response.data.message);
                            $scope.closeDialog();
                        }
                        else
                        {
                            notifyService.notify('error',Response.data.message);
                        }  
                    });
                    } 
                    $scope.editNote = function (notes) 
                    {
                        var UpdateArray = {};
                        UpdateArray.table ='purchase_notes';
                        UpdateArray.data = notes;
                        UpdateArray.cond = {id: notes.id};
                        delete UpdateArray.data.id;

                        $("#ajax_loader").show();
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                        {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                                $scope.closeDialog();
                                 $mdDialog.hide();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                            }
                            $("#ajax_loader").hide();
                        });

                    } 
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                controllerAs: 'vm',
                templateUrl: 'app/main/purchaseOrder/dialogs/'+flag+'/'+flag+'.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                },
                onRemoving : $scope.reloadCallback
            });
        }
         function openeditNoteDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'EditNoteController',
                controllerAs: 'vm',
                templateUrl: 'app/main/purchaseOrder/dialogs/editNote/editNote.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
      
        
//        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.dtInstanceCB = dtInstanceCB;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
    }
})();
