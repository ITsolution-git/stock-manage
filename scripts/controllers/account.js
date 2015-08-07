(function () {
    'use strict';
    angular.module('app.company', [])

    .controller('accountListCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {

                
                        var account = {};
                         $http.get('api/public/admin/account/list').success(function(result) {
                         
                        // console.log(result.data); return false;
                        return   $scope.account  = result.data; 
                        
                         
                    });
                       

}])
    .controller('accountAddCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {
                          
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

  }).call(this);
