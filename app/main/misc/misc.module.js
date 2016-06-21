(function ()
{
    'use strict';

    angular
        .module('app.misc', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.settings.misc', {
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                        setTimeout(function(){ 
                           $(".settings-block").removeClass("collapsed");
                        }, 2000);
                        return sessionService.AccessService('CA');
                    }
                },
                url    : '/misc',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/misc/misc.html',
                        controller : 'miscController as vm'
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/misc');

       
      /*  // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });*/

        msNavigationServiceProvider.saveItem('fuse.settings.misc', {
            title    : 'Misc',
            state    : 'app.settings.misc',
            stateParams: {
                'param1': 'page'
             },
            weight   : 9
        });
    }
})();