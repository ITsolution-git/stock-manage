(function () {
    'use strict';

    angular
            .module('app.settings')
            .controller('MachineController', MachineController);

    /** @ngInject */
    function MachineController($document, $window, $timeout,$state, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        var vm = this;
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.company_id = sessionService.get('company_id');

        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='FM' || $scope.role_slug=='PU' )
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }
        else
        {
            $scope.allow_access = 1; // CAN BE EDIT BY ANYONE
        }        


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
          'function': 'machine_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

       $scope.getResource = function (params, paramsObj, search)
        {   
        	$scope.params = params;
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
    	$scope.addMachine = function(ev, settings)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    
                    $scope.machine = {
                        'machine_type':'',
                        'machine_name':'',
                        'color_count':'',
                        'screen_width':'',
                        'screen_height':'',
                    }
                    $scope.addMachine = function (machine) 
                    {
      	 				var InserArray = {}; // INSERT RECORD ARRAY

    	                InserArray.data = machine;
    	                InserArray.data.company_id = sessionService.get('company_id');
    	                InserArray.table ='machine';            

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
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/settings/dialogs/machine/addmachine.html',
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
        $scope.editMachine = function (ev,id)
        {
        	    $mdDialog.show({
                controller: function($scope,params){
                    $("#ajax_loader").show();

                    $scope.params = params;

                    $scope.allow_access = params.allow_access;
                    $scope.states_all = params.states_all;

                    var companyData = {};
	                companyData.table ='machine';
	                companyData.cond = {id:id};
	                // GET CLIENT TABLE CALL
	                $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
	                {   
	                    if(result.data.success=='1')
	                    {  
	                    	$scope.machine = result.data.records[0];
	                    }
	                    else
	                    {
	                    	notifyService.notify( "error", result.data.message);
	                    }
	                    $("#ajax_loader").hide();
	                });
                    $scope.addMachine = function(machine){
                            
                        var UpdateArray = {};
			            UpdateArray.table ='machine';
			            UpdateArray.data = machine;
			            UpdateArray.cond = {id: machine.id};
			            delete UpdateArray.data.id;

			            $("#ajax_loader").show();
		                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
		                    if(result.data.success=='1')
		                    {
		                        notifyService.notify('success', result.data.message);
		                        $scope.closeDialog();
		                    }
		                    else
		                    {
		                        notifyService.notify('error', result.data.message);
		                        $scope.closeDialog();
		                    }
		                    $("#ajax_loader").hide();
		                });
                    }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/settings/dialogs/machine/addmachine.html',
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
        $scope.deleteMachine = function (ev,id)
        {
        	var UpdateArray = {};
            UpdateArray.table ='machine';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id: id};
            
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
	            $("#ajax_loader").show();
	            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
	                if(result.data.success=='1')
	                {
	                    notifyService.notify('success', result.data.message);
	                    $scope.reloadCallback();
	                }
	                else
	                {
	                    notifyService.notify('error', result.data.message);
	                }
	                $("#ajax_loader").hide();
	            });
       		}
        }
        $scope.changeStatus = function(status,id)
        {
            var UpdateArray = {};
            UpdateArray.table ='machine';
            UpdateArray.data = {operation_status:status};
            UpdateArray.cond = {id: id};
            
            $("#ajax_loader").show();
            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                if(result.data.success=='1')
                {
                    notifyService.notify('success', result.data.message);
                }
                else
                {
                    notifyService.notify('error', result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
    }
})();