(function ()
{
    'use strict';

    angular
        .module('app.finishingQueue', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.finishingQueue_remove', {
                resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    },
                },
                url    : '/finishingQueue_remove',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/finishingQueue/finishingQueue.html',
                        controller : 'FinishingQueueController as vm'
                    }
                }
            }).state('app.finishingBoard_remove', {
                url: '/finishingBoard_remove',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/finishingQueue/finishingBoard.html',
                        controller: 'FinishingBoardController as vm'
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
        /*$translatePartialLoaderProvider.addPart('app/main/finishing');*/

        // Api
        //msApiProvider.register('receiving', ['app/data/receiving/receiving.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.finishingQueue', {
            title: '',
            icon: 'icon-cart',
            state: 'app.finishingQueue',
            weight   : 11
        });
    }
})();