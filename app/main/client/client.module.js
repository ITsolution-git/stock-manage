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
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                      
                       //return sessionService.AccessService('BC,CA');
                    },
                },
                url    : '/client',
                views  : {
                    'content@app': {
                        controller : 'ClientMainController as vm'
                    }
                }

            })
            .state('app.client.list', {
                resolve: {
                    checksession : function (sessionService)
                    {
                        
                       return sessionService.AccessService('ALL','true');
                    },
                },
                url    : '/list',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/client/client.html',
                        controller : 'ClientController as vm'
                    }
                }

            })
            .state('app.client.profile', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                           return sessionService.AccessService('ALL','true');
                        }
                    },
                url  : '/profile/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/client/views/profile/profile-view.html',
                        controller : 'ProfileViewController as vm'
                    }
                }
            });

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.client', {
            title    : 'Clients',
            icon     : 'icon-account-multiple',
            state    : 'app.client.list',
            /*stateParams: {
                'param1': 'page'
             },*/
            translate: 'Clients',
            weight   : 2
        });
    }
})();