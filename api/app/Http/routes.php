<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');
/*
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
*/
Route::post('admin/login', 'LoginController@loginverify');
// ADMIN ACCOUNT ROUTERS
Route::get('admin/account', 'AccountController@listData');
Route::get('admin/account/list', 'AccountController@listData');
Route::post('admin/account/add', 'AccountController@addData');


// COMMON CONTROLLER 
Route::get('common/getAdminRoles', 'CommonController@getAdminRoles');

Route::get('auth/session', 'LoginController@check_session');
Route::get('auth/logout', 'LoginController@logout');