(function () {
    'use strict';

    angular
            .module('app.settings')
            .controller('SettingsController', SettingsController);

    /** @ngInject */
    function SettingsController(ReceivingData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;

        vm.openChangePasswordialog = openChangePasswordialog;


    }
})();