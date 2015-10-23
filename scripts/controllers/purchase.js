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
                          
                           $scope.order_id = $stateParams.id;
                           var order_id = $stateParams.id;
                           
                           GetPodata(order_id );
                           function GetPodata(order_id)
                           {
                           		$http.get('api/public/purchase/GetPodata/'+order_id ).success(function(PoData) 
                          		  {
                                          $scope.ArrPo = PoData.data.records.po[0];
                                          $scope.ArrPoLine = PoData.data.records.poline;
                                          $scope.ArrUnassign = PoData.data.records.unassign_order;
                                          $scope.ordered = PoData.data.records.order_total[0].ordered;
                                          $scope.received = PoData.data.records.received_total[0].received;
                                          $scope.received_line = PoData.data.records.received_line;
                                         // console.log(PoData.data.records);
                                  });
                       		}
                           $scope.ChangeOrderStatus = function(order_id,value){
                           //	console.log(value);
                            	  $http.get('api/public/purchase/ChangeOrderStatus/'+order_id+'/'+value ).success(function(PoData) 
                          		  {
                                       GetPodata(order_id ); 
                                  });
                          }
                          $scope.EditOrderLine = function(Poline_data){

                            	  $http.post('api/public/purchase/EditOrderLine',Poline_data ).success(function(PoData) 
                          		  {
                                       GetPodata(order_id ); 
                                  });
                          }
                          $scope.shipttoblock = function($event,id){

                          		 var Arrshift = {};
	                              
	                              Arrshift.data = $event.target.value;
	                              Arrshift.order_id = id;
								//console.log(Arrshift); return false;

                          		$http.post('api/public/purchase/Update_shiftlock',Arrshift ).success(function() 
                          		  {
                                       GetPodata(order_id ); 
                                  });
                          }
                          $scope.Receive_order = function(data){

                          		 $http.post('api/public/purchase/Receive_order',data ).success(function() 
                          		  {
                                       GetPodata(order_id ); 
                                  });
                          }

                          $scope.RemoveReceiveLine = function(id){

                          		$http.get('api/public/purchase/RemoveReceiveLine/'+id).success(function() 
                          		  {
                                       GetPodata(order_id ); 
                                  });
                          }
}]);
app.controller('PurchaseSGCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

                           $scope.order_id = $stateParams.id;

}]);
app.controller('PurchaseCPCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);
app.controller('PurchaseCECtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);