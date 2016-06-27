(function ()
{
    'use strict';

    angular
        .module('app.order', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.order', {
                url    : '/order',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/order/order.html',
                        controller : 'OrderController as vm'
                    }
                },
                resolve: {
                    checksession : function (sessionService,$stateParams,$state)
                    {
                        
                       return sessionService.AccessService('BC,CA');
                    }
                }
            }).state('app.order.order-info', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/order-info/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/order-info/order-info.html',
                        controller : 'OrderInfoController as vm'
                    }
                }
            }).state('app.order.design', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/design/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/design/design.html',
                        controller : 'DesignController as vm'
                    }
                }
            }).state('app.order.distribution', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/distribution/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/distribution/distribution.html',
                        controller : 'DistributionController as vm'
                    }
                }
            }).state('app.order.distributionProduct', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/distributionProduct/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/distributionProduct/distributionProduct.html',
                        controller : 'DistributionProductController as vm'
                    }
                }
            }).state('app.order.spiltAffiliate', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/spiltAffiliate/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/spiltAffiliate/spiltAffiliate.html',
                        controller : 'SpiltAffiliateController as vm'
                    }
                }
            }).state('app.order.affiliate-info', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/affiliate-info/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/spiltAffiliate/affiliate-info.html',
                        controller : 'AffiliateInfoController as vm'
                    }
                }
            }).state('app.order.affiliate-view', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('BC,CA');
                        }
                    },
                url  : '/affiliate-view/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/order/views/spiltAffiliate/affiliate-view.html',
                        controller : 'AffiliateViewController as vm'
                    }
                }
            });

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.order', {
            title    : 'Orders',
            icon     : 'icon-content-paste',
            state    : 'app.order',
            /*stateParams: {
                'param1': 'page'
             },*/
            
            weight   : 1
        });
    }
})();