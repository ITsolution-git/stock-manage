(function () {
    'use strict';

    angular
        .module('app.sample')
        .controller('SampleController', SampleController)
        .controller('AngularWayCtrl', AngularWayCtrl);

    /** @ngInject */
    function SampleController(SampleData) {
        var vmn = this;

        // Data
        vmn.helloText = SampleData.data.helloText;

        // Methods

        //////////
    }
    function AngularWayCtrl($resource) {
        var vm = this;
        $resource('i18n/data.json').query().$promise.then(function (persons) {
            vm.persons = persons;
        });
    }
})();
