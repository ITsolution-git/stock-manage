(function () {
    angular.module('app.services', [])

.factory('AuthService', function ($rootScope,$location, $http,$state, $q,flash,sessionService) {
    var currentUser = {}
    return {
        checksession: function (option) {
                $http.get('api/public/auth/session').success(function(result, status, headers, config) 
                {
                    if(result.data.success == '0') 
                    {
                         flash('danger','Please login first.'); 
                         $location.url('/access/signin');
                         return false;
                    } 
                    else 
                    {
                        $rootScope.username = result.data.username;
                    }
                });
            },
            AccessService: function (ret) {
                console.log(ret);
                if(sessionService.get('role_slug')!=ret)
                {
                    console.log('error');
                    $state.go('app.dashboard');
                   // $location.url('/app/dashboard');
                    return false;
                }
            },
        }
    })
    .factory('sessionService', [
                '$rootScope', '$state', '$http', function ($rootScope, $state, $http) {
                    return {
                        set: function (key, value) {
                            return sessionStorage.setItem(key, value);
                        },
                        get: function (key) {
                            return sessionStorage.getItem(key);
                        },
                        remove: function (key) {
                            return sessionStorage.removeItem(key);
                        },
                        destroy: function () {
                            $rootScope.showLogged = false;
                            $state.go('access.lockme');
                            return false;
                        }
                    };
                }
            ]);
}).call(this);
