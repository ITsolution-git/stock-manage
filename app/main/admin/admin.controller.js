(function () {
    'use strict';

    angular
            .module('app.admin')
            .controller('AdminController', AdminController)
            .controller('ColorController', ColorController)
            .controller('SizeController', SizeController);

    /** @ngInject */
    function AdminController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) 
    {
    	var originatorEv;
        var vm = this;
        $scope.companylist = function ()
        {   
        	$("#ajax_loader").show();     
	        $http.get('api/public/admin/company/list').success(function(result) 
	     	{
	     		if(result.data.success=='1')
	            {
	                $scope.company  = result.data.records;
	            }
	            else
	            {
	                notifyService.notify('error',result.data.message);
	            }
	            $("#ajax_loader").hide();
	        });
    	}

    	$scope.companylist(); // CALL COMPANY LIST

        $scope.addCompany = function(ev, settings)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                    $scope.AddUsers = function (users) 
                    {
                    //$("#ajax_loader").show();
                    $scope.account = users;
                    $scope.account.parent_id = "1";

	                    $http.post('api/public/admin/company/add',$scope.account).success(function(result, status, headers, config) 
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
                templateUrl: 'app/main/admin/dialogs/addcompany.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                },
                onRemoving : $scope.companylist
            });
        }
        $scope.edit_company = function (ev,user_id)
        {
        	    $mdDialog.show({
                controller: function($scope,params){
                    $("#ajax_loader").show();
                    $scope.params = params;
                    $scope.user_id = user_id;
                    $http.get('api/public/admin/account/edit/'+user_id+'/1').success(function(Listdata) 
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
                            account.parent_id=1;

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
                templateUrl: 'app/main/admin/dialogs/editcompany.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                },
                onRemoving : $scope.companylist
            });
        }

        $scope.resetUserPasswordDialog = function(ev, user_id)
        {
            $mdDialog.show({
                controller: function ($scope,params){
                        $scope.params = params; 
                        $scope.ResetPasswordMail = function()
                        {
                            $("#ajax_loader").show();
                            var account ={};
                            account.user_id = user_id;
                            account.company_id = "1";
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
       
        $scope.removeCompany = function(ev,id)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='users';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id:id};

 			var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
	            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
	            {
	                if(result.data.success=='1')
	                {
	                   notifyService.notify('success', "Record Updated Successfully!");
	                   $scope.companylist(); // CALL COMPANY LIST
	                }
	                else
	                {
	                    notifyService.notify('error',result.data.message);
	                }
	            });
        	}
        }

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }
        /** @ngInject */
    function ColorController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
        var vm = this;

    


    }
        /** @ngInject */
    function SizeController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
        var vm = this;

    


    }
})();