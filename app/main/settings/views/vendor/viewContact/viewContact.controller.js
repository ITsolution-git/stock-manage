(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('vendordetailController', vendordetailController);

    /** @ngInject */
    function vendordetailController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
         //vm.openaddNoteDialog = openaddNoteDialog;
         $scope.company_id = sessionService.get('company_id');

         $scope.v_id = $stateParams.id;
        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='FM' || $scope.role_slug=='PU'  || $scope.role_slug=='AT' )
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }
        else
        {
            $scope.allow_access = 1; // CAN BE EDIT BY ANYONE
        }

        /* STATE DATA*/   
        var state = {}; state.table ='state';
        $http.post('api/public/common/GetTableRecords',state).success(function(result) 
        {  if(result.data.success=='1'){  $scope.states_all = result.data.records;}});


     
         /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'vendor_contact'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        


       $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.v_id = $scope.v_id;
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
                    //notifyService.notify('error',result.message);
                }
                
            });
        }

        // DYNAMIC POPUP FOR INSERT RECORDS
        $scope.openInsertPopup = function(path,ev)
        {
            var insert_params = {vendor_id:$scope.v_id};
            //OPEN POPUP WITH TO INSERT DATA// SCOPE, PATH FILE, CONDITION DATA, TABLE
            sessionService.openAddPopup($scope,path,insert_params,'vendor_contacts');
        }
        $scope.openEditPopup = function(path,param,ev)
        {
            var edit_params = {data:param};
            // OPEN POPUP WITH DATA   // SCOPE, PATH FILE, DISPLAY DATA, TABLE
            sessionService.openEditPopup($scope,path,edit_params,'vendor_contacts');
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            //console.log(123);
            $scope.reloadCallback();
        }
         $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
        {
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);
            
            if(extra=='delete_note')
            {
                var permission = confirm(AllConstant.deleteMessage);
                if (permission == true) 
                {

                }
                else
                {
                    return false;
                }
            }

            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    notifyService.notify('success', result.data.message);
                    if(extra=='contact_main') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.UpdateTableField('is_main','1',table_name,'id',param,'','');
                       }
                    $scope.reloadCallback();
                }
                else
                {
                    notifyService.notify('error', result.data.message);
                }
            });

        }


       
        
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
       
    }
})();
