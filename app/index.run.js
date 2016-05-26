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
        
        // Activate loading indicator
        var stateChangeStartEvent = $rootScope.$on('$stateChangeStart', function ($stateChangeStart, $next)
        {   
            $rootScope.loadingProgress = true;      
            checkSession.post(null,function(result) 
            {   
                if(result.data.success=='1')
                {   
                    sessionService.set('email',result.data.email);
                    sessionService.set('role_slug',result.data.role_session);
                    sessionService.set('user_id',result.data.user_id);
                    sessionService.set('company_id',result.data.company_id);
                    sessionService.set('company_name',result.data.company_name);
                    sessionService.set('name',result.data.name);
                    // console.log(sessionService.get('company_id'));
                    var userId = result.data.user_id;            
                    if(userId === '' || userId === null) 
                    {                   
                        if($next.name.indexOf('login') == -1 || $next.name.indexOf('forget') == -1) 
                        {                                        
                            
                        }
                        else
                        {
                            $state.go('app.login');
                            notifyService.notify("error", "Please signin first.");
                            $stateChangeStart.preventDefault();
                        }
                    }
                }
                else
                {
                    if($next.name.indexOf('login') == -1 || $next.name.indexOf('forget') == -1) 
                    {                                        
                        
                    }
                    else
                    {
                        $state.go('app.login');
                        notifyService.notify("error", "Please signin first.");
                        $stateChangeStart.preventDefault();
                    }
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