<?php

namespace RocketShipIt\Service\Track;
use \RocketShipIt\Helper\XmlParser;

/**
* Main class for tracking shipments and packages
*
* This class is a wrapper for use with all carriers to track packages
* Valid carriers are: UPS, USPS, and FedEx.
*/
class Ontrac extends \RocketShipIt\Service\Common
{

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function buildParams()
    {
        return array(
            'pw' => $this->password,
            'requestType' => 'track',
            'logoFormat' => 'GIF',
            'sigFormat' => 'GIF',
        );
    }

    function buildPath()
    {
        return sprintf('/V1/%s/shipments', $this->accountNumber);
    }

    function trackONTRAC($trackingNumber)
    {
        $params = $this->buildParams();
        $params['tn'] = $trackingNumber;
        $this->core->request($this->buildPath(), 'get', $params);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
