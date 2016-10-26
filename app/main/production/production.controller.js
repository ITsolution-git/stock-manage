(function () {
    'use strict';

    angular
            .module('app.production')
            .controller('ProductionController', ProductionController);

    /** @ngInject */
    function ProductionController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter) 
    {
        var vm = this;
        vm.searchQuery = "";
        
        // Data
     
    }
})();