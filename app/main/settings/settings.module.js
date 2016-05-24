(function ()
{
    'use strict';

    angular
        .module('app.settings', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.settings', {
                url    : '/settings',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/settings/settings.html',
                        controller : 'SettingsController as vm'
                    }
                },
                resolve: {
                    
                }
            }).state('app.settings.userProfile', {
                url  : '/userProfile',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/settings/views/userProfile/userProfile.html',
                        controller : 'UserProfileController as vm'
                    }
                },resolve: {
                   
                }
            })
            ;

        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.settings', {
            title    : 'Settings',
            icon     : 'icon-cog',
            //state    : 'app.settings',
            weight   : 1
        });

        msNavigationServiceProvider.saveItem('fuse.settings.userProfile', {
            title      : 'User Profile',
            state      : 'app.settings.userProfile',
            stateParams: {'id': 1},
            weight     : 1
        });

        msNavigationServiceProvider.saveItem('fuse.settings.xyzw', {
            title      : 'Company Profile',
            state      : 'app.settings',
            stateParams: {'id': 2},
            weight     : 2
        });

        msNavigationServiceProvider.saveItem('fuse.settings.xydz', {
            title      : 'Company Details',
            state      : 'app.settings',
            stateParams: {'id': 3},
            weight     : 3
        });

        msNavigationServiceProvider.saveItem('fuse.settings.xyedz', {
            title      : 'User Management',
            state      : 'app.settings',
            stateParams: {'id': 4},
            weight     : 4
        });
        msNavigationServiceProvider.saveItem('fuse.settings.xyddez', {
            title      : 'Price Grid',
            state      : 'app.settings',
            stateParams: {'id': 5},
            weight     : 5
        });
        msNavigationServiceProvider.saveItem('fuse.settings.xyeadz', {
            title      : 'Affiliate',
            state      : 'app.settings',
            stateParams: {'id': 6},
            weight     : 6
        });
        msNavigationServiceProvider.saveItem('fuse.settings.xyadz', {
            title      : 'Integrations',
            state      : 'app.settings',
            stateParams: {'id': 7},
            weight     : 7
        });
        msNavigationServiceProvider.saveItem('fuse.settings.xydsz', {
            title      : 'Support',
            state      : 'app.settings',
            stateParams: {'id': 8},
            weight     : 8
        });
        msNavigationServiceProvider.saveItem('fuse.settings.xybdz', {
            title      : 'Billing',
            state      : 'app.settings',
            stateParams: {'id': 9},
            weight     : 9
        });


    }
})();