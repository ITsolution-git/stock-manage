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

use App\Order;
use DB;
use App;
//use Barryvdh\DomPDF\Facade as PDF;

use Request;
use PDF;






class QuickBookController extends Controller
{

    private $IntuitAnywhere;
    private $context;
    private $realm;

    public function __construct(){
        if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            \QuickBooks_Utilities::initialize(env('QBO_DSN'));
        }
        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
    }



    public function  qboConnect(){


        if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), env('QBO_TENANT')) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), env('QBO_TENANT'))) {

            // Set up the IPP instance
            $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), env('QBO_TENANT'));
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                env('QBO_USERNAME'),
                $creds);

            if (env('QBO_SANDBOX')) {
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



    public function qboOauth(){
        if ($this->IntuitAnywhere->handle(env('QBO_USERNAME'), env('QBO_TENANT')))
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

        $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), env('QBO_TENANT'),true);

        $response = array('success' => 1, 'message' => "Successful",'records' => true);
        
        
        return response()->json(["data" => $response]);
       // return redirect()->intended("/yourpath");// afer disconnect redirect where you want
 
    }



    public function createCustomer(){


       $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
        // Get our OAuth credentials from the database
        $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), env('QBO_TENANT'));
        // Tell the framework to load some data from the OAuth store
        $IPP->authMode(
            \QuickBooks_IPP::AUTHMODE_OAUTH,
            env('QBO_USERNAME'),
            $creds);

        if (env('QBO_SANDBOX')) {
            // Turn on sandbox mode/URLs
            $IPP->sandbox(true);
        }
        // This is our current realm
        $this->realm = $creds['qb_realm'];
        // Load the OAuth information from the database
        $this->context = $IPP->context();


        
        $CustomerService = new \QuickBooks_IPP_Service_Customer();

        $Customer = new \QuickBooks_IPP_Object_Customer();

         $Customer->setTitle('Mr');
         $Customer->setGivenName('Hardik Andy');
         $Customer->setMiddleName('M');
         $Customer->setFamilyName('Deliwala');
         $Customer->setDisplayName('Hardik Andy M Deliwala' . mt_rand(0, 1000));
        // Terms (e.g. Net 30, etc.)
        $Customer->setSalesTermRef(4);

        // Phone #
        $PrimaryPhone = new \QuickBooks_IPP_Object_PrimaryPhone();
        $PrimaryPhone->setFreeFormNumber('123-456-7890');
        $Customer->setPrimaryPhone($PrimaryPhone);

        // Mobile #
        $Mobile = new \QuickBooks_IPP_Object_Mobile();
        $Mobile->setFreeFormNumber('123-456-7890');
        $Customer->setMobile($Mobile);

        // Fax #
        $Fax = new \QuickBooks_IPP_Object_Fax();
        $Fax->setFreeFormNumber('123-456-7890');
        $Customer->setFax($Fax);

        // Bill address
        $BillAddr = new \QuickBooks_IPP_Object_BillAddr();
        $BillAddr->setLine1('G.B.shah road');
         $BillAddr->setLine2('Vasna');
         $BillAddr->setCity('AHD');
         $BillAddr->setCountrySubDivisionCode('IND');
         $BillAddr->setPostalCode('380007');
         $Customer->setBillAddr($BillAddr);

        // Email
        $PrimaryEmailAddr = new \QuickBooks_IPP_Object_PrimaryEmailAddr();
        $PrimaryEmailAddr->setAddress('support@consolibyte.com');
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