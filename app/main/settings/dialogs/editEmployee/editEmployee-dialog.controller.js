(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('EditEmployeeDialogController', EditEmployeeDialogController);

    /** @ngInject */
    function EditEmployeeDialogController($mdDialog,$controller,$state, event,$scope,sessionService,$resource)
    {
        var vm = this;

        this.permissionRole = '';

        this.permissionRoles = ('Super_Admin Brand_Coordinator Art_Team Simple_User').split(' ').map(function (state) { return { abbrev: state }; });
        
        $scope.cancel = function () {
            $mdDialog.hide();
        };

        
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();