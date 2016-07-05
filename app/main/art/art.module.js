(function ()
{
    'use strict';

    angular
        .module('app.art', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.art', {
                url    : '/art',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/art/art.html',
                        controller : 'ArtController as vm'
                    }
                },
                resolve: {
                    ArtData: function (msApi)
                    {
                        return msApi.resolve('ArtOrder@get');
                    }
                }
            }).state('app.art.orderView', {
                url  : '/artOrderView/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/art/views/orderview/orderView.html',
                        controller : 'orderViewController as vm'
                    }
                }
            }).state('app.art.screensetView', {
                url  : '/screensetView',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/art/views/screensetview/screensetView.html',
                        controller : 'screenSetViewController as vm'
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/art');

        // Api
        msApiProvider.register('ArtOrder', ['app/data/art/artOrder.json']);
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.art', {
            //title    : 'Art',
            icon     : 'icon-palette',
            state    : 'app.art',
            /*stateParams: {
                'param1': 'page'
             },*/
            weight   : 1
        });
    }
})();