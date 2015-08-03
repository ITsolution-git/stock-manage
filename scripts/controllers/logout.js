app.controller('logoutCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {



    $http.get('api/public/auth/logout').success(function(result, status, headers, config) {
        
              if(result.data.success == '1') {

                $location.url('/access/signin');
                return false;

              } 
        });

}]);
