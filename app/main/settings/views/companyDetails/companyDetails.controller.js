(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('CompanyDetailsController', CompanyDetailsController);
            

    /** @ngInject */
    function CompanyDetailsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {
       $scope.btns = [{
        label: "SCREEN PRINTING",
        state: false
    }, {
        label: "EMBROIDERING",
        state: true
    }, {
        label: "PACKAGING",
        state: false
    }, {
        label: "SHIPPING",
        state: false
    }, {
        label: "ART WORK",
        state: false
    }];

    $scope.toggle = function () {
        this.b.state = !this.b.state;
    };
    }
    
})();
