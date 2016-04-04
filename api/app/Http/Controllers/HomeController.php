<?php 
namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');





/**
 * @SWG\Swagger(
 *      @SWG\Info(title="Stokkup", version="1.0",
 *      @SWG\Contact(name="codal api team", url="http://104.236.31.186/"),
 *      @SWG\License(name="Codal Systems", url="http://104.236.31.186/")
 *      ),
 *      host="localhost",
 *      basePath = "/stokkup",
 *      consumes = {"application/json", "multipart/form-data"}
 * )
 * 
 * @SWG\Tag(
 *  name="Login",
 *  description="Mange Login and Logout",
 * )
 * @SWG\Tag(
 *  name="Order",
 *  description="Order Module",
 * )
 * @SWG\Tag(
 *  name="Staff",
 *  description="Staff Module",
 * )
 * @SWG\Tag(
 *  name="Product",
 *  description="Product Module",
 * )
 * @SWG\Tag(
 *  name="Vendor",
 *  description="Vendor Module",
 * )
 * @SWG\Tag(
 *  name="Setting",
 *  description="Setting Module",
 * )
 * @SWG\Tag(
 *  name="Misc",
 *  description="Misc Module",
 * )
 * @SWG\Tag(
 *  name="API",
 *  description="API Module",
 * )
 * @SWG\Tag(
 *  name="Client",
 *  description="Client Module",
 * )
 * @SWG\Tag(
 *  name="Purchasing",
 *  description="Purchasing Module",
 * )
 * @SWG\Tag(
 *  name="Art",
 *  description="Art Module",
 * )
 * @SWG\Tag(
 *  name="Finishing",
 *  description="Finishing Module",
 * )
  * @SWG\Tag(
 *  name="Shipping",
 *  description="Shipping Module",
 * )
 * 
 *
 */


class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

}
