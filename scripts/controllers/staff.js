app.controller('StaffCtrl', ['$scope','$http','$location','$state', function($scope,$http,$location,$state) {

                         $http.get('api/public/admin/staff').success(function(result, status, headers, config) {

                                  $scope.staffs = result.data.records;
                         
                    });


                         $scope.saveStaff = function(staff) {
                         
                          
                         $http.post('api/public/admin/staffAdd',staff).success(function(result, status, headers, config) {
        
                          if(result.data.success == '1') {

                                  $location.url('/staff/list');
                                  return false;

                          } 
                         
                    });

                         };
                    

}]);
