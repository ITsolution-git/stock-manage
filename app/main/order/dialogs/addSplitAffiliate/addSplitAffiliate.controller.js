(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddSplitAffiliateController', AddSplitAffiliateController);

    /** @ngInject */
    function AddSplitAffiliateController($mdDialog)
    {
        var vm = this;
          vm.title = 'Split Affiliate';

        // Data
        vm.designSelect = {
            "designOption":
                    [
                        {"option": "Design 1"},
                        {"option": "Design 2"},
                        {"option": "Design 3"}
                    ],
            "design": ""

        };
        vm.productSelect = {
            "productOption":
                    [
                        {"option": "Product 1"},
                        {"option": "Product 2"},
                        {"option": "Product 3"}
                    ],
            "design": ""

        };
        vm.affiliateSelect = {
            "affiliateOption":
                    [
                        {"option": "Affiliate 1"},
                        {"option": "Affiliate 2"},
                        {"option": "Affiliate 3"}
                    ],
            "design": ""

        };
        vm.splitAffiliateSize={
          "s":"",
         "m":"",
         "l":"",
         "xl":"",
        
        };
       
         vm.splitAffiliateDialog={
          "affiliateTotal":"200",
         "affiliateNotTotal":"800",
         "shopInvoice":"$1,000",
         "affilateInvoice":"$800",
         "additonalCharges":"$100",
         "total":"$200",
         additionalCharges:"",
         notes:"",
         
        
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