
app.controller('vendorListCtrl', ['$scope','$rootScope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$rootScope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
   AuthService.AccessService('FM');
                         $("#ajax_loader").show();
                         var company_id = $rootScope.company_profile.company_id;

                         var vendor_list_data = {};
                         var condition_obj = {};
                         condition_obj['company_id'] =  company_id;
                         vendor_list_data.cond = angular.copy(condition_obj);

                        
                          $http.post('api/public/admin/vendor',vendor_list_data).success(function(result, status, headers, config) {

                                  $scope.vendors = result.data.records;
                                  $scope.pagination = AllConstant.pagination;
                                  $("#ajax_loader").hide();

                                  var init;

                                  $scope.searchKeywords = '';
                                  $scope.filteredVendors = [];
                                  $scope.row = '';
                                  $scope.select = function (page) {
                                      var end, start;
                                      start = (page - 1) * $scope.numPerPage;
                                      end = start + $scope.numPerPage;
                                      return $scope.currentPageVendors = $scope.filteredVendors.slice(start, end);
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
                                      $scope.filteredVendors = $filter('filter')($scope.vendors, $scope.searchKeywords);
                                      return $scope.onFilterChange();
                                  };
                                  $scope.order = function (rowName) {
                                      if ($scope.row === rowName) {
                                          return;
                                      }
                                      $scope.row = rowName;
                                      $scope.filteredVendors = $filter('orderBy')($scope.vendors, rowName);
                                      return $scope.onOrderChange();
                                  };
                                  $scope.numPerPageOpt = [3, 5, 10, 20];
                                  $scope.numPerPage = $scope.numPerPageOpt[2];
                                  $scope.currentPage = 1;
                                  $scope.currentPageVendors = [];

                                  init = function () {
                                      $scope.search();

                                      return $scope.select($scope.currentPage);
                                  };
                                  return init();
                         
                          });

                         $scope.delete = function (vendor_id) {
                         
                            var permission = confirm(AllConstant.deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/vendorDelete',vendor_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('vendor.list');
                                            $("#vendor_"+vendor_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              }

}]);

app.controller('vendorAddEditCtrl', ['$scope','$rootScope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant', function($scope,$rootScope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant) {
   AuthService.AccessService('FM');
   var company_id = $rootScope.company_profile.company_id;
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


                    $scope.saveVendor = function () {

                        $scope.vendor.company_id = company_id;
                        var vendor_data = $scope.vendor;


                        var fd = new FormData()
                        for (var i in $scope.files) {

                            fd.append("image", $scope.files[i])
                        }

                       fd.append("vendor_contact_data_all", angular.toJson($scope.allContacts))

                      
                         $.each(vendor_data, function( index, value ) {
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
                                          $state.go('vendor.list');
                                     });
                                    }, 10);

                                }
                            }
                        }
                        if(vendor_data.id) {

                           xhr.open("POST","api/public/admin/vendorEdit")
                           xhr.send(fd);

                        } else {
                           xhr.open("POST","api/public/admin/vendorAdd")
                           xhr.send(fd);
                        }

                       
                    };

                         

                          if($stateParams.id) {
                            $("#ajax_loader").show();
                           $http.post('api/public/admin/vendorDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       

                                     $scope.vendor = result.data.records[0];
                                     $scope.allContacts = result.data.allContacts;
                                     $("#ajax_loader").hide();

                                     

                             }  else {
                               $state.go('app.dashboard');
                             }
                         
                          });

                         }

                          $scope.allContacts = [];
                          $scope.addInput = function(){
                            $scope.allContacts.push({first_name:'', last_name:'', role_id:'', prime_email:'', prime_phone:''});
                          }

                          $scope.removeInput = function(index){
                              $scope.allContacts.splice(index,1);
                          }


                          $scope.addpopup = function(url){

                          window.open(url,'1440657862503','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
                          return false;
                          }


}]);