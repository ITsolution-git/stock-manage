(function ()
{
    'use strict';

    angular
        .module('app.login')
        .controller('LoginController', LoginController)
        .controller('LogoutController', LogoutController);

    /** @ngInject */
    function LoginController(sessionService,$rootScope,$resource,notifyService,$state)
    {
        var vm = this;
        // Data
        vm.Login_verify = Login_verify;
        function Login_verify(data)
        {
            var user_data = data;
            sessionService.remove('role_slug');

            var login = $resource('api/public/admin/login',null,{
                post : {
                    method : 'post'
                }
            });


           login.post(user_data,function(result) 
            {                
                  if(result.data.success == '0') {
                                  var data = {"status": "error", "message": "Please check Email and Password"}
                                  notifyService.notify(data.status, data.message);
                                  $state.go('app.login');
                                  return false;

                                } else {

                                   sessionService.set('useremail',result.data.records.useremail);
                                   sessionService.set('role_slug',result.data.records.role_slug);
                                   sessionService.set('login_id',result.data.records.login_id);
                                   sessionService.set('name',result.data.records.name);
                                   sessionService.set('user_id',result.data.records.user_id);
                                   sessionService.set('role_title',result.data.records.role_title);
                                   sessionService.set('username',result.data.records.username);
                                   sessionService.set('password',result.data.records.password);
                                   sessionService.set('company_id',result.data.records.company_id);
                                   var data = {"status": "success", "message": "Login Successful, Please wait..."}
                                   notifyService.notify(data.status, data.message);
                                   
                                   //window.location.href = $state.go('app.client');
                                    //$state.go('app.client');
                                    
                                   setTimeout(function(){ 
                                        window.open('client', '_self'); }, 1000);
                                   // 
                                    //window.location.reload();
                                    return false;


                                }
                         
            });
        }
        
        // Methods

        //////////
    }
    function LogoutController(sessionService)
    {
        sessionService.destroy();
        return false;
    }
})();