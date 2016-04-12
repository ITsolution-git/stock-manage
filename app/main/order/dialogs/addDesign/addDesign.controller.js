(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddDesignController', AddDesignController);

    /** @ngInject */
    function AddDesignController($mdDialog)
    {
        var vm = this;

        // Data
        vm.orderInfo={
          "customerPo":"######",
         "sales":"keval Baxi",
         "blind":"Yes",
         "accountManger":"Nancy McPhee",
         "mainContact":"Joshi Goodman",
         "priceGrid":"ABC Grid"
        };
        vm.addOrder={
            companyName:"Company Name",
            jobName:""
        };
        
        vm.addDesign={
            compnayName:"",
            "front":"",
            back:"",
            sideLeft:"",
            sideRight:"",
            top:"",
            bottom:""
            
        }
        

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