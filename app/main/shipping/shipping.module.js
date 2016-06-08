(function ()
{
    'use strict';

    angular
        .module('app.shipping', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.shipping', {
                url    : '/shipping',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/shipping/shipping.html',
                        controller : 'shippingController as vm'
                    }
                },
                resolve: {
                    shippingData: function (msApi)
                    {
                        return msApi.resolve('shipping@get');
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/shipping');

        // Api
        msApiProvider.register('shipping', ['app/data/shipping/shipping.json']);
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.shipping', {
            title    : 'Shipping',
            icon     : 'icon-truck',
            state    : 'app.shipping',
            /*stateParams: {
                'param1': 'page'
             },*/
            weight   : 1
        });
    }
})();