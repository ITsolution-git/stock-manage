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
app.controller('ArtJobCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService','notifyService','$modal',function($scope,$http,$state,$stateParams,$rootScope,AuthService,notifyService,$modal) {
						  $("#ajax_loader").hide();
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.art_id = $stateParams.id;

                          Get_artDetail();
                          function Get_artDetail()
                          {
                          	$("#ajax_loader").show();
                          	$http.get('api/public/art/Art_detail/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$("#ajax_loader").hide();
                          			$scope.art_position = RetArray.data.records.art_position;
                          			$scope.art_orderline = RetArray.data.records.art_orderline;
                          			$scope.artjobscreen_list = RetArray.data.records.artjobscreen_list;
                          			$scope.graphic_size = RetArray.data.records.graphic_size;
                          			//console.log($scope.art_orderline.line_array);
                              	}
                              	if(RetArray.data.success=='2')
                          		{
                          			$("#ajax_loader").hide();
                          			var data = {"status": "success", "message": RetArray.data.message}
                                    notifyService.notify(data.status, data.message);
                                    window.location.reload();
                          		}
                            });
						}


                           $scope.getNumber = function(num) {
							    return new Array(num);   
							}

						  $scope.UpdateField_field = function($event,id,table){
                          		  var Receive_data = {};
                          		  Receive_data.table =table;
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
                          }
                          $scope.UpdateField_detail = function(orderline_id){

                          	$http.get('api/public/art/artworkproof_data/'+orderline_id+'/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.workproof_array = RetArray.data.records;
                          			$scope.Artworkproof();
		                            
		                        }
		                        else
		                        {
		                        	$("#ajax_loader").hide();
                          			var data = {"status": "info", "message": RetArray.data.message}
                                    notifyService.notify(data.status, data.message);
		                        }
                     	    });
                        }
                        $scope.Artworkproof= function () {
			                            var modalInstanceEdit = $modal.open({
			                              templateUrl: 'views/front/art/artjob/artwork_proof.html',
			                              scope : $scope,
			                             // controller:'ArtJobCtrl'
			                            });
			                            modalInstanceEdit.result.then(function (selectedItem) {
			                              $scope.selected = selectedItem;
			                            }, function () {
			                            });
			                            $scope.ClosePopup = function (cancel)
			                            {
			                               modalInstanceEdit.dismiss('cancel');
			                            };
			                            $scope.SaveArtWorkProof=function()
			                            {
			                            	alert('KPJ');
			                            }
		                            };

		                $scope.create_screen = function(num) {

		                	 var Address_data = {};
                                Address_data.data = {art_id:$scope.art_id};
                                Address_data.table ='artjob_screensets'
                                
                                $http.post('api/public/common/InsertRecords',Address_data).success(function(result) {
                                    if(result.data.success == '1') 
                                    {
                                       Get_artDetail();
                                    }
                                    else
                                    {
                                        $("#ajax_loader").hide();
	                          			var data = {"status": "error", "message": "Something wrong please try again."}
	                                    notifyService.notify(data.status, data.message);
                                    }
                                });

							    return new Array(num);   
							}

                       

}]);

