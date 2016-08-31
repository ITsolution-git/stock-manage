<?php

namespace RocketShipIt\Service\Track;
use RocketShipIt\Helper\XmlBuilder as XmlBuilder;
use RocketShipIt\Helper\XmlParser as XmlParser;

/**
* Main class for tracking shipments and packages
*
* This class is a wrapper for use with all carriers to track packages
* Valid carriers are: UPS, USPS, and FedEx.
*/
class Dhl extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function trackDHL($trackingNumber)
    {
        $this->core->request('', $this->buildTrackXml($trackingNumber));

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function buildTrackXml($trackingNumber)
    {
        $xml = new xmlBuilder();
        $xml->push('req:KnownTrackingRequest', array('xmlns:req' => "http://www.dhl.com", 'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance", 'xsi:schemaLocation' => "http://www.dhl.com TrackingRequestKnown.xsd"));
            $xml->append($this->buildRequestXml());
            $xml->element('LanguageCode', 'en');
            if ($this->trackingIdType == 'AWBNumber') {
                $xml->element('AWBNumber', $trackingNumber);
            } else {
                $xml->element('LPNumber', $trackingNumber);
            }
            $xml->element('LevelOfDetails', 'ALL_CHECK_POINTS');
        $xml->pop(); // end KnownTrackingRequest
        return $xml->getXml();
    }

    function buildRequestXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('Request');
            $xml->push('ServiceHeader');
                $xml->element('MessageTime', date('c'));
                $xml->element('MessageReference', $this->generateRandomString());
                $xml->element('SiteID', $this->siteId);
                $xml->element('Password', $this->password);
            $xml->pop(); // end ServiceHeader
        $xml->pop(); // end Request
        return $xml->getXml();
    }

    /**
     * Creates random string of alphanumeric characters
     * 
     * @return string
     */
    private function generateRandomString()
    {
        $length = 32;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = "";
        
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }
        return $string;
    }
}
