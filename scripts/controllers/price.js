
app.controller('priceListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  
  $http.get('api/public/admin/price').success(function(result, status, headers, config) {

                                  $scope.price = result.data.records;
                         
                          });

                         $scope.delete = function (price_id) {
                          
                            var permission = confirm(deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/priceDelete',price_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('setting.price');
                                            $("#price_"+price_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } 

}]);

app.controller('priceAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage','$filter', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage,$filter) {
   
                    if($stateParams.id) {

                           $http.post('api/public/admin/priceDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                      
                                     $scope.price = result.data.records[0];
                                  

                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                         }


                          $scope.savePrice = function(price) {
                         
                         if(price.id) {
                               
                          $http.post('api/public/admin/priceEdit',price).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {


                                   $state.go('setting.price');
                                    return false;
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                           $http.post('api/public/admin/priceAdd',price).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                   $state.go('setting.price');
                                    return false;
                                   
                             } 
                         
                          });

                         }
                         

                         };

                       

}]);