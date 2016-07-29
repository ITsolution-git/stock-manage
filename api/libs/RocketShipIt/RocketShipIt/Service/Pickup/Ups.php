<?php

namespace RocketShipIt\Service\Pickup;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

class Ups extends \RocketShipIt\Service\Common
{
    var $customsLines;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);    
    }

    function upsRateRequest()
    {
        $request = array();

        // Pickup address
        $a = array();
        $a['AddressLine'] = $this->pickupAddr1;
        $a['City'] = $this->pickupCity;
        $a['StateProvince'] = $this->pickupState;
        $a['PostalCode'] = $this->pickupCode;
        $a['CountryCode'] = $this->pickupCountry;
        if ($this->pickupResidential != '') {
            $a['ResidentialIndicator'] = 'Y';
        } else {
            $a['ResidentialIndicator'] = 'N';
        }

        $request['Request'] = '';
        $request['PickupAddress'] = $a;

        // Y - Indicates alternative address than what is associated with shipper account
        // N - Default address associated with shipper account, defaults to N
        if ($this->pickupAlternative != '') {
            $request['AlternateAddressIndicator'] = 'Y';
        } else {
            $request['AlternateAddressIndicator'] = 'N';
        }

        if ($this->pickupDate != '') {
            $pickupDate = array();
            $pickupDate['CloseTime'] = $this->closeTime; //HHmm 0-23, 0-59
            $pickupDate['ReadyTime'] = $this->readyTime; //HHmm 0-23, 0-59
            $pickupDate['PickupDate'] = $this->pickupDate; //yyyyMMdd
            $request['PickupDateInfo'] = $pickupDate;
        }

        // 01 - Same-Day pickup
        // 02 - Future-Day pickup
        // 03 - A Specific-Day pickup
        $request['ServiceDateOption'] = '02';

        return $this->upsSoapRequest($request, 1);
    }

    function requestPendingStatus()
    {
        $request = array();
        $request['Request'] = '';
        
        $request['PickupType'] = "01";
        $request['AccountNumber'] = $this->accountNumber;
        
        return $this->upsSoapRequest($request, 3);
    }

    public function buildPickupRequest()
    {
        $request = array();
        $request['Request'] = '';
        
        $request['RatePickupIndicator'] = 'Y';
            
        // Pickup Address
        $request['PickupAddress'] = array(
            'CompanyName'	       => $this->pickupCompanyName,
            'ContactName'	       => $this->pickupContactName,
            'AddressLine'          => $this->pickupAddr1,
            'City'                 => $this->pickupCity,
            'StateProvince'        => $this->pickupState,
            'PostalCode'           => $this->pickupCode,
            'CountryCode'          => $this->pickupCountry,
            'Phone'                => array('Number' => $this->pickupPhone),
            'ResidentialIndicator' => ($this->pickupResidential != '' ? 'Y' : 'N'),
        );
        
        if($this->pickupRoom) {
            $request['PickupAddress']['Room'] = $this->pickupRoom;
        }
        
        if($this->pickupRoom) {
            $request['PickupAddress']['Floor'] = $this->pickupFloor;
        }
        
        $request['AlternateAddressIndicator'] = ($this->pickupAlternative != '' ? 'Y' : 'N');
        
        // Tracking number
        if($this->trackingNumber != "") {
            $request['TrackingData'] = array(
                "TrackingNumber" => $this->trackingNumber,
            );
        }
        
        // Pickup Date Info
        $request['PickupDateInfo'] = array(
            'CloseTime'  => $this->closeTime,
            'ReadyTime'  => $this->readyTime,
            'PickupDate' => $this->pickupDate,
        );
        
        /**
         * Pickup Piece Type
         * 
         * ServiceCode:
         * 001 - UPS Next Day Air
         * 002 - UPS Next Day Air
         * 003 - UPS Ground
         * 004 - UPS Ground, UPS Standard
         * 007 - UPS Worldwide Express
         * 008 - UPS Worldwide Expedited
         * 011 - UPS Standard
         * 012 - UPS Three Day Select
         * 013 - UPS Next Day Air Saver
         * 014 - UPS Next Day Air Early A.M.
         * 021 - UPS Economy
         * 031 - UPS Basic
         * 054 - UPS Worldwide Express Plus
         * 059 - UPS Second Day Air A.M.
         * 064 - UPS Express NA1
         * 065 - UPS Saver
         * 082 - UPS Today Standard
         * 083 - UPS Today Dedicated Courier
         * 084 - UPS Today Intercity
         * 085 - UPS Today Express
         * 086 - UPS Today Express Saver
         * 
         * ContainerCode:
         * 01 = PACKAGE
         * 02 = UPS LETTER
         */
        $request['PickupPiece'] = array(
        	'ServiceCode'            => $this->pickupServiceCode,
            'Quantity'               => $this->pickupQuantity,
            'DestinationCountryCode' => $this->pickupDestination,
            'ContainerCode'          => $this->pickupContainerCode,
        );
        
        $request['OverweightIndicator'] = ($this->pickupOverweight != '' ? 'N' : 'Y');
        
        $request['Shipper'] = array(
            'Account' => array(
                'AccountNumber'      => $this->accountNumber,
                'AccountCountryCode' => $this->pickupCountry,
            )
        );
        
        /**
         * Pickup Method Type
         * 
         * 00 = No payment needed
		 * 01 = Pay by shipper account
		 * 02 = Pay by return service
		 * 03 = Pay by charge card
		 * 04 = Pay by tracking number
         */
        $request['PaymentMethod'] = $this->paymentMethodCode;
        
        /**
         * Pickup Charge Card
         * 
         * CardType:
         * 01 = American Express
 		 * 03 = Discover	
 		 * 04 = Mastercard
		 * 06 = VISA
		 * 
		 * ExpirationDate: yyyyMM
		 * SecurityCode: 3 or 4 digit
         */
        if($this->paymentMethodCode == '03') {
            $request['Shipper']['ChargeCard'] = array(
				"CardHolderName" => $this->pickupCardHolder,
				"CardType"       => $this->pickupCardType, 
				"CardNumber"     => $this->pickupCardNumber,
                "ExpirationDate" => $this->pickupCardExpiry, 
      			"SecurityCode"   => $this->pickupCardSecurity,
      			"CardAddress"    => array(
                    "Address"     => $this->pickupCardAddress,
                    "CountryCode" => $this->pickupCardCountry,
                )
            );
        }

        if ($this->specialInstruction != '') {
            $request['SpecialInstruction'] = $this->specialInstruction;
        }

        return $request;
    }

    function createPickupRequest()
    {
        $request = $this->buildPickupRequest();

        return $this->upsSoapRequest($request, 0);
    }

    
    function cancelPickupRequest()
    {
        $request = array();
        $request['Request'] = '';
        
        $request['CancelBy'] = '02';
        $request['PRN'] = $this->pickupPRN;
        
        return $this->upsSoapRequest($request, 2);
    }
    
    // Main class for sending soap requests to the pickup API.
    // This is not done in XML because UPS doesn't support XML
    // for pickup requests.
    function upsSoapRequest($request, $methodCode)
    {
        $soapUrl = 'https://wwwcie.ups.com/webservices/Pickup';
        if ($this->debugMode == 0) {
            $soapUrl = 'https://onlinetools.ups.com/webservices/Pickup';
        }

        $client = new \RocketShipIt\Helper\SoapClient(
            __DIR__ . "/schemas/UPSPickup/Pickup.wsdl",
            array(
                'trace' => 1,
                'cache_wsdl'=> WSDL_CACHE_NONE,
                'location' => $soapUrl,
                'exceptions' => 0,
            )
        );
        $ns = 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0';
        $security = array(
            'UsernameToken' => array('Username' => $this->username,
            'Password' => $this->password),
            'ServiceAccessToken' => array('AccessLicenseNumber' => $this->license)
        );
        $header = new \SOAPHeader($ns, 'UPSSecurity', $security);
        $client->__setSoapHeaders($header);

        // Allows for mocking of SOAP requests
        if ($this->core->mockXmlResponse != '') {
            $client->mockXmlResponse = $this->core->mockXmlResponse;
        }

        //var_dump($client->__getFunctions());die();

        $methods = array(
            0 => 'ProcessPickupCreation',
            1 => 'ProcessPickupRate',
            2 => 'ProcessPickupCancel',
            3 => 'ProcessPickupPendingStatus',
        );

        $this->core->xmlResponse = '';
        $response = call_user_func_array(array($client, $methods[$methodCode]), array($request));
        $this->core->xmlSent = $client->__getLastRequest();
        $this->core->xmlResponse = $client->__getLastResponse();

        if (is_soap_fault($response)) {
            $description = '';
            if (isset($response->detail->Errors->ErrorDetail->PrimaryErrorCode->Description)) {
                $description = '- '. $response->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
            }
            return array('error' => "{$response->faultstring} $description");
        }

        return $response;
    }

}
