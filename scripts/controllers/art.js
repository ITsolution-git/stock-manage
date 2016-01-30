app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService',function($scope,$http,$state,$stateParams,$rootScope,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                         
                          $http.get('api/public/art/listing/'+$scope.company_id).success(function(Listdata) {
	                          	if(Listdata.data.success=='1')
                          		{
                          			$scope.Art_array = Listdata.data;
                              	}
                            });

}]);