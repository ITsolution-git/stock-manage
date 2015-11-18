app.controller('loginCtrl', ['$scope','$http','$location','$state','flash','sessionService', function($scope,$http,$location,$state,flash,sessionService) {

    var role = sessionService.get('role_slug');

   $scope.dosignin = function () {
                        var user_data = $scope.user;
                        
                         $http.post('api/public/admin/login',user_data).success(function(result, status, headers, config) {
        
                          if(result.data.success == '0') {
                                  flash('danger',result.data.message); 
                                  $state.go('access.signin');
                                  return false;

                                } else {
                                   flash('success',result.data.message); 
                                   $("#ajax_loader").show();
                                   sessionService.set('username',result.data.records.username);
                                   sessionService.set('password',result.data.records.password);
                                   sessionService.set('useremail',result.data.records.useremail);
                                   sessionService.set('role_title',result.data.records.role_title);
                                   sessionService.set('role_slug',result.data.records.role_slug);
                                   sessionService.set('login_id',result.data.records.login_id);
                                   sessionService.set('name',result.data.records.name);
                                   sessionService.set('user_id',result.data.records.user_id);

                                   //$location.url('/app/dashboard');
                                   $state.go('app.dashboard');
                                   setTimeout(function(){ window.location.reload(); }, 200);
                                   //window.location.href="#/app/dashboard";
                                   return false;

                                }
                         
                    });
                    }
}]);