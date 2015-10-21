app.controller('PurchaseListCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $type = $stateParams.id;
                          $http.get('api/public/purchase/ListPurchase/'+$type ).success(function(Listdata) 
                          		  {
                                          $scope.ListPurchase = Listdata.data;
                                  });
}]);
app.controller('PurchasePOCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                          
                           $order_id = $stateParams.id;
                           $http.get('api/public/purchase/GetPodata/'+$order_id ).success(function(PoData) 
                          		  {
                                          $scope.ArrPo = PoData.data.records[0];
                                  });

}]);
app.controller('PurchaseSGCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');



}]);
app.controller('PurchaseCPCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

}]);
app.controller('PurchaseCECtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

}]);