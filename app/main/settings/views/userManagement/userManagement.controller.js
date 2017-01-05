(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserManagementController', UserManagementController);
            

    /** @ngInject */
    function UserManagementController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$rootScope,$resource,$http,$scope,$state,notifyService,AllConstant)
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
                delete_data.data = {is_delete:0};
                //delete_data.extra = extra;
                var permission = confirm(AllConstant.deleteMessage);
                if (permission == true) 
                {
                    $http.post('api/public/common/UpdateTableRecords',delete_data).success(function(result) 
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


      


         $scope.loginUser = function(id,email){
          
            var combine_array_id = {};
            combine_array_id.id = id;
            combine_array_id.email = email;
            combine_array_id.company_id = sessionService.get('company_id');
            combine_array_id.relogin = 0;

            sessionService.remove('role_slug');

            var login_user = $resource('api/public/admin/loginUser',null,{
                post : {
                    method : 'post'
                }
            });


           login_user.post(combine_array_id,function(result) 
            {   $("#ajax_loader").show();             
                  if(result.data.success == '0') {
                                  var data = {"status": "error", "message": "Please check Email and Password"}
                                  notifyService.notify(data.status, data.message);
                                  $state.go('app.login');
                                  $("#ajax_loader").hide();
                                  return false;

                                } else {
                                    

                                   sessionService.set('oldLoginId',result.data.records.oldLoginId);
                                   sessionService.set('oldEmail',result.data.records.oldEmail);
                                   sessionService.set('useremail',result.data.records.useremail);
                                   sessionService.set('role_slug',result.data.records.role_slug);
                                   sessionService.set('login_id',result.data.records.login_id);
                                   sessionService.set('name',result.data.records.name);
                                   sessionService.set('user_id',result.data.records.user_id);
                                   sessionService.set('role_title',result.data.records.role_title);
                                   sessionService.set('username',result.data.records.username);
                                   sessionService.set('password',result.data.records.password);
                                   sessionService.set('company_id',result.data.records.company_id);
                                   sessionService.set('company',result.data.records.company);
                                   sessionService.set('profile_photo',result.data.records.profile_photo);
                                   if(result.data.records.reset_password=='1'){
                                    sessionService.set('reset_password',result.data.records.reset_password);
                                   }else{
                                    sessionService.set('reset_password','0');
                                   }

                                   sessionService.set('token',result.data.records.token);
                                   
                                   var data = {"status": "success", "message": "Login Successfully, Please wait..."}
                                   notifyService.notify(data.status, data.message);
                                   
                                   //window.location.href = $state.go('app.client');
                                    //$state.go('app.client');
                                    
                                   setTimeout(function(){ 
                                        window.open('dashboard', '_self'); }, 1000);
                                   // 
                                    //window.location.reload();
                                    return false;


                                }

                         
            });
        }

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
       
})();
