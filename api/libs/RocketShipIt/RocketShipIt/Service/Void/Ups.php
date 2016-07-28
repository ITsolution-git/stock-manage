<?php

namespace RocketShipIt\Service\Void;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

/**
* Main class for voiding shipments.
*
* This class is a wrapper for use with all carriers to cancel 
* shipments.  Valid carriers are: UPS, USPS, and FedEx.
* To create a shipment see {@link RocketShipShipment}.
*/
class Ups extends \RocketShipIt\Service\Common implements \RocketShipIt\VoidInterface
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function processResponse()
    {
        $xmlArray = $this->arrayFromXml($this->core->xmlResponse);
        $this->responseArray = $xmlArray;

        if (isset($xmlArray['VoidShipmentResponse']['Response']['ResponseStatusCode'])) {
            if ($xmlArray['VoidShipmentResponse']['Response']['ResponseStatusCode'] == 1) {
                $this->status = 'success';
            }
            if ($xmlArray['VoidShipmentResponse']['Response']['ResponseStatusCode'] == 0) {
                $this->status = 'failure';
            }
        }
    }

    function buildVoidUpsPackageXml()
    {
        $this->core->access();
        $accessXml = $this->core->xmlObject;    

        $xml = new xmlBuilder(false);
        
        $xml->push('VoidShipmentRequest');
            $xml->push('Request');
                $xml->element('RequestAction', '1');
            $xml->pop(); // end Request
        $xml->push('ExpandedVoidShipment');
            $xml->element('ShipmentIdentificationNumber', $this->shipmentIdentification);
            if (is_array($this->packageIdentification)) {
                foreach ($this->packageIdentification as $trackingNumber) {
                    $xml->element('TrackingNumber', $trackingNumber);
                }
            } else {
                    $xml->element('TrackingNumber', $this->packageIdentification);
            }
        $xml->pop(); // end ExpandedVoidShipment
        $xml->pop(); // end VoidShipmentRequest

        return $accessXml->getXml(). $xml->getXml();
    }

    function voidPackage()
    {
        $xmlString = $this->buildVoidUpsPackageXml(); 
        $this->core->request('Void', $xmlString);

        $this->processResponse();
        return $this->responseArray;
    }

    function voidShipment()
    {
        $this->core->access();
        $accessXml = $this->core->xmlObject;    

        $xml = new xmlBuilder(false);

        $xml->push('VoidShipmentRequest');
            $xml->push('Request');
                $xml->element('RequestAction', '1');
            $xml->pop(); // end Request
            $xml->element('ShipmentIdentificationNumber', $this->shipmentIdentification);
        $xml->pop(); // end VoidShipmentRequest

        $xmlString = $accessXml->getXml(). $xml->getXml();

        $this->core->request('Void', $xmlString);

        $this->processResponse();
        return $this->responseArray;
    }
}
