(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('IntegrationsController', IntegrationsController);
            

    /** @ngInject */
    function IntegrationsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService)
    {


            $scope.company_id = sessionService.get('company_id');
            var originatorEv;
            var vm = this ;

           /* vm.ssActivewearDialog = ssActivewearDialog ;
            vm.authorizeNet = authorizeNet ;
            vm.upsDialog = upsDialog ;*/
            vm.qbActivewearSetup = qbActivewearSetup ;
           // vm.fedexDialog = fedexDialog;
            
            vm.quickbookDisconnect = quickbookDisconnect;

            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

            // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
            $scope.role_slug = sessionService.get('role_slug');
            if($scope.role_slug=='CA')
            {
                $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
            }
            else
            {
                $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
            }

             // AFTER INSERT CLIENT CONTACT, GET LAST INSERTED ID WITH GET THAT RECORD
            var state = {};
            state.table ='state';

            $http.post('api/public/common/GetTableRecords',state).success(function(result) 
            {   
                if(result.data.success=='1')
                {   
                    $scope.states_all = result.data.records;
                }
            });


            $scope.GetAllApi = function ()
            {
                $("#ajax_loader").show();
                var CheckArray = {};
                CheckArray.company_id = $scope.company_id;
                $http.post('api/public/admin/company/GetAllApi',CheckArray).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.sns = result.data.data.sns;
                        $scope.authorize = result.data.data.authorize;
                        $scope.qb = result.data.data.qb;
                        $scope.fedex = result.data.data.fedex;
                        $scope.ups = result.data.data.ups;
                        $scope.location_data = result.data.data.location;
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
                });

            }
            $scope.GetAllApi();

            $scope.cancel = function () {
                $mdDialog.hide();
            };
            
            /**
             * Close dialog
             */
            function closeDialog()
            {
                $mdDialog.hide();
            }
        
//======================
﻿        // DYNAMIC POPUP FOR INSERT RECORDS
        $scope.openInsertPopup = function(path,ev,table)
        {
            var insert_params = {company_id:$scope.company_id};
            sessionService.openAddPopup($scope,path,insert_params,table);
        }
        // DYNAMIC POPUP FOR UPDATE RECORDS
        $scope.openEditPopup = function(path,param,ev,table)
        {
            var edit_params = {data:param}; // REQUIRED PARAMETERS
            sessionService.openEditPopup($scope,path,edit_params,table);
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            $scope.GetAllApi();
        }
//======================

            // ============= UPDATE TABLE RECORD WITH CONDITION ============= // 
        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);

            if(param==1)
            {
                notifyService.notify('error','Please select another Main Location first !');
                return false;
            }
            if(extra=='remove_address')
            {
                var permission = confirm("Are you sure to delete this Record ?");
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
                    notifyService.notify('success', "Record Updated Successfully!");
                    if(extra=='is_main') // SECOND CALL CONDITION WITH EXTRA PARAMS
                    {
                        $scope.UpdateTableField('is_main','1',table_name,'id',param,'','');
                    }
                    $scope.GetAllApi();
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                
            });
        }


            $scope.OpenForm = function (ev, all_data,path)
            {
                $("#ajax_loader").show();
                $mdDialog.show({
                    controller: function ($scope,params)
                    {
                        $scope.params = params;
                        $("#ajax_loader").hide();

                        $scope.closeDialog = function() 
                        {
                            $mdDialog.hide();
                        } 
                        $scope.UpdateTableData = function(tableData,table_name,cond_field,cond_value,extra,extra_cond)
                        {
                            var vm = this;
                            var UpdateArray = {};
                            UpdateArray.table =table_name;
                            UpdateArray.data = tableData;

                            var condition_obj = {};
                            condition_obj[cond_field] =  cond_value;
                            UpdateArray.cond = angular.copy(condition_obj);

                            delete UpdateArray.data.id;

                            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                            {
                                if(result.data.success=='1')
                                {
                                    notifyService.notify('success',result.data.message);   
                                }
                                else
                                {
                                    notifyService.notify('error',result.data.message);
                                }
                                $mdDialog.hide();
                           });
                        }

                    },
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/'+path,
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        params:all_data,
                        event: ev
                    },
                    onRemoving : $scope.GetAllApi  
                });
            }


            function quickbookDisconnect(ev, settings)
            {


                  $http.get('api/public/qbo/disconnect').success(function(result, status, headers, config) 
              {
                 $state.reload();
                  
              });
            }

            function qbActivewearSetup(ev,id) {

                 var company_list_data = {};
                    var condition_obj = {};
                    condition_obj['company_id'] =  sessionService.get('company_id');
                    condition_obj['id'] =  id;
                    company_list_data.cond = angular.copy(condition_obj);

                $("#ajax_loader").show();
                $http.post('api/public/qbo/AddItem',company_list_data).success(function(result) {
                    $("#ajax_loader").hide();
                            if(result != '0')
                            {
                                notifyService.notify('success',"Setup successfully");   
                            }
                            else
                            {
                                notifyService.notify('error',"Please connect to quickbook first");
                            }

                            $mdDialog.hide();
                            $state.go($state.current,'', {reload: true, inherit: false});
                            return false;

                           });
            }
        
    }

       
})();
