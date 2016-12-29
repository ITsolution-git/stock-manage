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
Route::post('admin/forgot_password', 'LoginController@forgot_password');
Route::post('admin/check_user_password', 'LoginController@check_user_password');
Route::post('admin/change_password', 'LoginController@change_password');
Route::post('auth/session', 'LoginController@check_session');
Route::get('auth/logout', 'LoginController@logout');
Route::post('admin/loginUser', 'LoginController@loginUser');

// COMPANY ROUTERS
Route::get('admin/company', 'CompanyController@listData');
Route::post('admin/company/add', 'CompanyController@addData');
Route::get('admin/company/edit/{id}/{company_id}', 'CompanyController@GetData');
Route::post('admin/company/save', 'CompanyController@SaveData');
Route::post('admin/company/change_password', 'CompanyController@change_password');
Route::get('admin/company/getCompanyInfo/{company_id}', 'CompanyController@getCompanyInfo');
Route::get('admin/company/getAffiliate/{company_id}/{affiliate_id}', 'CompanyController@getAffiliate');
Route::post('admin/company/addAffilite', 'CompanyController@addAffilite');
Route::post('admin/company/UpdateAffilite', 'CompanyController@UpdateAffilite');
Route::post('admin/company/GetAllApi', 'CompanyController@GetAllApi');
Route::post('admin/company/deleteDataIphFactor', 'CompanyController@deleteDataIphFactor');


// COMPANY USERS ROUTERS
Route::get('admin/account', 'AccountController@listData');
Route::get('admin/account/list/{parent_id}', 'AccountController@listData');
Route::post('admin/account/add', 'AccountController@addData');
Route::get('admin/account/edit/{id}/{parent}', 'AccountController@GetData');
Route::post('admin/account/save', 'AccountController@SaveData');
Route::post('admin/account/delete', 'AccountController@DeleteData');
Route::post('admin/account/ResetPasswordMail', 'AccountController@ResetPasswordMail');

// ADMIN STAFF ROUTERS
Route::post('admin/staff', 'StaffController@index');
Route::post('admin/staffAdd', 'StaffController@add');
Route::post('admin/staffEdit', 'StaffController@edit');
Route::post('admin/staffDelete', 'StaffController@delete');
Route::post('admin/staffDetail', 'StaffController@detail');
Route::get('admin/StaffEdit/{id}', 'StaffController@detail');
Route::post('admin/staffNoteTimeoff', 'StaffController@staffNoteTimeoff');

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
Route::post('admin/vendor', 'VendorController@index');
Route::post('admin/vendorDelete', 'VendorController@delete');
Route::post('admin/vendorAdd', 'VendorController@add');
Route::post('admin/vendorEdit', 'VendorController@edit');
Route::post('admin/vendorDetail', 'VendorController@detail');
Route::get('admin/VendorEdit/{id}', 'VendorController@detail');
Route::post('admin/productVendor', 'VendorController@productVendor');


// ADMIN SETTING ROUTERS

Route::post('admin/price',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'SettingController@price'
]);

Route::post('admin/priceDelete',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@delete'
]);

Route::post('admin/priceGridDuplicate',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@priceGridDuplicate'
]);

Route::post('admin/priceEdit',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@priceEdit'
]);

Route::post('admin/priceDetail',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'SettingController@priceDetail'
]);

Route::post('admin/priceEdit/{id}',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'SettingController@priceDetail'
]);

Route::post('admin/priceGridPrimaryDuplicate',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@priceGridPrimaryDuplicate'
]);

Route::post('admin/priceSecondary',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'SettingController@priceSecondary'
]);

Route::post('admin/downloadPricegridCSV',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@downloadPricegridCSV'
]);

Route::post('admin/uploadPricingCSV',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@uploadPricingCSV'
]);

Route::get('admin/uploadSnsCSV',[
   'middleware' => 'check',
   'role' => array('SA'),
   'action' => 'true',
   'uses' => 'SettingController@uploadSnsCSV'
]);

Route::post('admin/downloadPriceGridExcel',[
   'middleware' => 'check',
   'role' => array('AM','CA'),
   'action' => 'true',
   'uses' => 'SettingController@downloadPriceGridExcel'
]);

Route::post('admin/getApprovedOrders', 'SettingController@getApprovedOrders');
Route::post('admin/getPendingOrders', 'SettingController@getPendingOrders');
Route::post('admin/getDeniedOrders', 'SettingController@getDeniedOrders');

// ADMIN MISC ROUTERS

