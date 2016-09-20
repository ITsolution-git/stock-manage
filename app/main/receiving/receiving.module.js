(function ()
{
    'use strict';

    angular
        .module('app.receiving', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.receiving', {
                url    : '/receiving',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/receiving/receiving.html',
                        controller : 'ReceivingController as vm'
                    }
                },
               
                resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('ALL','true');
                    },
                }
            }).state('app.receiving.receivingInfo', {
            url: '/receivePO/:id',
            views: {
                'content@app': {
                    templateUrl: 'app/main/receiving/views/receivingInfo/receivingInfo.html',
                    controller: 'ReceivingInfoController as vm'
                }
            },resolve: {
                    checksession : function (sessionService)
                    {
                        return sessionService.AccessService('SU,AT','false');
                    }
                }
        })
            ;

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/receiving');
        // Api

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.receiving', {
            title: 'Receiving',
            icon: 'icon-cart',
            state: 'app.receiving',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();