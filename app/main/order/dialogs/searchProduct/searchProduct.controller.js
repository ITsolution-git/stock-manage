(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductController', SearchProductController);

    /** @ngInject */
    function SearchProductController(data,$mdDialog,$document,$scope,$http,$state,AllConstant,$stateParams,sessionService,notifyService)
    {



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

        
        $scope.productSearch = data.productSearch;
        $scope.vendor_id = data.vendor_id;
        $scope.vendors = data.vendors;
        $scope.toggle = true;
        $scope.color = true;
        $scope.size = true;
        $scope.NoImage = AllConstant.NoImage;
        $scope.product_base_path = AllConstant.base_path+'api/public/uploads/'+sessionService.get('company_id')+'/products/';
        $scope.custom_product_base_path = AllConstant.base_path+'api/public/uploads/'+sessionService.get('company_id')+'/custom_image/';

        
        $scope.init = {
          'count': 20,
          'page': 1,
          'sortBy': 'p.name',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'vendor_id':'',
          'search': '',
          'category_id':'',
          'color_id':'',
          'size_id':'',
          'client_id': ''
        };

        $scope.filterBy.vendor_id = $scope.vendor_id;
        $scope.filterBy.search = $scope.productSearch;
        $scope.filterBy.client_id = data.client_id;

        $scope.filterBy.category_id = [];
        $scope.filterBy.color_id = [];
        $scope.filterBy.size_id = [];

        $scope.filterProducts = function(type,value){

            $scope.reloadCallback = function () { };

            $scope.filterBy.vendor_id = 0;
            $scope.filterBy.search = '';

            if($scope.filterBy.category_id.length == 0)
            {
                $scope.filterBy.category_id = [];
            }
            if($scope.filterBy.color_id.length == 0)
            {
                $scope.filterBy.color_id = [];
            }
            if($scope.filterBy.size_id.length == 0)
            {
                $scope.filterBy.size_id = [];
            }

            if(type == 'category_id')
            {
                $scope.filterBy.category_id.push(value);
            }
            if(type == 'color_id')
            {
                $scope.filterBy.color_id.push(value)
            }
            if(type == 'size_id')
            {
                $scope.filterBy.size_id.push(value);
            }
            $scope.filterBy.vendor_id = $scope.vendor_id;
            $scope.filterBy.search = $scope.productSearch;
        }

        $scope.resetFilter = function()
        {
            $scope.reloadCallback = function () { };

            $scope.filterBy.category_id = [];
            $scope.filterBy.color_id = [];
            $scope.filterBy.size_id = [];

            if($scope.productSearch != '')
            {
                $scope.filterBy.vendor_id = $scope.vendor_id;
                $scope.filterBy.search = $scope.productSearch;
            }
        }

        $scope.getResource = function (params, paramsObj, search) {
            $scope.params = params;
            $scope.paramsObj = paramsObj;
 
            var orderData = {};
            $("#ajax_loader").show();

              orderData.cond ={params:$scope.params};
              //var vendor_arr = {'vendor_id' : $scope.vendor_id, 'search' : $scope.productSearch};

              return $http.post('api/public/product/getProductByVendor',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.category_filter = response.category_filter;
                $scope.color_filter = response.color_filter;
                $scope.size_filter = response.size_filter;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        $scope.reloadPage = function(){

         $state.reload();
        }


        $scope.openSearchProductViewDialog = function(ev,product_id,product_image,description,vendor_name,operation,product_name,colorName,size_group_id,warehouse,brand_name)
        {
          
            var check_data = {};
            check_data.design_id = $scope.design_id;
            check_data.product_id = product_id;

            $http.post('api/public/product/checkProductExist',check_data).success(function(result) {
                   
                if(result.data.success == '1') {
                    var data = {"status": "error", "message": "This product is already added to your design select different one."}
                    notifyService.notify(data.status, data.message);
                }
                else
                {
                    $mdDialog.show({
                        controller: 'SearchProductViewController',
                        controllerAs: 'vm',
                        templateUrl: 'app/main/order/dialogs/searchProductView/searchProductView.html',
                        parent: angular.element($document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        locals: {
                            product_id: product_id,
                            product_image:product_image,
                            description:description,
                            vendor_name:vendor_name,
                            operation:operation,
                            product_name:product_name,
                            colorName:colorName,
                            design_id:0,
                            design_product_id:0,
                            size_group_id:size_group_id,
                            warehouse:warehouse,
                            brand_name:brand_name,
                            event: ev
                        },
                        onRemoving : $scope.reloadPage
                    });
                }
            });
        }

        $scope.openAddProductDialog = function(ev,controller, file,product_id,operation,color_id,is_supply,design_product_id,vendor_id,product_name,description,vendor_name)
        {
            var check_data = {};
            check_data.design_id = $scope.design_id;
            check_data.product_id = product_id;

            $http.post('api/public/product/checkProductExist',check_data).success(function(result) {
                   
                if(result.data.success == '1') {
                    var data = {"status": "error", "message": "This product is already added to your design select different one."}
                    notifyService.notify(data.status, data.message);
                }
                else
                {
                    $mdDialog.show({
                        controller: controller,
                        controllerAs: $scope,
                        templateUrl: file,
                        parent: angular.element($document.body),
                        targetEvent: ev,
                        clickOutsideToClose: true,
                        locals: {
                            product_id: product_id,
                            operation:operation,
                            design_id:$stateParams.id,
                            color_id:color_id,
                            is_supply:is_supply,
                            vendor_id: $scope.vendor_id,
                            product_name:product_name,
                            description:description,
                            vendor_name:vendor_name,
                            event: ev
                        },
                        onRemoving : $scope.reloadPage
                    });
                }
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