Route::post('admin/miscSave',[
   'middleware' => 'check',
   'role' => array('SM'),
   'action' => 'false',
   'uses' => 'MiscController@miscSave'
]);

Route::post('admin/placementSave', 'SettingController@placementSave');
Route::post('admin/colorSave', 'SettingController@colorSave');
Route::post('admin/colorInsert', 'SettingController@colorInsert');

// COMMON CONTROLLER 
Route::get('common/type/{type}', 'CommonController@type');
Route::get('common/staffRole', 'CommonController@getStaffRoles');
Route::get('common/checkemail/{email}/{userid}', 'CommonController@checkemailExist');

Route::post('common/getAllMiscData',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'CommonController@getAllMiscData'
]);


Route::post('common/getAllMiscDataWithoutBlank', 'CommonController@getAllMiscDataWithoutBlank');
Route::get('common/GetMicType/{type}', 'CommonController@GetMicType');
Route::get('common/getStaffList/{id}', 'CommonController@getStaffList');
Route::get('common/getAllColorData', 'CommonController@getAllColorData');
Route::post('common/getCompanyDetail', 'CommonController@getCompanyDetail');
Route::post('common/SaveImage', 'CommonController@SaveImage');
Route::post('common/InsertRecords', 'CommonController@InsertRecords'); // INSERT RECORD FOR ANY TABLE, @PARAMS: TABLE,POST ARRAY.
Route::post('common/GetTableRecords', 'CommonController@GetTableRecords'); // GET RECORD FOR ANY SINGLE TABLE, @PARAMS: TABLE,COND ARRAY.
Route::post('common/UpdateTableRecords', 'CommonController@UpdateTableRecords'); // UPDATE RECORD FOR ANY TABLE, @PARAMS: TABLE,COND, POST ARRAY.
Route::post('common/DeleteTableRecords', 'CommonController@DeleteTableRecords'); // DELETE RECORD FOR ANY TABLE, @PARAMS: TABLE,COND, POST ARRAY.
Route::post('common/InsertUserRecords', 'CommonController@InsertUserRecords'); // INSERT RECORD FOR ANY TABLE, @PARAMS: TABLE,POST ARRAY.
Route::post('common/deleteImage', 'CommonController@deleteImage'); //Update Image
Route::post('common/insertRecordsEmail', 'CommonController@insertRecordsEmail'); // INSERT RECORD FOR ANY TABLE, @PARAMS: TABLE,POST ARRAY.
Route::post('common/getTestyRecords', 'CommonController@getTestyRecords'); // GET RECORDS WITH PAGINATION PARAMETERS.
Route::post('common/AddEditClient', 'CommonController@addEditClient');
Route::post('common/GetMiscApprovalData', 'CommonController@GetMiscApprovalData');

// CLIENT CONTROLLER 
Route::post('client/addclient', 'ClientController@addclient');
Route::post('client/ListClient', 'ClientController@ListClient');
Route::post('client/getClientFilterData', 'ClientController@getClientFilterData');
Route::post('client/GetclientDetail','ClientController@GetclientDetail');
Route::post('client/SaveClientInfo', 'ClientController@SaveClientInfo');
Route::get('client/getDocumentDetailbyId/{id}/{company_id}', 'ClientController@getDocumentDetailbyId');
Route::post('client/updateDoc', 'ClientController@updateDoc');
Route::post('client/saveDoc', 'ClientController@saveDoc');
Route::get('client/SelectionData/{id}', 'ClientController@SelectionData');
Route::post('client/saveTaxDoc', 'ClientController@saveTaxDoc');
Route::post('client/setin_destribution', 'ClientController@setin_destribution');

//PURCHASE CONTROLLER
Route::post('purchase/ListPurchase', 'PurchaseController@ListPurchase');
Route::get('purchase/GetPodata/{id}/{company_id}', 'PurchaseController@GetPodata');
Route::post('purchase/EditOrderLine', 'PurchaseController@EditOrderLine');
Route::post('purchase/createPDF', 'PurchaseController@createPDF');
Route::post('purchase/createPO', 'PurchaseController@createPO');
Route::get('purchase/GetPoReceived/{id}/{company_id}', 'PurchaseController@GetPoReceived');
Route::post('purchase/purchasePDF', 'PurchaseController@purchasePDF');
Route::post('purchase/getAllReceiveProducts', 'PurchaseController@getAllReceiveProducts');

