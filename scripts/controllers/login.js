app.controller('loginCtrl', ['$scope','$http','$location','$state','$timeout','sessionService','notifyService', function($scope,$http,$location,$state,$timeout,sessionService,notifyService) {

    var role = sessionService.get('role_slug');
    $("#ajax_loader").hide();


  var vid = document.getElementById("bgvid");
  var pauseButton = document.querySelector("#polina button");

  function vidFade() {
    vid.classList.add("stopfade");
  }

  vid.addEventListener('ended', function() {
    // only functional if "loop" is removed 
    vid.pause();

    // to capture IE10
    vidFade();
  });




   $scope.dosignin = function () {
                         var user_data = $scope.user;
                         sessionService.remove('role_slug');
                         $http.post('api/public/admin/login',user_data).success(function(result,event, status, headers, config) {
        
                          if(result.data.success == '0') {
                                  var data = {"status": "error", "message": "Please check Username and Password"}
                                  notifyService.notify(data.status, data.message);
                                  $state.go('access.signin');
                                  return false;

                                } else {
                                   //flash('success',result.data.message); 
                                   $("#ajax_loader").show();
                                   sessionService.set('useremail',result.data.records.useremail).then(function(){
                                   });

                                   sessionService.set('role_slug',result.data.records.role_slug).then(function(){

                                   });
                                   sessionService.set('login_id',result.data.records.login_id).then(function(){

                                   });
                                   sessionService.set('name',result.data.records.name).then(function(){

                                   });
                                   sessionService.set('user_id',result.data.records.user_id).then(function(){

                                     });
                                    sessionService.set('role_title',result.data.records.role_title).then(function(){

                                    $location.url('/app/dashboard');
                                    
                                   });
                                    sessionService.set('username',result.data.records.username).then(function(){

                                   });
                                    sessionService.set('password',result.data.records.password).then(function(){

                                    setTimeout(function(){ window.location.reload(); }, 100);
                                   });

                                    var data = {"status": "success", "message": "Login Successful, Please wait..."}
                                    notifyService.notify(data.status, data.message);

                                   return false;

                                }
                         
                          });
                    }
}]);


app.controller('forgot_password', ['$scope','$http','$location','$state','$timeout','notifyService', function($scope,$http,$location,$state,$timeout,notifyService) {

    $("#ajax_loader").hide();

    $scope.forgot_password = function()
    {
         $("#ajax_loader").show();
         var user_data = $scope.user;
         $http.post('api/public/admin/forgot_password',user_data).success(function(result,event, status, headers, config) {
              if(result.data.success==0)
              {
                  var data = {"status": "error", "message": result.data.message}
                  notifyService.notify(data.status, data.message);
                  $("#ajax_loader").hide();
                  return false;
              }
              else
              {
                  var data = {"status": "success", "message": result.data.message}
                  notifyService.notify(data.status, data.message);
                  $("#ajax_loader").hide();
                  return false;
            }
          });
    }


  }]);

app.controller('reset_password', ['$scope','$http','$location','$state','$stateParams','$timeout','notifyService', function($scope,$http,$location,$state,$stateParams,$timeout,notifyService) {

    $("#ajax_loader").hide();
    $scope.string = {};

    $scope.string.string = $stateParams.string;
    $http.post('api/public/admin/check_user_password',$scope.string).success(function(result,event, status, headers, config) {
          if(result.data.success==0)
          {
              var data = {"status": "error", "message": result.data.message}
              notifyService.notify(data.status, data.message);
              $state.go('access.signin');
              return false;
          }
    });


    $scope.change_password = function($user)
    {
          $("#ajax_loader").show();
         $scope.data = {};
         $scope.data.form_data = $user;
         $scope.data.string = $scope.string.string;
        // console.log($scope.data); return false;
         $http.post('api/public/admin/change_password', $scope.data).success(function(result,event, status, headers, config) {

            if(result.data.success==0)
            {
                var data = {"status": "error", "message": result.data.message}
                notifyService.notify(data.status, data.message);
                $("#ajax_loader").hide();
                return false;
            }
            else
            {
                var data = {"status": "success", "message": result.data.message}
                notifyService.notify(data.status, data.message);
                $state.go('access.signin');
                $("#ajax_loader").hide();
                return false;
            }

          });
    }


  }]);

