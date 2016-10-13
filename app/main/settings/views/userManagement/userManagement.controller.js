(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserManagementController', UserManagementController);
            

    /** @ngInject */
    function UserManagementController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var originatorEv;
        var vm = this ;
        $scope.company_id = sessionService.get('company_id');
        $scope.role_slug = sessionService.get('role_slug');

        vm.openAddEmployeeDialog = openAddEmployeeDialog;
        //vm.openEditEmployeeDialog = openEditEmployeeDialog;
        vm.resetUserPasswordDialog = resetUserPasswordDialog;
        //vm.deleteEmployeeDialog = deleteEmployeeDialog;
                // AFTER INSERT CLIENT CONTACT, GET LAST INSERTED ID WITH GET THAT RECORD

        $scope.usersList = function ()
            {   $("#ajax_loader").show();     
                $http.get('api/public/admin/account/list/'+$scope.company_id).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {  
                       $scope.users_data = result.data.records;
                    }
                    $("#ajax_loader").hide();
                });
            }
            $http.get('api/public/common/staffRole').success(function(Listdata) {

                  $scope.rolelist = Listdata.data.records
                 // console.log(Listdata); 
            });
                       

        $scope.usersList();

        function openAddEmployeeDialog(ev, settings)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params,rolelist){
                    $scope.params = params;
                    $scope.rolelist = rolelist;

                    $scope.AddUsers = function (users) {
                    $("#ajax_loader").show();
                    $scope.account = users;
                    $scope.account.parent_id = $scope.params.company_id;

                    $http.post('api/public/admin/account/add',$scope.account).success(function(result, status, headers, config) 
                    {
                        if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                                $mdDialog.hide();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                                $("#ajax_loader").hide();
                            }
                            
                        });
                    } 
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/settings/dialogs/addEmployee/addEmployee-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    rolelist:$scope.rolelist,
                    event: ev
                },
                onRemoving : $scope.usersList
            });
        }

        $scope.openEditEmployeeDialog=function (ev, user_id)
        {
            $mdDialog.show({
                controller: function($scope, params,user_id){
                    $("#ajax_loader").show();
                    $scope.params = params;
                    $scope.rolelist =$scope.params.rolelist;
                    $scope.user_id = user_id;
                    $http.get('api/public/admin/account/edit/'+user_id+'/'+ $scope.params.company_id).success(function(Listdata) 
                    {
                        if(Listdata.data.success=='1')
                        {
                            $scope.users = Listdata.data.records[0];
                        }
                        else
                        {
                            notifyService.notify( "error", Listdata.data);
                            $mdDialog.hide();
                        }
                        $("#ajax_loader").hide();
                    });
                    $scope.SaveRecords = function(account){
                            
                            account.id= $scope.user_id;
                            account.parent_id=$scope.params.company_id;

                            $http.post('api/public/admin/account/save',account).success(function(result, status, headers, config) 
                            {
                                if(result.data.success=='1')
                                {
                                    notifyService.notify('success', result.data.message);
                                    $mdDialog.hide();
                                }
                                else
                                {
                                    notifyService.notify( "error", result.data.message);
                                }
                            });
                    }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/editEmployee/editEmployee-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                   params:$scope,
                   user_id:user_id,
                    event: ev
                },
                onRemoving : $scope.usersList
            });
        }

        function resetUserPasswordDialog(ev, user_id)
        {
            $mdDialog.show({
                controller: function ($scope,params){
                        $scope.params = params; 
                        $scope.ResetPasswordMail = function()
                        {
                            $("#ajax_loader").show();
                            var account ={};
                            account.user_id = user_id;
                            account.company_id = $scope.params.company_id;
                            $http.post('api/public/admin/account/ResetPasswordMail',account).success(function(result, status, headers, config) 
                            {
                                if(result.data.success=='1')
                                {
                                    notifyService.notify('success', result.data.message);
                                    $mdDialog.hide();
                                }
                                else
                                {
                                    notifyService.notify( "error", result.data.message);
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
                templateUrl: 'app/main/settings/dialogs/resetUserPassword/resetUserPassword-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                }
            });
        }

        // ============= REMOVE TABLE RECORD WITH CONDITION ============= // 
        $scope.RemoveFields = function(table,cond_field,cond_value,extra){
              
               $("#ajax_loader").show();
                var delete_data = {};
                
                $scope.name_filed = cond_field;
                var obj = {};
                obj[$scope.name_filed] =  cond_value;
                delete_data.cond = angular.copy(obj);
                
                delete_data.table = table;
                delete_data.extra = extra;
                var permission = confirm(AllConstant.deleteMessage);
                if (permission == true) 
                {
                    $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) 
                    {
                        if(result.data.success=='1')
                        {
                            notifyService.notify('success',result.data.message);
                            $scope.usersList();
                        }
                        else
                        {
                             notifyService.notify('error',result.data.message);
                              $("#ajax_loader").hide();
                        }
                    });
                }
      }

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
       
})();
