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
       var product_image_main;
       combine_array_id.product_id = product_id;
       product_image_main = "https://www.ssactivewear.com/"+product_image;
        product_image = "https://www.ssactivewear.com/"+product_image;
        

       
        $scope.product_image_display = product_image;
        $scope.product_image_display_main = product_image_main;
        $scope.description = description;
        $scope.vendor_name = vendor_name;
        $scope.product_id = product_id;

        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
           $scope.AllProductDetail = Listdata.data.colorData;
           $scope.colorName = angular.copy(Listdata.data.colorSelection);
           $scope.modelDisplay = '';
        });

       

        $scope.changeColorData = function(colorName,colorImage)
        {
            $scope.colorName = colorName;
            $scope.product_image_display ="https://www.ssactivewear.com/"+colorImage;
            $scope.modelDisplay = 'display';
        }

        $scope.changeModelImage = function(modelImage)
        {
            $scope.modelDisplay = '';
            $scope.product_image_display = modelImage;
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