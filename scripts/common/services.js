(function () {
    angular.module('app.services', [])

.factory('AuthService', function ($rootScope,$http,$state,sessionService,notifyService,$q) {
                    var currentUser = {}
                    

    return {
        checksession: function (option) {

                var deferred = $q.defer();
                $http.get('api/public/auth/session').success(function(result, status, headers, config) 
                {
                    deferred.resolve(result.data);
                    if(result.data.success == '0') 
                    {
                        var data = {"status": "error", "message": "Please signin first."}
                        notifyService.notify(data.status, data.message);
                        $state.go('access.signin');
                        return false;
                    } 
                    else 
                    {
                       // console.log('Login with - '+result.data.username);
                        $rootScope.username = result.data.username;
                        sessionService.set('role_slug',result.data.role_session);
                        return true;
                    }
                });
                return deferred.promise;
            },
            AccessService: function (ret) {
                var role = sessionService.get('role_slug');
                //console.log('Permission Allow for Role - '+role);
                if(ret.indexOf(role) <= -1 && ret != 'ALL')
                {
                    //console.log('error');
                    var data = {"status": "error", "message": "You are Not authorized, Please wait"}
                    notifyService.notify(data.status, data.message);
                    $state.go('app.dashboard');
                    setTimeout(function(){ window.location.reload(); }, 200);
                    return false;
                }
             },
            CompanyService: function () {
             $rootScope.company_profile={};
             var deferred = $q.defer();
             var user_data = {};
             user_data.user_id=sessionService.get('user_id');
             if(sessionService.get('role_slug')!='SA' && sessionService.get('role_slug')!='' && sessionService.get('role_slug')!=null)
             {
                 $http.post('api/public/auth/company',user_data).then(function(Response) 
                  {
                          deferred.resolve(Response.data);
                          
                          if(Response.data.data.success=='1')
                          {
                              $rootScope.company_profile =  Response.data.data.records;
                              //console.log('ajax call');
                             
                          }
                          else
                          {
                              var data = {"status": "error", "message": "Company not assigned!"}
                              notifyService.notify(data.status, data.message);
                              $state.go('access.lockme');
                              return false;
                          }
                       
                     });
                 return deferred.promise;
             }
            }
                
        }
    })
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
    .factory('sessionService', [
                '$rootScope', '$state', '$http','$q', function ($rootScope, $state, $http,$q) {
                    var deferred = $q.defer();
                    return {
                        set: function (key, value) {
                            deferred.resolve(Response.data);
                            sessionStorage.setItem(key, value);
                            return deferred.promise;
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
                        },
  
                    };
                }
            ])

    .filter('dateWithFormat', function($filter) {
     
         return function(input)
         {
          if(input == null){ return ""; } 
     
          if(input !=  "0000-00-00 00:00:00" && input !="0000-00-00" )
          {
            var d1 = Date.parse(input);
           
          var _date = d1.toString('M/d/yyyy');
          }
          else {
            var _date = '';
          }
         
          return _date;

         };
    })
    


}).call(this);





