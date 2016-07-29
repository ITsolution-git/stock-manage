<?php

namespace RocketShipIt\Service\Rate;

class UpsSurePost extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    public $soapClient;
    public $soapHeader;
    public $packages = array();

    public function __construct($soapClient)
    {
        $this->soapClient = $soapClient;
        parent::__construct('UPS');    
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

    public function buildRequest($requestOption='Shop')
    {
        $shipper = new \stdClass();
        $shipper->Address = $this->buildAddress();

        $shipTo = new \stdClass();
        $shipTo->Name = $this->shipper;
        $shipTo->Address = $this->buildAddress();

        $shipment = new \stdClass();
        $shipment->Shipper = $shipper;
        $shipment->ShipTo = $shipTo;

        if ($this->packages) {
            $shipment->Package = $this->packages;
        } else {
            $shipment->Package = array($this->buildPackage());
        }

        $rateRequest = new \stdClass();
        // Rate or Shop, Shop to get all rates
        // Rate to get single rate
        $rateRequest->RequestOption = $requestOption;

        $request = new \stdClass();
        $request->Request = $rateRequest;
        $request->Shipment = $shipment;

        return $request;
    }

    public function addSecurityHeader()
    {
        // Get credential information from config
        $license = $this->config->getDefault('ups', 'license');
        $username = $this->config->getDefault('ups', 'username');
        $password = $this->config->getDefault('ups', 'password');
        $ns = 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0';
        $security = array(
            'UsernameToken' => array('Username' => $username,
            'Password' => $password),
            'ServiceAccessToken' => array(
                'AccessLicenseNumber' => $license
            )
        );
        $header = $this->getSoapHeader($ns, 'UPSSecurity', $security);
        $this->soapClient->__setSoapHeaders($header);
    }

    public function doRequest($requestOption='Shop')
    {
        $this->addSecurityHeader();
        try {
            $response = $this->soapClient->ProcessRate($this->buildRequest($requestOption));
        } catch (\Exception $e) {
            echo $e->faultstring;
            echo $e->faultcode;
            print_r($e->detail);
            echo $this->soapClient->__getLastRequest();
            echo $this->soapClient->__getLastResponse();
        }
        $this->core->xmlSent = $this->soapClient->__getLastRequest();
        $this->core->xmlResponse = $this->soapClient->__getLastResponse();

        return $response;
    }

    public function getAllRates()
    {
        return $this->doRequest();
    }

    public function getRate()
    {
        return $this->getAllRates('Rate');
    }

    public function addPackageToShipment($packageObj)
    {
        $this->packages[] = $this->buildPackage($packageObj);
    }

    public function getSimpleRate()
    {
    }

    public function getSimpleRates($userFunc=null)
    {
        $arr = array();
        $response = $this->getAllRates();

        if (!isset($response->RatedShipment)) {
            return array('error' => '');
        }

        foreach ($response->RatedShipment as $rate) {
            $r = array();
            $r['desc'] = $this->core->getServiceDescriptionFromCode($rate->Service->Code);
            $r['rate'] = $rate->TotalCharges->MonetaryValue;
            $r['service_code'] = $rate->Service->Code;
            $arr[] = $r;
        }

        return $arr;
    }
}
