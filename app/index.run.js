(function ()
{
    'use strict';

    angular
        .module('fuse')
        .run(runBlock);

   function runBlock($rootScope, $timeout, $state,  $resource,sessionService,notifyService,$q)
    {
        // Store state in the root scope for easy access
        
           // De-activate loading indicator
        var stateChangeSuccessEvent = $rootScope.$on('$stateChangeSuccess', function ()
        {
            $timeout(function ()
            {
                $rootScope.loadingProgress = false;
                $(".settings-block").addClass("collapsed");
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