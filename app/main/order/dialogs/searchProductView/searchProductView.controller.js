(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductViewController', SearchProductViewController);
    /** @ngInject */
    function SearchProductViewController(product_id,product_image,description,vendor_name,operation,product_name,colorName,design_id,design_product_id,size_group_id,warehouse,brand_name,$mdDialog,$document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$stateParams,$http,sessionService,notifyService)
    {
        $("#ajax_loader").show();


          // change display number to design Id for fetching the order data
          var design_data = {};
           design_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           design_data.table ='order_design';
          
          $http.post('api/public/common/GetTableRecords',design_data).success(function(result) {
              
              if(result.data.success == '1') 
              {
                  $scope.design_id = result.data.records[0].id;

              } 
          });

          
        var vm = this;
       
        var combine_array_id = {};
        var product_image_main;
        combine_array_id.product_id = product_id;
        combine_array_id.design_id = design_id;
        combine_array_id.company_id = sessionService.get('company_id');
        product_image_main = "https://www.ssactivewear.com/"+product_image;
        product_image = "https://www.ssactivewear.com/"+product_image;
        $scope.operation = operation;
        $scope.warehouse = warehouse;

        $scope.product_name = product_name;
        $scope.brand_name = brand_name;
        $scope.product_image_display = product_image;
        $scope.product_image_display_main = product_image_main;
        $scope.description = description;
        $scope.vendor_name = vendor_name;
        $scope.product_id = product_id;
        $scope.colorName = colorName;
        $scope.size_group_id = size_group_id;

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
            $scope.miscData = result.data.records;
        });

        var product_data = {};
        product_data.cond ={'product_id':product_id,'design_id':design_id,is_delete :'1'};
        product_data.table ='design_product';

        $scope.is_supply = false;

        $http.post('api/public/common/GetTableRecords',product_data).success(function(result) {
          
            if(result.data.success == '1') 
            {
                $scope.product =result.data.records[0];
                $scope.is_supply = result.data.records[0].is_supply;

                if($scope.is_supply == 1) {
                    $scope.is_supply = true;
                }
                else {
                    $scope.is_supply = false;
                }
            } 
            else
            {
                $scope.product=[];
            }
        });

       
      if(operation == 'Edit') {

        combine_array_id.design_product_id = design_product_id;
        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
            
            if(Listdata.data.success == 0)
            {
                var data = {"status": "error", "message": Listdata.data.message}
                notifyService.notify(data.status, data.message);
                $mdDialog.hide();
            }
            else
            {
                $scope.AllProductDetail = Listdata.data.colorData;
                $scope.total_all = Listdata.data.total_all;
                $scope.total_price = Listdata.data.total_price;
                $scope.colorName = colorName;
                $scope.modelDisplay = '';
            }
           $("#ajax_loader").hide();
        });

      } else {

        $http.post('api/public/product/productDetailData',combine_array_id).success(function(Listdata) {
            if(Listdata.data.success == 0)
            {
                var data = {"status": "error", "message": Listdata.data.message}
                notifyService.notify(data.status, data.message);
                $mdDialog.hide();
            }
            else
            {
                $scope.total_all = Listdata.data.total_all;
                $scope.total_price = Listdata.data.total_price;
                $scope.AllProductDetail = Listdata.data.colorData;
                $scope.colorName = angular.copy(Listdata.data.colorSelection);
                $scope.modelDisplay = '';
            }
           $("#ajax_loader").hide();
        });

      }
        

       

        $scope.changeColorData = function(colorName,colorImage)
        {
            $scope.colorName = colorName;
            $scope.product_image_display ="https://www.ssactivewear.com/"+colorImage;
            $scope.modelDisplay = 'display';
        }

        $scope.updateWarehouse = function(warehouse)
        {
                 $scope.warehouse = warehouse;
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

        $scope.addProduct = function (productData,warehouse) {
            $("#ajax_loader").show();
          
           /* if(size_group_id == 0)
            {
                var data = {"status": "error", "message": "Please select size group"}
                notifyService.notify(data.status, data.message);
                return false;
            }*/
            
            var combine_array_id = {};
            combine_array_id.id = $scope.design_id;
            combine_array_id.product_id = product_id;
            combine_array_id.company_id = sessionService.get('company_id');
            combine_array_id.productData = productData;
            combine_array_id.action = operation;
            combine_array_id.is_supply = $scope.is_supply;
            //combine_array_id.size_group_id = size_group_id;
            combine_array_id.warehouse = warehouse;
            combine_array_id.design_product_id = design_product_id;

            $scope.execute = 0;
            angular.forEach(productData, function(colorArray, key) {
               //console.log(colorArray);console.log(key);
                angular.forEach(colorArray.sizes, function(size) {
                    //console.log(size);
                    if(size.qnty > 0)
                    {
                        $scope.execute = 1;
                    }
                     return false;
                });
            });
            //console.log($scope.execute); return false;
            if($scope.execute == 0)
            {
                var data = {"status": "error", "message": "Please enter quantity to add product"}
                notifyService.notify(data.status, data.message);
                $("#ajax_loader").hide();
            }
            else
            {
             $http.post('api/public/product/addProduct',combine_array_id).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success == 0)
                    {
                        var data = {"status": result.data.status, "message": result.data.message}
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

        

      
        vm.closeDialog = closeDialog;
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
                                                     
         $scope.checkSizeData = function(qnty,maxqnty)
        {
             $scope.checkSize =  0;
            if(qnty > maxqnty) {
              var data = {"status": "error", "message": "Qntity must be less then inventory"}
                     notifyService.notify(data.status, data.message);
                     $scope.checkSize =  1;

            } 
        }

         $scope.findTotal = function(productData,inventory,allProducts)
        {
         
          /* if(inventory == undefined || inventory == 0) {
            return false;
           }*/
         var combine_array_id={};
         combine_array_id.productData = productData;
         combine_array_id.allProducts = allProducts;
         $http.post('api/public/product/findTotal',combine_array_id).success(function(result) 
            {
                $scope.AllProductDetail[$scope.colorName].total = result.data.total;
                $scope.AllProductDetail[$scope.colorName].total_qnty = result.data.total_qnty;
                $scope.total_all = result.data.summary_total;
                $scope.total_price = result.data.summary_price;
                
            });
           
        }
    }
})();