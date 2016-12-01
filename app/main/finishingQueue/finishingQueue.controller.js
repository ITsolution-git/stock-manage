(function () {
    'use strict';

    angular
            .module('app.finishingQueue')
            .controller('FinishingQueueController', FinishingQueueController)
            .controller('FinishingUnscheduleController', FinishingUnscheduleController)
            .controller('FinishingScheduleController', FinishingScheduleController)
            .controller('FinishingBoardController', FinishingBoardController);

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
    function FinishingBoardController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        $scope.company_id = sessionService.get('company_id');
        $scope.run_date = AllConstant.currentdate;
        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='FM' || $scope.role_slug=='PU' )
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }
        else
        {
            $scope.allow_access = 1; // CAN BE EDIT BY ANYONE FOR NOW
        }        

        
        var companyData = {};
        companyData.table ='machine';
        companyData.cond = {company_id:$scope.company_id,is_delete:1};
        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
        {   
            if(result.data.success=='1')
            {   
               $scope.machine_all = result.data.records;
            }
        });

        $scope.SchedualBoardData = function(run_date)
        {
            $("#ajax_loader").show();
            var schedule_data = {};
            schedule_data.company_id =$scope.company_id;
            schedule_data.run_date =run_date;

            $http.post('api/public/production/SchedualBoardData',schedule_data).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.get_data = 1;
                    $scope.SchedualData = result.data.SchedualBoardData;
                    $scope.current_date = result.data.current_date;
                    $scope.prev_date = result.data.prev_date;
                    $scope.next_date = result.data.next_date;
                }
                else if(result.data.success=='2')
                {
                    $scope.get_data = 0;
                    $scope.current_date = result.data.current_date;
                    $scope.prev_date = result.data.prev_date;
                    $scope.next_date = result.data.next_date;
                    notifyService.notify('error',result.data.message);
                }
                else
                {
                    $scope.get_data = 0;
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.SchedualBoardweekData = function(run_date)
        {
            $("#ajax_loader").show();
            var schedule_data = {};
            schedule_data.company_id =$scope.company_id;
            schedule_data.run_date =run_date;

            $http.post('api/public/production/SchedualBoardweekData',schedule_data).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.getweek_data = 1;
                    $scope.SchedualweekData = result.data.SchedualBoardweekData;
                    $scope.currentweek_date = result.data.current_date;
                    $scope.prevweek_date = result.data.prev_date;
                    $scope.nextweek_date = result.data.next_date;
                }
                else if(result.data.success=='2')
                {
                    $scope.getweek_data = 0;
                    $scope.currentweek_date = result.data.current_date;
                    $scope.prevweek_date = result.data.prev_date;
                    $scope.nextweek_date = result.data.next_date;
                    notifyService.notify('error',result.data.message);
                }
                else
                {
                    $scope.getweek_data = 0;
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.SchedualBoardMachineData = function(run_date,machine_id)
        {
            //console.log(machine_id);
            $("#ajax_loader").show();
            $scope.machineDate = run_date;
            $scope.machine_id = machine_id;
            var schedule_data = {};
            schedule_data.company_id =$scope.company_id;
            schedule_data.run_date =run_date;
            schedule_data.machine_id =machine_id;

            $http.post('api/public/production/SchedualBoardMachineData',schedule_data).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.getmachine_data = 1;
                    $scope.SchedualmachineData = result.data.SchedualBoardMachineData;
                    $scope.currentmachine_date = result.data.current_date;
                    $scope.prevmachine_date = result.data.prev_date;
                    $scope.nextmachine_date = result.data.next_date;
                }
                else if(result.data.success=='2')
                {
                    $scope.getmachine_data = 0;
                    $scope.currentmachine_date = result.data.current_date;
                    $scope.prevmachine_date = result.data.prev_date;
                    $scope.nextmachine_date = result.data.next_date;
                    notifyService.notify('error',result.data.message);
                }
                else
                {
                    $scope.getmachine_data = 0;
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
        
        $scope.SchedualBoardData($scope.run_date); // DAY TAB DATA
        $scope.SchedualBoardweekData($scope.run_date); // WEEKLY TAB DATA
        $scope.SchedualBoardMachineData($scope.run_date); // MACHINE TAB DATA
    }
})();