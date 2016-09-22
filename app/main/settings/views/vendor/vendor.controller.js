(function () {
    'use strict';

    angular
            .module('app.settings')
            .controller('VendorController', VendorController);

    /** @ngInject */
    function VendorController($document, $window, $timeout,$state, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
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
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }        

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
          'function': 'vendor_list'
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
    	$scope.addVendor = function(ev, settings)
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                    $scope.states_all = params.states_all;
                    $scope.addVendor = function (vendor) 
                    {
  	 				var InserArray = {}; // INSERT RECORD ARRAY

	                InserArray.data = vendor;
	                InserArray.data.company_id = $scope.params.company_id;
	                InserArray.table ='vendors';            

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
                templateUrl: 'app/main/settings/dialogs/vendor/addvendor.html',
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

                    $scope.allow_access = params.allow_access;
                    $scope.states_all = params.states_all;

                    var companyData = {};
	                companyData.table ='vendors';
	                companyData.cond = {id:user_id};
	                // GET CLIENT TABLE CALL
	                $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
	                {   
	                    if(result.data.success=='1')
	                    {  
	                    	$scope.users = result.data.records[0];
	                    }
	                    else
	                    {
	                    	notifyService.notify( "error", result.data.message);
	                    }
	                    $("#ajax_loader").hide();
	                });
                    $scope.SaveRecords = function(account){
                            
                            var UpdateArray = {};
				            UpdateArray.table ='vendors';
				            

				            UpdateArray.data = account;

				            UpdateArray.cond = {id: account.id};
				            delete UpdateArray.data.id;
				            //console.log(UpdateArray); return false;

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
                templateUrl: 'app/main/settings/dialogs/vendor/editvendor.html',
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
        $scope.delete_vendor = function (ev,vendor_id)
        {
        	var UpdateArray = {};
            UpdateArray.table ='vendors';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id: vendor_id};
            
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
        $scope.vendor_contact = function(ev,vendor_id)
        {
            $state.go('app.settings.vendor.contact',{id: vendor_id});
            return false;
        }


    }
})();