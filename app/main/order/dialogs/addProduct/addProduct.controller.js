(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddProductController', AddProductController);

    /** @ngInject */

    function AddProductController(product_id,operation,design_id,color_id,is_supply,vendor_id,$mdDialog,$document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$stateParams,$http,sessionService,notifyService, $timeout,AllConstant)

    {
       
        var vm = this;
        $scope.product_id = product_id;
        $scope.NoImage = AllConstant.NoImage;
        $scope.vendor_id = vendor_id;

        $scope.operation = operation;
        var companyData = {};
        companyData.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1',vendor_id :$scope.vendor_id};
        companyData.table ='products';

        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {

                if(result.data.success == '1') 
                {
                    $scope.allProduct =result.data.records;
                } 
                else
                {
                    $scope.allProduct=[];
                }
        });

        $scope.changeProduct = function(id,color_id,operation)
        {
            var combine_array_id = {}
            combine_array_id.id = id;
            combine_array_id.design_id = design_id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/product/getProductDetailColorSize',combine_array_id).success(function(result) {

                $scope.productId =result.data.product_id;
                $scope.productColorSize =result.data.productColorSizeData;
                $scope.product_image_url =result.data.product_image_url;
                      $scope.product_image =result.data.product_image;

                if(operation == 'Add') {
                    $scope.color_id = '0';
                    $scope.sizeAll = {}
                } else {
                    $scope.sizeAll =$scope.productColorSize[color_id].size_data;
                    $scope.changeColor(color_id);
                }
            });
        }
        $scope.changeColor = function(color_id)
        {
            $scope.sizeAll = {}
            if(color_id != 0){
                $scope.sizeAll =$scope.productColorSize[color_id].size_data;
            }
        }

        if(product_id > 0) {
            if(operation == 'Add')
            {
                $scope.changeProduct(product_id,0,'Add');
            }
        }

       $scope.addProduct = function (productData,product_id,is_supply)
       {
            if(product_id == undefined) {
                var data = {"status": "error", "message": "Please select product"}
                notifyService.notify(data.status, data.message);
                return false;
            }

            if(productData.length == undefined){
                var data = {"status": "error", "message": "Please select color"}
                notifyService.notify(data.status, data.message);
                return false;
            }

            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.product_id = product_id;
            combine_array_id.company_id = sessionService.get('company_id');
            combine_array_id.productData = productData;
            combine_array_id.is_supply = is_supply;
            combine_array_id.action = operation;

            $scope.execute = 0;
            angular.forEach(productData, function(size) {
                if(size.qnty > 0)
                {
                    $scope.execute = 1;
                }
            });
            
            if($scope.execute == 0)
            {
                var data = {"status": "error", "message": "Please enter quantity to add product"}
                notifyService.notify(data.status, data.message);
            }
            else
            {
             $http.post('api/public/product/addProduct',combine_array_id).success(function(result) 
                {
                    if(result.data.success == 0)
                    {
                        var data = {"status": "error", "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                    }
                    else
                    {
                        var data = {"status": "success", "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                        $mdDialog.hide();
                    }
                });
            }
        };

        if(operation == 'Edit') {

            $scope.product_id = product_id;
            $scope.color_id = color_id;

            $scope.is_supply = false;
            if(is_supply == 1) {
                $scope.is_supply = true;
            }
            $scope.changeProduct(product_id,color_id,operation);
        } 
        
        $scope.cancel = function()
        {
            $mdDialog.hide();
        }
    }
})();