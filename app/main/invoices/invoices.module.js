(function ()
{
    'use strict';

    angular
        .module('app.invoices', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.invoices', {
                url    : '/invoices',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/invoices/invoices.html',
                        controller : 'invoiceController as vm'
                    }
                },
                resolve: {
                    invoiceData: function (msApi)
                    {
                        return msApi.resolve('invoicesDetail@get');
                    }
                }
            }).state('app.invoices.singleInvoice', {
                url  : '/singleInvoice',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/invoices/views/singleInvoice/singleInvoice.html',
                        controller : 'singleInvoiceController as vm'
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/invoices');

        // Api
        msApiProvider.register('invoicesDetail', ['app/data/invoices/invoiceData.json']);
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.invoices', {
            title    : 'Invoices',
            icon     : 'icon-file-document',
            state    : 'app.invoices',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();