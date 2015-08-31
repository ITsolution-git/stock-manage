app.controller('logoutCtrl', ['$scope','$http','$location','$state','sessionService', function($scope,$http,$location,$state,sessionService) {



    $http.get('api/public/auth/logout').success(function(result, status, headers, config) {
        
              if(result.data.success == '1') {
					   sessionService.remove('username');
		               sessionService.remove('password');
		               sessionService.remove('useremail');
		               sessionService.remove('role_title');
		               sessionService.remove('role_slug');
		               sessionService.remove('login_id');
		               sessionService.remove('name');   
		              // sessionService.destroy();           	
		               $state.go('access.signin');
		               return false;
              } 
        });

}]);