// ORDER CONTROLLER 
Route::post('order/listOrder',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'OrderController@listOrder'
]);
Route::post('order/orderDetail',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'OrderController@orderDetail'
]);
Route::post('order/updatePositions',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@updatePositions'
]);
Route::post('order/deleteOrderCommon',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@deleteOrderCommon'
]);
Route::post('order/sendEmail',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@sendEmail'
]);
Route::post('order/addOrder',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@addOrder'
]);
Route::post('order/addDesign',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@addDesign'
]);
Route::post('order/designListing',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'OrderController@designListing'
]);
Route::post('order/designDetail',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'OrderController@designDetail'
]);
Route::post('order/editDesign',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@editDesign'
]);
Route::post('order/getDesignPositionDetail',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'OrderController@getDesignPositionDetail'
]);
Route::post('order/editOrder',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@editOrder'
]);
Route::post('order/orderDetailInfo',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@orderDetailInfo'
]);
Route::post('order/updateOrderCharge',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@updateOrderCharge'
]);
Route::post('order/updateMarkup',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@updateMarkup'
]);
Route::post('order/updateOverride',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@updateOverride'
]);
Route::get('order/calculateAll/{order_id}/{company_id}',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@calculateAll'
]);
Route::post('order/snsOrder',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@snsOrder'
]);
Route::post('order/addPosition',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@addPosition'
]);
Route::post('order/addInvoice',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@addInvoice'
]);
Route::post('order/createInvoice',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@createInvoice'
]);
Route::post('order/paymentInvoiceCash',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@paymentInvoiceCash'
]);
Route::post('order/paymentLinkToPay', 'OrderController@paymentLinkToPay');
Route::post('payment/chargeCreditCard', 'PaymentController@chargeCreditCard');
Route::post('order/GetAllClientsLowerCase',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@GetAllClientsLowerCase'
]);
Route::get('invoice/linktopay/{link}', 'PaymentController@linktopay');
Route::post('payment/refundTransaction',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'PaymentController@refundTransaction'
]);
Route::post('order/updateInvoicePayment',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@updateInvoicePayment'
]);
Route::post('order/GetAllClientsAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@GetAllClientsAddress'
]);
Route::post('order/allOrderAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'OrderController@allOrderAddress'
]);

// FINISHING CONTROLLER 
Route::post('finishing/listFinishing',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'FinishingController@listFinishing'
]);
Route::get('finishing/listFinishing',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'FinishingController@listFinishing'
]);
Route::post('finishing/updateFinishing',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'FinishingController@updateFinishing'
]);
Route::post('finishing/addRemoveToFinishing',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'FinishingController@addRemoveToFinishing'
]);

// FINISHING QUEUE CONTROLLER
Route::get('finishingQueue/listFinishingQueue', 'FinishingQueueController@listFinishingQueue');
Route::post('finishingQueue/listFinishingQueue', 'FinishingQueueController@listFinishingQueue');
Route::post('finishingQueue/GetShiftMachine','FinishingQueueController@GetShiftMachine');
Route::post('finishingQueue/scheduleFinishing','FinishingQueueController@scheduleFinishing');

Route::post('finishing/FinishingBoardData','FinishingQueueController@FinishingBoardData');
Route::post('finishing/FinishingBoardweekData','FinishingQueueController@FinishingBoardweekData');
Route::post('finishing/FinishingBoardMachineData','FinishingQueueController@FinishingBoardMachineData');

// SHIPPING CONTROLLER
Route::get('shipping/listShipping',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ShippingController@listShipping'
]);
Route::post('shipping/listShipping',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ShippingController@listShipping'
]);
Route::post('shipping/shippingDetail',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@shippingDetail'
]);
Route::post('shipping/getShippingOrders',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@getShippingOrders'
]);
Route::post('shipping/CreateBoxShipment',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@CreateBoxShipment'
]);
Route::post('shipping/updateShipping',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@updateShipping'
]);
Route::post('shipping/DeleteBox',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@DeleteBox'
]);
Route::post('shipping/addShippingItem',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@addShippingItem'
]);
Route::post('shipping/getBoxItems',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@getBoxItems'
]);
Route::post('shipping/createPDF', 'ShippingController@createPDF');
Route::post('shipping/addRemoveAddressToPdf',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@addRemoveAddressToPdf'
]);
Route::post('shipping/shipOrder',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@shipOrder'
]);
Route::post('shipping/getProductByAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@getProductByAddress'
]);
Route::post('shipping/addProductToShip',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@addProductToShip'
]);
Route::post('shipping/addAllProductToShip',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@addAllProductToShip'
]);
Route::post('shipping/getShippingAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@getShippingAddress'
]);
Route::post('shipping/getShippingBoxes',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@getShippingBoxes'
]);
Route::post('shipping/getShippingOverview',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ShippingController@getShippingOverview'
]);
Route::post('shipping/createLabel',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@createLabel'
]);
Route::post('shipping/checkAddressValid',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@checkAddressValid'
]);
Route::post('shipping/vewLabelPDF', 'ShippingController@vewLabelPDF');
Route::post('shipping/unAllocateProduct',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ShippingController@unAllocateProduct'
]);
// PRODUCT CONTROLLER

