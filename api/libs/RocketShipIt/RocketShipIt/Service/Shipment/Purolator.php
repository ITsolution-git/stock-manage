<?php

namespace RocketShipIt\Service\Shipment;

use \RocketShipIt\Helper\General;

class Purolator extends \RocketShipIt\Service\Common
{
    
    public $shipmentRequest;
    public $packageObjs = array();
    public $pin;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
        $this->shipmentRequest = new \stdClass();
    }

    function request($action, $request)
    {
        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => 0
        );

        $options['login'] = $this->username;
        $options['password'] = $this->password;

        if ($this->debugMode) {
            $options['location'] = 'https://devwebservices.purolator.com/EWS/V1/Shipping/ShippingService.asmx';
        }

        $wsdl = ROCKETSHIPIT_RESOURCE_PATH.'/schemas/purolator/ShippingService.wsdl';
        $this->soapClient = new \RocketShipIt\Helper\SoapClient($wsdl, $options);
        $this->soapHeader = new \SoapHeader('http://purolator.com/pws/datatypes/v1',
            'RequestContext',
            array(
                'Version' => '1.6',
                'Language' => 'en',
                'GroupID' => 'xxx',
                'RequestReference' => 'Shipping 123',
                'UserToken' => 'f6c95f22-9338-48a4-841e-ed215bd88ed2',
            )
        );

        //Define the SOAP Envelope Headers
        $headers = array();
        $headers[] = $this->soapHeader;

        $this->soapClient->__setSoapHeaders($headers);

        // Allows for mocking of soap requests
        if ($this->mockXmlResponse != '') {
            $this->soapClient->mockXmlResponse = $this->mockXmlResponse;
        }

        if ($this->validateOnly != '') {
            $this->setValidateOnlyOnClient();
        }

        $response = $this->soapClient->$action($request);

        $this->core->transactions[] = array(
            'xmlSent' => $this->soapClient->__getLastRequest(),
            'xmlResponse' => $this->soapClient->__getLastResponse(),
        );

        return $response;
    }

    function docRequest($action, $request)
    {
        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => 0
        );

        $options['login'] = $this->username;
        $options['password'] = $this->password;

        if ($this->debugMode) {
            $options['location'] = 'https://devwebservices.purolator.com/EWS/V1/ShippingDocuments/ShippingDocumentsService.asmx';
        }

        $wsdl = ROCKETSHIPIT_RESOURCE_PATH.'/schemas/purolator/ShippingDocumentsService.wsdl';
        $this->soapClient = new \RocketShipIt\Helper\SoapClient($wsdl, $options);
        $this->soapHeader = new \SoapHeader('http://purolator.com/pws/datatypes/v1',
            'RequestContext',
            array(
                'Version' => '1.3',
                'Language' => 'en',
                'GroupID' => 'xxx',
                'RequestReference' => 'Shipping Doc 123',
                'UserToken' => 'f6c95f22-9338-48a4-841e-ed215bd88ed2',
            )
        );

        //Define the SOAP Envelope Headers
        $headers = array();
        $headers[] = $this->soapHeader;

        $this->soapClient->__setSoapHeaders($headers);

        // Allows for mocking of soap requests
        if ($this->mockXmlResponse != '') {
            $this->soapClient->mockXmlResponse = $this->mockXmlResponse;
        }

        if ($this->validateOnly != '') {
            $this->setValidateOnlyOnClient();
        }

        $response = $this->soapClient->$action($request);

        $this->core->transactions[] = array(
            'xmlSent' => $this->soapClient->__getLastRequest(),
            'xmlResponse' => $this->soapClient->__getLastResponse(),
        );

        return $response;
    }

    function buildRequest()
    {
        $request = array();
        //Populate the Origin Information
        $request['Shipment']['SenderInformation']['Address']['Name'] = $this->shipper;
        $addrParts = $this->getAddressParts($this->shipAddr1);
        $request['Shipment']['SenderInformation']['Address']['StreetNumber'] = $addrParts['street_number'];
        $request['Shipment']['SenderInformation']['Address']['StreetName'] = $addrParts['street_name'];
        $request['Shipment']['SenderInformation']['Address']['City'] = $this->shipCity;
        $request['Shipment']['SenderInformation']['Address']['Province'] = $this->shipState;
        $request['Shipment']['SenderInformation']['Address']['Country'] = $this->shipCountry;
        $request['Shipment']['SenderInformation']['Address']['PostalCode'] = $this->shipCode;    
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['CountryCode'] = '1';
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['AreaCode'] = '905';
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['Phone'] = '5555555';

        //Populate the Desination Information
        $request['Shipment']['ReceiverInformation']['Address']['Name'] = $this->toName;
        $addrParts = $this->getAddressParts($this->toAddr1);
        $request['Shipment']['ReceiverInformation']['Address']['StreetNumber'] = $addrParts['street_number'];
        $request['Shipment']['ReceiverInformation']['Address']['StreetName'] = $addrParts['street_name'];
        $request['Shipment']['ReceiverInformation']['Address']['City'] = $this->toCity;
        $request['Shipment']['ReceiverInformation']['Address']['Province'] = $this->toState;
        $request['Shipment']['ReceiverInformation']['Address']['Country'] = $this->toCountry;
        $request['Shipment']['ReceiverInformation']['Address']['PostalCode'] = $this->toCode;
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['CountryCode'] = '1';
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['AreaCode'] = '604';
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['Phone'] = '2982181';

        $request['Shipment']['PackageInformation']['TotalWeight']['Value'] = $this->weight;
        $request['Shipment']['PackageInformation']['TotalWeight']['WeightUnit']= $this->weightUnit;
        $request['Shipment']['PackageInformation']['TotalPieces'] = '1';
        $request['Shipment']['PackageInformation']['ServiceID'] = $this->service;

        $request['Shipment']['PickupInformation']['PickupType'] = 'DropOff';
        $request['Shipment']['TrackingReferenceInformation']['Reference1'] = 'RMA123';

        if ($this->imageType == 'PDF') {
            //Define the CreateShipment Document Type
            $request['PrinterType'] = 'Regular';
        } else {
            //Define the CreateShipment Document Type
            $request['PrinterType'] = 'Thermal';
        }


        $request['Shipment']['PaymentInformation']['PaymentType'] = 'Sender';
        $request['Shipment']['PaymentInformation']['BillingAccountNumber'] = $this->accountNumber;
        $request['Shipment']['PaymentInformation']['RegisteredAccountNumber'] = $this->accountNumber;

        return $request;
    }

    function buildDocRequest()
    {
        $request = array();

        // Set sane defaults
        if ($this->imageType == '') {
            $this->imageType = 'PDF';
        }

        $request['OutputType'] = $this->imageType;
        $request['Synchronous'] = true;
        $request['DocumentCriterium']['DocumentCriteria']['OutputType'] = $this->pin;
        $request['DocumentCriterium']['DocumentCriteria']['PIN']['Value'] = $this->pin;

        // COSBillOfLading
        // CustomsInvoice
        // CustomsInvoiceThermal
        // DangerousGoodsDeclaration
        // DomesticBillOfLading
        // DomesticBillOfLadingThermal
        // ExpressChequeReceipt
        // ExpressChequeReceiptThermal
        // FCC740
        // FDA2877
        // InternationalBillOfLading
        // InternationalBillOfLadingThermal
        // NAFTA
        if ($this->imageType == 'EPL') {
            $labelType = 'DomesticBillOfLadingThermal';
        } else {
            $labelType = 'DomesticBillOfLading';
        }
        $request['DocumentCriterium']['DocumentCriteria']['DocumentTypes']['DocumentType'] = $labelType;


        return $request;
    }

    public function submitShipment()
    {
        $resp = $this->request('CreateShipment', $this->buildRequest());
        if (!isset($resp->ShipmentPIN->Value)) {
            if (is_soap_fault($resp)) {
                $r = array('errors' => array());
                $r['errors'][] = array(
                    'code' => $resp->faultcode,
                    'description' => $resp->faultstring,
                    'type' => 'Error',
                );

                return $r;
            }
        }

        $this->pin = $resp->ShipmentPIN->Value;
        $d = $this->getDocument();

        $this->response = array(
            'charges' => 0,
            'trk_main' => $this->pin,
            'pkgs' => array(
                array(
                    'pkg_trk_num' => $this->pin,
                    'label_fmt' => $this->imageType,
                    'label_img' => '',
                ),
            ),
            'errors' => array(),
        );

        if (isset($d->Documents->Document->DocumentDetails->DocumentDetail->Data)) {
            $this->response['pkgs'][0]['label_img'] = $d->Documents->Document->DocumentDetails->DocumentDetail->Data;
        }

        return $this->response;
    }

    // Split street number and street name from given addr
    public function getAddressParts($address)
    {
        $matches = array();
        $result = preg_match("/(\d+\w*)\s(.*)/", $address, $matches);
        if (!$result) {
            return array('street_number' => '', 'street_name' => $address);
        }
        return array('street_number' => $matches[1], 'street_name' => $matches[2]);
    }

    public function getDocument()
    {
        return $this->docRequest('GetDocuments', $this->buildDocRequest());
    }

}
