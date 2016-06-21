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
            .state('app.settings.customProduct', {
                resolve: {
                         checksession : function (sessionService,$state)
                        {
                            setTimeout(function(){ 
                               $(".settings-block").removeClass("collapsed");
                            }, 2000);
                            return sessionService.AccessService('CA');
                        }
                    },
                url    : '/customProduct',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/customProduct/customProduct.html',
                        controller : 'customProductController as vm'
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

        msNavigationServiceProvider.saveItem('fuse.settings.customProduct', {
            title    : 'Custom Product',
            state    : 'app.settings.customProduct',
            weight   : 10
        });
    }
})();