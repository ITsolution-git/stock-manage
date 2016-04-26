(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductController', SearchProductController);
    /** @ngInject */
    function SearchProductController(productSearch,$mdDialog,$document,$scope,$http)
    {
        $scope.productSearch = productSearch;
        
        $scope.getProducts = function()
        {
            var vendor_arr = {'vendor_id' : 1, 'search' : $scope.productSearch};
            $http.post('api/public/product/getProductByVendor',vendor_arr).success(function(result, status, headers, config) {
                $scope.products = result.data.records;
            });
        }

        $scope.getProducts();
        // Data
        $scope.filterDialog = {
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
        $scope.openSearchProductViewDialog = openSearchProductViewDialog;
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

        $scope.closeDialog = closeDialog;
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();