(function ()
{
    'use strict';

    angular
        .module('fuse')
        .run(runBlock);

   function runBlock($rootScope, $timeout, $state,  $resource,sessionService,notifyService)
    {
        // Store state in the root scope for easy access
        
        $rootScope.state = $state;

        var checkSession = $resource('api/public/auth/session',null,{
            post : {
                method : 'get'
            }
        });

        // Activate loading indicator
        var stateChangeStartEvent = $rootScope.$on('$stateChangeStart', function ($stateChangeStart, $next)
        {
            $rootScope.loadingProgress = true;
            
            checkSession.get(null,function(result) {

                        if(result.data.success == '0') 
                        {
                            var data = {"status": "error", "message": "Please signin first."}
                            notifyService.notify(data.status, data.message);
                            //console.log(234);
                            // $state.go('app.login');
                            //return false;
                        } 
                        else 
                        {
                            $rootScope.email = result.data.email;
                             sessionService.set('role_slug',result.data.role_session);
                             sessionService.set('user_id',result.data.user_id);
                            $rootScope.company_profile =  result.data.company;

                            var role = result.data.role_session;
                            // console.log('Permission Allow for Role - '+role);
                            // if(ret.indexOf(role) <= -1 && ret != 'ALL' && ret!='')
                            //{
                            //   // console.log('error');
                            //    var data = {"status": "error", "message": "You are Not authorized, Please wait"}
                            //   notifyService.notify(data.status, data.message);
                            // $location.url('/app/dashboard');
                            // setTimeout(function(){ window.location.reload(); }, 200);
                            //return false;
                            // 
                            //}
                        }
                    
            });
        });

        // De-activate loading indicator
        var stateChangeSuccessEvent = $rootScope.$on('$stateChangeSuccess', function ()
        {
            $timeout(function ()
            {
                $rootScope.loadingProgress = false;
            });
        });

        // Cleanup
        $rootScope.$on('$destroy', function ()
        {
            stateChangeStartEvent();
            stateChangeSuccessEvent();
        });
    }


})();