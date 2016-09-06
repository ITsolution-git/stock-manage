<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
require_once(app_path() . '/quickbook_constants.php');
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

    public function __construct(Company $company,Common $common){
        $this->company = $company;
        $this->common = $common;

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


    public function updateCustomer($client,$contact){

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

        // Get the existing customer first (you need the latest SyncToken value)
        $customers = $CustomerService->query($this->context,$this->realm, "SELECT * FROM Customer WHERE Id = '".$client['qid']."' ");
       
        $Customer = $customers[0];
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

        if ($CustomerService->update($this->context,$this->realm, $Customer->getId(), $Customer))
        {
            return 1;
            print('&nbsp; Updated!<br>');
        }
        else
        {
            return 0;
            print('&nbsp; Error: ' . $CustomerService->lastError($Context));
        }

    }



    public function addItem(){
        $post = Input::all();

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

        $static_charge = array('0' => 'S&S','1' => 'Custom Product','2' => 'Separations Charge','3' => 'Rush Charge','4' => 'Distribution Charge',
                        '5' => 'Digitize Charge','6' => 'Shipping Charge','7' => 'Setup Charge','8' => 'Artwork Charge','9' => 'Tax','10' => 'Discount',
                        '11' => 'Screen Charge','12' => 'Press Setup Charge','13' => 'Foil','14' => 'Number On Dark','15' => 'Ink Charge','16' => 'Number On Light',
                        '17' => 'Discharge','18' => 'Speciality','19' => 'Oversize screen');

          
          foreach($static_charge as $charge) {
            
                $ItemService = new \QuickBooks_IPP_Service_Item();

                $Item = new \QuickBooks_IPP_Object_Item();

                 $Item->setName($charge);
                 $Item->setType('Inventory');
                 $Item->setIncomeAccountRef('53');

                if ($resp = $ItemService->add($this->context, $this->realm, $Item))
                {
                    $id = $this->getId($resp);

                    if($charge == 'S&S') {
                            $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('ss' => $id));

                    } elseif ($charge == 'Custom Product') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('custom_product' => $id));

                    }elseif ($charge == 'Separations Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('separations_charge' => $id));

                    }elseif ($charge == 'Rush Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('rush_charge' => $id));

                    }elseif ($charge == 'Distribution Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('distribution_charge' => $id));

                    }elseif ($charge == 'Digitize Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('digitize_charge' => $id));

                    }elseif ($charge == 'Shipping Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('shipping_charge' => $id));

                    }elseif ($charge == 'Setup Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('setup_charge' => $id));

                    }elseif ($charge == 'Artwork Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('artwork_charge' => $id));

                    }elseif ($charge == 'Tax') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('tax_charge' => $id));

                    }elseif ($charge == 'Discount') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('discount_charge' => $id));

                    }elseif ($charge == 'Screen Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('screen_charge' => $id));

                    }elseif ($charge == 'Press Setup Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('press_setup_charge' => $id));

                    }elseif ($charge == 'Foil') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('foil_charge' => $id));

                    }elseif ($charge == 'Number On Dark') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('number_on_dark_charge' => $id));

                    }elseif ($charge == 'Ink Charge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('ink_charge' => $id));

                    }elseif ($charge == 'Number On Light') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('number_on_light_charge' => $id));

                    }elseif ($charge == 'Discharge') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('discharge_charge' => $id));

                    }elseif ($charge == 'Speciality') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('speciality_charge' => $id));

                    }elseif ($charge == 'Oversize screen') {
                         $this->common->UpdateTableRecords('quickbook_detail',array('id' => $post['cond']['id']),array('oversize_screen_charge' => $id));

                    }

                } else {
                    return 0;
                }

          }
          return 1;


        /*$ItemService = new \QuickBooks_IPP_Service_Item();

        $Item = new \QuickBooks_IPP_Object_Item();

        $Item->setName('My Item123456');
        $Item->setType('Inventory');
        $Item->setIncomeAccountRef('53');

        if ($resp = $ItemService->add($this->context, $this->realm, $Item))
        {
            return $this->getId($resp);
        }
        else
        {
           return 0;
        }*/
    }

    public function addInvoice($invoiceArray,$chargeArray,$customerRef,$db_product,$invoice_id,$other_charges,$price_grid,$payment){
      

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


        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();

        $Invoice = new \QuickBooks_IPP_Object_Invoice();

         $Invoice->setDocNumber('WEB' . mt_rand(0, 10000));

         
         $Invoice->setTxnDate(date('Y-m-d'));

         if($payment == '15') {

            $setDate  = date('Y-m-d', strtotime("+15 days"));
           
            $Invoice->setDueDate($setDate);
            


         } else if($payment == '30') {

            $setDate  = date('Y-m-d', strtotime("+30 days"));
            $Invoice->setDueDate($setDate);
           

         } else {
            $Invoice->setDueDate(date('Y-m-d'));
         }


         
         
         


        foreach ($invoiceArray as $key => $value) {

                $desc = array();

                foreach ($value->sizeData as  $sizeAll) {
                   $desc[] = $sizeAll->size.'('.$sizeAll->qnty.')'; 
                }

                $description = implode(', ' , $desc);

                $product_name_desc_display = $value->product_name.' : '.$value->color_name.' : '.$description;

                
                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($value->total_line_charge * $value->total_qnty);
                 $Line->setDescription($product_name_desc_display);

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 if($value->vendor_id == 1) {
                     $SalesItemLineDetail->setItemRef($db_product[0]->ss);
                 } else {
                     $SalesItemLineDetail->setItemRef($db_product[0]->custom_product);
                 }
                
                 $SalesItemLineDetail->setUnitPrice($value->total_line_charge);
                 $SalesItemLineDetail->setQty($value->total_qnty);


                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
           
         }


          if($other_charges[0]->foil_qnty != 0 && $other_charges[0]->foil_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->foil_qnty * $price_grid->foil);
                 $Line->setDescription('Foil Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->foil_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->foil);
                 $SalesItemLineDetail->setQty($other_charges[0]->foil_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

          if($other_charges[0]->number_on_dark_qnty != 0 && $other_charges[0]->number_on_dark_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->number_on_dark_qnty * $price_grid->number_on_dark);
                 $Line->setDescription('Number On Dark Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->number_on_dark_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->number_on_dark);
                 $SalesItemLineDetail->setQty($other_charges[0]->number_on_dark_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($other_charges[0]->oversize_screens_qnty != 0 && $other_charges[0]->oversize_screens_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->oversize_screens_qnty * $price_grid->over_size_screens);
                 $Line->setDescription('Oversize Screen Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->oversize_screen_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->over_size_screens);
                 $SalesItemLineDetail->setQty($other_charges[0]->oversize_screens_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($other_charges[0]->ink_charge_qnty != 0 && $other_charges[0]->ink_charge_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->ink_charge_qnty * $price_grid->ink_changes);
                 $Line->setDescription('Ink Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->ink_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->ink_changes);
                 $SalesItemLineDetail->setQty($other_charges[0]->ink_charge_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($other_charges[0]->number_on_light_qnty != 0 && $other_charges[0]->number_on_light_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->number_on_light_qnty * $price_grid->number_on_light);
                 $Line->setDescription('Number On Light Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->number_on_light_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->number_on_light);
                 $SalesItemLineDetail->setQty($other_charges[0]->number_on_light_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

          if($other_charges[0]->discharge_qnty != 0 && $other_charges[0]->discharge_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->discharge_qnty * $price_grid->discharge);
                 $Line->setDescription('Discharge Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->discharge_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->discharge);
                 $SalesItemLineDetail->setQty($other_charges[0]->discharge_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($other_charges[0]->speciality_qnty != 0 && $other_charges[0]->speciality_qnty != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($other_charges[0]->speciality_qnty * $price_grid->specialty);
                 $Line->setDescription('Speciality Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->speciality_charge);
                 $SalesItemLineDetail->setUnitPrice($price_grid->specialty);
                 $SalesItemLineDetail->setQty($other_charges[0]->speciality_qnty);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }



         if($chargeArray[0]->screen_charge != 0 && $chargeArray[0]->screen_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->screen_charge);
                 $Line->setDescription('Screen Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->screen_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->screen_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }


         if($chargeArray[0]->press_setup_charge != 0 && $chargeArray[0]->press_setup_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->press_setup_charge);
                 $Line->setDescription('Screen Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->press_setup_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->press_setup_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }



         if($chargeArray[0]->separations_charge != 0 && $chargeArray[0]->separations_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->separations_charge);
                 $Line->setDescription('Seperation Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->separations_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->separations_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

          if($chargeArray[0]->rush_charge != 0 && $chargeArray[0]->rush_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->rush_charge);
                 $Line->setDescription('Rush Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->rush_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->rush_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }


         if($chargeArray[0]->distribution_charge != 0 && $chargeArray[0]->distribution_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->distribution_charge);
                 $Line->setDescription('Distribution Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->distribution_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->distribution_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($chargeArray[0]->digitize_charge != 0 && $chargeArray[0]->digitize_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->digitize_charge);
                 $Line->setDescription('Digitize Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->digitize_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->digitize_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }


         if($chargeArray[0]->shipping_charge != 0 && $chargeArray[0]->shipping_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->shipping_charge);
                 $Line->setDescription('Shipping Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->shipping_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->shipping_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

          if($chargeArray[0]->setup_charge != 0 && $chargeArray[0]->setup_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->setup_charge);
                 $Line->setDescription('Setup Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->setup_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->setup_charge);
                 $SalesItemLineDetail->setQty(1);

                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($chargeArray[0]->artwork_charge != 0 && $chargeArray[0]->artwork_charge != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(1 * $chargeArray[0]->artwork_charge);
                 $Line->setDescription('Artwork Charges');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->artwork_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->artwork_charge);
                 $SalesItemLineDetail->setQty(1);


                 $Line->addSalesItemLineDetail($SalesItemLineDetail);

                 $Invoice->addLine($Line);

                 $Invoice->setCustomerRef($customerRef);
         }

         if($chargeArray[0]->order_total != 0 && $chargeArray[0]->order_total != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount($chargeArray[0]->tax);
                 $Line->setDescription('Tax-'.$chargeArray[0]->tax_rate);

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->tax_charge);
                 $SalesItemLineDetail->setUnitPrice($chargeArray[0]->tax);
                 $SalesItemLineDetail->setQty(1);
                 $Line->addSalesItemLineDetail($SalesItemLineDetail);
                 $Invoice->addLine($Line);
                 $Invoice->setCustomerRef($customerRef);
         }

         if($chargeArray[0]->discount != 0 && $chargeArray[0]->discount != '') {

                 $Line = new \QuickBooks_IPP_Object_Line();
                 $Line->setDetailType('SalesItemLineDetail');
                 $Line->setAmount(-$chargeArray[0]->discount);
                 $Line->setDescription('Discount');

                 $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                 $SalesItemLineDetail->setItemRef($db_product[0]->discount_charge);
                 $SalesItemLineDetail->setUnitPrice(-$chargeArray[0]->discount);
                 $SalesItemLineDetail->setQty(1);
                 $Line->addSalesItemLineDetail($SalesItemLineDetail);
                 $Invoice->addLine($Line);
                 $Invoice->setCustomerRef($customerRef);
         }

        


         
         /*$Line = new \QuickBooks_IPP_Object_Line();
         $Line->setDetailType('SalesItemLineDetail');
         $Line->setAmount(12.95 * 2);
         $Line->setDescription('Test description goes here.');

         $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
         $SalesItemLineDetail->setItemRef('8');
         $SalesItemLineDetail->setUnitPrice(12.95);
         $SalesItemLineDetail->setQty(2);

         $Line->addSalesItemLineDetail($SalesItemLineDetail);

         $Invoice->addLine($Line);

         $Invoice->setCustomerRef($customerRef);*/


        if ($resp = $InvoiceService->add($this->context, $this->realm, $Invoice))
        {
            $qb_invoice_id =  $this->getId($resp);
             $this->common->UpdateTableRecords('invoice',array('id' => $invoice_id),array('qb_id' => $qb_invoice_id));

           // $data_record = array("success"=>1,"message"=>"Success");
            return 1; 
        }
        else
        {

          //  $data_record = array("success"=>0,"message"=>"Please complete Quickbook Setup First");
           // return response()->json(["data" => $data_record]);
            return 0; 
           // print($InvoiceService->lastError());
        }
    }

    public function getId($resp){
        $resp = str_replace('{','',$resp);
        $resp = str_replace('}','',$resp);
        $resp = abs($resp);
        return $resp;
    }  


     public function updateInvoicePayment(){
        $data = array("success"=>1,'message' =>"Invoice Payments Sync successfully");

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


        $PaymentService = new \QuickBooks_IPP_Service_Payment();

        // Create payment object
        $Payment = new \QuickBooks_IPP_Object_Payment();


        // Get invoice IDs
        $post = Input::all();
        $company_id=$post['company_id'];

        $company_data = $this->common->GetTableRecords('company_info',array('user_id' => $company_id),array());
        $result = $this->GetAllclientInovicesAddDelete($company_id,0);
        $resultDelete = $this->GetAllclientInovicesAddDelete($company_id,1);
        if((count($result)<1) && (count($resultDelete)<1)){
            $data = array("success"=>0,'message' =>"No Payments are there to update.");
            return response()->json(['data'=>$data]);
        }

        // Updating add invoice payment data
        foreach ($result as $key => $all_data) 
        {
            $setPaymentRefNum='WEB' . mt_rand(0, 10000);
            $Payment->setPaymentRefNum($setPaymentRefNum);
            $Payment->setTxnDate($all_data->payment_date);
            //$Payment->setTotalAmt(7870);
            $Payment->setTotalAmt($all_data->totalAmount);

            // Create line for payment (this details what it's applied to)
            $Line = new \QuickBooks_IPP_Object_Line();
            //$Line->setAmount(500);
            $Line->setAmount($all_data->payment_amount);

            // The line has a LinkedTxn node which links to the actual invoice
            $LinkedTxn = new \QuickBooks_IPP_Object_LinkedTxn();
            //$LinkedTxn->setTxnId(152);
            $LinkedTxn->setTxnId($all_data->invoiceId);
            $LinkedTxn->setTxnType('Invoice');

            $Line->setLinkedTxn($LinkedTxn);

            $Payment->addLine($Line);

            //$Payment->setCustomerRef(1);
            $Payment->setCustomerRef($all_data->CustomerRef);
            //print_r($Payment);exit;

            // Send payment to QBO 
            if ($resp = $PaymentService->add($this->context, $this->realm, $Payment))
            {
                //print('Our new Payment ID is: [' . $resp . ']');
                //return 1;
                $qbData=array('qb_payment_id' => $resp, 'qb_web_reference' => $setPaymentRefNum, 'qb_flag' => 1);
                $this->common->UpdateTableRecords('payment_history',array('payment_id' => $all_data->payment_id),$qbData);
            }
            else
            {
                //print($PaymentService->lastError());
                $data = array("success"=>0,'message' =>$PaymentService->lastError());
                return response()->json(['data'=>$data]);
                //return 0;
            }
        }

        // Updating delete invoice payment data
        /*foreach ($resultDelete as $key => $all_data) 
        {
            $setPaymentRefNum='WEB' . mt_rand(0, 10000);
            $Payment->setPaymentRefNum($setPaymentRefNum);
            $Payment->setTxnDate($all_data->payment_date);
            //$Payment->setTotalAmt(7870);
            $Payment->setTotalAmt($all_data->totalAmount);

            // Create line for payment (this details what it's applied to)
            $Line = new \QuickBooks_IPP_Object_Line();
            //$Line->setAmount(500);
            $Line->setAmount($all_data->payment_amount);

            // The line has a LinkedTxn node which links to the actual invoice
            $LinkedTxn = new \QuickBooks_IPP_Object_LinkedTxn();
            //$LinkedTxn->setTxnId(152);
            $LinkedTxn->setTxnId($all_data->invoiceId);
            $LinkedTxn->setTxnType('Invoice');

            $Line->setLinkedTxn($LinkedTxn);

            $Payment->addLine($Line);

            //$Payment->setCustomerRef(1);
            $Payment->setCustomerRef($all_data->CustomerRef);
            //print_r($Payment);exit;

            // Send payment to QBO 
            if ($resp = $PaymentService->add($this->context, $this->realm, $Payment))
            {
                //print('Our new Payment ID is: [' . $resp . ']');
                //return 1;
                $qbData=array('qb_payment_id' => $resp, 'qb_web_reference' => $setPaymentRefNum, 'qb_flag' => 1);
                $this->common->UpdateTableRecords('payment_history',array('payment_id' => $all_data->payment_id),$qbData);
            }
            else
            {
                //print($PaymentService->lastError());
                $data = array("success"=>0,'message' =>$PaymentService->lastError());
                return response()->json(['data'=>$data]);
                //return 0;
            }
        }*/
        return response()->json(['data'=>$data]);
        //print_r($result);exit;
    }



    /*public function GetAllclientInovices($company_id){
        $retArray = DB::table('users as u')
            ->select('i.qb_id as invoiceId', 'p.payment_id as payment_id', 'p.payment_date as payment_date', 'p.payment_amount as payment_amount', 'o.grand_total as totalAmount', 'c.qid as CustomerRef')
            ->leftJoin('client as c','c.company_id','=',"u.id")
            ->leftJoin('orders as o','o.client_id','=',"c.client_id")
            ->leftJoin('invoice as i','i.order_id','=',"o.id")
            ->leftJoin('payment_history as p','p.qb_id','=',"i.qb_id")
            ->where('u.id','=',$company_id)
            ->where('i.qb_id','!=',0)
            ->where('p.qb_flag','=',0)
            ->get();
        return $retArray;
    }*/

    public function GetAllclientInovicesAddDelete($company_id,$operation){
        $retArray = DB::table('users as u')
            ->select('i.qb_id as invoiceId', 'p.payment_id as payment_id', 'p.payment_date as payment_date', 'p.payment_amount as payment_amount', 'o.grand_total as totalAmount', 'c.qid as CustomerRef')
            ->leftJoin('client as c','c.company_id','=',"u.id")
            ->leftJoin('orders as o','o.client_id','=',"c.client_id")
            ->leftJoin('invoice as i','i.order_id','=',"o.id")
            ->leftJoin('payment_history as p','p.qb_id','=',"i.qb_id")
            ->where('u.id','=',$company_id)
            ->where('i.qb_id','!=',0)
            ->where('p.qb_flag','=',$operation)
            ->get();
        return $retArray;
    }


}