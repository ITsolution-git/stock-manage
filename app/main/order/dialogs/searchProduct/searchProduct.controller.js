(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductController', SearchProductController);

    /** @ngInject */
    function SearchProductController(data,$mdDialog,$document,$scope,$http)
    {
        $scope.productSearch = data.productSearch;
        $scope.vendor_id = data.vendor_id;
        $scope.toggle = true;
        $scope.color = true;
        $scope.size = true;
        
        /*$scope.getProducts = function()
        {
            var vendor_arr = {'vendor_id' : $scope.vendor_id, 'search' : $scope.productSearch};
            $scope.allVendors = data['vendors'];
            $http.post('api/public/product/getProductByVendor',vendor_arr).success(function(result, status, headers, config) {
                $scope.products = result.data.records;
            });
        }*/

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
          'category_id':''
        };

        $scope.filterBy.vendor_id = $scope.vendor_id;
        $scope.filterBy.search = $scope.productSearch;

        $scope.filterProducts = function(type,value){
            $scope.filterBy.category_id = '';
            $scope.filterBy.color_id = '';
            $scope.filterBy.size_id = '';

            if(type == 'category_id')
            {
                $scope.filterBy.category_id = angular.copy(value);
            }
            if(type == 'color_id')
            {
                $scope.filterBy.color_id = angular.copy(value);
            }
            if(type == 'size_id')
            {
                $scope.filterBy.size_id = angular.copy(value);
            }
            $scope.filterBy.vendor_id = $scope.vendor_id;
            $scope.filterBy.search = $scope.productSearch;
        }
        $scope.filterByCategory = function(category_id) {
            $scope.filterBy.category_id = category_id;
        }
        $scope.filterByColor = function(color_id) {
            $scope.filterBy.color_id = color_id;
        }
        $scope.filterBySize = function(size_id) {
            $scope.filterBy.size_id = size_id;
        }

        $scope.getResource = function (params, paramsObj, search) {
            $scope.params = params;
            $scope.paramsObj = paramsObj;
 
            var orderData = {};

              orderData.cond ={params:$scope.params};
              //var vendor_arr = {'vendor_id' : $scope.vendor_id, 'search' : $scope.productSearch};

              return $http.post('api/public/product/getProductByVendor',orderData).success(function(response) {
                var header = response.header;
                $scope.category_filter = response.category_filter;
                $scope.color_filter = response.color_filter;
                $scope.size_filter = response.size_filter;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }

        $scope.openSearchProductViewDialog = function(ev,product_id,product_image,description,vendor_name)
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
                    event: ev
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