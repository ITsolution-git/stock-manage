(function ()
{
    'use strict';

    angular
        .module('app.admin', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        
        //State
        $stateProvider
            .state('app.admin', {
                resolve: {
                    checksession : function (sessionService)
                    {
                        setTimeout(function(){ 
                           $(".admin-block").removeClass("collapsed");
                        }, 2000);
                    },
                },
                url    : '/admin',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/admin/admin.html',
                        controller : 'AdminController as vm'
                    }
                }
            })
            .state('app.admin.companyProfile', {
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                        setTimeout(function(){ 
                           $(".admin-block").removeClass("collapsed");
                        }, 2000);
                         return sessionService.AccessService('SA');
                    },
                },
                url  : '/companyProfile',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/admin/views/company_list.html',
                        controller : 'AdminController as vm'
                    }
                }
            })
            .state('app.admin.colors', {
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                        setTimeout(function(){ 
                           $(".admin-block").removeClass("collapsed");
                        }, 2000);
                         return sessionService.AccessService('SA');
                    },
                },
                url  : '/colors',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/admin/views/colors_list.html',
                        controller : 'ColorController as vm'
                    }
                }
            })
            .state('app.admin.sizes', {
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                        setTimeout(function(){ 
                           $(".admin-block").removeClass("collapsed");
                        }, 2000);
                        return sessionService.AccessService('SA');
                    },
                },
                url  : '/sizes',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/admin/views/sizes_list.html',
                        controller : 'SizeController as vm'
                    }
                }
            })
            .state('app.admin.snsinventory', {
                resolve: {
                    checksession : function (sessionService,$state)
                    {
                        setTimeout(function(){ 
                           $(".admin-block").removeClass("collapsed");
                        }, 2000);
                         return sessionService.AccessService('SA');
                    },
                },
                url  : '/admin.sizes',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/admin/views/sizes_list.html',
                        controller : 'AdminController as vm'
                    }
                }
            });
        // Navigation
       /* msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });*/

        msNavigationServiceProvider.saveItem('fuse.admin', {
            title    : 'Admin',
            icon     : 'icon-cog',
            //state    : 'app.settings',
            class      : 'navigation-dashboards project-dashboard admin-block',
            
            weight   : 1
        });

        msNavigationServiceProvider.saveItem('fuse.admin.companyProfile', {
            title      : 'Company List',
            state      : 'app.admin.companyProfile',
            stateParams: {'id': 1},
            class      : 'navigation-dashboards project-dashboard',
            
            weight     : 1
        });
        msNavigationServiceProvider.saveItem('fuse.admin.colors', {
            title      : 'Colors',
            state      : 'app.admin.colors',
            stateParams: {'id': 2},
            class      : 'navigation-dashboards project-dashboard',
            
            weight     : 2
        });
        msNavigationServiceProvider.saveItem('fuse.admin.sizes', {
            title      : 'Sizes',
            state      : 'app.admin.sizes',
            stateParams: {'id': 3},
            class      : 'navigation-dashboards project-dashboard',
            
            weight     : 3
        });
        msNavigationServiceProvider.saveItem('fuse.admin.snsinventory', {
            title      : 'S&S Inventory',
            state      : '',
            class      : 'navigation-dashboards project-dashboard',
            
            weight     : 4
        });
    }

})();