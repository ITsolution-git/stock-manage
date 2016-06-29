(function () {
    'use strict';

    angular
            .module('app.admin')
            .controller('AdminController', AdminController)
            .controller('ColorController', ColorController)
            .controller('SizeController', SizeController)
            .controller('SnsController', SnsController);

    /** @ngInject */
    function AdminController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) 
    {
    	var originatorEv;
        var vm = this;
         

    	/* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'user.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'company_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        

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



       $scope.getResource = function (params, paramsObj, search)
        {   
        	$scope.params = params;
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

        	//$("#ajax_loader").show();     
	       return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
	     	{
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
	            $("#ajax_loader").hide();
	        });
    	}

    	//$scope.getResource(); // CALL COMPANY LIST

        $scope.addCompany = function(ev, settings)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                    $scope.states_all = params.states_all;
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
                onRemoving : $scope.reloadCallback
            });
        }
        $scope.edit_company = function (ev,user_id)
        {
        	    $mdDialog.show({
                controller: function($scope,params){
                    $("#ajax_loader").show();
                    $scope.params = params;
                    $scope.user_id = user_id;
                    $scope.states_all = params.states_all;
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

                            $http.post('api/public/admin/company/save',account).success(function(result, status, headers, config) 
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
                onRemoving : $scope.reloadCallback
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
	                   notifyService.notify('success', "Record Deleted Successfully!");
	                   $scope.reloadCallback(); // CALL COMPANY LIST
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
    function ColorController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) 
    {
        var originatorEv;
        var vm = this;
         
         vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'user.id',
          'sortOrder': 'dsc'

        };
        $scope.reloadCallback = function () { };
        $scope.filterBy = {
          'search': '',
          'name': '',
          'email': '',
          'function':'color_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        $scope.getResource = function (params, paramsObj, search)
        {
        	$scope.params = params;
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
        $scope.removeColor = function(ev,id)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='color';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id:id};

 			var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
	            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
	            {
	                if(result.data.success=='1')
	                {
	                   notifyService.notify('success', "Record Deleted Successfully!");
	                   $scope.reloadCallback(); // CALL COMPANY LIST
	                }
	                else
	                {
	                    notifyService.notify('error',result.data.message);
	                }
	            });
        	}
        }
        $scope.EditColor = function(ev, id)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                   
                    var colorData = {};
                    colorData.table ='color';
                    colorData.cond ={id:id}

                    // GET CLIENT TABLE CALL
                    $http.post('api/public/common/GetTableRecords',colorData).success(function(result) 
                    {   
                        if(result.data.success=='1')
                        {   
							$scope.colors = result.data.records[0];
                        }
                        else
                        {
                    	    notifyService.notify('error', result.data.message);
                            $("#ajax_loader").hide();
                        }
                    });


                    $scope.SaveRecords = function (name,id) 
                    {
                    	var UpdateArray = {};
			            //console.log(name); return false;
			            UpdateArray.table ='color';
			            UpdateArray.data = {name:name};
			            UpdateArray.cond ={id:id}
			            if(name.trim()!='')
			            {
		                    $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
		                    {
		                    	if(result.data.success=='1')
	                    		{
	                    			notifyService.notify('success',result.data.message);
	                    			$mdDialog.hide();
	                    		}
	                			else
	                			{
									notifyService.notify('error',result.data.message);
	                			}
		                    });
		                }
                    } 

                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/admin/dialogs/editcolor.html',
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
    


    }
        /** @ngInject */
    function SizeController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) 
    {
        
        var originatorEv;
        var vm = this;
         
         vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'user.id',
          'sortOrder': 'dsc'

        };
        $scope.reloadCallback = function () { };
        $scope.filterBy = {
          'search': '',
          'name': '',
          'email': '',
          'function':'size_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        $scope.getResource = function (params, paramsObj, search)
        {
        	$scope.params = params;
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
        $scope.removesize = function(ev,id)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='product_size';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id:id};

 			var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
	            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
	            {
	                if(result.data.success=='1')
	                {
	                   notifyService.notify('success', "Record Deleted Successfully!");
	                   $scope.reloadCallback(); // CALL COMPANY LIST
	                }
	                else
	                {
	                    notifyService.notify('error',result.data.message);
	                }
	            });
        	}
        }
        $scope.Editsize = function(ev, id)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                   
                    var sizeData = {};
                    sizeData.table ='product_size';
                    sizeData.cond ={id:id}

                    // GET CLIENT TABLE CALL
                    $http.post('api/public/common/GetTableRecords',sizeData).success(function(result) 
                    {   
                        if(result.data.success=='1')
                        {   
							$scope.sizes = result.data.records[0];
                        }
                        else
                        {
                    	    notifyService.notify('error', result.data.message);
                            $("#ajax_loader").hide();
                        }
                    });


                    $scope.SaveRecords = function (name,id) 
                    {
                    	var UpdateArray = {};
			            //console.log(name); return false;
			            UpdateArray.table ='product_size';
			            UpdateArray.data = {name:name};
			            UpdateArray.cond ={id:id}
			            if(name.trim()!='')
			            {
		                    $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
		                    {
		                    	if(result.data.success=='1')
	                    		{
	                    			notifyService.notify('success',result.data.message);
	                    			$mdDialog.hide();
	                    		}
	                			else
	                			{
									notifyService.notify('error',result.data.message);
	                			}
		                    });
		                }
                    } 
                    
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/admin/dialogs/editsize.html',
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
    }
    function SnsController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http,notifyService,AllConstant) 
    {
        $scope.importSnsData = function()
        {
            var permission = confirm(AllConstant.snsImport);

            if (permission == true) {

                $("#ajax_loader").show();
                $http.get('api/public/admin/uploadSnsCSV').success(function(result)
                {   
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {   
                        notifyService.notify('success',result.data.message);
                    }
                });
            }
        }
    }
})();