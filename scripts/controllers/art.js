app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService',function($scope,$http,$state,$stateParams,$rootScope,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $("#ajax_loader").show();
                          $http.get('api/public/art/listing/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.Art_array = RetArray.data;
                          			 $("#ajax_loader").hide();
                              	}
                            });

}]);
app.controller('ArtJobCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService','notifyService',function($scope,$http,$state,$stateParams,$rootScope,AuthService,notifyService) {
						  $("#ajax_loader").hide();
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.art_id = $stateParams.id;
                          $("#ajax_loader").show();
                          $http.get('api/public/art/Art_detail/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$("#ajax_loader").hide();
                          			$scope.art_position = RetArray.data.records.art_position;
                          			$scope.art_orderline = RetArray.data.records.art_orderline;
                          			//console.log($scope.art_orderline.line_array);
                              	}
                            });
                           $scope.getNumber = function(num) {
							    return new Array(num);   
							}

						  $scope.UpdateField_detail = function($event,id){
                          		  var Receive_data = {};
                          		  Receive_data.table ='art';
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ art_id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
                          }

}]);

