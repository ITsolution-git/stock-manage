<?php

namespace RocketShipIt\Service\TimeInTransit;

use \RocketShipIt\Helper\XmlParser;
use RocketShipIt\Helper\XmlBuilder as XmlBuilder;

// USPS requires 4 seperate APIs for getting TimeInTransit for various services
// We will do a request for all 4 at once and combine them.

/**
* Main class for getting time in transit information
*
*/
class Usps extends \RocketShipIt\Service\Common
{
    var $inherited;

    function __construct() {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildUSPSTimeInTransitXml()
    {
        $xml = new xmlBuilder(true);
        
        $xml->push('ExpressMailCommitmentRequest',array('USERID' => $this->userid));
            $xml->element('OriginZIP', $this->shipCode);
            $xml->element('DestinationZIP', $this->toCode);
            $xml->element('Date', $this->pickupDate);
        $xml->pop();
        
        return 'API=ExpressMailCommitment&XML='. $xml->getXml();
    }

    public function buildUSPSPriorityTimeInTransitXml()
    {
        $xml = new xmlBuilder(true);
        
        $xml->push('PriorityMailRequest',array('USERID' => $this->userid));
            $xml->element('OriginZip', $this->shipCode);
            $xml->element('DestinationZip', $this->toCode);
            //$xml->element('Date', $this->pickupDate);
        $xml->pop();
        
        return 'API=PriorityMail&XML='. $xml->getXml();
    }

    public function buildUSPSFirstClassTimeInTransitXml()
    {
        $xml = new xmlBuilder(true);
        
        $xml->push('FirstClassMailRequest',array('USERID' => $this->userid));
            $xml->element('OriginZip', $this->shipCode);
            $xml->element('DestinationZip', $this->toCode);
            //$xml->element('Date', $this->pickupDate);
        $xml->pop();
        
        return 'API=FirstClassMail&XML='. $xml->getXml();
    }

    public function buildUSPSStandardTimeInTransitXml()
    {
        $xml = new xmlBuilder(true);
        
        $xml->push('StandardBRequest',array('USERID' => $this->userid));
            $xml->element('OriginZip', $this->shipCode);
            $xml->element('DestinationZip', $this->toCode);
            //$xml->element('Date', $this->pickupDate);
        $xml->pop();
        
        return 'API=StandardB&XML='. $xml->getXml();
    }

    public function combineRequest($combinedResponse, $func)
    {
        $xmlString = $this->{$func}();
        $this->core->request('ShippingAPI.dll', $xmlString);
        // Convert the xmlString to an array
        $xmlArray = $this->arrayFromXml($this->core->xmlResponse);
        $combinedResponse = array_merge($combinedResponse, $xmlArray);

        return $combinedResponse;
    }

    function getUSPSTimeInTransit()
    {
        $combinedResponse = array();
        $combinedResponse = $this->combineRequest($combinedResponse, 'buildUSPSTimeInTransitXml');
        $combinedResponse = $this->combineRequest($combinedResponse, 'buildUSPSPriorityTimeInTransitXml');
        $combinedResponse = $this->combineRequest($combinedResponse, 'buildUSPSFirstClassTimeInTransitXml');
        $combinedResponse = $this->combineRequest($combinedResponse, 'buildUSPSStandardTimeInTransitXml');

        return $combinedResponse; 
    }
}
