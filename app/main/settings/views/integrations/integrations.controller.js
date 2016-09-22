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

            $scope.GetAllApi = function ()
            {
                $http.get('api/public/admin/company/getSnsAPI/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.sns = result.data.data[0];
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
                 $http.get('api/public/admin/company/getAuthorizeAPI/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.authorize = result.data.data[0];
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
                $http.get('api/public/admin/company/getUpsAPI/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.ups = result.data.data[0];
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
                });

                $http.get('api/public/admin/company/getQBAPI/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.qb = result.data.data[0];

                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
                $http.get('api/public/admin/company/getFedexAPI/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {
                        $scope.fedex = result.data.data[0];

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
