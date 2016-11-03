(function ()
{
    'use strict';

    angular
        .module('fuse')
        .run(runBlock);

   function runBlock($rootScope, $timeout, $state,  $resource,sessionService,$http,notifyService,$q,$window)
    {
        // Store state in the root scope for easy access
        
           // De-activate loading indicator
        var stateChangeSuccessEvent = $rootScope.$on('$stateChangeSuccess', function ()
        {
           if ($window.Appcues) 
            {
                $window.Appcues.start();
            }
            

            $timeout(function ()
            {
                $rootScope.loadingProgress = false;
                $(".settings-block").addClass("collapsed");
                $(".admin-block").removeClass("collapsed");
            });
        });

        // Cleanup
        $rootScope.$on('$destroy', function ()
        {
            stateChangeStartEvent();
            stateChangeSuccessEvent();
        });
        if(sessionService.get('token') && sessionService.get('user_id')){
            $http.defaults.headers.common.Authorization = sessionService.get('token');
            $http.defaults.headers.common.AuthUserId = sessionService.get('user_id');
        }
    }


})();