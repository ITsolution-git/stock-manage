(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductController', SearchProductController);
    /** @ngInject */
    function SearchProductController($mdDialog,$document)
    {
        var vm = this;
        // Data
        vm.filterDialog = {
            "search": "",
            "productCategory": [
                {"category": "PRODUCT CATEGORY"},
                {"category": "PRODUCT CATEGORY"},
                {"category": "PRODUCT CATEGORY"},
                {"category": "PRODUCT CATEGORY"},
                {"category": "PRODUCT CATEGORY"}
            ],
            "color":[
                {"colorName":"color"},
                {"colorName":"color"},
                {"colorName":"color"},
                {"colorName":"color"}
            ],
            "vendor":[
                {"vendorName":"Vendor Name"},
                {"vendorName":"Vendor Name"},
                {"vendorName":"Vendor Name"},
                {"vendorName":"Vendor Name"}
            ],
            "fit":[
                {"fitNo":"fit"},
                {"fitNo":"fit"},
                {"fitNo":"fit"}
            ],
            "fabric":[
                {"fabricName":"Fabric Name"},
                {"fabricName":"Fabric Name"},
                {"fabricName":"Fabric Name"},
                {"fabricName":"Fabric Name"}
            ],
            "sizes":[
                {"size":"Size No"},
                {"size":"Size No"},
                {"size":"Size No"},
                {"size":"Size No"}
            ],
            "productsImages":[
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""},
                {"productName":"Product Image", "src":""}
            ]
        };
        // Methods
        vm.openSearchProductViewDialog = openSearchProductViewDialog;
              function openSearchProductViewDialog(ev)
        {
            $mdDialog.show({
                controller: 'SearchProductViewController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/searchProductView/searchProductView.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
               
            });
        }

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