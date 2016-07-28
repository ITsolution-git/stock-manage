<?php

namespace RocketShipIt\Service\Shipment;

use \RocketShipIt\Helper\General;

/**
* Main Shipping class for producing labels and notifying carriers of pickups.
*
* This class is a wrapper for use with all carriers to produce labels for
* shipments.  Valid carriers are: UPS, DHL, Stamps, CANADA USPS, and FedEx.
*/
class Stamps extends \RocketShipIt\Service\Common
{
    
    var $customsLines;
    var $helper;
    public $addressValidationResponse;
    public $shipmentRequest;
    public $package;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General;
        parent::__construct($carrier);
        $this->customsLines = array();
        $this->shipmentRequest = new \stdClass();
    }

    
    function addPackageToSTAMPSshipment($packageObj)
    {
        // Add Rate array to the stamps shipment object
        $this->package = $packageObj;
    }

    function addCustomsLineToSTAMPSshipment($customsObj)
    {
        $customs = $customsObj;
        // Customs document generation
        $custLine = new \stdClass();
        if (strlen($customs->customsDescription) > 60) {
            $customs->customsDescription = substr($customs->customsDescription, 0, 60);
        }
        $custLine->Description = $customs->customsDescription; //required
        $custLine->Quantity = $customs->customsQuantity; //required
        $custLine->Value = $customs->customsValue; //required
        $lbsAndOunces = $this->helper->weightToLbsOunces($customs->customsWeight);
        $custLine->WeightLb = $lbsAndOunces[0]; //required
        $custLine->WeightOz = $lbsAndOunces[1]; //required
        $custLine->HSTariffNumber = $customs->customsHsTariff; //max 6 digits, required
        $custLine->CountryOfOrigin = $customs->customsOriginCountry; //max 2 digits, required
        array_push($this->customsLines, $custLine);
    }

    public function validateAddress()
    {
        $v = new \RocketShipIt\AddressValidate('STAMPS');

        // Pass shipment user/pass over to av so user
        // can set this per request
        $v->setParameter('username', $this->username);
        $v->setParameter('password', $this->password);

        $v->setParameter('toState', $this->toState);
        $v->setParameter('toCity', $this->toCity);
        $v->setParameter('toAddr1', $this->toAddr1);
        if ($this->toAddr2 != '') {
            $v->setParameter('toAddr2', $this->toAddr2);
        }
        $v->setParameter('toCode', $this->toCode);
        $v->setParameter('toCountry', $this->toCountry);
        $v->setParameter('toPhone', $this->toPhone);
        if ($this->shipCountry != '') {
            $v->setParameter('shipCountry', $this->shipCountry);
        } else {
            $v->setParameter('shipCountry', $this->toCountry);
        }
        $this->addressValidationResponse = $v->validate();
    }

    public function addCredentialsToRequest()
    {
        $creds = $this->core->getCredentials();
        if ($this->core->authToken != '') {
            $this->shipmentRequest->Authenticator = $this->authToken;
        } else {
            $this->shipmentRequest->Credentials = $creds;
        }
    }

    public function addFromToRequest()
    {
        $from = new \stdClass;
        if ($this->shipContact != '') {
            $from->FullName = $this->shipContact;
        }
        if ($this->shipper != '') {
            $from->Company = $this->shipper;
        }

        if ($this->shipPhone != '') {
            $from->PhoneNumber = $this->shipPhone;
        }
        $from->Address1 = $this->shipAddr1;
        if ($this->shipAddr2 != '') {
            $from->Address2 = $this->shipAddr2;
        }
        $from->City = $this->shipCity;
        $from->State = $this->shipState;

        if ($this->shipCountry == 'US') {
            $from->ZIPCode = $this->shipCode;
        } else {
            $from->PostalCode = $this->shipCode;
        }

        $this->shipmentRequest->From = $from;
    }

    public function addRecipientToRequest()
    {
        $to = $this->addressValidationResponse->Address;
        if ($this->toCompany != '') {
            $to->Company = $this->toCompany;
        }
        $to->FullName = $this->toName;

        $this->shipmentRequest->To = $to;
    }

    public function addCustomsToRequest()
    {
        $customs = new \stdClass();
        // Commercial Sample, Gift, Document, or Other.
        $customs->ContentType = $this->customsContentType; //required
        $customs->Comments = $this->customsComments; //not required
        $customs->LicenseNumber = $this->customsLicenseNumber; //max length 6 //not required
        $customs->CertificateNumber = $this->customsCertificateNumber; //max length 8 //not required
        $customs->InvoiceNumber = $this->customsInvoiceNumber; //max length 10 //not required
        $customs->OtherDescribe = $this->customsOtherDescribe; //max length 20 //required when contentType is other
        $customs->CustomsLines = $this->customsLines; //required
        $this->shipmentRequest->Customs = $customs;
    }

    public function buildShipmentRequest()
    {
        $this->validateAddress();
        if (is_soap_fault($this->addressValidationResponse)) {
            return $this->addressValidationResponse;
        }

        $this->addCredentialsToRequest();
        $this->shipmentRequest->IntegratorTxID = $this->generateRandomString();
        $this->shipmentRequest->Rate = $this->package;
        $this->addFromToRequest();
        $this->addRecipientToRequest();
        if ($this->toCountry != 'US') {
            $this->addCustomsToRequest();
        }
        $this->shipmentRequest->ImageType = $this->upCaseFirstLetter($this->imageType);

        if ($this->core->debugMode) {
            $this->shipmentRequest->SampleOnly = 'true';
        }
        if ($this->referenceValue != '') {
            $this->shipmentRequest->memo = $this->referenceValue;
        }
        if ($this->emailTo != '') {
            $this->shipmentRequest->recipient_email = $this->emailTo;
        }
    }

    function sendSTAMPSshipment()
    {
        $this->buildShipmentRequest();

        if ($this->package->PackageType == 'Letter') {
            $response = @$this->core->request('CreateEnvelopeIndicium', $this->shipmentRequest);
        } else {
            $response = @$this->core->request('CreateIndicium', $this->shipmentRequest);
        }

        if (is_soap_fault($response)) {
            $resp = array();
            $resp['error'] = $response->faultstring;

            return $resp;
        }

        $this->response = $response;

        return $this->processStampsResponse();
    }

    public function upCaseFirstLetter($word)
    {
        $word = strtolower($word);
        return ucfirst($word);
    }

    /**
     * Creates random string of alphanumeric characters
     * 
     * @return string
     */
    private function generateRandomString()
    {
        $length = 128;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = "";
        
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }
        return $string;
    }

    public function getMediaUrls()
    {
        if (!isset($this->response->URL)) {
            return array();
        }

        return explode(' ', $this->response->URL);
    }

    public function getMedia()
    {
        $media = array();
        foreach ($this->getMediaUrls() as $url) {
            $media[] = $this->core->mediaRequest($url);
        }

        return $media;
    }

    public function getEncodedLabel()
    {
        $urls = $this->getMediaUrls();
        if (!$urls) {
            return '';
        }

        return $this->core->mediaRequest($urls[0]);
    }

    public function processStampsResponse()
    {
        $response = array(
            'charges' => '',
            'trk_main' => '',
            'transaction_id' => '',
            'pkgs' => array(
                array(
                    'pkg_trk_num' => '',
                    'label_fmt' => '',
                    'label_img' => '',
                ),
            ),
            'media' => array(),
        );

        if (isset($this->response->Rate->Amount)) {
            $response['charges'] = number_format($this->response->Rate->Amount + $this->getAddonAmount(), 2);
        }
        if (isset($this->response->TrackingNumber)) {
            $response['trk_main'] = $this->response->TrackingNumber;
        }
        if (isset($this->response->StampsTxID)) {
            $response['transaction_id'] = $this->response->StampsTxID;
        }
        if (isset($this->response->TrackingNumber)) {
            $response['pkgs'][0]['pkg_trk_num'] = $this->response->TrackingNumber;
        }
        $response['pkgs'][0]['label_fmt'] = $this->imageType;
        $label = $this->getEncodedLabel();
        $response['pkgs'][0]['label_img'] = $label;

        $response['media'] = $this->getMedia();

        return $response;
    }

    function getAddonAmount()
    {
        if (isset($this->response->Rate->AddOns->AddOnV7->Amount)) {
            return $this->response->Rate->AddOns->AddOnV7->Amount;
        }

        return 0.0;
    }
}
