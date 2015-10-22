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
                           
                           GetPodata($order_id );
                           function GetPodata(order_id)
                           {
                           		$http.get('api/public/purchase/GetPodata/'+$order_id ).success(function(PoData) 
                          		  {
                                          $scope.ArrPo = PoData.data.records.po[0];
                                          $scope.ArrPoLine = PoData.data.records.poline;
                                          $scope.ArrUnassign = PoData.data.records.unassign_order;
                                          console.log(PoData.data.records);
                                  });
                       		}
                           $scope.ChangeOrderStatus = function(value){
                           	console.log(value);
                            	  $http.get('api/public/purchase/ChangeOrderStatus/'+$order_id+'/'+value ).success(function(PoData) 
                          		  {
                                       GetPodata($order_id ); 
                                  });
                          }
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