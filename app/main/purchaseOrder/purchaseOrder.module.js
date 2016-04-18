(function ()
{
    'use strict';

    angular
        .module('app.purchaseOrder', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.purchaseOrder', {
                url    : '/purchaseOrder',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/purchaseOrder/purchaseOrder.html',
                        controller : 'PurchaseOrderController as vm'
                    }
                },
                resolve: {
                    PurchaseOrderData: function (msApi)
                    {
                        return msApi.resolve('purchaseOrder@get');
                    }
                }
            }).state('app.purchaseOrder.companyPO', {
                url  : '/companyPO/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/purchaseOrder/views/companyPO/companyPO.html',
                        controller : 'CompanyPOController as vm'
                    }
                }
            }).state('app.purchaseOrder.viewNote', {
                url  : '/viewNote/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/purchaseOrder/views/viewNote/viewNote.html',
                        controller : 'ViewNoteController as vm'
                    }
                }
            }).state('app.purchaseOrder.affiliatePO', {
                url  : '/affiliatePO/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/purchaseOrder/views/affiliatePO/affiliatePO.html',
                        controller : 'AffiliatePOController as vm'
                    }
                }
            })
            ;

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/purchaseOrder');

        // Api
        msApiProvider.register('purchaseOrder', ['app/data/purchaseOrder/purchaseOrder.json']);

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.purchaseOrder', {
            title    : 'Purchase Order',
            icon     : 'icon-basket',
            state    : 'app.purchaseOrder',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();