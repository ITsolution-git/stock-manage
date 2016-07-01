(function() {
	'use strict',
	angular
	.module('app.core')
	.factory('sessionService', sessionService);

	/* ngInject */
	function sessionService($document,msNavigationService, $window,$state, $timeout, $mdDialog, $stateParams,$resource,$http,notifyService,AllConstant,$filter) {
		var vm = this;
		var service = {
			set : set,
			get : get,
			remove : remove,
			destroy : destroy,
			AccessService: AccessService,
			openAddPopup:openAddPopup,
			openEditPopup:openEditPopup
		};

		return service;

		function set(key, value) {
			return sessionStorage.setItem(key, value);
		};

		function get(key) {
			return sessionStorage.getItem(key);
		};

		function remove(key) {
			return sessionStorage.removeItem(key);
		}

		function destroy() {
			var logout = $resource('api/public/auth/logout', null, 
				{
					post : {
						method : 'get'
					}
				});
			logout.post(null, function(response) {
				notifyService.notify('success',response.data.message);				
				remove('useremail');
                remove('role_slug');
                remove('login_id');
                remove('name');
                remove('user_id');
                remove('role_title');
                remove('username');
                remove('password');
                remove('company_id');
                remove('company');
                $state.go('app.login');
			},function(response) {
				notifyService.notify('error',response.data.message);
			});
		}	

		function AccessService(ret)
		{
				$http.get('api/public/auth/session').success(function(result) 
                {  
	           		
	                if(result.data.success=='1')
	                {   
	                	set('email',result.data.email);
	                    set('role_slug',result.data.role_session);
	                    set('user_id',result.data.user_id);
	                    set('company_id',result.data.company_id);
	                    set('company_name',result.data.company_name);
	                    set('name',result.data.name);
	                    set('role_title',result.data.role_title);
	                    set('login_id',result.data.login_id);

	                    var role = result.data.role_session;
	                    checkRollMenu(result.data.role_session);
	                    if(ret.indexOf(role) <= -1 && ret != 'ALL' && ret!='')
			            {
			               // console.log('error');
			                var data = {"status": "error", "message": "You are Not authorized, Please wait"}
			                notifyService.notify(data.status, data.message);
			               	setTimeout(function(){  window.open('dashboard', '_self'); }, 1000);
			                return false;
			            }

	                }
	                else
	                {
	                    /*if($next.name == 'app.login' || $next.name == 'app.forget'  || $next.name == 'app.reset') 
	                    {                                        
	                        console.log('break,else');
	                    }
	                    else
	                    {*/
	                        $state.go('app.login');
	                        notifyService.notify("error", "Please signin first.");
	                        //$stateChangeStart.preventDefault();
	                    //}
	                }
            });

		}
		function checkRollMenu(role)
		{
			//console.log(role);
			if(role=='SA')
			{
				msNavigationService.deleteItem('fuse.settings');
				msNavigationService.deleteItem('fuse.art');
				msNavigationService.deleteItem('fuse.client');
				msNavigationService.deleteItem('fuse.order');
				msNavigationService.deleteItem('fuse.invoices');
				msNavigationService.deleteItem('fuse.purchaseOrder');
				msNavigationService.deleteItem('fuse.receiving');
				msNavigationService.deleteItem('fuse.finishing');
				msNavigationService.deleteItem('fuse.customProduct');
				msNavigationService.deleteItem('fuse.customProduct');
				msNavigationService.deleteItem('fuse.shipping');
				msNavigationService.deleteItem('fuse.dashboard');

			}
			else if(role=='CA')
			{
				msNavigationService.deleteItem('fuse.admin');
			}
			else if(role=='BC')
			{
				msNavigationService.deleteItem('fuse.admin');
				msNavigationService.deleteItem('fuse.settings');
			}
			else if(role=='FM')
			{
				msNavigationService.deleteItem('fuse.admin');
				msNavigationService.deleteItem('fuse.settings');
			}
			else
			{
				msNavigationService.deleteItem('fuse.settings');
				msNavigationService.deleteItem('fuse.art');
				msNavigationService.deleteItem('fuse.order');
				msNavigationService.deleteItem('fuse.invoices');
				msNavigationService.deleteItem('fuse.purchaseOrder');
				msNavigationService.deleteItem('fuse.receiving');
				msNavigationService.deleteItem('fuse.finishing');
				msNavigationService.deleteItem('fuse.customProduct');
				msNavigationService.deleteItem('fuse.customProduct');
				msNavigationService.deleteItem('fuse.shipping');
				msNavigationService.deleteItem('fuse.admin');
			}
		}

		function openAddPopup(scope,path,params,table)
		{
			$mdDialog.show({
                controller:function ($scope, params)
                {
                    $scope.params = params; // GET PARAMETERS FOR POPUP
                    $scope.InsertTableData = function(insert_data,extra,cond)
			        {
			        	$("#ajax_loader").show();
			        	var InserArray = {}; // INSERT RECORD ARRAY
                		InserArray.data = insert_data;
                		InserArray.table =table;

                		// SPECIAL CONDITIONS
                		if(extra=='vendorcontact') { InserArray.data.vendor_id = $scope.params.vendor_id;}
                		
			        	$http.post('api/public/common/InsertRecords',InserArray).success(function(result) 
			        	{ 
			        		if(result.data.success=='1')
		                    { notifyService.notify('success',result.data.success); $mdDialog.hide();}
			                else
		                    { notifyService.notify('error',result.data.message); }
		                    $("#ajax_loader").hide();
                   		});
			        }
                    $scope.closeDialog = function() 
                    { $mdDialog.hide(); } 
                },
                templateUrl: 'app/main/'+path,
                parent: angular.element($document.body),
                clickOutsideToClose: false,
                    locals: {
                        params:params,  // PARAMETERS PASS TO POPUP
                    },
                onRemoving : scope.returnFunction  // THIS FUNCTION WILL BE FIXED AND MUST BE PRESENT IN YOUR CONTROLLER
            });
		}
		function openEditPopup(scope,path,params,table)
		{
			$mdDialog.show({
                controller:function ($scope, params)
                {
                    $scope.params = params; // GET PARAMETERS FOR POPUP
                    $scope.UpdateTableData = function(field_name,field_value,table_name,cond_field,cond_value,extra,extra_cond)
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
                		
                	$http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
			        	{
		                    if(result.data.success=='1')
		                    { 
		                    	notifyService.notify('success', result.data.message);
		                    	//$mdDialog.hide();
		                    }
			                else
		                    {
		                        notifyService.notify('error',result.data.message);
		                    }
                   		});
			        }
			        $scope.UpdateTableDataAll = function(tableData,table_name,cond_field,cond_value,extra,extra_cond)
			        {
			        	$("#ajax_loader").show();
			        	var UpdateArray = {};
			            UpdateArray.table =table_name;
			            UpdateArray.data = tableData;

			            var condition_obj = {};
			            condition_obj[cond_field] =  cond_value;
			            UpdateArray.cond = angular.copy(condition_obj);

			            if(extra=='vendorcontact') { delete UpdateArray.data.id;}
                		//console.log(UpdateArray); return false;
                		$http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
			        	{
		                    if(result.data.success=='1')
		                    { 
		                    	notifyService.notify('success', result.data.message);
		                    	$mdDialog.hide();
		                    }
			                else
		                    {
		                        notifyService.notify('error',result.data.message);
		                    }
		                    $("#ajax_loader").hide();
                   		});
			        }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                },
                templateUrl: 'app/main/'+path,
                parent: angular.element($document.body),
                clickOutsideToClose: false,
                    locals: {
                        params:params,  // PARAMETERS PASS TO POPUP
                    },
                onRemoving : scope.returnFunction  // THIS FUNCTION WILL BE FIXED AND MUST BE PRESENT IN YOUR CONTROLLER
            });
		}


	}
	
})();