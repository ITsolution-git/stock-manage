app.controller('loginCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {



   $scope.dosignin = function () {
                        var user_data = $scope.user;
                        
                         $http.post('api/public/admin/login',user_data).success(function(result, status, headers, config) {
        
                          if(result.data.success == '0') {

                                  $location.url('/access/signin');
                                  return false;

                                } else {

                                   $location.url('/app/dashboard');
                                   return false;

                                }
                         
                    });
                    }

}]);
