<?php

namespace RocketShipIt\Service\Locator;

use \RocketShipIt\Helper\XmlParser;

class Ups extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function buildLocateXml()
    {
        $this->core->access();
        $xml = $this->core->xmlObject;

        /*
        RequestOption Indicates the type of request. 
        Valid values are:
        1  = Locations (Drop Locations and Will call locations)
        8  = All available Additional Services
        16 = All available Program Types
        24 = All available Additional Services and Program types
        32 = All available Retail Locations
        40 = All available Retail Locations and Additional Services
        48 = All available Retail Locations and Program Types
        56 = All available Retail Locations, Additional Services and Program Types.
        64 = Access points
        */

        /*
        UnitOfMeasurement/Code
        Valid values are: MI = Miles or KM = Kilometers
        */

        if ($this->requestOption == '') {
            $this->requestOption = '1';
        }

        $xml->append('<?xml version="1.0"?>
        <LocatorRequest>
          <Request>
            <RequestAction>Locator</RequestAction>
            <RequestOption>'. $this->requestOption. '</RequestOption>
            <TransactionReference>
              <CustomerContext>RocketShipIt</CustomerContext>
              <XpciVersion>1.0014</XpciVersion>
            </TransactionReference>
              </Request>
              <OriginAddress>
                <AddressKeyFormat>
                    <AddressLine>'. $this->shipAddr1. '</AddressLine>
                    <PoliticalDivision2>'. $this->shipCity. '</PoliticalDivision2>
                    <PoliticalDivision1>'. $this->shipState. '</PoliticalDivision1>
                    <PostcodePrimaryLow>'. $this->shipCode. '</PostcodePrimaryLow>
                    <PostcodeExtendedLow>'. $this->shipCodeExtended. '</PostcodeExtendedLow>
                    <CountryCode>'. $this->shipCountry. '</CountryCode>
                </AddressKeyFormat>
            </OriginAddress>
              <Translate>
                <LanguageCode>ENG</LanguageCode>
              </Translate>
              <UnitOfMeasurement>
                <Code>MI</Code>
              </UnitOfMeasurement>
         </LocatorRequest>');
         return $xml->getXml();
    }

    function getUPSLocate()
    {
        $xmlString = $this->buildLocateXml();
        $this->core->request('Locator', $xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
