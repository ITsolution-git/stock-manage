app.controller('companyListCtrl', ['$scope','$http','$location','$state','AuthService','sessionService','$filter', function($scope,$http,$location,$state,AuthService,sessionService,$filter) {

                                AuthService.AccessService('SA');
                                
                            var company = {};
                            
                            $http.get('api/public/admin/company/list').success(function(result) {
                                 $scope.company  = result.data.records;

                                 var init;

                                $scope.searchKeywords = '';
                                $scope.filteredCompany = [];
                                $scope.row = '';
                                $scope.select = function (page) {
                                  var end, start;
                                  start = (page - 1) * $scope.numPerPage;
                                  end = start + $scope.numPerPage;
                                  return $scope.currentPageCompany = $scope.filteredCompany.slice(start, end);
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
                                  $scope.filteredCompany = $filter('filter')($scope.company, $scope.searchKeywords);
                                  return $scope.onFilterChange();
                                };
                                $scope.order = function (rowName) {
                                  if ($scope.row === rowName) {
                                      return;
                                  }
                                  $scope.row = rowName;
                                  $scope.filteredCompany = $filter('orderBy')($scope.company, rowName);
                                  return $scope.onOrderChange();
                                };
                                $scope.numPerPageOpt = [10, 20, 50, 100];
                                $scope.numPerPage = 10;
                                $scope.currentPage = 1;
                                $scope.currentPageCompany = [];

                                init = function () {
                                  $scope.search();

                                  return $scope.select($scope.currentPage);
                                };
                                return init();
                             });
                      
                              var delete_params = {};
                              $scope.deletecompany = function (comp_id) {
                              delete_params.id = comp_id;
                              var permission = confirm("Are you sure to delete this record ?");
                              if (permission == true) {
                              $http.post('api/public/admin/company/delete',delete_params).success(function(result, status, headers, config) {
                                            
                                            if(result.data.success=='1')
                                            {
                                              $http.get('api/public/admin/company/list').success(function(result) {
                                                $scope.company  = result.data.records;
                                                $scope.filteredCompany = $scope.company;
                                                $scope.currentPageCompany = $scope.company;
                                                $scope.select($scope.currentPage);
                                              });
                                            }  
                                       });
                                    }
                                } // DELETE COMPANY FINISH

}]);
app.controller('companyAddCtrl', ['$scope','$http','$location','$state','AuthService','sessionService', function($scope,$http,$location,$state,AuthService,sessionService) {
                          
                          AuthService.AccessService('SA');
                         $scope.parent_id = '0';
                          $scope.CurrentController=$state.current.controller;
                          // GET ADMIN ROLE LIST
                          $http.get('api/public/common/getAdminRoles').success(function(Listdata) {

                                  $scope.rolelist = Listdata.data
                                 // console.log(Listdata); 
                            });
                       
                          // COMPANY ADD TIME CALL
                         $scope.addcompany = function () {
                         	$scope.company.role_id = $scope.app.company_roleid;
                            $scope.company.parent_id = $scope.parent_id;
                            $http.post('api/public/admin/company/add',$scope.company).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='1')
                                          {
                                            $state.go('company.list');
                                           }
                                     });
                                   } 
                              $scope.checkEmail = function (kem) {

                               var mail = $('#comp_email').val();
                               $http.get('api/public/common/checkemail/'+mail).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='2')
                                          {
                                            $("#err_email").hide();
                                            return false;
                                          }
                                          else
                                          {
                                            $("#err_email").show();
                                            return false;
                                          }
                                     });
                              }      

}]);
app.controller('companyEditCtrl', ['$scope','$http','$stateParams','$location','$state','notifyService','AuthService','sessionService', function($scope,$http,$stateParams,$location,$state,notifyService,AuthService,sessionService) {
                          
                            AuthService.AccessService('SA');
                            $scope.parent_id = $scope.app.user_id;
                            $scope.CurrentController=$state.current.controller;

                              // GET ADMIN ROLE LIST
                              $http.get('api/public/admin/company/edit/'+$stateParams.id+'/'+$scope.app.company_roleid).success(function(Listdata) {

                              		if(Listdata.data.success==1)
                              		{
                                      $scope.company = Listdata.data.records[0];
                                      $scope.company.password = 'testcodal';
                                      $scope.confirm_password = 'testcodal';
                                  	}
                                  	else
                                  	{
                                  		var data = {"status": "error", "message": "Something Wrong, Please try again !"}
                    				    notifyService.notify(data.status, data.message);
                                  		$state.go('company.list');
                                  		return false;
                                  	}
                                      
                                     // console.log(Listdata); 
                              });
                          
                          // COMPANY EDIT TIME CALL
                          $scope.editcompany = function () {

                            $scope.company.id= $stateParams.id;
                            $scope.company.parent_id=$scope.parent_id;
                            $scope.company.role_id = $scope.company_roleid;
                            $http.post('api/public/admin/company/save',$scope.company).success(function(result, status, headers, config) {
        
                                          if(result.data.success=='1')
                                          {
                                            $state.go('company.list');
                                            return false;
                                          }
                                     });
                                   } 
                           



}]);
