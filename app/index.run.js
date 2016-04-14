(function ()
{
    'use strict';

    angular
        .module('fuse')
        .run(runBlock);

   function runBlock($rootScope, $timeout, $state,  $resource,sessionService,notifyService,$q)
    {
        // Store state in the root scope for easy access
        
        $rootScope.state = $state;

        var checkSession = $resource('api/public/auth/session',null,{
            post : {
                method : 'get'
            }
        });
        
        funCheckSession();
       
        // Activate loading indicator
        var stateChangeStartEvent = $rootScope.$on('$stateChangeStart', function ($stateChangeStart, $next)
        {   
            $rootScope.loadingProgress = true;      
            funCheckSession();      
            var userId = sessionService.get('user_id');            
            if(userId === '' || userId === null) 
            {                    
                if($next.name !== 'app.login' && $state.current.name !== 'app.login') 
                {                                        
                    $state.go('app.login');
                    notifyService.notify("error", "Please signin first.");
                    $stateChangeStart.preventDefault();
                }
            }
        });

        // CHECK SESSION FUNCITON ON EACH CALL
        function funCheckSession() 
        {

            checkSession.post(null,function(result) 
            {   
                if(result.data.success=='1')
                {   
                    sessionService.set('email',result.data.email);
                    sessionService.set('role_slug',result.data.role_session);
                    sessionService.set('user_id',result.data.user_id);
                    sessionService.set('company_id',result.data.company_id);
                    sessionService.set('company_name',result.data.company_name);
                    //console.log(sessionService.get('company_name'));
                }
            });
        }


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