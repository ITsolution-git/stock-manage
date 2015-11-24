app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                          //$("#ajax_loader").show();
                          $scope.CurrentController=$state.current.controller;

}]);