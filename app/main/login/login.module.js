(function ()
{
    'use strict';

    angular
        .module('app.login', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider.state('app.login', {
            url      : '/login',
            views    : {
                'main@'                       : {
                    templateUrl: 'app/core/layouts/content-only.html',
                    controller : 'MainController as vm'
                },
                'content@app.login': {
                    templateUrl: 'app/main/login/login.html',
                    controller : 'LoginController as vm'
                }
            },
            bodyClass: 'login'
        })
        .state('app.logout', {
                url  : '/logout',
                views: {
                    'content@app': {
                        controller : 'LogoutController as vm'
                    }
                }
            })
        .state('app.dashboard', {
                url  : '/dashboard',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/login/dashboard.html',
                        controller : 'DashboardController as vm'
                    }
                }
            });
        ;
        


        // Translation
        $translatePartialLoaderProvider.addPart('app/main/login');

        // Navigation
/*        msNavigationServiceProvider.saveItem('login', {
            title : 'Authentication',
            icon  : 'icon-lock',
            weight: 1
        });*/

        msNavigationServiceProvider.saveItem('fuse.dashboard', {
            title : 'Dashboard',
            state : 'app.dashboard',
            icon  : 'icon-bank',
            weight: 1
        });
    }

})();