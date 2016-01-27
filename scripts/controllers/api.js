
app.controller('ApiController', ['$scope','$http','$location','$state','$timeout','$modal','notifyService','$rootScope', function($scope,$http,$location,$state,$timeout,$modal,notifyService,$rootScope) {

							$("#ajax_loader").hide();
							$scope.company_id = $rootScope.company_profile.company_id;
    					    var Address_data = {};
                            Address_data.table ='api'
                            $http.post('api/public/common/GetTableRecords',Address_data).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.api_array =result.data.records;
                                } 
                                else
                                {
                                    $scope.api_array=[];
                                }
                              });


	                    $scope.items = 'item1';
                          $scope.openPopup = function (page) {
                            $scope.edit='add';
                            var modalInstance = $modal.open({
                              templateUrl: 'views/front/api/'+page,
                              scope : $scope,
                              
                            });

                            modalInstance.result.then(function (selectedItem) {
                              $scope.selected = selectedItem;

                            }, function () {
                              //$log.info('Modal dismissed at: ' + new Date());
                            });
                            $scope.ClosePopup = function (cancel)
                            {
                               modalInstance.dismiss('cancel');
                            };
                            $scope.SaveApi=function(ret_api)
                            {
                                var save_api = {};
                                save_api.data = ret_api;
                                save_api.data.company_id = $scope.company_id;
                                save_api.table ='api_detail'

                                $http.post('api/public/common/InsertRecords',save_api).success(function(result) {
                                    if(result.data.success == '1') 
                                    {
                                        GetCompanyApi();
                                    }
                                    else
                                    {
                                        console.log(result.data.message);
                                    }
                                });
                                 modalInstance.dismiss('cancel');
                            };
                        };
                         GetCompanyApi();
                          function GetCompanyApi()
                          {
                            $http.get('api/public/api/GetCompanyApi/'+$scope.company_id).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.api_records =result.data.records;
                                } 
                                else
                                {
                                    $scope.api_records=result.data;
                                }
                              });
                          }


  }]);

app.controller('SnsController', ['$scope','$http','$location','$state','$timeout','$modal','notifyService','$rootScope','$stateParams', function($scope,$http,$location,$state,$timeout,$modal,notifyService,$rootScope,$stateParams) {

						$("#ajax_loader").hide();
						$scope.company_id = $rootScope.company_profile.company_id;
                        GetSnsData($stateParams.id);
                        function GetSnsData(SnsId)
                          {
                            $http.get('api/public/api/GetSNSData/'+SnsId+'/'+$scope.company_id).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.api_records =result.data.records[0];
                                } 
                                else
                                {
                                    $scope.api_records=result.data;
                                }
                              });
                          }
                          $scope.save_SnsApi=function(params)
                          {
                          	$("#ajax_loader").show();
                          	params.company_id=  $scope.company_id;
                          		$http.post('api/public/api/save_SnsApi',params).success(function(response) {
                                       $("#ajax_loader").hide();
                                       var data = {"status": "error", "message": result.data.message}
						               notifyService.notify(data.status, data.message);
						               $state.go('api.list');
						               return false;
                                });
                          }


	  }]);