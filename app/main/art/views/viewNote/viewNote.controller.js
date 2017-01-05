(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('ArtViewNoteController', ArtViewNoteController);

    /** @ngInject */
    function ArtViewNoteController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
         //vm.openaddNoteDialog = openaddNoteDialog;
         $scope.company_id = sessionService.get('company_id');
         $scope.display_number = $stateParams.id;

            var companyData = {};
            companyData.table ='artjob_screensets';
            companyData.cond = {display_number: $stateParams.id,company_id:$scope.company_id};
            // GET CLIENT TABLE CALL
            $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
            {   
                if(result.data.success=='1')
                {   
                    $scope.screenset_id =result.data.records[0].id;
                }
            });
         
        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='SU')
        {
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }
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
          'function': 'art_notes'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        


       $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.display_number = $scope.display_number;
            $scope.params.company_id = $scope.company_id;
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


        // DYNAMIC POPUP FOR INSERT RECORDS
        $scope.openInsertPopup = function(path,ev)
        {
            var insert_params = {screenset_id:$scope.screenset_id,flag:'add'};
            sessionService.openAddPopup($scope,path,insert_params,'art_notes');
        }
        $scope.openEditPopup = function(path,param,ev)
        {
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'art_notes');
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            //console.log(123);
            $scope.reloadCallback();
        }

        $scope.RemoveEditPopup = function(id)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='art_notes';
            UpdateArray.data = {is_deleted:0};
            UpdateArray.cond = {id:id};
            
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', 'Record Deleted Successfully.');
                       $scope.reloadCallback();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                });
            }
        }

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
})();
