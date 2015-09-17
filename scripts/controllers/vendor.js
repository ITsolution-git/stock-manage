
app.controller('vendorListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant) {
  

                         $http.get('api/public/admin/vendor').success(function(result, status, headers, config) {

                                  $scope.vendors = result.data.records;
                                  $scope.pagination = AllConstant.pagination;
                         
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

app.controller('vendorAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant) {
  
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

                           $http.post('api/public/admin/vendorDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       

                                     $scope.vendor = result.data.records[0];
                                     $scope.allContacts = result.data.allContacts;
                                     
                                     

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