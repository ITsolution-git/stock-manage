(function ()
{
    'use strict';

    angular
        .module('app.customProduct')
        .controller('CustomProductDialogController', CustomProductDialogController);
/** @ngInject */
    function CustomProductDialogController(product_id,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {

      
        if(product_id == 0) {

            var product_data = {};
            var productData = {};
            productData.company_id =sessionService.get('company_id');
           
            product_data.data = productData;
           
          //  product_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');
            product_data.data.vendor_id =0;
            product_data.data.name ='Default Product';
           

            product_data.table ='products'

            $http.post('api/public/common/InsertRecords',product_data).success(function(result) {
               
                var id = result.data.id;
                
                getProductDetailByIdAll(id);
                 $scope.product_id_new  = id;
                 
                
            });
                       

           
                 
            } else {
                 getProductDetailByIdAll(product_id);
                // getProductDetailColorSize(value);

                 $scope.product_id_new  = product_id;
                // console.log($scope.product_data);return false;

        }




         function getProductDetailByIdAll(id)
          {
             
             var product_arr = {}
              product_arr = {'id':id};
              $http.post('api/public/order/productDetail',product_arr).success(function(result) {
                      $scope.allProductColorSize =result.data;
                      $scope.productDetail =result.data.product_data[0];
                   
                       if($scope.allProductColorSize.colorData.length == '0'){
                        $scope.allProductColorSize.colorData = [];
                  }
               });
              }




    

        $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.updateProduct = function(column_name,id,value,table_name,match_condition)
        {
            var position_main_data = {};
            position_main_data.table =table_name;
            $scope.name_filed = column_name;
          
            var obj = {};
            obj[$scope.name_filed] =  value;
            position_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[match_condition] =  id;
            position_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',position_main_data).success(function(result) {
            });
        }
        $scope.rows = ['Row 1'];
        $scope.counter = 2;
        $scope.addAttribute = function(){
          $scope.rows.push('Row ' + $scope.counter);
          $scope.counter++;
        }
        $scope.removeAttribute = function (rowContent) {
          var index = $scope.rows.indexOf(rowContent);
          $scope.rows.splice(index, 1);
          $scope.counter--;
        };
        $scope.sizeElement = [{}];
        $scope.addSize =  function(){
          $(".size-attribute.display-none").css("display", "block");
          $scope.sizeElement.push({});
        };
        $scope.removeSize =  function(size){
          var sizeindex = $scope.sizeElement.indexOf(size);
          $scope.sizeElement.splice(sizeindex, 1);
        };
    }
})();