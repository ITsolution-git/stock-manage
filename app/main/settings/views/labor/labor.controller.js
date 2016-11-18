(function () {
    'use strict';

    angular
            .module('app.settings')
            .controller('laborController', laborController);

    /** @ngInject */
    function laborController($document, $window, $timeout,$state, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {

        var order_data = {};
           order_data.cond ={status :1};
           order_data.table ='days';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
                  $scope.items =result.data.records;

          });

        var vm = this;
        vm.openaddLaborDialog = openaddLaborDialog;
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.company_id = sessionService.get('company_id');
        $scope.filter_days = 0;
        
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
          'sortBy': 'labor.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'filter_days': 0,
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


        function openaddLaborDialog(ev,labor_id)
        {
            $mdDialog.show({
                controller: 'AddLaborController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/labor/addlabor.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    labor_id: labor_id,
                    event: ev
                 },
                 onRemoving : $scope.reloadCallback
            });
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
                        notifyService.notify('success', "Record Deleted Successfully.");
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


        $scope.filterLabor = function(filter_day){
            
            var flag = true;
            
            $scope.filterBy.filter_days = '';
          
            if(filter_day > 0)
            {
                flag = false;
                $scope.filterBy.filter_days = filter_day;
            }
            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }



         /*$scope.selected = [];
       
     
              $scope.toggle = function (item, list) {
                var idx = list.indexOf(item);
                if (idx > -1) {
                  list.splice(idx, 1);
                }
                else {
                  list.push(item);
                }
              };

              $scope.exists = function (item, list) {
                return list.indexOf(item) > -1;
              };*/


    }
})();