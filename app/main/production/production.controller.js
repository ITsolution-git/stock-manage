(function () {
    'use strict';

    angular
            .module('app.production')
            .controller('ProductionController', ProductionController)
            .controller('FinishingqueueController', FinishingqueueController)
            .controller('ProductionqueueController', ProductionqueueController)
            .controller('ScheduleBoardController', ScheduleBoardController)
            .controller('FinishboardController', FinishboardController);

    /** @ngInject */
    function ProductionController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
    function FinishingqueueController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
    function FinishboardController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
    function ProductionqueueController($document,$mdSidenav,$window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        vm.resetFilter = resetFilter;
        $scope.company_id = sessionService.get('company_id');

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

        vm.ClientModal = [];
        vm.ProductionModal = [];
        var Filterdata = {};
        Filterdata.company_id =$scope.company_id;

        $http.post('api/public/production/GetFilterData',Filterdata).success(function(result) 
        {
            if(result.data.success=='1')
            {
                $scope.clients = result.data.clients;
                $scope.production_type = result.data.production_type;
            }
            else
            {
                notifyService.notify('error',result.data.message);
            }
            $("#ajax_loader").hide();
        });

        function resetFilter() {

            vm.inhandDate = vm.rundate  = false;
            this.searchOrder = null;
            jQuery('.dateFilter').prop("value", " ");
           
            vm.clientsettings = {externalIdProp: clientFunction()}
            function clientFunction(){
                vm.ClientModal = [];
            }
            vm.productionksettings = {externalIdProp:productionFunction()}
            function productionFunction(){
                vm.ProductionModal = [];
            }

            for (var i = 0; i < this.ClientModal.length; i++) {
               this.ClientModal[i].id = null;
            }
            for (var i = 0; i < vm.ProductionModal.length; i++) {
                vm.ProductionModal[i].id = null;

            }

            vm.inhandDate = vm.rundate = null;
            this.searchOrder = null;
            jQuery('.dateFilter').prop("value", " ");

            //console.log(vm.statusCheckModal);

            $scope.filterProduction();
        }


                /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'ord.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'production_list'
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


        $scope.filterProduction = function(){
            
            var flag = true;
            $scope.filterBy.client = '';
            $scope.filterBy.rundate = '';
            $scope.filterBy.inhandDate = '';
            $scope.filterBy.production='';
            $scope.filterBy.temp = '';
            

            $scope.clientArray = [];
            angular.forEach(vm.ClientModal, function(company){
                    $scope.clientArray.push(company.id);
            })
            if($scope.clientArray.length > 0)
            {
                flag = false;
                $scope.filterBy.client = angular.copy($scope.clientArray);
            }


            $scope.ProductionArray = [];
            angular.forEach(vm.ProductionModal, function(company){
                    $scope.ProductionArray.push(company.id);
            })
            if($scope.ProductionArray.length > 0)
            {
                flag = false;
                $scope.filterBy.production = angular.copy($scope.ProductionArray);
            }



            if(vm.rundate != '' && vm.rundate != undefined && vm.rundate != false)
            {
                flag = false;
                $scope.filterBy.rundate = vm.rundate;
            }
            if(vm.inhandDate != '' && vm.inhandDate != undefined && vm.inhandDate != false)
            {
                flag = false;
                $scope.filterBy.inhandDate = vm.inhandDate;
            }
            
            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }


        $scope.DisplayMokup = function(image)
        {
            $mdDialog.show({
                 controller: function ($scope, params){
                            $scope.params = params;
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                    },
                templateUrl: 'app/main/production/view/mokupImgageDisplay.html',
                parent: angular.element($document.body),
                clickOutsideToClose: true,
                locals: {
                    params:image
                }
            });
        }


        $scope.openEditPopup = function(path,param)
        {
            // PATH WILL BE SET AFTER MAIN WITHOUT /
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'position_schedule');
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            //console.log(123);
            $scope.reloadCallback();
        }

        $scope.JobSchedualPopup = function (position_id)
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:$scope.company_id,position_id:position_id};

            $http.post('api/public/production/GetShiftMachine',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.machine_data = result.data.machine_data;
                    $scope.shift_data = result.data.shift_data;
                    $scope.Position_scheduleData = result.data.Position_scheduleData;
                    
                    $mdDialog.show({
                        controller: function ($scope, params,position_id)
                        {
                            $scope.params = params;
                            $scope.company_id = params.company_id;
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                            $scope.SaveSchedulePosition = function(data)
                            {
                                //console.log(data); return false;
                                data.company_id = $scope.company_id;
                                $http.post('api/public/production/SaveSchedulePosition',data).success(function(result) 
                                {
                                    $scope.closeDialog();
                                    notifyService.notify(result.data.success,result.data.message);
                                });
                            }
                        },
                        controllerAs: 'vm',
                        templateUrl: 'app/main/production/view/schedule_position.html',
                        parent: angular.element($document.body),
                        clickOutsideToClose: true,
                        locals: 
                        {
                            params:$scope,
                            position_id:position_id
                        },
                        onRemoving : $scope.reloadCallback
                    });

                    //$scope.openEditPopup('production/view/schedule_position.html',$scope);

                    //notifyService.notify('success',result.data.message);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }

        $scope.JobDetail = function(position_id)
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:$scope.company_id,position_id:position_id};

            $http.post('api/public/production/GetPositionDetails',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.PositionDetail = result.data.PositionDetail;
                    $scope.GarmentDetail = result.data.GarmentDetail;
                    $scope.openEditPopup('production/view/jobdetail_popup.html',$scope);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }

        function jobpopup(ev)
        {
            
            $mdDialog.show({
                controller: 'ProductionqueueController',
                controllerAs: 'vm',
                templateUrl: 'app/main/production/view/jobdetail_popup.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                // locals: {
                //     Client: client,
                //     Clients: vm.clients,
                //     event: ev
                // }
            });
        }
        // Data
         // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };
        vm.openRightMenu1 = function () {
            $mdSidenav('left').toggle();
        };
     
    }
    function ScheduleBoardController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
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

        $scope.openEditPopup = function(path,param)
        {
            // PATH WILL BE SET AFTER MAIN WITHOUT /
            var edit_params = {data:param,flag:'edit'};
            sessionService.openEditPopup($scope,path,edit_params,'position_schedule');
        }
        // RETURN FUNCTION FROM POPUP.
        $scope.returnFunction = function()
        {
            //console.log(123);
            $scope.reloadCallback();
        }



        $scope.GetSchedulePositionDetail = function(position_id)
        {
            $("#ajax_loader").show();
            var companyData = {company_id:$scope.company_id,position_id:position_id};

            $http.post('api/public/production/GetSchedulePositionDetail',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    $scope.PositionDetail = result.data.PositionDetail;
                    $scope.GarmentDetail = result.data.GarmentDetail;
                    $scope.openEditPopup('production/view/scheduleposition_popup.html',$scope);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    $("#ajax_loader").hide();
                }
                
            });
        }

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