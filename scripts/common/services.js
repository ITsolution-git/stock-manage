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
                        $state.go('access.signin');
                        return false;
                    } 
                    else 
                    {
                        $rootScope.username = result.data.username;
                        return true;
                    }
                });
            },
            AccessService: function (ret) {
                //console.log(ret);
                var role = sessionService.get('role_slug');
                console.log('Permission Allow for Role - '+role)
                if(ret.indexOf(role) <= -1 && ret != 'ALL')
                {
                    console.log('error');
                    $state.go('app.dashboard');
                    setTimeout(function(){ window.location.reload(); }, 200);
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
            ])

    .filter('dateWithFormat', function($filter) {
     
         return function(input)
         {
          if(input == null){ return ""; } 
     
          if(input !=  "0000-00-00 00:00:00")
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
      .factory('Common_Misc', [
                '$rootScope', '$state', '$http', function ($rootScope, $state, $http) {
    return {

        GetMicType: function (art_type) {
                      
                $http.get('api/public/common/GetMicType/'+art_type).success(function(result, status, headers, config) 
                {
                    var totalData = {};
                    console.log(result.data);
                    if(result.data.success == '0') 
                    {
                        totalData =result.data;
                    } 
                    
                });
                return sharedService = {thing:totalData};
               
            }
        }
        }
       ])


}).call(this);





