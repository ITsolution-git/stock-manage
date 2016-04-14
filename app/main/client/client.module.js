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
                       return sessionService.AccessService('ALL');
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
                       return sessionService.AccessService('ALL');
                    },
                    ClientData: function (msApi,sessionService,$rootScope)
                    {
                       var price_list_data = {};
                        //console.log(sessionService.get('company_id'));
                       price_list_data.cond ={company_id :sessionService.get('company_id')};

                       return msApi.resolve('client@post',price_list_data);
                    }
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
                        checksession : function (sessionService)
                        {
                           return sessionService.AccessService('ALL');
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

        // Translation
        //$translatePartialLoaderProvider.addPart('app/main/client');

        // Api
        // msApiProvider.register('client', ['app/data/client/client.json']);
        msApiProvider.register('client',['api/public/client/ListClient',null, {post:{method:'post'}}]);

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
            weight   : 1
        });
    }
})();