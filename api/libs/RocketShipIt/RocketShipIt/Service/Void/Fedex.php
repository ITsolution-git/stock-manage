<?php

namespace RocketShipIt\Service\Void;

use \RocketShipIt\Helper\XmlParser;

/**
* Main class for voiding shipments.
*
* This class is a wrapper for use with all carriers to cancel 
* shipments.  Valid carriers are: UPS, USPS, and FedEx.
* To create a shipment see {@link RocketShipShipment}.
*/
class Fedex extends \RocketShipIt\Service\Common implements \RocketShipIt\VoidInterface
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildFEDEXVoidXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:DeleteShipmentRequest',array('xmlns:ns' => 'http://fedex.com/ws/ship/v7', 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation' => 'http://fedex.com/ws/ship/v7 ShipService v7.xsd'));
        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('ns:TransactionDetail');
            $xml->element('ns:CustomerTransactionId','Dleteshipment_POS');
        $xml->pop(); // end TransactionDetail
        $xml->push('ns:Version');
            $xml->element('ns:ServiceId','ship');
            $xml->element('ns:Major','7');
            $xml->element('ns:Intermediate','0');
            $xml->element('ns:Minor','0');
        $xml->pop(); // end Version
        $xml->element('ns:ShipTimestamp',date("c")); // FedEx uses ISO8601 style timestamps
        $xml->push('ns:TrackingId');
            $xml->element('ns:TrackingIdType',$this->trackingIdType); //GROUND,EXPRESS,USPS
            $xml->element('ns:FormId','String');
            $xml->element('ns:TrackingNumber',$this->shipmentIdentification);
        $xml->pop(); // end TrackingId
        $xml->element('ns:DeletionControl','DELETE_ALL_PACKAGES');
        $xml->pop(); // end DeleteShipmentRequest

        return $xml->getXml();
    }

    function voidShipment()
    {
        $xmlString = $this->buildFEDEXVoidXml();
        $this->core->request($xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function voidPackage()
    {
        return array('error' => 'not implemented');
    }
}
