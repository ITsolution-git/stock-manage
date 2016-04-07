(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('OrderDialogController', OrderDialogController);

    /** @ngInject */
    function OrderDialogController($mdDialog)
    {
        var vm = this;

        // Data
        vm.title = 'Create New Order';
        vm.addOrder={
            companyName:"Company Name",
            jobName:""
        };

        // Methods
    
        vm.closeDialog = closeDialog;
     
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();