(function () {
    'use strict';

    angular
            .module('app.production')
            .controller('ProductionController', ProductionController)
            .controller('FinishingqueueController', FinishingqueueController)
            .controller('ProductionqueueController', ProductionqueueController);

            


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
    function ProductionqueueController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
})();