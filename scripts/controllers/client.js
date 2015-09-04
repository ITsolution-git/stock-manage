app.controller('clientAddCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log', function($scope,$http,$location,$state,$modal,AuthService,$log) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;

                          $http.get('api/public/common/type/client').success(function(Listdata) {
                                  $scope.Typelist = Listdata.data
                            });

                          $scope.SaveClient = function () {
                            var company_post = $scope.client;
                            if($scope.client.client_company!='')
                            {
                              $http.post('api/public/client/addclient',company_post).success(function(Listdata) {
                                      if(Listdata.data.success=='1')
                                       {
                                           $state.go('client.list');
                                           return false;
                                       }  
                              });
                            }
                            
                          }

}]);
app.controller('clientListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log', function($scope,$http,$location,$state,$modal,AuthService,$log) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          var delete_params = {};
                          $scope.deleteclient = function (comp_id) {
                                delete_params.id = comp_id;
                                var permission = confirm("Are you sure to delete this record ?");
                                if (permission == true) {
                                $http.post('api/public/client/DeleteClient',delete_params).success(function(result, status, headers, config) {
                                              
                                              if(result.data.success=='1')
                                              {
                                                $state.go('client.list');
                                                $("#comp_"+comp_id).remove();
                                                return false;
                                              }  
                                         });
                                      }
                                  } // DELETE COMPANY FINISH
                          $http.get('api/public/client/ListClient').success(function(Listdata) {
                                       if(Listdata.data.success=='1')
                                       {
                                          $scope.ListClient = Listdata.data
                                       }
                                  });


}]);
app.controller('clientEditCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log', function($scope,$http,$location,$state,$modal,AuthService,$log) {
                          
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          
                          $scope.allContacts = [];
                          $scope.addContacts = function(){
                            $scope.allContacts.push({contactmain:'', contactfirst_name:'' ,contactlast_name:'', contactlocation:'', contactphone:'', contactemail:''});
                          }

                          $scope.removeContacts = function(index){
                              $scope.allContacts.splice(index,1);
                          }

                          $scope.alladdress = [];
                          $scope.addAddress = function(){
                            $scope.alladdress.push({address_address:'', address_city:'' ,address_state:'', address_zip:'', address_type:''});
                          }

                          $scope.removeAddress = function(index){
                              $scope.alladdress.splice(index,1);
                          }

                          $scope.items = ['item1', 'item2', 'item3'];
                          $scope.open = function (size) {
                            var modalInstance = $modal.open({
                              templateUrl: 'views/front/client/document.html',
                              size: size,
                              resolve: {
                                items: function () {
                                  return $scope.items;
                                }
                              }
                            });

                            modalInstance.result.then(function (selectedItem) {
                              $scope.selected = selectedItem;
                            }, function () {
                              $log.info('Modal dismissed at: ' + new Date());
                            });
                          };
                          
                          $scope.selected = {
                            item: $scope.items[0]
                          };

                          $scope.ok = function () {
                            $modalInstance.close($scope.selected.item);
                          };

                          $scope.cancel = function () {
                            $modalInstance.dismiss('cancel');
                          };
}]);