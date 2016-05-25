(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('PriceGridController', PriceGridController);
            

    /** @ngInject */
    function PriceGridController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
        var originatorEv;
        var vm = this ;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
    }

       
   

       
    }
)();
