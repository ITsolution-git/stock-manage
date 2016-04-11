(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductViewController', SearchProductViewController);
    /** @ngInject */
    function SearchProductViewController($mdDialog)
    {
        var vm = this;
        // Data
        vm.productViewDialog = {
            "productImage": "Product Image",
            "vendor": "American Apparel",
            "itemNo": "######",
            "description": "Lorem spunm text that describe the product.",
            "colors": [
                {"colorName": "color1", "value": ""},
                {"colorName": "color1", "value": ""},
                {"colorName": "color1", "value": ""},
                {"colorName": "color1", "value": ""}
            ],
            "inventorySize": [
                {"size": "S", "value": "80"},
                {"size": "M", "value": "100"},
                {"size": "L", "value": "75"},
                {"size": "XL", "value": "90"}
            ],
            "selectSize": [
                {"qty": "", "name": "S"},
                {"qty": "", "name": "M"},
                {"qty": "", "name": "L"},
                {"qty": "", "name": "XL"}
            ]
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