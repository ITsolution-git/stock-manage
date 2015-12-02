app.controller('accountListCtrl', ['$scope','$http','$location','$state','AuthService','sessionService', function($scope,$http,$location,$state,AuthService,sessionService) {

                                AuthService.AccessService('CA');
                                $scope.parent_id = $scope.app.user_id;
                                var delete_params = {};
                                $scope.deletecompany = function (comp_id) {
                                delete_params.id = comp_id;
                                var permission = confirm("Are you sure to delete this record ?");
                                if (permission == true) {
                                $http.post('api/public/admin/account/delete',delete_params).success(function(result, status, headers, config) {
                                              
                                              if(result.data.success=='1')
                                              {
                                                $state.go('account.list');
                                                $("#comp_"+comp_id).remove();
                                                return false;
                                              }  
                                         });
                                      }
                                  } // DELETE COMPANY FINISH
                            var account = {};
                            
                            $http.get('api/public/admin/account/list/'+$scope.parent_id).success(function(result) {
                                 $scope.account  = result.data; 
                             });
                      
                       

}]);
app.controller('accountAddCtrl', ['$scope','$http','$location','$state','AuthService','sessionService', function($scope,$http,$location,$state,AuthService,sessionService) {
                          
                          AuthService.AccessService('CA');
                         $scope.parent_id = $scope.app.user_id;
                          $scope.CurrentController=$state.current.controller;
                          // GET ADMIN ROLE LIST
                          $http.get('api/public/common/getAdminRoles').success(function(Listdata) {

                                  $scope.rolelist = Listdata.data
                                 // console.log(Listdata); 
                            });
                       
                          // COMPANY ADD TIME CALL
                         $scope.addcompany = function () {
                            $scope.account.parent_id = $scope.parent_id;
                            $http.post('api/public/admin/account/add',$scope.account).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='1')
                                          {
                                            $state.go('account.list');
                                           }
                                     });
                                   } 
                              $scope.checkEmail = function (kem) {

                               var mail = $('#comp_email').val();
                               $http.get('api/public/common/checkemail/'+mail).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='2')
                                          {
                                            $("#err_email").hide();
                                            return false;
                                          }
                                          else
                                          {
                                            $("#err_email").show();
                                            return false;
                                          }
                                     });
                              }      

}]);
app.controller('accountEditCtrl', ['$scope','$http','$stateParams','$location','$state','AuthService','sessionService', function($scope,$http,$stateParams,$location,$state,AuthService,sessionService) {
                          
                            AuthService.checksession();
                            AuthService.AccessService('CA');
                            $scope.parent_id = $scope.app.user_id;
                            $scope.CurrentController=$state.current.controller;

                            // GET ADMIN ROLE LIST
                            $http.get('api/public/common/getAdminRoles').success(function(Listdata) {

                                    $scope.rolelist = Listdata.data
                                   // console.log(Listdata); 
                              });
                              
                              // GET ADMIN ROLE LIST
                              $http.get('api/public/admin/account/edit/'+$stateParams.id+'/'+$scope.parent_id).success(function(Listdata) {

                                      $scope.account = Listdata.data.records[0];
                                      $scope.account.password = 'testcodal';
                                      $scope.confirm_password = 'testcodal';
                                      
                                     // console.log(Listdata); 
                              });
                          
                          // COMPANY EDIT TIME CALL
                          $scope.editcompany = function () {

                            $scope.account.id= $stateParams.id;
                            $scope.account.parent_id=$scope.parent_id;
                            $http.post('api/public/admin/account/save',$scope.account).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='1')
                                          {
                                            $state.go('account.list');
                                            return false;
                                          }
                                     });
                                   } 
                           



}]);
