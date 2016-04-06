(function() {
	'use strict',
	angular
	.module('app.core')
	.factory('sessionService', sessionService);

	/* ngInject */
	function sessionService($state, $resource, notifyService) {
		var service = {
			set : set,
			get : get,
			remove : remove,
			destroy : destroy
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

	}
})();