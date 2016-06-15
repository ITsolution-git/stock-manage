(function ()
{
    'use strict';

    angular
        .module('app.finishing', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.finishing', {
                resolve: {
                    checksession : function (sessionService)
                    {
                       return sessionService.AccessService('BC,CA');
                    },
                },
                url    : '/finishing',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/finishing/finishing.html',
                        controller : 'FinishingController as vm'
                    }
                }
            })
            ;

        // Translation
        /*$translatePartialLoaderProvider.addPart('app/main/finishing');*/

        // Api
        //msApiProvider.register('receiving', ['app/data/receiving/receiving.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.finishing', {
           // title: 'Finishing',
            icon: 'icon-cart',
            state: 'app.finishing',        
            weight   : 1
        });
    }
})();