<?php namespace App\Http\Middleware;

use Closure;
use Request;

class checkUserMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$token = Request::header('Authorization');
        $UserId = Request::header('AuthUserId');

		$action = $request->route()->getAction();

/*        print_r($action['role']);exit;
            dump($action['role']);
            die();*/
		return $next($request);
	}

}
