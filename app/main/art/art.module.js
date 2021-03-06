(function ()
{
    'use strict';

    angular
        .module('app.art', ['ngTasty'])
        .config(config);

    /** @ngInject */
    function config($stateProvider, msApiProvider, msNavigationServiceProvider)
    {
        // State
        $stateProvider
            .state('app.art', {
                url    : '/art',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/art/art.html',
                        controller : 'ArtController as vm'
                    }
                },
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('ALL','true');
                        }
                }
            }) .state('app.screen', {
                url    : '/screen',
                views  : {
                    'content@app': {
                        templateUrl: 'app/main/art/artscreen.html',
                        controller : 'ArtScreenController as vm'
                    }
                },
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('ALL','true');
                        }
                }
            }).state('app.art.orderView', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('ALL','true');
                        }
                },
                url  : '/artOrderView/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/art/views/orderview/orderView.html',
                        controller : 'orderViewController as vm'
                    }
                }
            }).state('app.art.screensetView', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('ALL','true');
                        }
                },
                url  : '/screensetView/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/art/views/screensetview/screensetView.html',
                        controller : 'screenSetViewController as vm'
                    }
                }
            })
            .state('app.art.notes', {
                resolve: {
                        checksession : function (sessionService,$stateParams,$state)
                        {
                            
                           return sessionService.AccessService('ALL','true');
                        }
                },
                url  : '/notes/:id',
                views: {
                    'content@app': {
                        templateUrl: 'app/main/art/views/viewNote/viewNote.html',
                        controller : 'ArtViewNoteController as vm'
                    }
                }
            });

       // Translation
       //$translatePartialLoaderProvider.addPart('app/main/art');

        // Api
      //  msApiProvider.register('ArtOrder', ['app/data/art/artOrder.json']);
        // Navigation
        msNavigationServiceProvider.saveItem('fuse', {
            title : '',
            group : true,
            weight: 1
        });

        msNavigationServiceProvider.saveItem('fuse.art', {
            title    : 'Art',
            icon     : 'icon-palette',
            state    : 'app.art',
            /*stateParams: {
                'param1': 'page'
             },*/
            weight   : 7
        });
    }
})();