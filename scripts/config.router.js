'use strict';

/**
 * @ngdoc function
 * @name app.config:uiRouter
 * @description
 * # Config
 * Config for the router
 */
angular.module('app')
  .run(
    [           '$rootScope', '$state', '$stateParams','$location','$http',
      function ( $rootScope,   $state,  $stateParams ,$location,$http) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
      }
    ]
  )
  .config(
    [          '$stateProvider', '$urlRouterProvider', 'MODULE_CONFIG',
      function ( $stateProvider,   $urlRouterProvider,  MODULE_CONFIG ) {
        $urlRouterProvider
          .otherwise('/app/dashboard');
        $stateProvider
          .state('app', {
            abstract: true,
            url: '/app',
            controller: 'dasboardCtrl',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })


            .state('app.dashboard', {
              url: '/dashboard',
              controller: 'dasboardCtrl',
              templateUrl: 'views/pages/dashboard.html',
              data : { title: 'Dashboard', folded: true }, 
              resolve: { 
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       },
            })


            .state('app.home', {
              url: '/home',
              controller: 'dasboardCtrl',
              templateUrl: 'views/pages/home.html',
              data : { title: 'Home', folded: true }, 
              resolve: { 
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       },
            })
           
          
            
         
          .state('page', {
            url: '/page',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })
            .state('page.profile', {
              url: '/profile',
              templateUrl: 'views/pages/profile.html',
              data : { title: 'Profile', theme: { primary: 'green'} }
            })
            .state('page.settings', {
              url: '/settings',
              templateUrl: 'views/pages/settings.html',
              data : { title: 'Settings' }
            })
            .state('page.blank', {
              url: '/blank',
              templateUrl: 'views/pages/blank.html',
              data : { title: 'Blank' }
            })
            .state('page.document', {
              url: '/document',
              templateUrl: 'views/pages/document.html',
              data : { title: 'Document' }
            })
            .state('404', {
              url: '/404',
              templateUrl: 'views/pages/404.html'
            })
            .state('505', {
              url: '/505',
              templateUrl: 'views/pages/505.html'
            })
            .state('access', {
              url: '/access',
              template: '<div class="indigo bg-big"><div ui-view class="fade-in-down smooth"></div></div>'
            })
            .state('access.signin', {
              url: '/signin',
              templateUrl: 'views/pages/signin.html',
              controller: 'loginCtrl',

            })
            .state('access.signup', {
              url: '/signup',
              templateUrl: 'views/pages/signup.html'
            })
            .state('access.forgot-password', {
              url: '/forgot-password',
              templateUrl: 'views/pages/forgot-password.html'
            })
            .state('access.lockme', {
             url: '/signin',
              templateUrl: 'views/pages/signin.html',
              controller: 'logoutCtrl',

            })

        .state('staff', {
              url: '/staff',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })
          .state('account', {
              url: '/account',
              views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
              },
              resolve: {
                checklogin: function (AuthService) {
                   return AuthService.checksession();
                },
              }
          })

          .state('vendor', {
              url: '/vendor',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

          .state('product', {
              url: '/product',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

          .state('setting', {
              url: '/setting',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

          .state('misc', {
              url: '/misc',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

            .state('staff.list', {
              url: '/list',
              templateUrl: 'views/staff/staff.html',
              data : { title: 'Staff' },
              controller: 'staffListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.add', {
              url: '/add',
              templateUrl: 'views/staff/staff-add.html',
              data : { title: 'Staff Add' },
               controller: 'staffAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.edit', {
              url: '/edit/:id',
              templateUrl: 'views/staff/staff-add.html',
              data : { title: 'Staff Edit' },
               controller: 'staffAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.note', {
              url: '/:staff_id/note',
              templateUrl: 'views/staff/note.html',
              data : { title: 'Note' },
              controller: 'noteListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.noteAdd', {
              url: '/:staff_id/note',
              templateUrl: 'views/staff/note-add.html',
              data : { title: 'Note Add' },
              controller: 'noteAddeditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.noteEdit', {
              url: '/:staff_id/note/:note_id',
             templateUrl: 'views/staff/note-add.html',
              data : { title: 'Staff Edit' },
                controller: 'noteAddeditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

           .state('staff.timeoff', {
            url: '/:staff_id/timeoff',
            templateUrl: 'views/staff/timeoff.html',
            data : { title: 'Time Off' },
            controller: 'timeoffListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
          })

           .state('staff.timeoffAdd', {
              url: '/:staff_id/timeoff',
              templateUrl: 'views/staff/timeoff-add.html',
              data : { title: 'Timeoff Add' },
              controller: 'timeoffAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('staff.timeoffEdit', {
              url: '/:staff_id/timeoff/:timeoff_id',
             templateUrl: 'views/staff/timeoff-add.html',
              data : { title: 'Timeoff Edit' },
                controller: 'timeoffAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })


            .state('vendor.list', {
              url: '/list',
              templateUrl: 'views/vendor/vendor.html',
              data : { title: 'Vendor' },
              controller: 'vendorListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

            .state('vendor.add', {
              url: '/add',
              templateUrl: 'views/vendor/vendor-add.html',
              data : { title: 'Vendor Add' },
               controller: 'vendorAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('vendor.edit', {
              url: '/edit/:id',
             templateUrl: 'views/vendor/vendor-add.html',
              data : { title: 'Vendor Edit' },
               controller: 'vendorAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })


          .state('account.list', {
              url: '/list',
              templateUrl: 'views/account/list.html',
              controller: 'accountListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
              
            })
           .state('account.add', {
              url: '/add',
              templateUrl: 'views/account/add.html',
              controller: 'accountAddCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
           .state('account.edit', {
              url: '/edit/:id',
              templateUrl: 'views/account/add.html',
              controller: 'accountEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
           .state('account.delete', {
              url: '/delete/:id',
              controller: 'accountEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

            .state('product.list', {
              url: '/list',
              templateUrl: 'views/product/product.html',
              data : { title: 'Product' },
              controller: 'productListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

            .state('product.add', {
              url: '/add',
              templateUrl: 'views/product/product-add.html',
              data : { title: 'Product Add' },
               controller: 'productAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('product.edit', {
              url: '/edit/:id',
              templateUrl: 'views/product/product-add.html',
              data : { title: 'Product Edit' },
               controller: 'productAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
            .state('client', {
              url: '/front/client',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

            .state('client.list', {
              url: '/list',
              templateUrl: 'views/front/client/list.html',
              controller: 'clientListCtrl',
              data : { title: 'Client listing' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
            .state('client.add', {
              url: '/add',
              templateUrl: 'views/front/client/add.html',
               controller: 'clientAddCtrl',
              data : { title: 'Client listing' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
            .state('client.edit', {
              url: '/edit/:id',
              templateUrl: 'views/front/client/edit.html',
               controller: 'clientEditCtrl',
              data : { title: 'Client Edit' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
            .state('setting.price', {
              url: '/price',
              templateUrl: 'views/setting/price.html',
              data : { title: 'Price Grid' },
              controller: 'priceListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

            .state('setting.priceedit', {
              url: '/edit/:id',
              templateUrl: 'views/setting/price-add.html',
              data : { title: 'Price Edit' },
               controller: 'priceAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               //return AuthService.checksession();
                            },
                       }
            })

            .state('setting.placement', {
              url: '/placement',
              templateUrl: 'views/setting/placement.html',
              data : { title: 'Misc' },
              controller: 'XeditableCtrl',
              resolve: load(['xeditable','scripts/controllers/xeditable.js'])
            })

            /*.state('setting.priceadd', {
              url: '/add',
               templateUrl: 'views/setting/price-add.html',
               controller: 'priceAddEditCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })*/

              .state('misc.value1', {
              url: '/value1',
              templateUrl: 'views/misc/value1.html',
              data : { title: 'Misc' },
              controller: 'XeditableCtrl',
              resolve: load(['xeditable','scripts/controllers/xeditable.js'])
            })

              .state('misc.value2', {
              url: '/value2',
              templateUrl: 'views/misc/value2.html',
              data : { title: 'Misc' },
             controller: 'XeditableCtrl',
              resolve: load(['xeditable','scripts/controllers/xeditable.js'])
            })

              .state('misc.value3', {
              url: '/value3',
              templateUrl: 'views/misc/value3.html',
              data : { title: 'Misc' },
             controller: 'XeditableCtrl',
              resolve: load(['xeditable','scripts/controllers/xeditable.js'])
            })
            .state('purchase', {
                  url: '/front/purchase',
                views: {
                  '': {
                    templateUrl: 'views/layout.html'
                  },
                  'aside': {
                    templateUrl: 'views/aside.html'
                  },
                  'content': {
                    templateUrl: 'views/content.html'
                  }
                }
              })
            .state('purchase.list', {
              url: '/list/:id',
              templateUrl: 'views/front/purchase/index.html',
              data : { title: 'Purchase Listing' },
             controller: 'PurchaseListCtrl',
              resolve: load(['xeditable','scripts/controllers/purchase.js'])
            })
            .state('purchase.po', {
              url: '/po/:id',
              templateUrl: 'views/front/purchase/po.html',
              data : { title: 'Purchase Orders' },
             controller: 'PurchasePOCtrl',
              resolve: load(['xeditable','scripts/controllers/purchase.js'])
            })
            .state('purchase.sg', {
              url: '/sg/:id',
              templateUrl: 'views/front/purchase/sg.html',
              data : { title: 'Supplied Garments' },
             controller: 'PurchasePOCtrl',
              resolve: load(['xeditable','scripts/controllers/purchase.js'])
            })
            .state('purchase.ce', {
              url: '/ce/:id',
              templateUrl: 'views/front/purchase/ce.html',
              data : { title: 'Contract Embrodiery' },
             controller: 'PurchaseCECtrl',
              resolve: load(['xeditable','scripts/controllers/purchase.js'])
            })
            .state('purchase.cp', {
              url: '/cp/:id',
              templateUrl: 'views/front/purchase/cp.html',
              data : { title: 'Contract Printing' },
             controller: 'PurchaseCPCtrl',
              resolve: load(['xeditable','scripts/controllers/purchase.js'])
            })

              .state('order', {
              url: '/front/order',
            views: {
              '': {
                templateUrl: 'views/layout.html'
              },
              'aside': {
                templateUrl: 'views/aside.html'
              },
              'content': {
                templateUrl: 'views/content.html'
              }
            }
          })

            .state('order.list', {
              url: '/list',
              templateUrl: 'views/front/order/list.html',
              controller: 'orderListCtrl',
              data : { title: 'Order listing' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })

             .state('order.add', {
              url: '/add',
              templateUrl: 'views/front/order/add.html',
               controller: 'orderAddCtrl',
              data : { title: 'Order Add' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })
            .state('order.edit', {
              url: '/:id/edit/:client_id',
              templateUrl: 'views/front/order/edit.html',
               controller: 'orderEditCtrl',
              data : { title: 'Order Edit' },
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            },
                       }
            })


;

          function load(srcs, callback) {
            return {
                deps: ['$ocLazyLoad', '$q',
                  function( $ocLazyLoad, $q ){
                    var deferred = $q.defer();
                    var promise  = false;
                    srcs = angular.isArray(srcs) ? srcs : srcs.split(/\s+/);
                    if(!promise){
                      promise = deferred.promise;
                    }
                    angular.forEach(srcs, function(src) {
                      promise = promise.then( function(){
                        angular.forEach(MODULE_CONFIG, function(module) {
                          if( module.name == src){
                            if(!module.module){
                              name = module.files;
                            }else{
                              name = module.name;
                            }
                          }else{
                            name = src;
                          }
                        });
                        return $ocLazyLoad.load(name);
                      } );
                    });
                    deferred.resolve();
                    return callback ? promise.then(function(){ return callback(); }) : promise;
                }]
            }
          }
      }
    ]
  );
