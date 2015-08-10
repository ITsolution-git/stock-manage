app.controller('StaffCtrl', ['$scope','$http','$location','$state','$stateParams', function($scope,$http,$location,$state,$stateParams) {
  

                         $http.get('api/public/admin/staff').success(function(result, status, headers, config) {

                                  $scope.staffs = result.data.records;
                         
                          });

                         $http.get('api/public/admin/type').success(function(result, status, headers, config) {

                                  $scope.types = result.data.records;
                         
                          });


                         $scope.saveStaff = function(staff) {
                         
                          
                         
                         if(staff.id) {

                          $http.post('api/public/admin/StaffEdit',staff).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                    $location.url('/staff/list');
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                           $http.post('api/public/admin/staffAdd',staff).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                    $location.url('/staff/list');
                                   

                             } 
                         
                          });

                         }
                         

                         };


                         if($stateParams.id) {

                           $http.post('api/public/admin/staffDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.staff = result.data.records[0];
                                     

                             }  else {
                              $location.url('/app/dashboard');
                             }
                         
                          });

                         }



                         $scope.openStaff = function() {
                         
                          $location.url('/staff/add');
                         
                         };

                          $scope.openList = function() {
                         
                          $location.url('/staff/list');
                          return false;
                         
                         };
                    

}]);
