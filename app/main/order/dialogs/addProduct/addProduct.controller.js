(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddProductController', AddProductController);

    /** @ngInject */
    function AddProductController($mdDialog)
    {
        var vm = this;

        // Data
        vm.addProduct={
          "productName":"",
         "s":"",
         "m":"",
         "l":"",
         "xl":"",
         "notes":""
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