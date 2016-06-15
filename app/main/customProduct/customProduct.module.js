(function ()
{
    'use strict';

    angular
        .module('app.customProduct', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.customProduct', {
                url    : '/customProduct',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/customProduct/customProduct.html',
                        controller : 'customProductController as vm'
                    }
                }
            }).state('app.customProduct.companyPO', {
                 resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/companyPO/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/customProduct/views/companyPO/companyPO.html',
                        controller : 'CompanyPOController as vm'
                    }
                }
            }).state('app.customProduct.viewNote', {
                 resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/viewNote/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/customProduct/views/viewNote/viewNote.html',
                        controller : 'ViewNoteController as vm'
                    }
                }
            }).state('app.customProduct.affiliatePO', {
                 resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/affiliatePO/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/customProduct/views/affiliatePO/affiliatePO.html',
                        controller : 'AffiliatePOController as vm'
                    }
                }
            })
             function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }
            ;

        // Translation
        $translatePartialLoaderProvider.addPart('app/main/customProduct');

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.customProduct', {
            title    : 'Custom Product',
            icon     : 'icon-basket',
            state    : 'app.customProduct',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();