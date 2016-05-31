(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('IntegrationsController', IntegrationsController);
            

    /** @ngInject */
    function IntegrationsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
            
            var originatorEv;
            var vm = this ;


            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

        
    }


       
})();
