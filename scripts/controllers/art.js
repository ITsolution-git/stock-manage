app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          
                          $scope.pdf = function (){ 
                          				$('#kavi').tableExport({type:'pdf',escape:'false'}); 
                          			}

}]);