


app.controller('productListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  
  $http.get('api/public/admin/product').success(function(result, status, headers, config) {

                                  $scope.products = result.data.records;
                         
                          });

                         $scope.delete = function (product_id) {
                          
                            var permission = confirm(deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/productDelete',product_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('product.list');
                                            $("#product_"+product_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } 

}]);


app.controller('productAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
   
   
                        if($stateParams.id) {

                           $http.post('api/public/admin/productDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.product = result.data.records[0];
                                   
                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                         }

}]);


