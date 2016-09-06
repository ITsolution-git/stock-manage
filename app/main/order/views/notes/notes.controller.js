(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('NotesController', NotesController);

    /** @ngInject */
    function NotesController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;
         //vm.openaddNoteDialog = openaddNoteDialog;
         $scope.company_id = sessionService.get('company_id');
         $scope.order_id = $stateParams.id;

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
          'function': 'order_notes'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        


       $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.order_id = $scope.order_id;
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
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                    $scope.addNote = function (notes) 
                    {
                        var InserArray = {}; // INSERT RECORD ARRAY

                        InserArray.data = notes;
                        InserArray.data.order_id = $scope.params.order_id;
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
                        UpdateArray.table ='order_design_position';
                        UpdateArray.data = notes;
                        UpdateArray.cond = {id: notes.id};
                        delete UpdateArray.data.id;

                        $("#ajax_loader").show();
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                        {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                            }
                            $scope.closeDialog();
                            $("#ajax_loader").hide();
                        });

                    } 
                },
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/'+flag+'/'+flag+'.html',
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
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
})();

