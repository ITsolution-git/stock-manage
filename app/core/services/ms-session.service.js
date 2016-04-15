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
			var logout = $resource('api/public/auth/logout', null, 
				{
					post : {
						method : 'get'
					}
				});
			logout.post(null, function(response) {
				notifyService.notify('success',response.data.message);				
				remove('useremail');
                remove('role_slug');
                remove('login_id');
                remove('name');
                remove('user_id');
                remove('role_title');
                remove('username');
                remove('password');
                remove('company_id');
                remove('company');
                $state.go('app.login');
			},function(response) {
				notifyService.notify('error',response.data.message);
			});
		}	

		function AccessService(ret)
		{
            var role = get('role_slug');
            //console.log(ret + ' - '+role);
            if(ret.indexOf(role) <= -1 && ret != 'ALL' && ret!='')
            {
               // console.log('error');
                var data = {"status": "error", "message": "You are Not authorized, Please wait"}
                notifyService.notify(data.status, data.message);
               	//setTimeout(function(){ 
                //window.open('dashboard', '_self'); }, 1000);
                //return false;
            }
		}

	}
	
})();