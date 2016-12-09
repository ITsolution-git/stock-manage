<?php namespace App\Http\Middleware;

use Closure;
use Request;
use App\Common;

class checkUserMiddleware {

	public function __construct(Common $common)
    {
        $this->common = $common;
    }

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

        $userData = $this->common->getUserRole($UserId);
        $role = $userData[0]->role;

		$action = $request->route()->getAction();

		if($action['role'] == 'ALL')
		{
			return $next($request);
		}

		if($action['action'] == 'false')
		{
			if(in_array($role, $action['role']))
			{
				$data = json_encode(array("success"=>0,'message' =>'You have no rights to access this'));
            	print_r($data);
          		exit;
			}
			else
			{
				return $next($request);
			}
			
		}
		if($action['action'] == 'true')
		{
			if(in_array($role, $action['role']))
			{
				return $next($request);
			}
			else
			{
				$data = json_encode(array("success"=>0,'message' =>'You have no rights to access this'));
            	print_r($data);
          		exit;
			}
		}
	}

}
