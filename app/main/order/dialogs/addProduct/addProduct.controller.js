(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddProductController', AddProductController);

    /** @ngInject */
    function AddProductController(product_id,operation,design_id,$mdDialog,$document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$stateParams,$http,sessionService,notifyService)
    {
        var vm = this;



         if(operation == 'Edit') {
            $scope.product_id = product_id;
            $scope.changeProduct(product_id);
/*
               var combine_array_id = {};
               combine_array_id.product_id = product_id;
               combine_array_id.design_id = design_id;
               combine_array_id.company_id = sessionService.get('company_id');

                $http.post('api/public/product/productCustomDetailData',combine_array_id).success(function(Listdata) {
                   $scope.AllProductDetail = Listdata.data.colorData;
      
                   $scope.modelDisplay = '';
                   $("#ajax_loader").hide();
                });
*/
              } else {

               /* $http.post('api/public/product/productCustomDetailData',combine_array_id).success(function(Listdata) {
                   $scope.AllProductDetail = Listdata.data.colorData;
                   
                   $("#ajax_loader").hide();
                });*/

              }

         var companyData = {};
            companyData.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1',vendor_id :0};
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


                $scope.changeProduct = function(id,color_id){

                     var product_arr = {}
                     product_arr = {'id':id};
                       $http.post('api/public/product/getProductDetailColorSize',product_arr).success(function(result) {
                      

                      $scope.productId =result.data.product_id;
                      $scope.productColorSize =result.data.productColorSizeData;

                       if(color_id != 0) {
                        $scope.sizeAll = result.data.productColorSizeData[color_id];

                      } else {
                        $scope.sizeAll = {}
                      }

                   
                    });
                }


       $scope.addProduct = function (productData,product_id,description,is_supply) {
            
            
             var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.product_id = product_id;
            combine_array_id.company_id = sessionService.get('company_id');
            combine_array_id.productData = productData;
            combine_array_id.description = description;
            combine_array_id.is_supply = is_supply;
             
            

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
                        var data = {"status": result.data.status, "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                    }
                    else
                    {
                        var data = {"status": "success", "message": "Product added successfully"}
                        notifyService.notify(data.status, data.message);
                        $mdDialog.hide();
                    }
                });
            }

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