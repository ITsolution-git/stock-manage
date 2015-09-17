


app.controller('noteListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant) {
  

if($stateParams.staff_id) {

                           $http.post('api/public/admin/staff/note',$stateParams.staff_id).success(function(result, status, headers, config) {
        
                            $scope.notes = result.data.records;
                         
                          });

                         }

                         $scope.delete = function (note_id,staff_id) {
                         
                           var combine_array_delete = {};
                          combine_array_delete.staff_id = staff_id;
                          combine_array_delete.note_id = note_id;

                         
                            var permission = confirm(AllConstant.deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/staff/noteDelete',combine_array_delete).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                            $state.go('staff.note',{staff_id: $stateParams.staff_id});

                                            $("#note_"+note_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } // DELETE Note FINISH


                               $scope.openNote = function() {

                                $state.go('staff.noteAdd',{staff_id: $stateParams.staff_id});
                                return false;
                               
                               };


}]);


app.controller('noteAddeditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant) {
  
 $scope.openNoteList = function() {
                                 
                                 $state.go('staff.note',{staff_id: $stateParams.staff_id});
                                 return false;
                               
                               };

 if($stateParams.note_id && $stateParams.staff_id) {

                          var combine_array_id = {};
                          combine_array_id.staff_id = $stateParams.staff_id;
                          combine_array_id.note_id = $stateParams.note_id;

                         

                           $http.post('api/public/admin/staff/noteDetail',combine_array_id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.notesDetail = result.data.records[0];
                                    
                             }  else {


                              $state.go('app.dashboard');
                             }
                         
                          });

                         }




                          $scope.saveNote = function(notesDetail) {
                         
                         notesDetail.type_note = 'staff';
                         notesDetail.all_id = $stateParams.staff_id;


                         if($stateParams.staff_id && notesDetail.id) {
                               
                          $http.post('api/public/admin/staff/noteEdit',notesDetail).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {


                                    $state.go('staff.note',{staff_id: $stateParams.staff_id});
                                    return false;
                                   

                             } 
                         
                          });
                          
                         } else {
                          
                           $http.post('api/public/admin/staff/noteAdd',notesDetail).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {

                                   $state.go('staff.note',{staff_id: $stateParams.staff_id});
                                    return false;
                                   
                             } 
                         
                          });

                         }
                         

                         };


}]);