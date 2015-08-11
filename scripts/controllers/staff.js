app.controller('StaffCtrl', ['$scope','$http','$location','$state','$stateParams', function($scope,$http,$location,$state,$stateParams) {
  

                         $http.get('api/public/admin/staff').success(function(result, status, headers, config) {

                                  $scope.staffs = result.data.records;
                         
                          });

                         $http.get('api/public/common/type/staff').success(function(result, status, headers, config) {

                                  $scope.types = result.data.records;
                         
                          });

                         $http.get('api/public/common/staffRole').success(function(result, status, headers, config) {

                                  $scope.staffRoles = result.data.records;
                         
                          });


                         $scope.saveStaff = function(staff,users) {
                         
                         
                          
                          var combine_array = {};
                          combine_array.staff = staff;
                          combine_array.users = users;

                         if(staff.id) {

                          $http.post('api/public/admin/staffEdit',combine_array).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                    $location.url('/staff/list');
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                           $http.post('api/public/admin/staffAdd',combine_array).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                    $location.url('/staff/list');
                                   

                             } 
                         
                          });

                         }
                         

                         };


                         $scope.delete = function (staff_id,user_id) {
                          
                           var combine_array_delete = {};
                          combine_array_delete.staff_id = staff_id;
                          combine_array_delete.user_id = user_id;

                         
                            var permission = confirm("Are you sure to delete this record ?");
                            if (permission == true) {
                            $http.post('api/public/admin/staffDelete',combine_array_delete).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                            $location.url('/staff/list');
                                            $("#comp_"+staff_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } // DELETE STAFF FINISH


                         if($stateParams.id) {

                           $http.post('api/public/admin/staffDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.staff = result.data.records[0];
                                     $scope.users = result.data.users_records[0];
                                     

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
