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
			$("#ajax_loader").show();
			$mdDialog.show({
                controller:function ($scope, params, all_scope)
                {
                	$("#ajax_loader").hide();
                    $scope.params = params; 		//	GET PARAMETERS FOR POPUP
                    $scope.flag = 'add'; 		//	GET PARAMETERS FOR POPUP
                    $scope.all_scope = all_scope; 	//	FULL SCOPE OF CONTROLLER DATA

                    $scope.closeDialog = function() 
                    { $mdDialog.hide(); } 
                    
                    $scope.InsertTableData = function(insert_data,extra,cond)
			        {
			        	$("#ajax_loader").show();
			        	var InserArray = {}; 		// INSERT RECORD ARRAY
                		InserArray.data = insert_data;
                		InserArray.table =table;

                		//=============== SPECIAL CONDITIONS ==============
                		if(extra=='vendorcontact') { InserArray.data.vendor_id = $scope.params.vendor_id;}
                		if(extra=='sales') { InserArray.data.company_id = $scope.params.company_id; InserArray.data.sales_created_date =AllConstant.currentdate;}
                		if(extra=='artnote') { InserArray.data.screenset_id = $scope.params.screenset_id; InserArray.data.note_date =AllConstant.currentdate;}
                		if(extra=='client_contact'){InserArray.data.client_id=$scope.all_scope.client_id;}
                		if(extra=='client_notes'){InserArray.data.client_id=$scope.all_scope.client_id; InserArray.data.user_id=$scope.all_scope.login_id;InserArray.data.created_date=AllConstant.currentdate;}
                		if(extra=='client_distaddress'){InserArray.data.client_id=$scope.all_scope.client_id;}
                		//=============== SPECIAL CONDITIONS ==============

                		//console.log(InserArray); return false;
			        	$http.post('api/public/common/InsertRecords',InserArray).success(function(result) 
			        	{ 
			        		if(result.data.success=='1')
		                    { notifyService.notify('success',result.data.message); $mdDialog.hide();}
			                else
		                    { notifyService.notify('error',result.data.message); }
		                    $("#ajax_loader").hide();
                   		});
			        }

                },
                templateUrl: 'app/main/'+path,
                parent: angular.element($document.body),
                clickOutsideToClose: true,
                    locals: {
                        params:params,  // PARAMETERS PASS TO POPUP
                        all_scope:scope
                    },
                onRemoving : scope.returnFunction  // THIS FUNCTION WILL BE FIXED AND MUST BE PRESENT IN YOUR CONTROLLER
            });
		}
		function openEditPopup(scope,path,params,table)
		{
			$("#ajax_loader").show();
			$mdDialog.show({
                controller:function ($scope, params,all_scope)
                {
                	$("#ajax_loader").hide();
                    $scope.params = params.data; // GET PARAMETERS FOR POPUP
                    $scope.flag = 'edit'; 		//	GET PARAMETERS FOR POPUP
                    $scope.all_scope = all_scope; 	//	FULL SCOPE OF CONTROLLER DATA
                   // console.log($scope.params); //return false;
                    $scope.UpdateTableData = function(field_name,field_value,table_name,cond_field,cond_value,extra,extra_cond)
			        {
			        	$("#ajax_loader").show();
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
		                    $("#ajax_loader").hide();
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

			            //=============== SPECIAL CONDITIONS ==============
			            if(extra=='vendorcontact' || extra=='sales' ) { delete UpdateArray.data.id; delete UpdateArray.data.sales_created_date;}
                		if(extra=='artnote'){ delete UpdateArray.data.id;}
                		if(extra=='client_contact'){ delete UpdateArray.data.id; }
                		if(extra=='client_address'){ delete UpdateArray.data.id;delete UpdateArray.data.address_type;delete UpdateArray.data.state_name; }
                		if(extra=='client_notes'){ delete UpdateArray.data.note_id; delete UpdateArray.data.user_name; delete UpdateArray.data.created_date;}
                		if(extra=='client_distaddress'){delete UpdateArray.data.id;delete UpdateArray.data.state_name;}
                		//=============== SPECIAL CONDITIONS ==============

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
                        all_scope:scope
                    },
                onRemoving : scope.returnFunction  // THIS FUNCTION WILL BE FIXED AND MUST BE PRESENT IN YOUR CONTROLLER
            });
		}


	}
	
})();