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
                        templateUrl: 'app/main/production/view/production.html',
                        controller : 'ProductionController as vm'
                    }
                },
               
                resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    },
                }
            }).state('app.finishingqueue', {
            url: '/finishingqueue',
            views: {
                'content@app': {
                    templateUrl: 'app/main/production/finishingqueue.html',
                    controller: 'FinishingqueueController as vm'
                }
            },resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    }
                }
        }).state('app.productionlist', {
            url: '/productionlist',
            views: {
                'content@app': {
                    templateUrl: 'app/main/production/view/prodqueue.html',
                    controller: 'ProductionqueueController as vm'
                }
            },resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    }
                }
        }).state('app.scheduleboard', {
            url: '/scheduleboard',
            views: {
                'content@app': {
                    templateUrl: 'app/main/production/scheduleboard.html',
                    controller: 'ScheduleBoardController as vm'
                }
            },resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    }
                }
        }).state('app.finishboard', {
            url: '/finishboard',
            views: {
                'content@app': {
                    templateUrl: 'app/main/production/finishboard.html',
                    controller: 'ScheduleBoardController as vm'
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

        msNavigationServiceProvider.saveItem('fuse.productionlist', {
            title: 'Production Queue',
            icon: 'icon-cart',
            state: 'app.productionlist',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 10
        });
    }
})();