<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Shipping;
use App\Common;
use App\Distribution;
use App\Company;

use App\Order;
use DB;
use App;

use Request;
use PDF;


class QuickBookController extends Controller
{

    private $IntuitAnywhere;
    private $context;
    private $realm;

    public function __construct(Company $company){
        $this->company = $company;

        if (!\QuickBooks_Utilities::initialized(QBO_DSN)) {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            \QuickBooks_Utilities::initialize(QBO_DSN);
        }

        $company_id = Session::get('company_id');
        $result = $this->company->getQBAPI($company_id);
        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(QBO_DSN,QBO_ENCRYPTION_KEY,$result[0]->consumer_key,$result[0]->consumer_secret_key,QBO_OAUTH_URL,QBO_SUCCESS_URL);
       
    }



    public function  qboConnect(){


        if ($this->IntuitAnywhere->check(QBO_USERNAME, QBO_TENANT) && $this->IntuitAnywhere->test(QBO_USERNAME, QBO_TENANT)) {

            // Set up the IPP instance
            $IPP = new \QuickBooks_IPP(QBO_DSN);
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load(QBO_USERNAME, QBO_TENANT);
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                QBO_USERNAME,
                $creds);

            if (QBO_SANDBOX) {
                // Turn on sandbox mode/URLs
                $IPP->sandbox(true);
            }
            // This is our current realm
            $this->realm = $creds['qb_realm'];
            // Load the OAuth information from the database
            $this->context = $IPP->context();



           
        $response = array('success' => 1, 'message' => "Successful",'records' => true);
        
        
        return response()->json(["data" => $response]);

        } else {

             $response = array('success' => 0, 'message' => "Error",'records' => false);
        
        
        return response()->json(["data" => $response]);

            
        }
    }



    public function qboOauth($oauth_token=''){
        /*if(!empty($oauth_token)) $_GET['oauth_token'] = $oauth_token;*/
        if ($this->IntuitAnywhere->handle(QBO_USERNAME, QBO_TENANT))
        {
            ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
        }
        else
        {
            // If this happens, something went wrong with the OAuth handshake
            die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }



    public function qboSuccess(){
       
        return view('settings.qbo_success');
       
    }



    public function qboDisconnect(){

        $this->IntuitAnywhere->disconnect(QBO_USERNAME, QBO_TENANT,true);

        $response = array('success' => 1, 'message' => "Successful",'records' => true);
        
        
        return response()->json(["data" => $response]);
       // return redirect()->intended("/yourpath");// afer disconnect redirect where you want
 
    }



    public function createCustomer($client,$contact){

        
       $IPP = new \QuickBooks_IPP(QBO_DSN);

        // Get our OAuth credentials from the database
        $creds = $this->IntuitAnywhere->load(QBO_USERNAME, QBO_TENANT);
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            \QuickBooks_IPP::AUTHMODE_OAUTH,
            QBO_USERNAME,
            $creds);

        if (QBO_SANDBOX) {
            // Turn on sandbox mode/URLs
            $IPP->sandbox(true);
        }
        // This is our current realm
        $this->realm = $creds['qb_realm'];

        // Load the OAuth information from the database
        $this->context = $IPP->context();


        
        
        $CustomerService = new \QuickBooks_IPP_Service_Customer();

        $Customer = new \QuickBooks_IPP_Object_Customer();

        // $Customer->setTitle('Mr');
         $Customer->setGivenName($contact['first_name']);
       //  $Customer->setMiddleName('M');
         $Customer->setFamilyName($contact['last_name']);
         $Customer->setDisplayName($contact['first_name'].' '.$contact['last_name'].' '. mt_rand(0, 1000));
        // Terms (e.g. Net 30, etc.)
        $Customer->setSalesTermRef(4);

        // Phone #
        $PrimaryPhone = new \QuickBooks_IPP_Object_PrimaryPhone();
        $PrimaryPhone->setFreeFormNumber($contact['phone']);
        $Customer->setPrimaryPhone($PrimaryPhone);

        // Mobile #
        $Mobile = new \QuickBooks_IPP_Object_Mobile();
        $Mobile->setFreeFormNumber($contact['phone']);
        $Customer->setMobile($Mobile);

        // Fax #
        $Fax = new \QuickBooks_IPP_Object_Fax();
        $Fax->setFreeFormNumber($contact['phone']);
        $Customer->setFax($Fax);

        // Bill address
        $BillAddr = new \QuickBooks_IPP_Object_BillAddr();
        $BillAddr->setLine1($client['pl_address']);
         $BillAddr->setLine2($client['pl_suite']);
         $BillAddr->setCity($client['pl_city']);
         $BillAddr->setCountrySubDivisionCode('US');
         $BillAddr->setPostalCode($client['pl_pincode']);
         $Customer->setBillAddr($BillAddr);

        // Email
        $PrimaryEmailAddr = new \QuickBooks_IPP_Object_PrimaryEmailAddr();
        $PrimaryEmailAddr->setAddress($client['billing_email']);
        $Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

            
        if ($resp = $CustomerService->add($this->context, $this->realm, $Customer))
        {

            //print('Our new customer ID is: [' . $resp . '] (name "' . $Customer->getDisplayName() . '")');
            //return $resp;
            //echo $resp;exit;
            //$resp = str_replace('{','',$resp);
            //$resp = str_replace('}','',$resp);
            //$resp = abs($resp);
            return $this->getId($resp);
        }
        else
        {
             return 0;
            //echo 'Not Added qbo';
            print($CustomerService->lastError($this->context));
        }
    }

    public function addItem(){
        $ItemService = new \QuickBooks_IPP_Service_Item();

        $Item = new \QuickBooks_IPP_Object_Item();

        $Item->setName('My Item');
 $Item->setType('Inventory');
 $Item->setIncomeAccountRef('53');

        if ($resp = $ItemService->add($this->context, $this->realm, $Item))
        {
            return $this->getId($resp);
        }
        else
        {
            print($ItemService->lastError($this->context));
        }
    }

    public function addInvoice($invoiceArray,$itemArray,$customerRef){

        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();

        $Invoice = new \QuickBooks_IPP_Object_Invoice();

        $Invoice = new QuickBooks_IPP_Object_Invoice();

 $Invoice->setDocNumber('WEB' . mt_rand(0, 10000));
 $Invoice->setTxnDate('2013-10-11');

 $Line = new QuickBooks_IPP_Object_Line();
 $Line->setDetailType('SalesItemLineDetail');
 $Line->setAmount(12.95 * 2);
 $Line->setDescription('Test description goes here.');

 $SalesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
 $SalesItemLineDetail->setItemRef('8');
 $SalesItemLineDetail->setUnitPrice(12.95);
 $SalesItemLineDetail->setQty(2);

 $Line->addSalesItemLineDetail($SalesItemLineDetail);

 $Invoice->addLine($Line);

 $Invoice->setCustomerRef('67');


        if ($resp = $InvoiceService->add($this->context, $this->realm, $Invoice))
        {
            return $this->getId($resp);
        }
        else
        {
            print($InvoiceService->lastError());
        }
    }

    public function getId($resp){
        $resp = str_replace('{','',$resp);
        $resp = str_replace('}','',$resp);
        $resp = abs($resp);
        return $resp;
    }  

}