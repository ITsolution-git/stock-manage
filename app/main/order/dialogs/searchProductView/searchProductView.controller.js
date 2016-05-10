(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductViewController', SearchProductViewController);
    /** @ngInject */
    function SearchProductViewController(product_id,product_image,description,vendor_name,$mdDialog,$document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService)
    {
       var vm = this;


       var combine_array_id = {};
       combine_array_id.product_id = product_id;
        product_image = "https://www.ssactivewear.com/"+product_image;

       
        $scope.product_image_display = product_image;
        $scope.description = description;
        $scope.vendor_name = vendor_name;
        $scope.product_id = product_id;

        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
           
        });


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