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

// ADMIN COMPANY ROUTERS
Route::get('admin/account', 'AccountController@listData');
Route::get('admin/account/list', 'AccountController@listData');
Route::post('admin/account/add', 'AccountController@addData');
Route::get('admin/account/edit/{id}', 'AccountController@GetData');
Route::post('admin/account/save', 'AccountController@SaveData');
Route::post('admin/account/delete', 'AccountController@DeleteData');

// ADMIN STAFF ROUTERS
Route::get('admin/staff', 'StaffController@index');
Route::post('admin/staffAdd', 'StaffController@add');
Route::post('admin/staffEdit', 'StaffController@edit');
Route::post('admin/staffDelete', 'StaffController@delete');
Route::post('admin/staffDetail', 'StaffController@detail');
Route::get('admin/StaffEdit/{id}', 'StaffController@detail');

// ADMIN NOTE ROUTERS
Route::post('admin/staff/note', 'StaffController@note');
Route::post('admin/staff/noteDelete', 'StaffController@noteDelete');
Route::post('admin/staff/noteAdd', 'StaffController@noteAdd');
Route::post('admin/staff/noteEdit', 'StaffController@noteEdit');
Route::post('admin/staff/noteDetail', 'StaffController@notedetail');

// ADMIN TIME OFF ROUTERS
Route::post('admin/staff/timeoff', 'StaffController@timeoff');
Route::post('admin/staff/timeoffDelete', 'StaffController@timeoffDelete');
Route::post('admin/staff/timeoffAdd', 'StaffController@timeoffAdd');
Route::post('admin/staff/timeoffEdit', 'StaffController@timeoffEdit');
Route::post('admin/staff/timeoffDetail', 'StaffController@timeoffdetail');


// ADMIN Vendor ROUTERS
Route::get('admin/vendor', 'VendorController@index');
Route::post('admin/vendorDelete', 'VendorController@delete');
Route::post('admin/vendorAdd', 'VendorController@add');
Route::post('admin/vendorEdit', 'VendorController@edit');
Route::post('admin/vendorDetail', 'VendorController@detail');
Route::get('admin/VendorEdit/{id}', 'VendorController@detail');

// ADMIN PRODUCT ROUTERS
Route::get('admin/product', 'ProductController@index');
Route::post('admin/productAdd', 'ProductController@add');
Route::post('admin/productEdit', 'ProductController@edit');
Route::post('admin/productDelete', 'ProductController@delete');
Route::post('admin/productDetail', 'ProductController@detail');
Route::get('admin/productEdit/{id}', 'ProductController@detail');

// ADMIN SETTING ROUTERS
Route::get('admin/price', 'SettingController@price');
Route::post('admin/priceDelete', 'SettingController@delete');
Route::post('admin/priceGridDuplicate', 'SettingController@priceGridDuplicate');
Route::post('admin/priceEdit', 'SettingController@priceEdit');
Route::post('admin/priceDetail', 'SettingController@priceDetail');
Route::get('admin/priceEdit/{id}', 'SettingController@priceDetail');
Route::post('admin/priceGridPrimaryDuplicate', 'SettingController@priceGridPrimaryDuplicate');
Route::post('admin/priceSecondary', 'SettingController@priceSecondary');

// ADMIN MISC ROUTERS
Route::post('admin/miscSave', 'MiscController@miscSave');
Route::post('admin/placementSave', 'SettingController@placementSave');
Route::post('admin/placementInsert', 'SettingController@placementInsert');



// COMMON CONTROLLER 
Route::get('common/getAdminRoles', 'CommonController@getAdminRoles');
Route::get('common/type/{type}', 'CommonController@type');
Route::get('common/staffRole', 'CommonController@getStaffRoles');
Route::get('common/checkemail/{email}', 'CommonController@checkemailExist');
Route::get('auth/session', 'LoginController@check_session');
Route::get('auth/logout', 'LoginController@logout');
Route::get('common/getAllVendors', 'CommonController@getAllVendors');
Route::get('common/getAllMiscData', 'CommonController@getAllMiscData');
Route::get('common/getAllMiscDataWithoutBlank', 'CommonController@getAllMiscDataWithoutBlank');
Route::get('common/GetMicType/{type}', 'CommonController@GetMicType');
Route::get('common/getStaffList', 'CommonController@getStaffList');
Route::get('common/getAllPlacementData', 'CommonController@getAllPlacementData');
Route::get('common/getMiscData', 'CommonController@getMiscData');



Route::get('common/getAllPlacementData', 'CommonController@getAllPlacementData');
Route::get('common/getMiscData', 'CommonController@getMiscData');

