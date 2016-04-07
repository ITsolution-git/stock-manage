(function() {
	'use strict',
	angular
	.module('app.core')
	.factory('sessionService', sessionService);

	/* ngInject */
	function sessionService($state, $resource, notifyService,$rootScope) {
		var service = {
			set : set,
			get : get,
			remove : remove,
			destroy : destroy,
			AccessService: AccessService
		};

		return service;

		function set(key, value) {
			return sessionStorage.setItem(key, value);
		};

		function get(key) {
			return sessionStorage.getItem(key);
		};

		function remove(key) {
			return sessionStorage.removeItem(key);
		}

		function destroy() {
			var logout = $resource($state.ENV.apiEndpoint + 'auth/logout', null, 
				{
					post : {
						method : 'post'
					}
				});
			logout.post(null, function(response) {
				notifyService.notify('success',response.message);				
				sessionStorage.removeItem('uid');
				$state.go('app.pages_login');
			},function(response) {

			});
		}	

		function AccessService(ret)
		{
			var access = $resource('api/public/auth/session', null, 
				{
					get : {
						method : 'get'
					}
				});
			access.get(null, function(result) {


            
            if(result.data.success == '0') 
            {
                var data = {"status": "error", "message": "Please signin first."}
                notifyService.notify(data.status, data.message);
                $rootScope.company_profile =  {company_id:'28'};
                set('user_id','28');
                console.log($rootScope.company_profile);
                //$state.go('app.login');
                //return false;
            } 
            else 
            {
                //$rootScope.email = result.data.email;
                //sessionService.set('role_slug',result.data.role_session);
               // sessionService.set('user_id',result.data.user_id);
                //$rootScope.company_profile =  result.data.company;
               // sessionService.set('company_id','28');
                var role = result.data.role_session;
               // console.log('Permission Allow for Role - '+role);
                if(ret.indexOf(role) <= -1 && ret != 'ALL' && ret!='')
                {
                   // console.log('error');
                    var data = {"status": "error", "message": "You are Not authorized, Please wait"}
                    notifyService.notify(data.status, data.message);
                    $location.url('/app/dashboard');
                    setTimeout(function(){ window.location.reload(); }, 200);
                    return false;
                }
            }

			},function(result) {

			});


      
             
	
		}

	}
	
})();