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


          $http.get('api/public/auth/session').success(function(result, status, headers, config) {
        
              if(result.data.success == '0') {

                $location.url('/access/signin');
                return false;

              } else {

                $rootScope.username = result.data.username;

              }
        });


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
              resolve: load('scripts/controllers/login.js')
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
              resolve: load('scripts/controllers/logout.js')
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

            .state('staff.list', {
              url: '/list',
              templateUrl: 'views/staff/staff.html',
              data : { title: 'Staff' },
              controller: 'staffListCtrl',
              resolve: load(['scripts/controllers/staff.js'])
            })

             .state('staff.add', {
              url: '/add',
              templateUrl: 'views/staff/staff-add.html',
              data : { title: 'Staff Add' },
               controller: 'staffAddEditCtrl',
              resolve: load(['scripts/controllers/bootstrap.js','scripts/controllers/staff.js'])
            })

             .state('staff.edit', {
              url: '/edit/:id',
              templateUrl: 'views/staff/staff-add.html',
              data : { title: 'Staff Edit' },
               controller: 'staffAddEditCtrl',
              resolve: load(['scripts/controllers/bootstrap.js','scripts/controllers/staff.js'])
            })

             .state('staff.note', {
              url: '/:staff_id/note',
              templateUrl: 'views/staff/note.html',
              data : { title: 'Note' },
              controller: 'noteListCtrl',
              resolve: load(['scripts/controllers/note.js'])
            })

             .state('staff.noteAdd', {
              url: '/:staff_id/note',
              templateUrl: 'views/staff/note-add.html',
              data : { title: 'Note Add' },
              controller: 'noteAddeditCtrl',
              resolve: load(['scripts/controllers/note.js'])
            })

             .state('staff.noteEdit', {
              url: '/:staff_id/note/:note_id',
             templateUrl: 'views/staff/note-add.html',
              data : { title: 'Staff Edit' },
                controller: 'noteAddeditCtrl',
              resolve: load(['scripts/controllers/note.js'])
            })

           .state('staff.timeoff', {
            url: '/:staff_id/timeoff',
            templateUrl: 'views/staff/timeoff.html',
            data : { title: 'Time Off' },
            controller: 'timeoffListCtrl',
            resolve: load(['scripts/controllers/timeoff.js'])
          })

           .state('staff.timeoffAdd', {
              url: '/:staff_id/timeoff',
              templateUrl: 'views/staff/timeoff-add.html',
              data : { title: 'Timeoff Add' },
              controller: 'timeoffAddEditCtrl',
             resolve: load(['scripts/controllers/bootstrap.js','scripts/controllers/timeoff.js'])
            })

             .state('staff.timeoffEdit', {
              url: '/:staff_id/timeoff/:timeoff_id',
             templateUrl: 'views/staff/timeoff-add.html',
              data : { title: 'Timeoff Edit' },
                controller: 'timeoffAddEditCtrl',
              resolve: load(['scripts/controllers/bootstrap.js','scripts/controllers/timeoff.js'])
            })


            .state('vendor.list', {
              url: '/list',
              templateUrl: 'views/vendor/vendor.html',
              data : { title: 'Vendor' },
              controller: 'vendorListCtrl',
              resolve: load(['scripts/controllers/vendor.js'])
            })

            .state('vendor.add', {
              url: '/add',
              templateUrl: 'views/vendor/vendor-add.html',
              data : { title: 'Vendor Add' },
               controller: 'vendorAddEditCtrl',
              resolve: load(['scripts/controllers/vendor.js'])
            })

             .state('vendor.edit', {
              url: '/edit/:id',
             templateUrl: 'views/vendor/vendor-add.html',
              data : { title: 'Vendor Edit' },
               controller: 'vendorAddEditCtrl',
              resolve: load(['scripts/controllers/vendor.js'])
            })


          .state('account.list', {
              url: '/list',
              templateUrl: 'views/account/list.html',
              controller: 'accountListCtrl',
              resolve: {
                            checklogin: function (AuthService) {
                               return AuthService.checksession();
                            }
                       }
              
            })
           .state('account.add', {
              url: '/add',
              templateUrl: 'views/account/add.html',
              controller: 'accountAddCtrl',
              resolve: load('scripts/controllers/account.js')
            })
           .state('account.edit', {
              url: '/edit/:id',
              templateUrl: 'views/account/add.html',
              controller: 'accountEditCtrl',
              resolve: load('scripts/controllers/account.js')
            })
           .state('account.delete', {
              url: '/delete/:id',
              controller: 'accountEditCtrl',
              resolve: load('scripts/controllers/account.js')
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
