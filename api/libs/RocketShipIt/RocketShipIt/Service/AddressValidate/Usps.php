<?php

namespace RocketShipIt\Service\AddressValidate;
use RocketShipIt\Helper\XmlBuilder as XmlBuilder;
use RocketShipIt\Helper\XmlParser as XmlParser;

/**
* Main Address Validation class for carrier.
*
* Valid carriers are: UPS, USPS, STAMPS, and FedEx.
*/
class Usps extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function getUSPSValidate()
    {
        $xml = $this->buildUSPSValidateStreetLevelXml();
        $xmlString = 'API=Verify&XML='. $xml;
        $this->core->request('ShippingAPI.dll', $xmlString);

        // Convert the xmlString to an array
        return $this->simplifyResponse($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyResponse($r)
    {
        $av = new \RocketShipIt\Response\AddressValidation;

        if ($this->get($r, 'Error.Description', '') != '') {
            $e = new \RocketShipIt\Response\Error;
            $e->Code = $this->get($r, 'Error.Number', '');
            $e->Description = $this->get($r, 'Error.Description', '');
            $e->Type = 'Error';
            $av->Data->Errors[] = $e;
        }
        if ($this->get($r, 'AddressValidateResponse.Address.Error', '') != '') {
            $e = new \RocketShipIt\Response\Error;
            $e->Code = $this->get($r, 'AddressValidateResponse.Address.Error.Number', '');
            $e->Description = $this->get($r, 'AddressValidateResponse.Address.Error.Description', '');
            $e->Type = 'Error';
            $av->Data->Errors[] = $e;
        }

        $av->Data->Addr1 = $this->get($r, 'AddressValidateResponse.Address.Address2', '');
        $av->Data->Addr2 = $this->get($r, 'AddressValidateResponse.Address.Address1', '');
        $av->Data->City = $this->get($r, 'AddressValidateResponse.Address.City', '');
        $av->Data->State = $this->get($r, 'AddressValidateResponse.Address.State', '');
        $av->Data->ZipCode = $this->get($r, 'AddressValidateResponse.Address.Zip5', '');
        $av->Data->ZipCodeAddon = $this->get($r, 'AddressValidateResponse.Address.Zip4', '');

        return (array) json_decode(json_encode($av), true);
    }

    function buildUSPSValidateStreetLevelXml()
    {
        $xml = new xmlBuilder();
        $xml->push('AddressValidateRequest', array('USERID' => $this->userid));
            $xml->push('Address');
                if ($this->toAddr2 != '') {
                    $xml->element('Address1', $this->toAddr1);
                    $xml->element('Address2', $this->toAddr2);
                } else {
                    $xml->emptyelement('Address1');
                    $xml->element('Address2', $this->toAddr1);
                }
                $xml->element('City', $this->toCity);
                $xml->element('State', $this->toState);
                $xml->emptyelement('Zip5');
                $xml->emptyelement('Zip4');
            $xml->pop(); //end Address
        $xml->pop(); //end OriginDetail
        return $xml->getXml();
    }

    public function lookupCityState()
    {
        $xml = new xmlBuilder();
        $xml->push('CityStateLookupRequest', array('USERID' => $this->userid));
            $xml->push('ZipCode', array('ID' => 0));
                $xml->element('Zip5', $this->toCode);
            $xml->pop();
        $xml->pop();
        $xml = $xml->getXml();

        $xmlString = 'API=CityStateLookup&XML='. $xml;
        $this->core->request('ShippingAPI.dll', $xmlString);

        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
