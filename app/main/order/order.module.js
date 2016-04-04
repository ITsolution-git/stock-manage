(function ()
{
    'use strict';

    angular
        .module('app.order', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.order', {
                url    : '/order',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/order/order.html',
                        controller : 'OrderController as vm'
                    }
                },
                resolve: {
                    OrderData: function (msApi)
                    {
                        return msApi.resolve('order@get');
                    }
                }
            });

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/order');

        // Api
        msApiProvider.register('order', ['app/data/order/order.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.order', {
            title    : 'Orders',
            icon     : 'icon-content-paste',
            state    : 'app.order',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();