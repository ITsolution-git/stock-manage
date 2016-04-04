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
            }).state('app.client.profile', {
                url  : '/profile/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/client/views/profile/profile-view.html',
                        controller : 'ProfileViewController as vm'
                    }
                }
            });

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/client');

        // Api
        msApiProvider.register('client', ['app/data/client/client.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.client', {
            title    : 'Clients',
            icon     : 'icon-account-multiple',
            state    : 'app.client',
            /*stateParams: {
                'param1': 'page'
             },*/
            translate: 'CLIENT.CLIENT_NAV',
            weight   : 1
        });
    }
})();