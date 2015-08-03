(function () {
    angular.module('app.services', [])
            /**
             * Notify Service
             * @param {type} logger
             * @returns {services_L7.servicesAnonym$2}
             */
            .factory('notifyService', [
                'logger', function (logger) {
                    return {
                        notify: function (type, message) {
                            switch (type) {
                                case 'info':
                                    return logger.log(message);
                                case 'success':
                                    return logger.logSuccess(message);
                                case 'error':
                                    return logger.logError(message);
                            }
                        }
                    };
                }
            ])
            /**
             * Login Service
             * @param {type} $route
             * @param {type} $rootScope
             * @param {type} $state
             * @param {type} $http
             * @param {type} $location
             * @param {type} notifyService
             * @param {type} sessionService
             * @param {type} $q
             * @returns {services_L25.servicesAnonym$4}
             */
            .factory('loginService', [
                '$route', "$rootScope", '$state', '$http', '$location', 'notifyService', 'sessionService', '$q',
                function ($route, $rootScope, $state, $http, $location, notifyService, sessionService, $q) {
                    return {
                        login: function (user_data) {
                            var request = $http({
                                method: "post",
                                url: $state.ENV.apiEndpoint + "user/login",
                                data: {
                                    email: user_data.email,
                                    password: user_data.password,
                                    rememberme: user_data.rememberme
                                },
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                            });
                            var rememberme = user_data.rememberme;
                            // Check whether the HTTP Request is successful or not. 
                            request.success(function (data) {
                                notifyService.notify(data.status, data.message);
                                if (data.status !== 'success') {
                                    $rootScope.showLogged = false;
                                    return false;
                                }
                                else {
                                    if (rememberme == 1)
                                    {
                                        document.cookie = "email=" + data.email;
                                    }
                                    $rootScope.showLogged = true;
                                    sessionService.set('currentUserName', data.username);
                                    sessionService.set('currentUser', data.uid);
                                    sessionService.set('currentUserType', data.user_type);
                                    sessionService.set('currentUserImage', data.user_photo);
                                    
                                    window.location.reload();
                                   
                                }
                            });
                        },
                        logout: function () {
                            sessionService.remove('currentUserName');
                            sessionService.remove('currentUser');
                            sessionService.remove('currentUserType');
                            sessionService.destroy();
                            $rootScope.showLogged = false;
                            var data = {"status": "info", "message": "You are logged Out successfully"}

                            notifyService.notify(data.status, data.message);
                            if (data.status !== 'success') {
                                $location.url('/pages/signin?msg=logged_out');
                                $rootScope.showLogged = false;
                                return false;
                            }
                        },
                        islogged: function () {
                            var $checkSessionServer = $http.get($state.ENV.apiEndpoint + "auth/session");
                            var deferred = $q.defer();
                            ($checkSessionServer).then(function (result) {
                                if (result.data.validated === false) {
                                    deferred.reject("notlogin");
                                    $location.url('/pages/signin?msg=login_first');
                                }
                                else {
                                    //  $location.url('/dashboard?msg=logged_in');
                                }
                                return deferred.promise;
                            })
                        },
                        isloggedData: function () {
                            alert('hardik');
                            var $checkSessionServer = $http.get($state.ENV.apiEndpoint + "auth/session");
                            var i = 0;
                            var deferred = $q.defer();
                            ($checkSessionServer).then(function (result) {
                                console.log(result);exit;
                                if (result.data.validated === false) {
                                    deferred.reject("notlogin");
                                    $location.url('/access/signin?msg=login_first');
                                }
                                else {

                                    $location.url('/app/dashboard?msg=logged_in');
                                }
                                return deferred.promise;
                            })
                        },
                        getCurrentUser: function () {
                            return sessionService.get('currentUserName');
                        }
                        /* This can be a way to check and resolve dependency
                         * ,
                         checklogged: function() {
                         var deferred = $q.defer();
                         deferred.reject("Allo!");
                         $location.url('/pages/signin?msg:logged_out');
                         return deferred.promise;
                         } */
                    };
                }
            ])
            /**
             * Forget Password Service
             * @param {type} $state
             * @param {type} $http
             * @param {type} $location
             * @param {type} notifyService
             * @returns {services_L127.servicesAnonym$7}
             */
            .factory('forgotPasswordService', [
                '$state', '$http', '$location', 'notifyService',
                function ($state, $http, $location, notifyService) {
                    return {
                        forgotPass: function (user_data) {
                            var pageLink = window.location.origin + window.location.pathname + '#/pages/reset-password';
                            var request = $http({
                                method: "post",
                                url: $state.ENV.apiEndpoint + "user/forgot_password",
                                data: {
                                    email: user_data.email,
                                    ref: pageLink
                                },
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                            });
                            // Check whether the HTTP Request is successful or not. 
                            request.success(function (data) {
                                notifyService.notify(data.status, data.message);
                                if (data.status !== 'success') {
                                    return false;
                                }
                                else {
                                    $location.url('/pages/signin?msg=success');
                                }
                            });
                        }
                    };
                }
            ])
            /**
             * Session Service
             * @param {type} $rootScope
             * @param {type} $state
             * @param {type} $http
             * @returns {services_L156.servicesAnonym$10}
             */
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
                            $http.get($state.ENV.apiEndpoint + "auth/logout");
                        }
                    };
                }
            ])
            /**
             * Reset Password
             * @param {type} $state
             * @param {type} $http
             * @param {type} $location
             * @param {type} notifyService
             * @param {type} sessionService
             * @returns {services_L177.servicesAnonym$12}
             */
            .factory('resetPasswordService', [
                '$state', '$http', '$location', 'notifyService', 'sessionService',
                function ($state, $http, $location, notifyService, sessionService) {
                    return {
                        resetPass: function (user_data) {
                            if (user_data.req_type === 'reset_password') {
                                var reqName = 'reset_password';
                            }
                            if (user_data.req_type === 'activate_user') {
                                var reqName = 'activate';
                            }

                            var request = $http({
                                method: "post",
                                url: $state.ENV.apiEndpoint + "user/" + reqName,
                                data: {
                                    password: user_data.password,
                                    act_key: user_data.act_key
                                },
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                            });
                            // Check whether the HTTP Request is successful or not. 
                            request.success(function (data) {
                                notifyService.notify(data.status, data.message);
                                if (data.status !== 'success') {
                                    return false;
                                }
                                if (typeof data.uid !== 'undefined' && data.uid !== '') {
                                    sessionService.set('currentUser', data.uid);
                                    $location.url('/dashboard?msg=activated');
                                }
                                else {
                                    $location.url('/pages/signin?msg=reset_pass');
                                }
                            });
                        }
                    };
                }
            ])
            /**
             * Data Service
             * @param {type} $http
             * @returns {services_L216.servicesAnonym$15}
             */
            .factory('dataService', [
                '$http', function ($http) {
                      return {
                            get: function (url) {
                                  return $http.get(url).then(function (resp) {
                                        return resp.data; // success callback returns this
                                  });
                            },
                        post: function (url, params) {
                            return $http.post(url, params).then(function (resp) {
                                        return resp.data; // success callback returns this
                                  });
                        }
                      };
                }
            ])
            /**
             * Check the permission of the User
             * @param {type} $http
             * @param {type} $q
             * @param {type} $state
             * @param {type} $cacheFactory
             * @param {type} $location
             * @returns {services_L234.servicesAnonym$17}
             */
            .factory('permissionService', ['$http', '$q', '$state',
                function ($http, $q, $state) {
                    return {
                        permission: function (Id) {
                            var deferred = $q.defer();
                            // console.log($state.ENV.apiEndpoint + "user/checkUserPermissions/" + Id);
                            $http.post($state.ENV.apiEndpoint + "user/checkUserPermissions/" + Id)
                                    .success(function (data, status) {
                                        deferred.resolve(data);
                                    })
                                    .error(function (data, status) {
                                        deferred.reject(data);
                                    });
                            return deferred.promise
                        }
                    }
                }]);
}).call(this);
