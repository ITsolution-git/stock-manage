(function () {
    'use strict';

    angular
            .module('app.settings')
            .controller('laborController', laborController);

    /** @ngInject */
    function laborController($document, $window, $timeout,$state, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {


         // change display number to order Id for fetching the order data
          var order_data = {};
           order_data.cond ={status :1};
           order_data.table ='days';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
                  $scope.days =result.data.records;

          });


        var vm = this;
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.company_id = sessionService.get('company_id');
        
        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM')
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
          'sortBy': 'user.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'labor_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
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
    	$scope.openInsertPopup = function(ev, settings)
        {
            var Insert_params = {company_id:$scope.company_id};
            //OPEN POPUP WITH TO INSERT DATA// SCOPE, PATH INSIDE app/main FOLDER, CONDITION DATA, TABLE
            sessionService.openAddPopup($scope,'settings/dialogs/labor/addlabor.html',Insert_params,'labor');
        }
        $scope.openEditPopup = function(path,param,ev)
        {
            var ap_days = param.apply_days;
            param.AllDays = ap_days.split(',');
            param.DisplayDays = $scope.days;
           
            var edit_params = {data:param};
            // OPEN POPUP WITH DATA   // SCOPE, PATH FILE, DISPLAY DATA, TABLE
            sessionService.openEditPopup($scope,'settings/dialogs/labor/addlabor.html',edit_params,'labor');
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            //console.log(123);
            $scope.reloadCallback();
        }
        $scope.delete_labor = function (ev,id)
        {
        	var UpdateArray = {};
            UpdateArray.table ='labor';
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
    }
})();