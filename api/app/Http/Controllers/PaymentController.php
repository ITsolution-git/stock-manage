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

    public function chargeCreditCard()
    {
        $post = Input::all();
        
        $amount=round($post['amount'],2);

        if(isset($post['company_id']))
        {
            $company_id=$post['company_id'];
            $retCredsArray = DB::table('authorize_detail as au')
            ->select('au.login', 'au.transactionkey', 'au.is_live')
            ->leftJoin('api_link_table as ai','ai.id','=',"au.link_id")
            ->where('ai.company_id','=',$company_id)
            ->where('ai.status','=','1')
            ->where('au.is_active','=','1')
            ->where('ai.api_id','=',3)
            ->get();
        }

        if(count($retCredsArray)<1){
            $data = array("success"=>0,'message' =>"Please integrate Authorize.net details");
            return response()->json(['data'=>$data]);
        }

        $envConst=$retCredsArray[0]->is_live;

        $creditCardNumber=$post['creditCard'];
        $creditCardNumberStored=substr($creditCardNumber, -4);
        $expiry=$post['expMonth'].$post['expYear'];
        $cvv=$post['cvv'];
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($retCredsArray[0]->login);
        $merchantAuthentication->setTransactionKey($retCredsArray[0]->transactionkey);

        $qb_data = $this->common->GetTableRecords('invoice',array('id' => $post['invoice_id']),array());
        $qb_id = $qb_data[0]->qb_id;
        $order_id = $qb_data[0]->order_id;

        $order = new AnetAPI\OrderType();
        $order->setDescription("Payment for Order ID: ".$order_id);
        $order->setInvoiceNumber("INV - ".$order_id);

        $retArray = DB::table('orders as o')
            ->select('c.client_company', 'c.client_id', 'c.billing_email')
            ->leftJoin('client as c','c.client_id','=',"o.client_id")
            ->where('o.id','=',$order_id)
            ->where('o.is_delete','=','1')
            ->get();

        // direct payment with saved payment profile id on Authorized.net
        if(isset($post['savedCard']) && $post['savedCard']!=0 ){
            $profilePayment = $this->common->GetTableRecords('client_payment_profiles',array('client_id' => $retArray[0]->client_id));
            $resultProfile = $this->chargeCustomerProfile($merchantAuthentication, $profilePayment[0]->profile_id, $post['savedCard'], $amount, $order, $envConst);

            if($resultProfile['success']==0){
                $data = array("success"=>0,'message' =>"Error from Authorized.net. Please try with any other saved card or new credit card.");
                return response()->json(['data'=>$data]);
            }else{
                $orderData = array('qb_id' => $qb_id,'order_id' => $order_id, 'payment_card' => $creditCardNumberStored, 'payment_amount' => $post['amount'],'payment_date' => date('Y-m-d'), 'payment_method' => 'Credit Card', 'authorized_TransId' => $resultProfile['getTransId'],'authorized_AuthCode' => $resultProfile['getAuthCode'], 'qb_payment_id' => '', 'qb_web_reference' => '');

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

                $data = array("success"=>1,'amt' =>$amt);
                return response()->json(['data'=>$data]);
            }
        }

        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($creditCardNumber);
        $creditCard->setExpirationDate($expiry);
        $creditCard->setCardCode($cvv);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);

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

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);

        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        if($envConst==1){
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }else{
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }
        
      
        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == "Ok")
            {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null)   
                {
                  /*echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                  echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";*/

                  // update payments
                    $orderData = array('qb_id' => $qb_id,'order_id' => $order_id, 'payment_card' => $creditCardNumberStored, 'payment_amount' => $post['amount'],'payment_date' => date('Y-m-d'), 'payment_method' => 'Credit Card','authorized_TransId' => $tresponse->getTransId(),'authorized_AuthCode' => $tresponse->getAuthCode(),'qb_payment_id' => '', 'qb_web_reference' => '');

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

                    if(isset($post['storeCard']) && $post['storeCard']==1)
                    {
                        $profilePayment = $this->common->GetTableRecords('client_payment_profiles',array('client_id' => $retArray[0]->client_id));
                        if(count($profilePayment)<1){

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
                            if($envConst==1){
                                $responseProfile = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
                            }else{
                                $responseProfile = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
                            }
                      
                            if (($responseProfile != null) && ($responseProfile->getMessages()->getResultCode() == "Ok") )
                            {
                                /*echo "Succesfully create customer profile : " . $responseProfile->getCustomerProfileId() . "\n";*/
                                $paymentProfiles = $responseProfile->getCustomerPaymentProfileIdList();
                                //echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";

                                $profileData = array('profile_id' => $responseProfile->getCustomerProfileId(),'client_id' => $retArray[0]->client_id);

                                $cpp_id = $this->common->InsertRecords('client_payment_profiles',$profileData);
                                $expiryDate=$post['expMonth']."/".$post['expYear'];

                                $profileDetailData = array('cpp_id'=> $cpp_id, 'payment_profile_id' => $paymentProfiles[0],'card_number' => $creditCardNumber, 'expiration' => $expiryDate);

                                $id = $this->common->InsertRecords('client_payment_profiles_detail', $profileDetailData);
                                $amt['payment_profile_id'] = $paymentProfiles[0];
                                $amt['card_number'] = $creditCardNumber;
                                $amt['expiration'] = $expiryDate;
                            }

                        }else{

                            $creditCard = new AnetAPI\CreditCardType();
                            $creditCard->setCardNumber($creditCardNumber);
                            $creditCard->setExpirationDate($expiry);
                            $creditCard->setCardCode($cvv);
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

                            $result = $this->createCustomerPaymentProfile($profilePayment[0]->profile_id, $merchantAuthentication, $paymentCreditCard, $billto, $envConst);
                            if($result['success']==1){
                                $expiryDate=$post['expMonth']."/".$post['expYear'];
                                $payment_data = $this->common->GetTableRecords('client_payment_profiles_detail',array('cpp_id' => $profilePayment[0]->cpp_id, 'card_number' => $creditCardNumber));

                                if(count($payment_data)<1){
                                    // addding new credit card payment profile with expiry period entered by user
                                    $profileDetailData = array('cpp_id'=> $profilePayment[0]->cpp_id, 'payment_profile_id' => $result['profile_id'],'card_number' => $creditCardNumber, 'expiration' => $expiryDate);
                                    $id = $this->common->InsertRecords('client_payment_profiles_detail', $profileDetailData);
                                }else{
                                    // Updating existing credit card payment profile with new expiry period entered by user
                                    $profileDetailData = array('payment_profile_id' => $result['profile_id'],'expiration' => $expiryDate);
                                    $this->common->UpdateTableRecords('client_payment_profiles_detail',array('cppd_id' => $payment_data[0]->cppd_id),$profileDetailData);
                                }
                                $amt['payment_profile_id'] = $result['profile_id'];
                                $amt['card_number'] = $creditCardNumber;
                                $amt['expiration'] = $expiryDate;
                            }
                        }
                        
                    } 
                    // update link to pay records
                    if(($post['linkToPay']==1) && ($post['ltp_id']!=0))
                    {
                        $updateLtp=array('payment_flag' => 1, 'payment_date' => date('Y-m-d H:i:s'));
                        $this->common->UpdateTableRecords('link_to_pay',array('ltp_id' => $post['ltp_id']),$updateLtp);
                        $data = array("success"=>1, 'message' =>"Payment made Succesfully");
                        return response()->json(['data'=>$data]);
                    }
      // update credit card details stored for future use
                    if(($post['linkToPay']==0) && ($post['storeCard']==1))
                    {
                        $suite='';
                        if(isset($post['suite']))
                        {
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
                    //echo "Transaction Failed \n";
                    $message="Transaction Failed. ";
                    if($tresponse->getErrors() != null)
                    {
                        $message=$message.$tresponse->getErrors()[0]->getErrorText();
                    }
                    $data = array("success"=>0,'message' =>$message);
                    return response()->json(['data'=>$data]);
                }
            }
            else
            {
                  //echo "Transaction Failed \n";
                  $message="Transaction Failed. ";
                  $tresponse = $response->getTransactionResponse();
                  if($tresponse != null && $tresponse->getErrors() != null)
                  {
                      $message=$message.$tresponse->getErrors()[0]->getErrorText();
                  }
                  else
                  {
                      $message=$message.$response->getMessages()->getMessage()[0]->getText();
                  }
                  $data = array("success"=>0,'message' =>$message);
                  return response()->json(['data'=>$data]);
              }
        }
        else
        {
            $data = array("success"=>0,'message' =>"No response returned");
            return response()->json(['data'=>$data]);
        }
        //return $response;
    }
        
    // Create the payment data for a credit card
    public function createCustomerPaymentProfile($existingcustomerprofileid, $merchantAuthentication, $paymentCreditCard, $billto, $envConst){
        $refId = 'ref' . time();

        // Create a new Customer Payment Profile
        $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
        $paymentprofile->setCustomerType('individual');
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);

        $paymentprofiles[] = $paymentprofile;

        // Submit a CreateCustomerPaymentProfileRequest to create a new Customer Payment Profile
        $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);
        //Use an existing profile id
        $paymentprofilerequest->setCustomerProfileId( $existingcustomerprofileid );
        $paymentprofilerequest->setPaymentProfile( $paymentprofile );
        $paymentprofilerequest->setValidationMode("liveMode");
        $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
        if($envConst==1){
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }else{
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
        {
            //echo "Create Customer Payment Profile SUCCESS: " . $response->getCustomerPaymentProfileId() . "\n";
            $data = array("success"=>1,'profile_id' =>$response->getCustomerPaymentProfileId());
        }
        else
        {
            /*echo "Create Customer Payment Profile: ERROR Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";*/
            $data = array("success"=>0,'error' =>$errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText());
        }
        return $data;
    }   

    // Fetching transaction details from Authorized.net
    public function getTransactionDetails($transactionId, $merchantAuthentication, $envConst) {

      $refId = 'ref' . time();

      $request = new AnetAPI\GetTransactionDetailsRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setTransId($transactionId);

      $controller = new AnetController\GetTransactionDetailsController($request);
      if($envConst==1){
          $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
      }else{
          $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
      }

      if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
      {

        $responseData=array(
        'amount' => $response->getTransaction()->getAuthAmount(),
        'order' => $response->getTransaction()->getOrder()->getdescription(),
        'invoice' => $response->getTransaction()->getOrder()->getinvoiceNumber(),
        'firstName' => $response->getTransaction()->getBillTo()->getfirstName(),
        'lastName' => $response->getTransaction()->getBillTo()->getlastName(),
        'company' => $response->getTransaction()->getBillTo()->getcompany(),
        'address' => $response->getTransaction()->getBillTo()->getaddress(),
        'city' => $response->getTransaction()->getBillTo()->getcity(),
        'state' => $response->getTransaction()->getBillTo()->getstate(),
        'zip' => $response->getTransaction()->getBillTo()->getzip(),
        'country' => $response->getTransaction()->getBillTo()->getcountry()
        );

        $data = array("success"=>1,'message' =>$responseData);
      }
      else
      {
        //echo "ERROR :  Invalid response\n";
        //$errorMessages = $response->getMessages()->getMessage();
        //echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        $data = array("success"=>0,'message' =>'Authorized.net Transaction ID is not valid');
      }
      return $data;
    }

    public function refundTransaction()
    {
        $post = Input::all();
        $payment_id=$post['payment_id'];
        $retArrayRefund = DB::table('payment_history')
            ->select('payment_card', 'payment_amount', 'authorized_TransId', 'payment_date', 'authorized_AuthCode')
            ->where('payment_id','=',$payment_id)
            ->where('is_delete','=','1')
            ->get();
        if(count($retArrayRefund)<1){
            $data = array("success"=>0,'message' =>"Invalid Payment record");
            return response()->json(['data'=>$data]);
        }

        $amount=$retArrayRefund[0]->payment_amount;
        $refTransId=$retArrayRefund[0]->authorized_TransId;
        $creditCardNumber=$retArrayRefund[0]->payment_card;
        $creditCardNumberStored = substr($creditCardNumber, -4);
        $payment_date=$retArrayRefund[0]->payment_date;
        $authorized_AuthCode=$retArrayRefund[0]->authorized_AuthCode;

        if(isset($post['company_id']))
        {
            $company_id=$post['company_id'];
            $retCredsArray = DB::table('authorize_detail as au')
            ->select('au.login', 'au.transactionkey', 'au.is_live')
            ->leftJoin('api_link_table as ai','ai.id','=',"au.link_id")
            ->where('ai.company_id','=',$company_id)
            ->where('ai.status','=','1')
            ->where('au.is_active','=','1')
            ->where('ai.api_id','=',3)
            ->get();
        }
        if(count($retCredsArray)<1){
            $data = array("success"=>0,'message' =>"Please integrate Authorize.net details");
            return response()->json(['data'=>$data]);
        }

        $envConst=$retCredsArray[0]->is_live;

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($retCredsArray[0]->login);
        $merchantAuthentication->setTransactionKey($retCredsArray[0]->transactionkey);
        $result = $this->getTransactionDetails($refTransId, $merchantAuthentication, $envConst);
    
        if($result['success']==0){
            $data = array("success"=>0,'message' =>"Authorized.net Transaction ID is not valid");
            return response()->json(['data'=>$data]);
        }
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($creditCardNumberStored);
        $creditCard->setExpirationDate("XXXX");
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $retArrayOrder = DB::table('invoice')
        ->select('order_id')
        ->where('id','=',$post['invoice_id'])
        ->get();

        $order_id=$retArrayOrder[0]->order_id;
        $result=$result['message'];

        $order = new AnetAPI\OrderType();
        $order->setDescription("Refund - ".$result['order']);
        $order->setInvoiceNumber($result['invoice']);

        //create a transaction
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType( "refundTransaction"); 
        $transactionRequest->setAmount($result['amount']);
        $transactionRequest->setPayment($paymentOne);
        $transactionRequest->setrefTransId($refTransId);

        $retArrayBilling = DB::table('orders as o')
            ->select('c.client_company', 'c.client_id', 'c.billing_email')
            ->leftJoin('client as c','c.client_id','=',"o.client_id")
            ->where('o.id','=',$order_id)
            ->where('o.is_delete','=','1')
            ->get();

        // Billing information fetched from Transaction id of Authorized.net
        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($result['firstName']);
        $billto->setLastName($result['lastName']);
        $billto->setCompany($result['company']);
        $billto->setAddress($result['address']);
        $billto->setCity($result['city']);
        $billto->setState($result['state']);
        $billto->setZip($result['zip']);
        $billto->setCountry($result['country']);

        $transactionRequest->setBillTo($billto);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest( $transactionRequest);
        $controller = new AnetController\CreateTransactionController($request);
        if($envConst==1){
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }else{
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }

        if ($response != null)
        {
          if($response->getMessages()->getResultCode()  == "Ok")
          {
            $tresponse = $response->getTransactionResponse();
            
            if ($tresponse != null && $tresponse->getMessages() != null)   
            {
              //echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
              //echo "Refund SUCCESS: " . $tresponse->getTransId() . "\n";
              //echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
              //echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
              $message="This transaction has been approved.";
              //$tresponse->getTransId();

              $orderData = array('order_id' => $order_id, 'payment_card' => $creditCardNumber, 'payment_amount' => $amount,'payment_date' => $payment_date ,'payment_refund_date' => date('Y-m-d'), 'payment_method' => 'Credit Card', 'authorized_TransId' => $refTransId, 'authorized_TransId_refund' => $tresponse->getTransId(), 'authorized_AuthCode' => $authorized_AuthCode);

              $id = $this->common->InsertRecords('payment_refund',$orderData);

              //$result = $this->common->DeleteTableRecords('payment_history',array('payment_id'=>$payment_id, 'order_id'=>$order_id, 'authorized_TransId'=>$authorized_TransId));

              $data = array("success"=>1,'message' =>$message);
              return response()->json(['data'=>$data]);
            }
            else
            {
              $message = "Refund Transaction Failed. ";
              if($tresponse->getErrors() != null)
              {
                //echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                //echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                $message=$message.$tresponse->getErrors()[0]->getErrorText();
              }
              $data = array("success"=>0,'message' =>$message);
              return response()->json(['data'=>$data]);
            }
          }
          else
          {
            $message = "Refund Transaction Failed. ";
            $tresponse = $response->getTransactionResponse();
            if($tresponse != null && $tresponse->getErrors() != null)
            {
              //echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
              //echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
              $message=$message.$tresponse->getErrors()[0]->getErrorText();
            }
            else
            {
              //echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
              //echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
              $message=$message.$response->getMessages()->getMessage()[0]->getText();
            }
            $data = array("success"=>0,'message' =>$message);
            return response()->json(['data'=>$data]);
          }      
        }
        else
        {
          $message = "No response returned";
          $data = array("success"=>0,'message' =>$message);
          return response()->json(['data'=>$data]);
        }

        //return $response;
    }

    public function linktopay($token)
    {
        //$payment_data = $this->common->GetTableRecords('link_to_pay',array('session_link' => $token));

        $payment_data = DB::table('link_to_pay as lp')
            ->select('lp.session_link', 'lp.ltp_id', 'lp.created_date', 'o.balance_due', 'lp.order_id', 'u.id as company_id', 'i.id as invoice_id')
            ->leftJoin('orders as o','o.id','=',"lp.order_id")
            ->leftJoin('invoice as i','i.order_id','=',"o.id")
            ->leftJoin('client as c','c.client_id','=',"o.client_id")
            ->leftJoin('users as u','u.id','=',"c.company_id")
            ->where('lp.session_link','=',$token)
            ->where('lp.payment_flag','=','0')
            ->get();


        if(count($payment_data)<1){
          //$data['orderArray'] = new stdClass();
          //$newobject = (object) null;
          $data['orderArray'] = (object) null;
          $data['orderArray']->link_status=1;
        }else{
          $data['orderArray'] = $payment_data[0];
          $data['stateArray'] = $this->common->GetTableRecords('state',array());
          $time = strtotime($data['orderArray']->created_date);
          $curtime = time();
        
          //if(($curtime-$time) > 86400) {     //86400 seconds
            //echo "Link expired";
            //$payment_flag=array('payment_flag' => '1');
            //$this->common->UpdateTableRecords('link_to_pay',array('session_link' => $token),$payment_flag);
            //$data['orderArray']->link_status=1;
          //}else{

            $user_data = DB::table('orders as o')
            ->select('s.sales_name', 's.sales_email' , 's.sales_phone', 's.sales_web', 'u.name' , 'u.email' , 'u.phone')
            ->leftJoin('sales as s','s.id','=', 'o.sales_id')
            ->leftJoin('users as u','u.id','=', 'o.account_manager_id')
            ->where('o.id','=',$data['orderArray']->order_id)
            ->get();

            $data['orderArray']->sales_name=$user_data[0]->sales_name;
            $data['orderArray']->sales_email=$user_data[0]->sales_email;
            $data['orderArray']->sales_phone=$user_data[0]->sales_phone;
            $data['orderArray']->sales_web=$user_data[0]->sales_web;
            $data['orderArray']->account_name=$user_data[0]->name;
            $data['orderArray']->account_email=$user_data[0]->email;
            $data['orderArray']->account_phone=$user_data[0]->phone;
          //}
        }
        return view('auth.payment',$data)->render();
    }

    public function chargeCustomerProfile($merchantAuthentication, $profileid, $paymentprofileid, $amount, $order, $envConst){
        // Common setup for API credentials
        $refId = 'ref' . time();

        $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
        $profileToCharge->setCustomerProfileId($profileid);
        $paymentProfile = new AnetAPI\PaymentProfileType();
        $paymentProfile->setPaymentProfileId($paymentprofileid);
        $profileToCharge->setPaymentProfile($paymentProfile);

        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setProfile($profileToCharge);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        if($envConst==1){
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }else{
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }

        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == "Ok")
            {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null)   
                {
                    //echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                    //echo  "Charge Customer Profile APPROVED  :" . "\n";
                    //echo " Charge Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                    //echo " Charge Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n";
                    //echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
                    //echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";

                    $data = array("success"=>1, 'getTransId' =>$tresponse->getTransId(), 'getAuthCode' =>$tresponse->getAuthCode());
                }
                else
                {
                    //echo "Transaction Failed \n";
                    $message="Transaction Failed. ";
                    if($tresponse->getErrors() != null)
                    {
                        //echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        //echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                        $message=$message.$tresponse->getErrors()[0]->getErrorText();
                    }
                    $data = array("success"=>0,'message' =>$message);
                }
            }
            else
            {
                //echo "Transaction Failed \n";
                $message="Transaction Failed. ";
                $tresponse = $response->getTransactionResponse();
                if($tresponse != null && $tresponse->getErrors() != null)
                {
                    //echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    //echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    $message=$message.$tresponse->getErrors()[0]->getErrorText();
                }
                else
                {
                    //echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    //echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                    $message=$message.$response->getMessages()->getMessage()[0]->getText();
                }
                $data = array("success"=>0,'message' =>$message);
            }
        }
        else
        {
            //echo  "No response returned \n";
            $data = array("success"=>0,'message' =>'No response returned');
        }

    return $data;
    }
}