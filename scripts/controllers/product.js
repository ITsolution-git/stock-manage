


app.controller('productListCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
  AuthService.AccessService('FM');
  $("#ajax_loader").show();
  $http.get('api/public/admin/product').success(function(result, status, headers, config) {

                                  $scope.products = result.data.records;
                                  $scope.pagination = AllConstant.pagination;
                                  $("#ajax_loader").hide();

                                  var init;

                                  $scope.searchKeywords = '';
                                  $scope.filteredProducts = [];
                                  $scope.row = '';
                                  $scope.select = function (page) {
                                      var end, start;
                                      start = (page - 1) * $scope.numPerPage;
                                      end = start + $scope.numPerPage;
                                      return $scope.currentPageProducts = $scope.filteredProducts.slice(start, end);
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
                                      $scope.filteredProducts = $filter('filter')($scope.products, $scope.searchKeywords);
                                      return $scope.onFilterChange();
                                  };
                                  $scope.order = function (rowName) {
                                      if ($scope.row === rowName) {
                                          return;
                                      }
                                      $scope.row = rowName;
                                      $scope.filteredProducts = $filter('orderBy')($scope.products, rowName);
                                      return $scope.onOrderChange();
                                  };
                                  $scope.numPerPageOpt = [10, 20, 50, 100];
                                  $scope.numPerPage = 10;
                                  $scope.currentPage = 1;
                                  $scope.currentPageProducts = [];

                                  init = function () {
                                      $scope.search();

                                      return $scope.select($scope.currentPage);
                                  };
                                  return init();
                         
                          });

                         $scope.delete = function (product_id) {
                          
                            var permission = confirm(AllConstant.deleteMessage);
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


app.controller('productAddEditCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant', function($scope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant) {
   AuthService.AccessService('FM');
   
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
                           $("#ajax_loader").show();
                           $http.post('api/public/admin/productDetail',$stateParams.id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.product = result.data.records[0];
                                     $("#ajax_loader").hide();

                                   
                             }  else {
                             $state.go('app.dashboard');
                             }
                         
                          });

                         }

}]);


