<?php

namespace RocketShipIt\Service\Freight;

class Ups extends \RocketShipIt\Service\Common
{

    public $soapClient;
    public $soapHeader;
    public $commodities = array();
    public $packages = array();
    private $productionEndpoint = 'https://onlinetools.ups.com/webservices/FreightShip';
    private $testEndpoint = 'https://wwwcie.ups.com/webservices/FreightShip';

    // document imageType codes
    public $documentCodes = array(
        20 => 'bill_of_lading',
        30 => 'label'
    );

    public function __construct()
    {
        parent::__construct('Ups');
        $this->soapClient = $this->getSoapClient();
    }

    public function getSoapClient()
    {
        $path = ROCKETSHIPIT_RESOURCE_PATH. '/schemas/ups/Freight/FreightShip.wsdl';

        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'location' => $this->productionEndpoint,
            'exceptions' => 0,
        );

        if (isset($this->debugMode) && $this->debugMode) {
            $options['location'] = $this->testEndpoint;
        }

        $client = new \RocketShipIt\Helper\SoapClient(
            $path,
            $options
        );

        return $client;
    }

    private function getSoapHeader($ns, $type, $security)
    {
        if ($this->soapHeader) {
            return $this->soapHeader;
        }

        return new \SOAPHeader($ns, $type, $security);
    }

    private function addSecurityHeader()
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

    private function buildAddress()
    {
        $address = new \stdClass();
        $address->AddressLine = array($this->shipAddr1);
        if ($this->shipAddr2 != '') {
            $address->AddressLine[] = $this->shipAddr2;
        }
        $address->City = $this->shipCity;
        $address->StateProvinceCode = $this->shipState;
        $address->PostalCode = $this->shipCode;
        $address->CountryCode = $this->shipCountry;

        return $address;
    }

    private function buildToAddress()
    {
        $address = new \stdClass();
        $address->AddressLine = array($this->toAddr1);
        if ($this->toAddr2 != '') {
            $address->AddressLine[] = $this->toAddr2;
        }
        $address->City = $this->toCity;
        $address->StateProvinceCode = $this->toState;
        $address->PostalCode = $this->toCode;
        $address->CountryCode = $this->toCountry;

        return $address;
    }

    private function buildFromAddress()
    {
        $address = new \stdClass();
        $address->AddressLine = array($this->shipAddr1);
        if ($this->shipAddr2 != '') {
            $address->AddressLine[] = $this->shipAddr2;
        }
        $address->City = $this->shipCity;
        $address->StateProvinceCode = $this->shipState;
        $address->PostalCode = $this->shipCode;
        $address->CountryCode = $this->shipCountry;

        return $address;
    }

    public function buildRequest()
    {
        $request = new \stdClass();

        $rateRequest = new \stdClass();
        // 1 is the currently only valid option
        $rateRequest->RequestOption = '1';

        $request = new \stdClass();
        $request->Request = $rateRequest;

        $shipper = new \stdClass();
        $shipper->Address = $this->buildAddress();

        $shipTo = new \stdClass();
        $shipTo->Name = $this->toCompany;
        $shipTo->Address = $this->buildToAddress();

        $shipFrom = new \stdClass();
        $shipFrom->Name = $this->shipper;
        $shipFrom->Address = $this->buildFromAddress();
        $phone = new \stdClass();
        $phone->Number = $this->shipPhone;
        $shipFrom->Phone = $phone;

        $payer = new \stdClass();
        $payer->Name = $this->shipper;
        $payer->Address = $this->buildFromAddress();
        $phone = new \stdClass();
        $phone->Number = $this->shipPhone;
        $payer->Phone = $phone;

        // 10 - Prepaid
        // 30 - Bill to Third Party
        // 40 - Freight Collect
        $billOpt = new \stdClass();
        $billOpt->Code = '10';

        $paymentInformation = new \stdClass();
        $paymentInformation->Payer = $payer;
        $paymentInformation->ShipmentBillingOption = $billOpt;

        $shipment = new \stdClass();
        $shipment->Shipper = $shipper;
        $shipment->ShipFrom = $shipFrom;
        $shipment->ShipperNumber = $this->accountNumber;
        $shipment->ShipTo = $shipTo;
        $shipment->PaymentInformation = $paymentInformation;
        $service = new \stdClass();
        if ($this->service != '') {
            $service->Code = $this->service;
        } else {
            $service->Code = '308';
        }
        $shipment->Service = $service;
        $handleUnit = new \stdClass();
        if ($this->handleQty!= '') {
            $handleUnit->Quantity = $this->handleQty;
        } else {
            $handleUnit->Quantity = 1;
        }
        $type = new \stdClass();
        if ($this->handlePackageType != '') {
            $type->Code = $this->handlePackageType;
        } else {
            $type->Code = 'PLT';
        }
        $handleUnit->Type = $type;
        $shipment->HandlingUnitOne = $handleUnit;
        $shipment->Commodity = $this->buildCommodities();
        $shipment->Documents = $this->buildDocuments();

        $request->Shipment = $shipment;

        return $request;
    }

    private function buildCommodities()
    {
        $coms = array();

        foreach ($this->commodities as $i => $com) {
            $c = new \stdClass();
            $c->CommodityID = $i+1;
            $c->Description = $com->description;
            $weight = new \stdClass();
            $uom = new \stdClass();
            $uom->Code = $com->weightUnit;
            $weight->UnitOfMeasurement = $uom;
            $weight->Value = $com->weight;
            $c->Weight = $weight;
            $dimensions = new \stdClass();
            $uom = new \stdClass();
            $uom->Code = $com->lengthUnit;
            $dimensions->UnitOfMeasurement = $uom;
            $dimensions->Length = $com->length;
            $dimensions->Width = $com->width;
            $dimensions->Height = $com->height;
            $c->Dimensions = $dimensions;
            $c->NumberOfPieces = $com->numberOfPieces;
            $packagingType = new \stdClass();
            $packagingType->Code = $com->packagingType;
            $c->PackagingType = $packagingType;
            $commodityValue = new \stdClass();
            $commodityValue->CurrencyCode = $com->currency;
            $commodityValue->MonetaryValue = $com->commodityValue;
            $c->CommodityValue = $commodityValue;
            $c->FreightClass = $com->freightClass;
            $c->NMFCCommodityCode = $com->nmfcCode;
            $coms[] = $c;
        }

        return $coms;
    }

    private function buildDocuments()
    {
        $docs = new \stdClass();

        // BOL
        $image = new \stdClass();
        $type = new \stdClass();
        $type->Code = '20';
        $format = new \stdClass();
        $format->Code = '01'; // 01 - pdf, only valid option
        $image->Type = $type;
        $image->Format = $format;

        $images = array($image);

        // label
        $image = new \stdClass();
        $type = new \stdClass();
        $type->Code = '30';
        $format = new \stdClass();
        $format->Code = '01'; // 01 - pdf, only valid option
        $image->Type = $type;
        $image->Format = $format;
        $image->LabelsPerPage = 4; // valid values: 1, 2, 4

        $printFormat = new \stdClass();
        $printFormat->Code = '01'; // 01 - laser, 02 - Thermal
        $image->PrintFormat = $printFormat;

        $printSize = new \stdClass();
        // 4 x 6, 4 x 8, 8 x 11
        $printSize->Length = '8';
        $printSize->Width = '11';
        $image->PrintSize = $printSize;

        $images[] = $image;

        $docs->Image = $images;

        return $docs;
    }

    private function doRequest()
    {
        $this->addSecurityHeader();
        $response = new \stdClass;
        $request = $this->buildRequest();
        $response = $this->soapClient->ProcessShipment($request);

        $this->core->xmlSent = $this->soapClient->__getLastRequest();
        $this->core->xmlResponse = $this->soapClient->__getLastResponse();

        return $response;
    }

    public function submitShipment()
    {
        return $this->simplifyResponse($this->doRequest());
    }

    public function simplifyResponse($resp)
    {
        $r = new \RocketShipIt\Response\Shipment;

        if (is_soap_fault($resp)) {
            if (is_array($resp->detail->Errors->ErrorDetail)) {
                $errors = $resp->detail->Errors->ErrorDetail;
            } else {
                $errors = array($resp->detail->Errors->ErrorDetail);
            }

            foreach ($errors as $err) {
                $e = new \RocketShipIt\Response\Error;
                $e->Code = $err->PrimaryErrorCode->Code;
                $e->Description = $err->PrimaryErrorCode->Description;
                $e->Type = 'Error';
                $r->Data->Errors[] = $e;
            }
        }

        if (isset($resp->Response->Alert)) {
            if (is_array($resp->Response->Alert)) {
                foreach ($resp->Response->Alert as $alert) {
                    $e = new \RocketShipIt\Response\Error;
                    $e->Code = $alert->Code;
                    $e->Description = $alert->Description;
                    $e->Type = 'Warning';
                    $r->Data->Errors[] = $e;
                }
            }
        }

        if (isset($resp->ShipmentResults->ShipmentNumber)) {
            $r->Data->ShipmentId = $resp->ShipmentResults->ShipmentNumber;
            $r->Data->TrackingNumber = $r->Data->ShipmentId;
        }

        if (isset($resp->ShipmentResults->TotalShipmentCharge->MonetaryValue)) {
            $r->Data->Charges = $resp->ShipmentResults->TotalShipmentCharge->MonetaryValue;
        }

        if (isset($resp->ShipmentResults->Documents->Image)) {
            if (is_array($resp->ShipmentResults->Documents->Image)) {
                $images = $resp->ShipmentResults->Documents->Image;
            } else {
                $images = array($resp->ShipmentResults->Documents->Image);
            }

            foreach ($images as $img) {
                $d = new \RocketShipIt\Response\Document;
                $d->Code = $this->getDocumentCode($img->Type->Code);
                $d->Type = $this->getMediaType($img->Format->Code);
                $d->Media = $img->GraphicImage;
                $r->Data->Documents[] = $d;
            }
        }

        return (array) json_decode(json_encode($r), true);
    }

    private function getDocumentCode($code)
    {
        if (isset($this->documentCodes[$code])) {
            return $this->documentCodes[$code];
        }

        return $code;
    }

    private function getMediaType($code)
    {
        if ($code == 'PDF') {
            return 'application/pdf';
        }

        return $code;
    }

    public function addCommodityLineToShipment($commodity)
    {
        $this->commodities[] = $commodity;
    }
}
