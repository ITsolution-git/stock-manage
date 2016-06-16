(function ()
{
    'use strict';

    angular
        .module('app.misc', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.misc', {
                url    : '/misc',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/misc/misc.html',
                        controller : 'miscController as vm'
                    }
                }
            });

       // Translation
        $translatePartialLoaderProvider.addPart('app/main/misc');

       
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.misc', {
            //title    : 'Misc',
            icon     : 'icon-truck',
            state    : 'app.misc',
            /*stateParams: {
                'param1': 'page'
             },*/
            weight   : 1
        });
    }
})();