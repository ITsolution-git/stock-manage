<?php

namespace RocketShipIt\Service\Locator;

use \RocketShipIt\Helper\XmlParser;

class Fedex extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function buildFEDEXLocatorXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:FedExLocatorRequest',
            array('xmlns:ns' => 'http://fedex.com/ws/locator/v2',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://fedex.com/ws/locator/v2 FedExLocatorService v2.xsd'
        ));

        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('ns:Version');
            $xml->element('ns:ServiceId','dloc');
            $xml->element('ns:Major','2');
        $xml->pop(); // end Version

        if ($this->beginningRecord != '') {
            $xml->element('ns:BeginningRecordIndex', $this->beginningRecord);
        }

        if ($this->nearPhone != '') {
            $xml->element('ns:NearToPhoneNumber', $this->nearPhone);
        }

        if ($this->nearCode != '') {
            $xml->push('ns:NearToAddress');
                $xml->element('ns:StreetLines', $this->nearAddr1);
                $xml->element('ns:City', $this->nearCity);
                $xml->element('ns:StateOrProvinceCode', $this->nearState);
                $xml->element('ns:PostalCode', $this->nearCode);
            $xml->pop();
        }

        if ($this->holdAtLocation != '') {
            $xml->push('ns:DropoffServicesDesired');
                $xml->element('ns:HoldAtLocation', '1');
            $xml->pop();
        }

        $xml->pop(); // end FedExLocatorRequest

        $xmlString = $xml->getXml();
        return $xmlString;
    }

    function getFEDEXLocate()
    {
        $xmlString = $this->buildFEDEXLocatorXml();
        $this->core->request($xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
