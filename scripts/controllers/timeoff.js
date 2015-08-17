


app.controller('timeoffCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  


                         if($stateParams.staff_id) {

                           $http.post('api/public/admin/staff/timeoff',$stateParams.staff_id).success(function(result, status, headers, config) {
        
                            $scope.timeoff = result.data.records;
                         
                          });

                         }

                         $http.get('api/public/common/type/timeoff').success(function(result, status, headers, config) {

                                  $scope.types = result.data.records;
                         
                          });


                         if($stateParams.timeoff_id && $stateParams.staff_id) {

                          var combine_array_id = {};
                          combine_array_id.staff_id = $stateParams.staff_id;
                          combine_array_id.timeoff_id = $stateParams.timeoff_id;

                         

                           $http.post('api/public/admin/staff/timeoffDetail',combine_array_id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.timeoffDetail = result.data.records[0];
                                    
                             }  else {


                              $location.url('/app/dashboard');
                             }
                         
                          });

                         }


                          $scope.openStaff = function() {
                         
                          $location.url('/staff/list');
                          return false;
                         
                         };

                         $scope.delete = function (timeoff_id,staff_id) {
                          
                         
                           var combine_array_delete = {};
                          combine_array_delete.staff_id = staff_id;
                          combine_array_delete.timeoff_id = timeoff_id;

                         
                            var permission = confirm(deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/staff/timeoffDelete',combine_array_delete).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                            $state.go('staff.timeoff',{staff_id: $stateParams.staff_id});

                                            $("#timeoff_"+timeoff_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } // DELETE Note FINISH


                               $scope.openTimeoff = function() {

                                $state.go('staff.timeoffAdd',{staff_id: $stateParams.staff_id});
                                return false;
                               
                               };

                               $scope.openTimeoffList = function() {
                                 
                                 $state.go('staff.timeoff',{staff_id: $stateParams.staff_id});
                                 return false;
                               
                               };



                          $scope.saveTimeoff = function(timeoffDetail) {
                         
                         
                         timeoffDetail.staff_id = $stateParams.staff_id;


                         if($stateParams.staff_id && timeoffDetail.id) {
                               
                          $http.post('api/public/admin/staff/timeoffEdit',timeoffDetail).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {


                                    $state.go('staff.timeoff',{staff_id: $stateParams.staff_id});
                                    return false;
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                           $http.post('api/public/admin/staff/timeoffAdd',timeoffDetail).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                   $state.go('staff.timeoff',{staff_id: $stateParams.staff_id});
                                    return false;
                                   
                             } 
                         
                          });

                         }
                         

                         };



                        
                    

}]);
