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
        vm.calendarpopup = calpop;
        vm.jobpopup = jobpopup;
        
        function calpop(ev)
        {
            
            $mdDialog.show({
                controller: 'ProductionqueueController',
                controllerAs: 'vm',
                templateUrl: 'app/main/production/calendardialog.html',
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
                templateUrl: 'app/main/production/jobpopup.html',
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