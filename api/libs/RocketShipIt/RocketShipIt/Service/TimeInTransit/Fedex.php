<?php

namespace RocketShipIt\Service\TimeInTransit;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\General;

// Main class for getting FedEx time in transit information
class Fedex extends \RocketShipIt\Service\Common
{
    var $inherited;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General();
        parent::__construct($carrier);
    }

    public function buildSimpleTransit($respArray)
    {
        $response = array();

        if (!isset($respArray['ServiceAvailabilityReply']['Options'])) {
            return $respArray;
        }

        if (!is_array($respArray['ServiceAvailabilityReply']['Options'])) {
            return $respArray;
        }

        foreach ($respArray['ServiceAvailabilityReply']['Options'] as $service) {
            $simpleArr = array();
            $simpleArr['desc'] = '';
            $simpleArr['service_code'] = '';
            $simpleArr['is_guaranteed'] = false;
            $simpleArr['eta'] = 0;
            $simpleArr['formatted_eta'] = 'N/A';

            $simpleArr['desc'] = $this->core->getServiceDescriptionFromCode(
                $this->helper->getValueFromPath($service, 'Service', '')
            );
            $simpleArr['service_code'] = $this->helper->getValueFromPath($service, 'Service', '');
            $simpleArr['is_guaranteed'] = false;
            $date = $this->helper->getValueFromPath($service, 'DeliveryDate', 'N/A');
            if ($date != 'N/A' && $date != '') {
                $time = strtotime($date);
                $simpleArr['eta'] = $time;
                $simpleArr['formatted_eta'] = date("D F jS", $time);
            }
            $response[] = $simpleArr;
        }

        return $response;
    }

    public function buildFEDEXTimeInTransitXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push(
            'ns:ServiceAvailabilityRequest',
            array(
                'xmlns:ns' => 'http://fedex.com/ws/packagemovementinformationservice/v4',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://fedex.com/ws/packagemovementinformation/v4'
            )
        );
        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('ns:Version');
            $xml->element('ns:ServiceId', 'pmis');
            $xml->element('ns:Major', '4');
            $xml->element('ns:Intermediate', '0');
            $xml->element('ns:Minor', '0');
        $xml->pop(); // end Version
        $xml->push('ns:Origin');
            $xml->element('ns:PostalCode', $this->shipCode);
            $xml->element('ns:CountryCode', $this->shipCountry);
        $xml->pop(); // end Origin
        $xml->push('ns:Destination');
            $xml->element('ns:PostalCode', $this->toCode);
            $xml->element('ns:CountryCode', $this->toCountry);
        $xml->pop(); // end Destination
        $xml->element('ns:ShipDate', $this->pickupDate); // Y-m-d
        $xml->element('ns:CarrierCode', 'FDXE');
        $xml->element('ns:Packaging', $this->packagingType);
        $xml->pop(); // end Request

        return $xml->getXml();
    }

    public function getFEDEXTimeInTransit()
    {
        $xmlString = $this->buildFEDEXTimeInTransitXml();
        $this->core->request($xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function getSimpleTimeInTransit()
    {
        $arr = $this->getFEDEXTimeInTransit();
        return $this->buildSimpleTransit($arr);
    }
}
