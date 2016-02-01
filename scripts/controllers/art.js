app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService',function($scope,$http,$state,$stateParams,$rootScope,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                         
                          $http.get('api/public/art/listing/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.Art_array = RetArray.data;
                              	}
                            });

}]);
app.controller('ArtJobCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService',function($scope,$http,$state,$stateParams,$rootScope,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.art_id = $stateParams.id;
                          $http.get('api/public/art/Art_detail/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.Artjob_position = RetArray.data;
                              	}
                            });

}]);

