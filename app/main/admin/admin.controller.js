(function () {
    'use strict';

    angular
            .module('app.admin')
            .controller('AdminController', AdminController)
            .controller('ColorController', ColorController)
            .controller('SizeController', SizeController);

    /** @ngInject */
    function AdminController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
        var vm = this;

    


    }
        /** @ngInject */
    function ColorController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
        var vm = this;

    


    }
        /** @ngInject */
    function SizeController($mdDialog, $document,sessionService,$resource,$scope,$stateParams, $http) {
        var vm = this;

    


    }
})();