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
                    OrderData: function (msApi,sessionService)
                    {
                       
                         /*var order_list_data = {};
                         order_list_data.cond ={company_id :sessionService.get('company_id')};*/
                        //    return msApi.resolve('order@post',order_list_data);
                    },OrderUserData: function (msApi,sessionService)
                    {
                       return msApi.resolve('orderUser@get',{id:sessionService.get('company_id')});
                       
                    },OrderCompanyData: function (msApi,sessionService)
                    {
                         var order_comp_data = {};
                         order_comp_data.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1'};
                         order_comp_data.table ="client";

                         //return msApi.resolve('orderClient@post',order_comp_data);
                       //return msApi.resolve('order@post',order_list_data);
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
            })
            ;

        // Translation
       // $translatePartialLoaderProvider.addPart('app/main/order');

      // Api
      msApiProvider.register('orderUser',['api/public/common/getStaffList/:id',null, {get:{method:'get'}}]);
      //msApiProvider.register('orderClient',['api/public/common/GetTableRecords',null, {post:{method:'post'}}]);
  
     


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