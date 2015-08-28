


app.controller('productListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  
  $http.get('api/public/admin/product').success(function(result, status, headers, config) {

                                  $scope.products = result.data.records;
                         
                          });

                         $scope.delete = function (product_id) {
                          
                            var permission = confirm(deleteMessage);
                            if (permission == true) {
                            $http.post('api/public/admin/productDelete',product_id).success(function(result, status, headers, config) {
                                          
                                          if(result.data.success=='1')
                                          {
                                           
                                            $state.go('product.list');
                                            $("#product_"+product_id).remove();
                                            return false;
                                          }  
                                     });
                                  }
                              } 

}]);


app.controller('productAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
   
   
 $http.get('api/public/common/getAllVendors').success(function(result, status, headers, config) {

      $scope.vendors = result.data.records;
                         
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

                    $scope.saveProduct = function () {

                        var user_data_product = $scope.product;
                       
                        
                        var fd = new FormData()
                        for (var i in $scope.files) {
                            fd.append("image", $scope.files[i])
                        }


                         $.each(user_data_product, function( index, value ) {
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
                                          $state.go('product.list');
                                     });
                                    }, 10);

                                }
                            }
                        }
                        if(user_data_product.id) {

                           xhr.open("POST","api/public/admin/productEdit")
                           xhr.send(fd);

                        } else {
                           xhr.open("POST","api/public/admin/productAdd")
                           xhr.send(fd);
                        }

                       
                    };

                        if($stateParams.id) {

                           $http.post('api/public/admin/productDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.product = result.data.records[0];
                                   
                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                         }

}]);