Route::post('common/InsertRecords', 'CommonController@InsertRecords'); // INSERT RECORD FOR ANY TABLE, @PARAMS: TABLE,POST ARRAY.
Route::post('common/GetTableRecords', 'CommonController@GetTableRecords'); // GET RECORD FOR ANY SINGLE TABLE, @PARAMS: TABLE,COND ARRAY.
Route::post('common/UpdateTableRecords', 'CommonController@UpdateTableRecords'); // UPDATE RECORD FOR ANY TABLE, @PARAMS: TABLE,COND, POST ARRAY.
Route::post('common/DeleteTableRecords', 'CommonController@DeleteTableRecords'); // DELETE RECORD FOR ANY TABLE, @PARAMS: TABLE,COND, POST ARRAY.
Route::get('common/getBrandCo', 'CommonController@getBrandCo');


// CLIENT CONTROLLER 
Route::post('client/addclient', 'ClientController@addclient');
Route::get('client/ListClient', 'ClientController@ListClient');
Route::post('client/DeleteClient', 'ClientController@DeleteClient');
Route::post('client/ClientContacts', 'ClientController@ClientContacts');
Route::post('client/getContacts', 'ClientController@getContacts');
Route::post('client/ClientAddress', 'ClientController@ClientAddress');
Route::post('client/getAddress', 'ClientController@getAddress');
Route::get('client/GetclientDetail/{id}','ClientController@GetclientDetail');
Route::post('client/SaveSalesDetails', 'ClientController@SaveSalesDetails');
Route::post('client/SaveCleintDetails', 'ClientController@SaveCleintDetails');
Route::post('client/SaveCleintTax', 'ClientController@SaveCleintTax');
Route::post('client/SaveCleintPlimp', 'ClientController@SaveCleintPlimp');

Route::get('client/GetNoteDetails/{id}', 'ClientController@GetNoteDetails');
Route::post('client/SaveCleintNotes', 'ClientController@SaveCleintNotes');
Route::get('client/EditCleintNotes/{id}', 'ClientController@EditCleintNotes');
Route::get('client/DeleteCleintNotes/{id}', 'ClientController@DeleteCleintNotes');
Route::get('client/GetClientDetailById/{id}', 'ClientController@GetClientDetailById');
Route::post('client/UpdateCleintNotes', 'ClientController@UpdateCleintNotes');
Route::post('client/SaveDistAddress', 'ClientController@SaveDistAddress');
Route::post('client/getDistAdressDetail', 'ClientController@getDistAdressDetail');

//PURCHASE CONTROLLER
Route::get('purchase/ListPurchase/{id}', 'PurchaseController@ListPurchase');
Route::get('purchase/GetPodata/{id}', 'PurchaseController@GetPodata');
Route::get('purchase/GetSgData/{id}', 'PurchaseController@GetSgData');
Route::get('purchase/ChangeOrderStatus/{id}/{value}', 'PurchaseController@ChangeOrderStatus');
Route::post('purchase/EditOrderLine', 'PurchaseController@EditOrderLine');
Route::post('purchase/Receive_order', 'PurchaseController@Receive_order');
Route::post('purchase/Update_shiftlock', 'PurchaseController@Update_shiftlock');
Route::get('purchase/short_over/{id}', 'PurchaseController@short_over');


// ORDER CONTROLLER 
Route::get('order/listOrder', 'OrderController@listOrder');
Route::post('order/deleteOrder', 'OrderController@deleteOrder');
Route::post('order/orderAdd', 'OrderController@add');
Route::post('order/orderEdit', 'OrderController@edit');
Route::post('order/orderDetail', 'OrderController@orderDetail');
Route::get('order/getOrderNoteDetails/{id}','OrderController@getOrderNoteDetails');
Route::get('order/getOrderDetailById/{id}', 'OrderController@getOrderDetailById');
Route::post('order/updateOrderNotes', 'OrderController@updateOrderNotes');
Route::post('order/saveOrderNotes', 'OrderController@saveOrderNotes');
Route::get('order/deleteOrderNotes/{id}', 'OrderController@deleteOrderNotes');

// FINISHING CONTROLLER 
Route::get('finishing/listFinishing', 'FinishingController@listFinishing');
Route::post('finishing/listFinishing', 'FinishingController@listFinishing');
Route::post('finishing/deleteFinishing', 'FinishingController@deleteFinishing');
Route::post('finishing/updateFinishing', 'FinishingController@updateFinishing');
Route::post('finishing/orderAdd', 'FinishingController@add');
Route::post('finishing/orderEdit', 'FinishingController@edit');
