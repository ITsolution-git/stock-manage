(function () {
    'use strict';

    angular
            .module('app.finishingQueue')
            .controller('FinishingQueueController', FinishingQueueController)
            .controller('FinishingUnscheduleController', FinishingUnscheduleController)
            .controller('FinishingScheduleController', FinishingScheduleController);

    /** @ngInject */
    function FinishingQueueController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        
        var vm = this;
        vm.searchQuery = "";

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.currentTab = 'all';
        
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'f.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'temp':'',
          'search': ''
        };
         $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params,'type':'all'};

              return $http.post('api/public/finishingQueue/listFinishingQueue',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

        $scope.getTab = function(tab)
        {
            $scope.currentTab = 'all';
        }

        $scope.openEditPopup = function(path,param)
        {
            // PATH WILL BE SET AFTER MAIN WITHOUT /
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'position_schedule');
        }
        
        $scope.JobSchedualPopup = function (finishing_id)
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:sessionService.get('company_id'),finishing_id:finishing_id};

            $http.post('api/public/finishingQueue/GetShiftMachine',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.machine_data = result.data.machine_data;
                    $scope.shift_data = result.data.shift_data;
                    $scope.Position_scheduleData = result.data.Position_scheduleData;
                    $scope.openEditPopup('finishingQueue/dialogs/schedule_position.html',$scope);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
    }
    function FinishingUnscheduleController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        
        var vm = this;
        vm.searchQuery = "";

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.currentTab = 'unscheduled';

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'f.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'temp':'',
          'search': ''
        };
         $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params,'type':'unscheduled'};

              return $http.post('api/public/finishingQueue/listFinishingQueue',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

        $scope.getTab = function(tab)
        {
            $scope.currentTab = 'unscheduled';
        }
        $scope.openEditPopup = function(path,param)
        {
            // PATH WILL BE SET AFTER MAIN WITHOUT /
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'position_schedule');
        }
        
        $scope.JobSchedualPopup = function (finishing_id)
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:sessionService.get('company_id'),finishing_id:finishing_id};

            $http.post('api/public/finishingQueue/GetShiftMachine',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.machine_data = result.data.machine_data;
                    $scope.shift_data = result.data.shift_data;
                    $scope.Position_scheduleData = result.data.Position_scheduleData;
                    $scope.openEditPopup('finishingQueue/dialogs/schedule_position.html',$scope);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
    }
    function FinishingScheduleController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService) {
        
        var vm = this;
        vm.searchQuery = "";

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.currentTab = 'scheduled';

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'f.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'temp':'',
          'search': ''
        };
         $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

              orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params,'type':'scheduled'};

              return $http.post('api/public/finishingQueue/listFinishingQueue',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

        $scope.getTab = function(tab)
        {
            $scope.currentTab = 'scheduled';
        }
        $scope.openEditPopup = function(path,param)
        {
            // PATH WILL BE SET AFTER MAIN WITHOUT /
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'position_schedule');
        }
        
        $scope.JobSchedualPopup = function (finishing_id)
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:sessionService.get('company_id'),finishing_id:finishing_id};

            $http.post('api/public/finishingQueue/GetShiftMachine',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.machine_data = result.data.machine_data;
                    $scope.shift_data = result.data.shift_data;
                    $scope.Position_scheduleData = result.data.Position_scheduleData;
                    $scope.openEditPopup('finishingQueue/dialogs/schedule_position.html',$scope);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
    }
})();