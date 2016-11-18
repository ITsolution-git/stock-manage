(function () {
    'use strict';

    angular
            .module('app.production')
            .controller('ProductionController', ProductionController)
            .controller('FinishingqueueController', FinishingqueueController)
            .controller('ProductionqueueController', ProductionqueueController)
            .controller('ScheduleBoardController', ScheduleBoardController)
            .controller('FinishboardController', ScheduleBoardController);

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
    function ProductionqueueController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
       
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

        $scope.JobSchedualPopup = function ()
        {
            $("#ajax_loader").hide();
            var companyData = {company_id:$scope.company_id};

            $http.post('api/public/production/GetShiftMachine',companyData).success(function(result) 
            {
                if(result.data.success=='1')
                {
                    notifyService.notify('success',result.data.message);
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }

        function calpop(ev)
        {
            
            $mdDialog.show({
                controller: 'ProductionqueueController',
                controllerAs: 'vm',
                templateUrl: 'app/main/production/view/schedule_position.html',
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
     
    }
    function ScheduleBoardController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
})();