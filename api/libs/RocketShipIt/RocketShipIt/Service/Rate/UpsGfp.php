<?php

namespace RocketShipIt\Service\Rate;

class UpsGfp extends \RocketShipIt\Service\Common
    implements \RocketShipIt\RateInterface
{
    public $soapClient;
    public $soapHeader;
    public $packages = array();
    public $rateResponse;
    public $requestOption = 'Shop';
    public $productionEndpoint = 'https://onlinetools.ups.com/webservices/Rate';
    public $testEndpoint = 'https://wwwcie.ups.com/webservices/Rate';

    public function __construct()
    {
        $this->soapClient = $this->getSoapClient();
        $this->rateResponse = null;
        parent::__construct('Ups');
    }

    public function getSoapClient()
    {
        $path_to_wsdl = ROCKETSHIPIT_RESOURCE_PATH. '/schemas/ups/GFP/RateWS.wsdl';
        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'location' => $this->productionEndpoint,
        );

        if (isset($this->debugMode) && $this->debugMode) {
            $options['location'] = $this->testEndpoint;
        }

        $client = new \RocketShipIt\Helper\SoapClient(
            $path_to_wsdl,
            $options
        );

        return $client;
    }

    public function getSoapHeader($ns, $type, $security)
    {
        if ($this->soapHeader) {
            return $this->soapHeader;
        }

        return new \SOAPHeader($ns, $type, $security);
    }

    // Build individual packages for single and multi-
    // package shipments
    public function buildPackage($package=null)
    {
        $obj = $this;
        if ($package) {
            $obj = $package;
        }

        $packagingType = new \stdClass();
        $packagingType->Code = $obj->packagingType;

        $unitOfMeasurement = new \stdClass();
        $unitOfMeasurement->Code = $obj->weightUnit;

        $packageWeight = new \stdClass();
        $packageWeight->UnitOfMeasurement = $unitOfMeasurement;
        $packageWeight->Weight = $obj->weight;

        $package = new \stdClass();
        $package->PackagingType = $packagingType;
        $package->PackageWeight = $packageWeight;

        if ($obj->freightClass != '') {
            $commodity = new \stdClass();
            $commodity->FreightClass = $obj->freightClass;
            $package->Commodity = $commodity;
        }

        return $package;
    }

    public function buildAddress()
    {
        $address = new \stdClass();
        $address->AddressLine = $this->shipAddr1;
        $address->City = $this->shipCity;
        $address->StateProvinceCode = $this->shipState;
        $address->PostalCode = $this->shipCode;
        $address->CountryCode = $this->shipCountry;

        return $address;
    }

    public function buildToAddress()
    {
        $address = new \stdClass();
        $address->AddressLine = $this->toAddr1;
        $address->City = $this->toCity;
        $address->StateProvinceCode = $this->toState;
        $address->PostalCode = $this->toCode;
        $address->CountryCode = $this->toCountry;

        return $address;
    }

    public function buildRequest()
    {
        $shipper = new \stdClass();
        if ($this->groundFreight == '1') {
            $shipper->ShipperNumber = $this->accountNumber;
        }
        $shipper->Address = $this->buildAddress();

        $shipTo = new \stdClass();
        $shipTo->Name = $this->toCompany;
        $shipTo->Address = $this->buildToAddress();

        $shipment = new \stdClass();
        $shipment->Shipper = $shipper;
        $shipment->ShipTo = $shipTo;

        if ($this->groundFreight == '1') {
            $shipmentRatingOptions = new \stdClass();
            if ($this->negotiatedRates == '1') {
                $shipmentRatingOptions->NegotiatedRatesIndicator = 1;
            }
            $shipmentRatingOptions->FRSShipmentIndicator = 1;
            $shipment->ShipmentRatingOptions = $shipmentRatingOptions;
        }

        if ($this->groundFreight == '1') {
            $paymentinformation = new \stdClass();
            $type = new \stdClass();
            // TODO
            $type->Code = '01';
            $paymentinformation->Type = $type;
            $paymentinformation->AccountNumber = $this->accountNumber;
            $shipment->FRSPaymentInformation = $paymentinformation;
        }

        if ($this->service != '1') {
            $service = new \stdClass();
            $service->Code = $this->service;
            $service->Description = 'Service Code';
            $shipment->Service = $service;
        }

        if ($this->packages) {
            $shipment->Package = $this->packages;
        } else {
            $shipment->Package = array($this->buildPackage());
        }

        $rateRequest = new \stdClass();
        // Rate or Shop, Shop to get all rates
        // Rate to get single rate
        if ($this->groundFreight == '1') {
            // Shop is not allowed when requesting ground
            // freight pricing
            $this->requestOption = 'Rate';
        }
        $rateRequest->RequestOption = $this->requestOption;

        $request = new \stdClass();
        $request->Request = $rateRequest;
        $request->Shipment = $shipment;

        return $request;
    }

    public function addSecurityHeader()
    {
        $ns = 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0';
        $security = array(
            'UsernameToken' => array(
                    'Username' => $this->username,
                    'Password' => $this->password
                ),
                'ServiceAccessToken' => array(
                    'AccessLicenseNumber' => $this->license
                )
        );
        $header = $this->getSoapHeader($ns, 'UPSSecurity', $security);
        $this->soapClient->__setSoapHeaders($header);
    }

    public function doRequest()
    {
        $this->addSecurityHeader();
        $response = new \stdClass;
        $request = $this->buildRequest($this->requestOption);
        try {
            $response = $this->soapClient->ProcessRate($request);
        } catch (\Exception $e) {
            $response = $e->getMessage();
            if (isset($e->detail->Errors->ErrorDetail->PrimaryErrorCode->Description)) {
                $response = array('error' => $e->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
        }
        $this->core->xmlSent = $this->soapClient->__getLastRequest();
        $this->core->xmlResponse = $this->soapClient->__getLastResponse();

        return $response;
    }

    public function getAllRates()
    {
        $this->rateResponse = $this->doRequest();

        return $this->rateResponse;
    }

    public function getRate()
    {
        $this->requestOption = 'Rate';

        return $this->getAllRates();
    }

    public function addPackageToShipment($packageObj)
    {
        $this->packages[] = $this->buildPackage($packageObj);
    }

    public function getSimpleRate()
    {
        $this->requestOption = 'Rate';

        return $this->getSimpleRates();
    }

    public function processSimpleRates()
    {
        if (!isset($this->rateResponse->RatedShipment)) {
            return array('error' => '');
        }

        $ratedShipments = array();
        if (!is_array($this->rateResponse->RatedShipment)) {
            $ratedShipments[] = $this->rateResponse->RatedShipment;
        } else {
            $ratedShipments = $this->rateResponse->RatedShipment;
        }

        foreach ($ratedShipments as $rate) {
            $r = array();
            $r['desc'] = $this->core->getServiceDescriptionFromCode($rate->Service->Code);
            $r['rate'] = $rate->TotalCharges->MonetaryValue;
            $r['service_code'] = $rate->Service->Code;
            $arr[] = $r;
        }

        return $arr;
    }

    public function getSimpleRates($userFunc=null)
    {
        $arr = array();
        if (!$this->rateResponse) {
            $this->rateResponse = $this->getAllRates();
        }

        return $this->processSimpleRates();
    }
}
