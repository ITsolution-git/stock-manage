(function ()
{
    'use strict';

    angular
        .module('app.production', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider,  msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.production', {
                url    : '/production',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/production/production.html',
                        controller : 'ProductionController as vm'
                    }
                },
               
                resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    },
                }
            }).state('app.production.productionInfo', {
            url: '/productionDetail/:id',
            views: {
                'content@app': {
                    templateUrl: 'app/main/production/views/productionInfo/productionInfo.html',
                    controller: 'ProductionInfoController as vm'
                }
            },resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    }
                }
        })
            ;

        // Translation
     //  $translatePartialLoaderProvider.addPart('app/main/receiving');
        // Api

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.production', {
            //title: 'Production',
            icon: 'icon-cart',
            state: 'app.production',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();