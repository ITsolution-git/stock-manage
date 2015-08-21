'use strict';

(function () {
    angular.module('app.services', [])
            /**
             * Notify Service
             * @param {type} logger
             * @returns {services_L7.servicesAnonym$2}
             */
            .factory('company', [ '$route', "$rootScope", '$state', '$http', 

                function ($route, $rootScope, $state, $http) {
                    return {
                        list: function (type, message) {
                            alert(2345);
                        }
                    };
                }
            ])
           
}).call(this);