Route::post('product/getProductByVendor',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@getProductByVendor'
]);

Route::post('product/getProductCountByVendor',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@getProductCountByVendor'
]);

Route::post('product/productDetailData',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@productDetailData'
]);

Route::post('product/addProduct',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@addProduct'
]);

Route::post('product/designProduct',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@designProduct'
]);



Route::post('product/deleteAddProduct',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@deleteAddProduct'
]);


Route::post('product/getCustomProduct',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@getCustomProduct'
]);

Route::post('product/uploadCSV',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@uploadCSV'
]);

Route::post('product/getProductDetailColorSize',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@getProductDetailColorSize'
]);

Route::post('product/addcolorsize',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@addcolorsize'
]);

Route::post('product/deleteSizeLink',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@deleteSizeLink'
]);

Route::post('product/downloadCSV',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@downloadCSV'
]);

Route::post('product/downloadCustomProductCSV',[
   'middleware' => 'check',
   'role' => 'ALL',
   'uses' => 'ProductController@downloadCustomProductCSV'
]);


Route::post('product/checkSnsAuth', 'ProductController@checkSnsAuth');


Route::post('product/getVendorByProductCount',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@getVendorByProductCount'
]);

Route::post('product/getProductSize',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@getProductSize'
]);

Route::post('product/checkProductExist',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@checkProductExist'
]);

Route::post('product/findTotal',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'ProductController@findTotal'
]);

// API CONTROLLER
Route::get('api/GetCompanyApi/{company_id}', 'ApiController@GetCompanyApi');
Route::post('api/getApiData', 'ApiController@getApiData');
Route::post('api/save_SnsApi', 'ApiController@save_api');
Route::post('api/checkApi','ApiController@checkApi');

// ART CONTROLLER
Route::get('art/listing/{company_id}', 'ArtController@listing');
Route::get('art/Art_detail/{art_id}/{company_id}', 'ArtController@Art_detail');
Route::get('art/artworkproof_data/{orderline_id}/{company_id}', 'ArtController@artworkproof_data');
Route::get('art/artjob_screen_list/{art_id}/{company_id}', 'ArtController@artjob_screen_list');
Route::get('art/artjobgroup_list/{art_id}/{company_id}', 'ArtController@artjobgroup_list');
Route::post('art/artjob_screen_add', 'ArtController@artjob_screen_add');
Route::post('art/update_orderScreen', 'ArtController@update_orderScreen');
Route::post('art/ScreenSets', 'ArtController@ScreenSets');
Route::post('art/create_screen', 'ArtController@create_screen');
Route::post('art/DeleteScreenRecord', 'ArtController@DeleteScreenRecord');
Route::post('art/SaveArtWorkProof', 'ArtController@SaveArtWorkProof');
Route::get('art/Insert_artworkproof/{line_id}', 'ArtController@Insert_artworkproof');
Route::get('art/Client_art_screen/{client_id}/{company_id}', 'ArtController@Client_art_screen');
Route::get('art/screen_colorpopup/{screen_id}/{company_id}', 'ArtController@screen_colorpopup');
Route::get('art/art_worklist_listing/{art_id}/{company_id}', 'ArtController@art_worklist_listing');
Route::get('art/GetScreenset_detail/{position_id}', 'ArtController@GetScreenset_detail');
Route::get('art/GetscreenColor/{screen_id}/{company_id}', 'ArtController@GetscreenColor');
Route::post('art/UpdateColorScreen', 'ArtController@UpdateColorScreen');
Route::get('art/getScreenSizes/{company_id}', 'ArtController@getScreenSizes');
Route::post('art/change_sortcolor', 'ArtController@change_sortcolor');
Route::post('art/change_sortscreen', 'ArtController@change_sortscreen');
Route::post('art/ArtApprovalPDF', 'ArtController@ArtApprovalPDF');
Route::post('art/PressInstructionPDF', 'ArtController@PressInstructionPDF');
Route::post('art/PressInstructionAllPDF', 'ArtController@PressInstructionAllPDF');

