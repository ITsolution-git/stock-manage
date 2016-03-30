(function ()
{
    'use strict';

    angular
        .module('app.client', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.client', {
                url    : '/client',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/client/client.html',
                        controller : 'ClientController as vm'
                    }
                },
                resolve: {
                    ClientData: function (msApi)
                    {
                        return msApi.resolve('client@get');
                    }
                }
            });

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/client');

        // Api
        msApiProvider.register('client', ['app/data/client/client.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : 'Clients',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.client', {
            title    : 'Clients',
            icon     : 'icon-tile-four',
            state    : 'app.client',
            /*stateParams: {
                'param1': 'page'
             },*/
            translate: 'CLIENT.CLIENT_NAV',
            weight   : 1
        });
    }
})();