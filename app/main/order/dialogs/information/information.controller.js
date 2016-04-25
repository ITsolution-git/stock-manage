(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('InformationController', InformationController);

    /** @ngInject */
    function InformationController($mdDialog)
    {
        var vm = this;
          vm.title = 'Information';

        // Data
        
        
        vm.salesSelect = {
            "salesOption":
                    [
                        {"option": "Sales 1"},
                        {"option": "Sales 2"},
                        {"option": "Sales 3"}
                    ],
            "sales": ""

        };
        vm.blindSelect = {
            "blindOption":
                    [
                        {"option": "Blind 1"},
                        {"option": "Blind 2"},
                        {"option": "Blind 3"}
                    ],
            "blind": ""

        };
       
        vm.accountSelect = {
            "accountOption":
                    [
                        {"option": "Account 1"},
                        {"option": "Account 2"},
                        {"option": "Account 3"}
                    ],
            "account": ""

        };
        vm.contactSelect = {
            "contactOption":
                    [
                        {"option": "Contact 1"},
                        {"option": "Contact 2"},
                        {"option": "Contact 3"}
                    ],
            "contact": ""

        };
        vm.priceSelect = {
            "priceOption":
                    [
                        {"option": "Price 1"},
                        {"option": "Price 2"},
                        {"option": "Price 3"}
                    ],
            "price": ""

        };
        vm.customerPO="Customer PO";
        
        
        

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