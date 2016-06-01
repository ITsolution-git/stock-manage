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
            product_data.data.name ='';
           

            product_data.table ='products'

            $http.post('api/public/common/InsertRecords',product_data).success(function(result) {
               
                var id = result.data.id;
                
                getProductDetailByIdAll(id);
                 $scope.product_id_new  = id;
                 
                
            });
                       
                 
            } else {
                 getProductDetailByIdAll(product_id);
                 $scope.product_id_new  = product_id;
                // console.log($scope.product_data);return false;

        }




         function getProductDetailByIdAll(id)
          {
             
             var product_arr = {}
              product_arr = {'id':id};
              $http.post('api/public/product/getProductDetailColorSize',product_arr).success(function(result) {
                      
                      
                      $scope.productName =result.data.product_name;
                      $scope.productId =result.data.product_id;
                      $scope.productColorSize =result.data.productColorSizeData;
                   
                      
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


        
        $scope.addcolorsize = function(product_id,color_id,size_id){
          
            if(product_id !=0 && color_id == 0 && size_id ==0) {
              var combine_array_id = {};

              combine_array_id.product_id = product_id;
              combine_array_id.color_id = color_id;
              combine_array_id.size_id = size_id;
              combine_array_id.company_id =sessionService.get('company_id');
              
              $http.post('api/public/product/addcolorsize',combine_array_id).success(function(result, status, headers, config) {
              
                  if(result.data.success == '1') {
                     getProductDetailByIdAll(product_id);
                  } 
              });
          } else if(product_id !=0 && color_id != 0 && size_id ==0) {

              var combine_array_id = {};

              combine_array_id.product_id = product_id;
              combine_array_id.color_id = color_id;
              combine_array_id.size_id = size_id;
              combine_array_id.company_id =sessionService.get('company_id');
              
              $http.post('api/public/product/addcolorsize',combine_array_id).success(function(result, status, headers, config) {
              
                  if(result.data.success == '1') {
                     getProductDetailByIdAll(product_id);
                  } 
              });
          }   

        }

       
        
        $scope.removeColorSize =  function(product_id,color_id,size_id){
          
          var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");

            if (permission == true) {

                var combine_array_id = {};
                    combine_array_id.product_id = product_id;
                    combine_array_id.color_id = color_id;
                    combine_array_id.size_id = size_id;
                    combine_array_id.company_id =sessionService.get('company_id');
                    
                    
                    $http.post('api/public/product/deleteSizeLink',combine_array_id).success(function(result, status, headers, config) {
                       
                        if(result.data.success == '1') {
                            getProductDetailByIdAll(product_id);
                        } 
                        
                    });
              }

        };
    }
})();