// AFFILIATES ROUTERS
Route::post('affiliate/getAffiliateDetail',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@getAffiliateDetail'
]);
Route::post('affiliate/addAffiliate',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@addAffiliate'
]);
Route::post('affiliate/getAffiliateData',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@getAffiliateData'
]);
Route::post('affiliate/getAffiliateList',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@getAffiliateList'
]);
Route::post('affiliate/getAffiliateDesignProduct',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@getAffiliateDesignProduct'
]);
Route::post('affiliate/affiliateCalculation',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'AffiliateController@affiliateCalculation'
]);

//DISTRIBUTION ROUTERS
Route::post('distribution/getDistProductAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'DistributionController@getDistProductAddress'
]);
Route::post('distribution/addEditDistribute',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'DistributionController@addEditDistribute'
]);
Route::post('distribution/getDistSizeByProduct',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'DistributionController@getDistSizeByProduct'
]);
Route::post('distribution/getDistAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'DistributionController@getDistAddress'
]);
Route::post('distribution/getProductByAddress',[
   'middleware' => 'check',
   'role' => array('AT','SU'),
   'action' => 'false',
   'uses' => 'DistributionController@getProductByAddress'
]);

Route::get('qbo/oauth','QuickBookController@qboOauth');
Route::get('qbo/success','QuickBookController@qboSuccess');
Route::get('qbo/disconnect','QuickBookController@qboDisconnect');
Route::get('qbo/qboConnect','QuickBookController@qboConnect');
Route::get('qbo/createCustomer','QuickBookController@createCustomer');
Route::post('qbo/AddItem', 'QuickBookController@addItem');
Route::post('qbo/updateInvoicePayment', 'QuickBookController@updateInvoicePayment');

Route::post('invoice/listInvoice', 'InvoiceController@listInvoice');
Route::get('invoice/getInvoiceDetail/{invoice_id}/{company_id}/{type}/{order_id}', 'InvoiceController@getInvoiceDetail');
Route::get('invoice/getInvoiceHistory/{invoice_id}/{company_id}/{type}', 'InvoiceController@getInvoiceHistory');
Route::get('invoice/getInvoicePayment/{invoice_id}/{company_id}/{type}', 'InvoiceController@getInvoicePayment');
Route::get('invoice/getInvoiceCards/{invoice_id}/{company_id}/{type}', 'InvoiceController@getInvoiceCards');
Route::post('invoice/createInvoicePdf', 'InvoiceController@createInvoicePdf');
Route::post('invoice/getPaymentCard', 'InvoiceController@getPaymentCard');
Route::post('invoice/getNoQuickbook', 'InvoiceController@getNoQuickbook');
Route::post('invoice/getSalesClosed', 'InvoiceController@getSalesClosed');
Route::post('invoice/getUnpaid', 'InvoiceController@getUnpaid');
Route::post('invoice/getAverageOrders', 'InvoiceController@getAverageOrders');
Route::post('invoice/getLatestOrders', 'InvoiceController@getLatestOrders');
Route::post('invoice/getEstimates', 'InvoiceController@getEstimates');
Route::post('invoice/getComparison', 'InvoiceController@getComparison');
Route::post('invoice/getSalesPersons', 'InvoiceController@getSalesPersons');
Route::post('invoice/getUnshipped', 'InvoiceController@getUnshipped');
Route::post('invoice/getProduction', 'InvoiceController@getProduction');
Route::post('invoice/getFullShipped', 'InvoiceController@getFullShipped');
Route::post('invoice/getFullDashboard', 'InvoiceController@getFullDashboard');

Route::post('labor/laborDetail', 'LaborController@LaborDetail');
Route::post('labor/editLabor', 'LaborController@editLabor');
Route::post('labor/addLabor', 'LaborController@addLabor');

Route::post('production/GetProductionList','ProductionController@GetProductionList');
Route::post('production/GetShiftMachine','ProductionController@GetShiftMachine');
Route::post('production/GetPositionDetails','ProductionController@GetPositionDetails');
Route::post('production/GetFilterData','ProductionController@GetFilterData');

Route::post('production/SchedualBoardData','ProductionController@SchedualBoardData');
Route::post('production/SchedualBoardweekData','ProductionController@SchedualBoardweekData');
Route::post('production/SchedualBoardMachineData','ProductionController@SchedualBoardMachineData');
Route::post('production/GetSchedulePositionDetail','ProductionController@GetSchedulePositionDetail');
Route::post('production/SaveSchedulePosition','ProductionController@SaveSchedulePosition');
Route::get('production/GetRuntimeData/{position_id}/{company_id}','ProductionController@GetRuntimeData');
