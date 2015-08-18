
app.controller('vendorCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  

                         $http.get('api/public/admin/vendor').success(function(result, status, headers, config) {

                                  $scope.vendors = result.data.records;
                         
                          });

                         $scope.delete = function (vendor_id) {
                         
                            var permission = confirm(deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/vendorDelete',vendor_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('vendor.list');
                                            $("#vendor_"+vendor_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              }

                          $scope.openVendor = function() {
                          $state.go('vendor.add');
                          };

}]);
