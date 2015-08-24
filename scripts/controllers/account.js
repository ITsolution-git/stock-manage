(function () {
    'use strict';
    angular.module('app.company', [])

  .controller('accountListCtrl', ['$scope','$http','$location','$state','AuthService', function($scope,$http,$location,$state,AuthService) {

                                AuthService.AccessService('SA');
                            
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
                            
                            $http.get('api/public/admin/account/list').success(function(result) {
                                 $scope.account  = result.data; 
                             });
                      
                       

}])
    .controller('accountAddCtrl', ['$scope','$http','$location','$state','AuthService', function($scope,$http,$location,$state,AuthService) {
                          
                          AuthService.AccessService('SA');
                          $scope.CurrentController=$state.current.controller;
                          // GET ADMIN ROLE LIST
                          $http.get('api/public/common/getAdminRoles').success(function(Listdata) {

                                  $scope.rolelist = Listdata.data
                                 // console.log(Listdata); 
                            });
                       
                          // COMPANY ADD TIME CALL
                         $scope.addcompany = function () {
                            var company_post = $scope.account;
                            $http.post('api/public/admin/account/add',company_post).success(function(result, status, headers, config) {
        
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

}])
.controller('accountEditCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService', function($scope,$http,$location,$state,$stateParams,AuthService) {
                          
                            AuthService.checksession();
                            AuthService.AccessService('SA');
                            $scope.CurrentController=$state.current.controller;

                            // GET ADMIN ROLE LIST
                            $http.get('api/public/common/getAdminRoles').success(function(Listdata) {

                                    $scope.rolelist = Listdata.data
                                   // console.log(Listdata); 
                              });
                              
                              // GET ADMIN ROLE LIST
                              $http.get('api/public/admin/account/edit/'+$stateParams.id).success(function(Listdata) {

                                      $scope.account = Listdata.data.records[0];
                                      $scope.account.password = 'testcodal';
                                      $scope.confirm_password = 'testcodal';
                                      
                                     // console.log(Listdata); 
                              });
                          
                          // COMPANY EDIT TIME CALL
                          $scope.editcompany = function () {

                            $scope.account.id= $stateParams.id;
                            var company_post = $scope.account;
                            $http.post('api/public/admin/account/save',company_post).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='1')
                                          {
                                            $state.go('account.list');
                                            return false;
                                          }
                                     });
                                   } 
                           



}])

  }).call(this);
