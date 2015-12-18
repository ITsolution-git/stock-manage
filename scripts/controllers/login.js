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
                                  var data = {"status": "error", "message": "Please check uername and Password"}
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

                                    var data = {"status": "success", "message": "Login Successfull, Please wait..."}
                                    notifyService.notify(data.status, data.message);
                                    $("#ajax_loader").hide();

                                 
                                   
                                   //$location.url('/app/dashboard');
                                   //setTimeout(function(){ window.location.reload(); }, 200);
                                   //window.location.href="#/app/dashboard";
                                   return false;

                                }
                         
                    });
                    }
}]);