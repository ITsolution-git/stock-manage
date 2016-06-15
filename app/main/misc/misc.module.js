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
            .state('app.misc', {
                url    : '/misc',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/misc/misc.html',
                        controller : 'miscController as vm'
                    }
                },
                resolve: {
                    miscData: function (msApi)
                    {
                        return msApi.resolve('misc@get');
                    }
                }
            }).state('app.misc.orderwaitship', {
                url  : '/orderwaitship',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/misc/views/orderwaitship/orderwaitship.html',
                        controller : 'orderWaitController as vm'
                    }
                }
            }).state('app.misc.shipmentdetails', {
                url  : '/shipmentdetails',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/misc/views/shipmentdetails/shipmentdetails.html',
                        controller : 'shipmentController as vm'
                    }
                }
            }).state('app.misc.boxingdetail', {
                url  : '/boxingdetail',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/misc/views/boxingdetail/boxingdetail.html',
                        controller : 'boxingdetailController as vm'
                    }
                }
            }).state('app.misc.shipmentoverview', {
                url  : '/shipmentoverview',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/misc/views/shipmentoverview/shipmentoverview.html',
                        controller : 'shipmentOverviewController as vm'
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/misc');

        // Api
        msApiProvider.register('misc', ['app/data/misc/misc.json']);
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.misc', {
            title    : 'Misc',
            icon     : 'icon-truck',
            state    : 'app.misc',
            /*stateParams: {
                'param1': 'page'
             },*/
            weight   : 1
        });
    }
})();