app.controller('staffListCtrl', ['$scope','$rootScope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$rootScope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
    AuthService.AccessService('FM');
    $("#ajax_loader").show();
     var company_id = $rootScope.company_profile.company_id;

       var staff_list_data = {};
       var condition_obj = {};
       condition_obj['company_id'] =  company_id;
       staff_list_data.cond = angular.copy(condition_obj);

      $http.post('api/public/admin/staff',staff_list_data).success(function(result, status, headers, config) {
        $scope.staffs = result.data.records;

        $scope.pagination = AllConstant.pagination;
        $("#ajax_loader").hide();

        var init;

        $scope.searchKeywords = '';
        $scope.filteredStaffs = [];
        $scope.row = '';
        $scope.select = function (page) {
          var end, start;
          start = (page - 1) * $scope.numPerPage;
          end = start + $scope.numPerPage;
          return $scope.currentPageStaffs = $scope.filteredStaffs.slice(start, end);
        };
        $scope.onFilterChange = function () {
          $scope.select(1);
          $scope.currentPage = 1;
          return $scope.row = '';
        };
        $scope.onNumPerPageChange = function () {
          $scope.select(1);
          return $scope.currentPage = 1;
        };
        $scope.onOrderChange = function () {
          $scope.select(1);
          return $scope.currentPage = 1;
        };
        $scope.search = function () {
          $scope.filteredStaffs = $filter('filter')($scope.staffs, $scope.searchKeywords);
          return $scope.onFilterChange();
        };
        $scope.order = function (rowName) {
          if ($scope.row === rowName) {
              return;
          }
          $scope.row = rowName;
          $scope.filteredStaffs = $filter('orderBy')($scope.staffs, rowName);
          return $scope.onOrderChange();
        };
        $scope.numPerPageOpt = [10, 20, 50, 100];
        $scope.numPerPage = 10;
        $scope.currentPage = 1;
        $scope.currentPageStaffs = [];

        init = function () {
          $scope.search();

          return $scope.select($scope.currentPage);
        };
        return init();
    });

    $scope.delete = function (staff_id,user_id) {
        var combine_array_delete = {};
        combine_array_delete.staff_id = staff_id;
        combine_array_delete.user_id = user_id;
                         
        var permission = confirm(AllConstant.deleteMessage);
        if (permission == true) {
        $http.post('api/public/admin/staffDelete',combine_array_delete).success(function(result, status, headers, config) {
                      
                      if(result.data.success=='1')
                      {
                       
                        $state.go('staff.list');
                        $("#comp_"+staff_id).remove();
                        return false;
                      }  
                 });
              }
    }
}]);

