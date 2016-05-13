(function ()
{
    'use strict';
    angular
            .module('app.order')
            .controller('SearchProductController', SearchProductController);


    app.animation('.toggle:not(.ng-animate)', function($window) {
       return {
        leave: function(element, done) {
          $window.setTimeout(done, 2000);
        }
      };
    });
            
            $scope.toggle = false ;
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

        $scope.filterProducts = function(){
            $scope.filterBy.category_id = '';
            $scope.filterBy.vendor_id = $scope.vendor_id;
            $scope.filterBy.search = $scope.productSearch;
        }
        $scope.filterByCategory = function(category_id) {
            $scope.filterBy.category_id = category_id;
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

        //$scope.getProducts();
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
        //$scope.openSearchProductViewDialog = openSearchProductViewDialog;
        
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