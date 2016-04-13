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
                $state.go('app.login');
			},function(response) {
				notifyService.notify('error',response.data.message);
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
                    sessionService.set('email',result.data.email);
                    sessionService.set('role_slug',result.data.role_session);
                    sessionService.set('user_id',result.data.user_id);
                    sessionService.set('company_id',result.data.company_id);
                    $rootScope.company_profile =  result.data.company;
                    var role = result.data.role_session;
		                var role = result.data.role_session;
		               // console.log('Permission Allow for Role - '+role);
		                if(ret.indexOf(role) <= -1 && ret != 'ALL' && ret!='')
		                {
		                   // console.log('error');
		                    var data = {"status": "error", "message": "You are Not authorized, Please wait"}
		                    notifyService.notify(data.status, data.message);
		                   	setTimeout(function(){ 
                            window.open('client', '_self'); }, 1000);
		                    return false;
		                }
            		}

			},function(result) {

			});


      
             
	
		}

	}
	
})();