app.controller('staffAddEditCtrl', ['$scope','$rootScope','$http','notifyService','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$rootScope,$http,notifyService,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
   
    AuthService.AccessService('FM');

    var company_id = $rootScope.company_profile.company_id;
    $http.get('api/public/common/type/timeoff').success(function(result, status, headers, config) {
        $scope.timeOffTypes = result.data.records;
    });

    var miscData = {};
      miscData.table ='misc_type'
      miscData.cond ={status:1,is_delete:1,type:'staff_type',company_id:company_id}
      miscData.notcond ={value:""}
      $http.post('api/public/common/GetTableRecords',miscData).success(function(result) {
          if(result.data.success == '1') 
          {
              $scope.types =result.data.records;
          } 
          else
          {
              $scope.types=[];
          }
        });



     $http.get('api/public/common/staffRole').success(function(result, status, headers, config) {

              $scope.staffRoles = result.data.records;
     
      });

   $scope.files = [];
                    $scope.setFiles = function (element) {
                        $scope.$apply(function ($scope) {
                            console.log('files:', element.files);

                            var uploadFile, uploadFileExt;
                            // Turn the FileList object into an Array
                            $scope.files = []
                            for (var i = 0; i < element.files.length; i++) {
                                uploadFile = element.files[i].name
                                uploadFileExt = uploadFile.split('.').pop() // Find the upload file extension for select valid images only

                                if (uploadFileExt == 'jpg' || uploadFileExt == 'png' || uploadFileExt == 'jpeg' || uploadFileExt == 'bmp' || uploadFileExt == 'gif')
                                {
                                    $scope.files.push(element.files[i])
                                }
                                else
                                {
                                  
                                    angular.element("input[type='file']").val(null)
                                    var data = {"status": "error", "message": "Please select image file to upload"}
                                    
                                    return false;
                                }
                            }
                        });
                    };

                    $scope.saveStaff = function () {
                      $scope.staff.company_id = company_id;
                        var user_data_staff = $scope.staff;
                        var user_data_users = $scope.users;
                        
                        var fd = new FormData()
                        for (var i in $scope.files) {
                            fd.append("image", $scope.files[i])
                        }

                    
                         $.each(user_data_staff, function( index, value ) {
                            fd.append(index, value)
                          });

                           $.each(user_data_users, function( index, value ) {
                            fd.append(index, value)
                          });


                       var xhr = new XMLHttpRequest()
                        xhr.onreadystatechange = function () {
                         

                            if (xhr.readyState == 4 && xhr.status == 200) {
                                var data = JSON.parse(xhr.responseText)

                                
                                if (data.success === '0') {
                                    return false;
                                }
                                else {


                                  setTimeout(function () {
                                      
                                        
                                          $scope.$apply(function ($scope) {
                                          $state.go('staff.list');
                                     });
                                    }, 10);

                                }
                            }
                        }
                        if(user_data_staff.id) {

                           xhr.open("POST","api/public/admin/staffEdit")
                           xhr.send(fd);

                        } else {
                           xhr.open("POST","api/public/admin/staffAdd")
                           xhr.send(fd);
                        }

                       
                    };

                    if($stateParams.id) {
                      getStaffNoteTimeoff();

                      $("#ajax_loader").show();

                           $http.post('api/public/admin/staffDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                      
                                    
                                     result.data.records[0]['date_start'] = $filter('dateWithFormat')(result.data.records[0]['date_start']);
                                     result.data.records[0]['birthday'] = $filter('dateWithFormat')(result.data.records[0]['birthday']);
                                     result.data.records[0]['date_end'] = $filter('dateWithFormat')(result.data.records[0]['date_end']); 
                                     
                                     $scope.staff = result.data.records[0];
                                     $scope.users = result.data.users_records[0];
                                   
                                     $("#ajax_loader").hide();


                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                         } else {
                          $("#notes-tab").hide();
                         }

                   function getStaffNoteTimeoff() {

                         $http.post('api/public/admin/staffNoteTimeoff',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                      count_no = 0;

                                     angular.forEach(result.data.allTimeOff, function(){
                                  
                                     if(result.data.allTimeOff[count_no].date_begin != '0000-00-00 00:00:00' && result.data.allTimeOff[count_no].date_begin != '') {
                                        result.data.allTimeOff[count_no].date_begin = $filter('dateWithFormat')(result.data.allTimeOff[count_no].date_begin);
                                     } else {
                                      result.data.allTimeOff[count_no].date_begin = '';
                                     }

                                      if(result.data.allTimeOff[count_no].date_end != '0000-00-00 00:00:00' && result.data.allTimeOff[count_no].date_end != '') {
                                        result.data.allTimeOff[count_no].date_end = $filter('dateWithFormat')(result.data.allTimeOff[count_no].date_end);
                                     } else {
                                      result.data.allTimeOff[count_no].date_end = '';
                                     }
                                      count_no++;
                                    });

                                     $scope.allnotes = result.data.allnotes;
                                     $scope.allTimeOff = result.data.allTimeOff;
                                     $("#ajax_loader").hide();


                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                  }
                         $scope.allnotes = [];
                          $scope.addNotes = function(){

                            var order_note_data = {};
                            order_note_data.table ='notes'
                            order_note_data.data ={all_id:$stateParams.id,type_note:"staff"}
                            order_note_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');
                            order_note_data.data.updated_date = $filter('date')(new Date(), 'yyyy-MM-dd');

                            $http.post('api/public/common/InsertRecords',order_note_data).success(function(result) {
                                 getStaffNoteTimeoff();
                            });

                            $scope.allnotes.push({note:'', points:''});
                          }

                          $scope.removeNotes = function(index,id){


                             var delete_data = {};
                                  delete_data.cond = {id :id };
                                  delete_data.table ='notes';

                                  $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) {
                                   
                                  });


                              $scope.allnotes.splice(index,1);
                          }


                          $scope.allTimeOff = [];
                          $scope.addTimeOff = function(){


                             var order_timeoff_data = {};
                            order_timeoff_data.table ='time_off'
                            order_timeoff_data.data ={staff_id:$stateParams.id}
                            order_timeoff_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');
                            order_timeoff_data.data.updated_date = $filter('date')(new Date(), 'yyyy-MM-dd');

                            $http.post('api/public/common/InsertRecords',order_timeoff_data).success(function(result) {
                                 getStaffNoteTimeoff();
                            });


                            $scope.allTimeOff.push({classification:'', date_begin:'',date_end:'', applied_hours:''});
                          }

                          $scope.removeTimeOff = function(index,id){

                             var delete_data = {};
                              delete_data.cond = {id :id };
                              delete_data.table ='time_off';

                              $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) {
                               
                              });

                              $scope.allTimeOff.splice(index,1);
                          }


                          $scope.updateStaffData = function($event,id,table_name,match_condition)
                            {
                                  var staff_main_data = {};
                                  staff_main_data.table =table_name;
                                  $scope.name_filed = $event.target.name;
                                  var obj = {};
                                  obj[$scope.name_filed] =  $event.target.value;
                                  staff_main_data.data = angular.copy(obj);

                                  var condition_obj = {};
                                  condition_obj[match_condition] =  id;
                                  staff_main_data.cond = angular.copy(condition_obj);
                                  
                                    $http.post('api/public/common/UpdateTableRecords',staff_main_data).success(function(result) {

                                        var data = {"status": "success", "message": "Data Updated Successfully."}
                                            notifyService.notify(data.status, data.message);
                                    });
                              
                            }

                              $scope.UpdateDate=function($event,table,cond,value)
                        {
                          var Array_data = {};
                          Array_data.table =table;
                          Array_data.field =$event.target.name;
                          Array_data.date = $event.target.value
                          Array_data.cond =cond
                          Array_data.value =value;

                          $http.post('api/public/common/updatedate',Array_data).success(function(result) {
                               var data = {"status": "success", "message": "Data Updated successfully"}
                               notifyService.notify(data.status, data.message); 
                            });
                        }


                       

}]);
