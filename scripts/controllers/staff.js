app.controller('StaffCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {

                         $http.get('api/public/admin/staff').success(function(result, status, headers, config) {

                                  $scope.staffs = result.data.records;
                         
                          });

                         $http.get('api/public/admin/type').success(function(result, status, headers, config) {

                                  $scope.types = result.data.records;
                         
                          });


                         


                         $scope.saveStaff = function(staff) {
                         
                          
                         $http.post('api/public/admin/staffAdd',staff).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                    $location.url('/staff/list');
                                   

                             } 
                         
                          });

                         };


                         $scope.editStaff = function(staffId) {
                         
                          
                         $http.post('api/public/admin/staffDetail',staffId).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                     $location.url('/staff/add');
                                     $scope.staff = result.data.records;
                                     
                                     

                             }  else {
                              $location.url('/app/dashboard');
                             }
                         
                          });

                         };



                         $scope.openStaff = function() {
                         
                          $location.url('/staff/add');
                         
                         };

                          $scope.openList = function() {
                         
                          $location.url('/staff/list');
                          return false;
                         
                         };
                    

}]);
