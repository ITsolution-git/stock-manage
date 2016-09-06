<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Account;
use App\Order;
use App\Api;
use App\Common;
use App\Company;
use DB;
use App;
use Request;
use Response;
//use Barryvdh\DomPDF\Facade as PDF;
use PDF;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
define("AUTHORIZENET_LOG_FILE", "phplog");
// CREATE COMPANY AND SET RIGHTS, MANAGE BY SUPER ADMIN ONLY
class PaymentController extends Controller {  


 	public function __construct(Order $order,Common $common,Api $api,Company $company) 
 	{
    $this->order = $order;
        $this->common = $common;
        $this->api = $api;
        $this->company = $company;
    }


    /**
     * Get All account list data
     *
     * @param  limitstart,limitend.
     * @return Response, success, records, message
     */

	/*public function MerchantAuthentication()
	{
		// Common setup for API credentials
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$merchantAuthentication->setName("93Yd4M9fU");
		$merchantAuthentication->setTransactionKey("443U8c9zrK5UZyEd");

		// Create the payment data for a credit card
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber("4111111111111111");
		$creditCard->setExpirationDate("2038-12");
		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setCreditCard($creditCard);

		// Create a transaction
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType( "authCaptureTransaction"); 
		$transactionRequestType->setAmount(151.51);
		$transactionRequestType->setPayment($paymentOne);

		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setTransactionRequest( $transactionRequestType);
		$controller = new AnetController\CreateTransactionController($request);
		$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

		if ($response != null)
		{
		    $tresponse = $response->getTransactionResponse();

		    if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )   
		    {
		        echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
		        echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
		    }
		    else
		    {
		        echo  "Charge Credit Card ERROR :  Invalid response\n";
		    }
		}
		else
		{
		    echo  "Charge Credit card Null response returned";
		}
	}*/

