(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductViewController', SearchProductViewController);
    /** @ngInject */
    function SearchProductViewController(product_id,product_image,description,vendor_name,operation,product_name,colorName,design_id,$mdDialog,$document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$stateParams,$http,sessionService,notifyService)
    {
       var vm = this;

       
       var combine_array_id = {};
       var product_image_main;
       combine_array_id.product_id = product_id;
       combine_array_id.design_id = design_id;
       product_image_main = "https://www.ssactivewear.com/"+product_image;
       product_image = "https://www.ssactivewear.com/"+product_image;
        

        $scope.product_name = product_name;
        $scope.product_image_display = product_image;
        $scope.product_image_display_main = product_image_main;
        $scope.description = description;
        $scope.vendor_name = vendor_name;
        $scope.product_id = product_id;

      
      if(operation == 'Edit') {

        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
           $scope.AllProductDetail = Listdata.data.colorData;

           $scope.colorName = colorName;

           $scope.modelDisplay = '';
        });

      } else {

        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
           $scope.AllProductDetail = Listdata.data.colorData;
           $scope.colorName = angular.copy(Listdata.data.colorSelection);
           $scope.modelDisplay = '';
        });

      }
        

       

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


        $scope.changeColorPositionData = function(color,position){
           
            if(position == 'colorFrontImage') {
                $scope.product_image_display = "https://www.ssactivewear.com/" + $scope.AllProductDetail[color].colorFrontImage;
            }

            if(position == 'colorSideImage') {
                $scope.product_image_display = "https://www.ssactivewear.com/" + $scope.AllProductDetail[color].colorSideImage;
            }

            if(position == 'colorBackImage') {
                $scope.product_image_display = "https://www.ssactivewear.com/" + $scope.AllProductDetail[color].colorBackImage;
            }
            
        }

        $scope.addProduct = function (productData) {
            
             var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.product_id = product_id;
            combine_array_id.productData = productData;



             $http.post('api/public/product/addProduct',combine_array_id).success(function(result) 
                {
                    $mdDialog.hide();
                    var data = {"status": "success", "message": "Product Added Successfully."}
                     notifyService.notify(data.status, data.message);
                });

        };

        

      
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