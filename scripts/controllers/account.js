(function () {
    'use strict';
    angular.module('app.company', [])

    .controller('accountListCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {

                            
                            var delete_params = {};
                            $scope.deletecompany = function (comp_id) {
                            delete_params.id = comp_id;
                            var permission = confirm("Are you sure to delete this record ?");
                            if (permission == true) {
                            $http.post('api/public/admin/account/delete',delete_params).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                            $location.url('/account/list');
                                            $("#comp_"+comp_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                                   } 
                        var account = {};
                        
                        $http.get('api/public/admin/account/list').success(function(result) {
                            return   $scope.account  = result.data; 
                         });
                       

}])
    .controller('accountAddCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {
                          
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
                                            $location.url('/account/list');
                                            return false;
                                          }
                                     });
                                   } 

}])
.controller('accountEditCtrl', ['$scope','$http','$location','$state','$stateParams', function($scope,$http,$location,$state,$stateParams) {
                          
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
                                            $location.url('/account/list');
                                            return false;
                                          }
                                     });
                                   } 
                           



}])

  }).call(this);