	function chargeCreditCard(){
      $post = Input::all();
      $amount=$post['amount'];
      

      if(isset($post['company_id'])){
        $company_id=$post['company_id'];
        $retCredsArray = DB::table('authorize_detail as au')
              ->select('au.login', 'au.transactionkey')
              ->leftJoin('api_link_table as ai','ai.id','=',"au.link_id")
              ->where('ai.company_id','=',$company_id)
              ->where('ai.status','=','1')
              ->where('ai.api_id','=',3)
              ->get();
      }
      if(count($retCredsArray)<1){
          $data = array("success"=>0,'message' =>"Please integrate Authorize.net details");
          return response()->json(['data'=>$data]);
      }

      $creditCardNumber=$post['creditCard'];//4111111111111111
      $expiry=$post['expMonth'].$post['expYear'];
      //$expiry=$post['expiry'];//1226
      $cvv=$post['cvv'];//123
      // Common setup for API credentials
      $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
      /*$merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);*/
      $merchantAuthentication->setName($retCredsArray[0]->login);
      $merchantAuthentication->setTransactionKey($retCredsArray[0]->transactionkey);
      $refId = 'ref' . time();

      // Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber($creditCardNumber);
      $creditCard->setExpirationDate($expiry);
      $creditCard->setCardCode($cvv);
      $paymentOne = new AnetAPI\PaymentType();
      $paymentOne->setCreditCard($creditCard);

      $qb_data = $this->common->GetTableRecords('invoice',array('id' => $post['invoice_id']),array());
      $qb_id = $qb_data[0]->qb_id;
      $order_id = $qb_data[0]->order_id;

      $order = new AnetAPI\OrderType();
      $order->setDescription("Payment for Order ID: ".$order_id);
      $order->setInvoiceNumber("INV - ".$order_id);

      //create a transaction
      $transactionRequestType = new AnetAPI\TransactionRequestType();
      $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
      $transactionRequestType->setAmount($amount);
      $transactionRequestType->setOrder($order);
      $transactionRequestType->setPayment($paymentOne);

      $retArray = DB::table('orders as o')
            ->select('c.client_company', 'c.client_id', 'c.billing_email')
            ->leftJoin('client as c','c.client_id','=',"o.client_id")
            ->where('o.id','=',$order_id)
            ->where('o.is_delete','=','1')
            ->get();

      $billto = new AnetAPI\CustomerAddressType();
      $billto->setFirstName($post['creditFname']);
      $billto->setLastName($post['creditLname']);
      $billto->setCompany($retArray[0]->client_company);
      $billto->setAddress($post['street']);
      $billto->setCity($post['city']);
      $billto->setState($post['state']);
      $billto->setZip($post['zip']);
      $billto->setCountry("USA");

      $transactionRequestType->setBillTo($billto);
      
      $retArray = DB::table('payment_history as p')
        ->select('c.client_company', 'c.client_id', 'c.billing_email')
        ->leftJoin('orders as o','o.id','=',"p.order_id")
        ->leftJoin('client as c','o.client_id','=',"o.client_id")
        ->where('p.order_id','=',$order_id)
        ->where('p.is_delete','=',1)
        ->get();

      $billto = new AnetAPI\CustomerAddressType();
      $billto->setFirstName($post['creditFname']);
      $billto->setLastName($post['creditLname']);
      $billto->setCompany($retArray[0]->client_company);
      $billto->setAddress($post['street']);
      $billto->setCity($post['city']);
      $billto->setState($post['state']);
      $billto->setZip($post['zip']);
      $billto->setCountry("USA");


//       $billto = array("firstName"=>"Ellen","lastName"=>"Johnson","company"=>"Souveniropolis","address"=>"14 Main Street","city"=>"Pecan Springs","state"=>"TX","zip"=>"44628","country"=>"USA");
// $billto=json_encode($billto);
      $transactionRequestType->setBillTo($billto);

      $request = new AnetAPI\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId( $refId);
     
      $request->setTransactionRequest( $transactionRequestType);
      $controller = new AnetController\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
      
	  if ($response != null)
      {
        $tresponse = $response->getTransactionResponse();

        if (($tresponse != null) && ($tresponse->getResponseCode()== "1") )   
        {
          /*echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
          echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";*/


          ///// update payments

          $orderData = array('qb_id' => $qb_id,'order_id' => $order_id,'payment_amount' => $post['amount'],'payment_date' => date('Y-m-d'), 'payment_method' => 'Credit Card','authorized_TransId' => $tresponse->getTransId(),'authorized_AuthCode' => $tresponse->getAuthCode(),'qb_payment_id' => '', 'qb_web_reference' => '');

          $id = $this->common->InsertRecords('payment_history',$orderData);

          $retArrayPmt = DB::table('payment_history as p')
            ->select(DB::raw('SUM(p.payment_amount) as totalAmount'), 'o.grand_total')
            ->leftJoin('orders as o','o.id','=',"p.order_id")
            ->where('p.order_id','=',$order_id)
            ->where('p.is_delete','=',1)
            ->get();

          $balance_due = $retArrayPmt[0]->grand_total - $retArrayPmt[0]->totalAmount;
          $amt=array('total_payments' => round($retArrayPmt[0]->totalAmount, 2), 'balance_due' => round($balance_due, 2));

          $this->common->UpdateTableRecords('orders',array('id' => $order_id),$amt);

          

        if(($post['storeCard']==1) || ($post['linkToPay']==1)){
          // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($creditCardNumber);
            $creditCard->setExpirationDate($expiry);
            $paymentCreditCard = new AnetAPI\PaymentType();
            $paymentCreditCard->setCreditCard($creditCard);

            // Create the Bill To info
            $billto = new AnetAPI\CustomerAddressType();
            $billto->setFirstName($post['creditFname']);
            $billto->setLastName($post['creditLname']);
            $billto->setCompany($retArray[0]->client_company);
            $billto->setAddress($post['street']);
            $billto->setCity($post['city']);
            $billto->setState($post['state']);
            $billto->setZip($post['zip']);
            $billto->setCountry("USA");
            $billto->setEmail($retArray[0]->billing_email);


              // Create a Customer Profile Request
              //  1. create a Payment Profile
              //  2. create a Customer Profile   
              //  3. Submit a CreateCustomerProfile Request
              //  4. Validate Profiiel ID returned
              $date = date_create();
              $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

              $paymentprofile->setCustomerType('individual');
              $paymentprofile->setBillTo($billto);
              $paymentprofile->setPayment($paymentCreditCard);
              $paymentprofiles[] = $paymentprofile;
              $customerprofile = new AnetAPI\CustomerProfileType();
              $customerprofile->setDescription($retArray[0]->client_company);

              $customerprofile->setMerchantCustomerId("M_".date_timestamp_get($date));
              $customerprofile->setEmail($retArray[0]->billing_email);
              $customerprofile->setPaymentProfiles($paymentprofiles);

              $requestNew = new AnetAPI\CreateCustomerProfileRequest();
              $requestNew->setMerchantAuthentication($merchantAuthentication);
              $requestNew->setRefId( $refId);
              $requestNew->setProfile($customerprofile);
              $controller = new AnetController\CreateCustomerProfileController($requestNew);
              $responseProfile = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
              //print_r($responseProfile);exit;
              if (($responseProfile != null) && ($responseProfile->getMessages()->getResultCode() == "Ok") )
              {
                /*echo "Succesfully create customer profile : " . $responseProfile->getCustomerProfileId() . "\n";*/
                $paymentProfiles = $responseProfile->getCustomerPaymentProfileIdList();
                //echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";

                $profileData = array('profile_id' => $paymentProfiles[0],'client_id' => $retArray[0]->client_id);

                $id = $this->common->InsertRecords('client_payment_profiles',$profileData);

          if(($post['storeCard']==1) || ($post['linkToPay']==1)){
            // Create the payment data for a credit card
              $creditCard = new AnetAPI\CreditCardType();
              $creditCard->setCardNumber($creditCardNumber);
              $creditCard->setExpirationDate($expiry);
              $paymentCreditCard = new AnetAPI\PaymentType();
              $paymentCreditCard->setCreditCard($creditCard);

              // Create the Bill To info
              $billto = new AnetAPI\CustomerAddressType();
              $billto->setFirstName($post['creditFname']);
              $billto->setLastName($post['creditLname']);
              $billto->setCompany($retArray[0]->client_company);
              $billto->setAddress($post['street']);
              $billto->setCity($post['city']);
              $billto->setState($post['state']);
              $billto->setZip($post['zip']);
              $billto->setCountry("USA");


                // Create a Customer Profile Request
                //  1. create a Payment Profile
                //  2. create a Customer Profile   
                //  3. Submit a CreateCustomerProfile Request
                //  4. Validate Profiiel ID returned
                $date = date_create();
                $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

                $paymentprofile->setCustomerType('individual');
                $paymentprofile->setBillTo($billto);
                $paymentprofile->setPayment($paymentCreditCard);
                $paymentprofiles[] = $paymentprofile;
                $customerprofile = new AnetAPI\CustomerProfileType();
                $customerprofile->setDescription($retArray[0]->client_company);

                $customerprofile->setMerchantCustomerId("M_".date_timestamp_get($date));
                $customerprofile->setEmail($retArray[0]->billing_email);
                $customerprofile->setPaymentProfiles($paymentprofiles);

                $requestNew = new AnetAPI\CreateCustomerProfileRequest();
                $requestNew->setMerchantAuthentication($merchantAuthentication);
                $requestNew->setRefId( $refId);
                $requestNew->setProfile($customerprofile);
                $controller = new AnetController\CreateCustomerProfileController($requestNew);
                $responseProfile = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
                //print_r($responseProfile);exit;
                if (($responseProfile != null) && ($responseProfile->getMessages()->getResultCode() == "Ok") )
                {
                  /*echo "Succesfully create customer profile : " . $responseProfile->getCustomerProfileId() . "\n";*/
                  $paymentProfiles = $responseProfile->getCustomerPaymentProfileIdList();
                  //echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";

                  $profileData = array('profile_id' => $paymentProfiles[0],'client_id' => $retArray[0]->client_id);

                  $id = $this->common->InsertRecords('client_payment_profiles',$profileData);
                }
                /*else
                {
                  echo "ERROR :  Invalid response\n";
                  $errorMessages = $responseProfile->getMessages()->getMessage();
                  echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
                }*/
          }
          // update link to pay records
          if(($post['linkToPay']==1) && ($post['ltp_id']!=0)){
            $updateLtp=array('payment_flag' => 1, 'payment_date' => date('Y-m-d H:i:s'));
            $this->common->UpdateTableRecords('link_to_pay',array('ltp_id' => $post['ltp_id']),$updateLtp);

            $data = array("success"=>1, 'message' =>"Payment made Succesfully");
            return response()->json(['data'=>$data]);
          }
          // update credit card details stored for future use
            if(($post['linkToPay']==0) && ($post['storeCard']==1)){
              $suite='';
              if(isset($post['suite'])){
               $suite=$post['suite'];

              }

              $updateInvoice=array('creditFname' => $post['creditFname'], 'creditLname' => $post['creditLname'], 'creditCard' => $post['creditCard'], 'month' => $post['expMonth'],  'year' => $post['expYear'],  'street' => $post['street'],  'suite' => $suite,  'city' => $post['city'],  'state' => $post['state'],  'zip' => $post['zip']);

              $this->common->UpdateTableRecords('invoice',array('id' => $post['invoice_id']),$updateInvoice);
            }
          $data = array("success"=>1,'amt' =>$amt);
          return response()->json(['data'=>$data]);
        }
        else
        {
            //echo  "Charge Credit Card ERROR :  Invalid response";
            $data = array("success"=>0,'message' =>"Charge Credit Card ERROR :  Invalid response");
            return response()->json(['data'=>$data]);
        }
      }
      else
      {
          //echo  "Charge Credit card Null response returned";
          $data = array("success"=>0,'message' =>"Charge Credit card Null response returned");
          return response()->json(['data'=>$data]);
      }
      //return $response;
  }


function refundTransaction($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("0015");
    $creditCard->setExpirationDate("XXXX");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    //create a transaction
    $transactionRequest = new AnetAPI\TransactionRequestType();
    $transactionRequest->setTransactionType( "refundTransaction"); 
    $transactionRequest->setAmount($amount);
    $transactionRequest->setPayment($paymentOne);
 

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest( $transactionRequest);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null)
    {
      if($response->getMessages()->getResultCode() == \SampleCode\Constants::RESPONSE_OK)
      {
        $tresponse = $response->getTransactionResponse();
        
        if ($tresponse != null && $tresponse->getMessages() != null)   
        {
          echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
          echo "Refund SUCCESS: " . $tresponse->getTransId() . "\n";
          echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
          echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
        }
        else
        {
          echo "Transaction Failed \n";
          if($tresponse->getErrors() != null)
          {
            echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
            echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";            
          }
        }
      }
      else
      {
        echo "Transaction Failed \n";
        $tresponse = $response->getTransactionResponse();
        if($tresponse != null && $tresponse->getErrors() != null)
        {
          echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
          echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";                      
        }
        else
        {
          echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
          echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
        }
      }      
    }
    else
    {
      echo  "No response returned \n";
    }

    return $response;
  }

    public function linktopay($token)
    {
        $payment_data = $this->common->GetTableRecords('link_to_pay',array('session_link' => $token));
        $data['stateArray'] = $this->common->GetTableRecords('state',array());
        $data['orderArray'] = $payment_data[0];

        return view('auth.payment',$data)->render();
    }

  /*function createCustomerProfile($email){
    
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
      $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
      $refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber(  "4111111111111111");
    $creditCard->setExpirationDate( "2038-12");
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);

    // Create the Bill To info
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName("Ellen");
    $billto->setLastName("Johnson");
    $billto->setCompany("Souveniropolis");
    $billto->setAddress("14 Main Street");
    $billto->setCity("Pecan Springs");
    $billto->setState("TX");
    $billto->setZip("44628");
    $billto->setCountry("USA");
    
   // Create a Customer Profile Request
   //  1. create a Payment Profile
   //  2. create a Customer Profile   
   //  3. Submit a CreateCustomerProfile Request
   //  4. Validate Profiiel ID returned

    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription("Customer 2 Test PHP");

    $customerprofile->setMerchantCustomerId("M_".$email);
    $customerprofile->setEmail($email);
    $customerprofile->setPaymentProfiles($paymentprofiles);

    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId( $refId);
    $request->setProfile($customerprofile);
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "Succesfully create customer profile : " . $response->getCustomerProfileId() . "\n";
      $paymentProfiles = $response->getCustomerPaymentProfileIdList();
      echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
     }
    else
    {
      echo "ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      createCustomerProfile("test123@test.com");*/
}