<?php

namespace RocketShipIt\Service\AddressValidate;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

/**
* Main Address Validation class for carrier.
*
* Valid carriers are: UPS, USPS, STAMPS, and FedEx.
*/
class Ups extends \RocketShipIt\Service\Common
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

        if (isset($xmlArray['AddressValidationResponse']['Response']['ResponseStatusCode'])) {
            if ($xmlArray['AddressValidationResponse']['Response']['ResponseStatusCode'] == 1) {
                $this->status = 'success';
            }
            if ($xmlArray['AddressValidationResponse']['Response']['ResponseStatusCode'] == 0) {
                $this->status = 'failure';
            }
        }
    }

    // Builds xml for a rate request converts xml to a string, sends the xml to ups,
    // stores the xmlSent and xmlResponse in the ups class incase you want to see it.
    // Finally, this class returns the xml response from UPS as an array.
    function getUPSValidate()
    {
        // Grab the auth portion of the xml from the ups class
        $this->core->access();
        $accessXml = $this->core->xmlObject;

        // Start a new xml object
        $xml = new xmlBuilder();

        $xml->push('AddressValidationRequest',array('xml:lang' => 'en-US'));
            $xml->push('Request');
                $xml->push('TransactionReference'); // Not required
                    $xml->element('CustomerContext', 'RocketShipIt'); // Not required
                    //$xml->element('XpciVersion', '1.0'); // Not required
                $xml->pop(); // close TransactionReference, not required
                $xml->element('RequestAction', 'AV');
            $xml->pop(); // Close Request
            $xml->push('Address');
                if ($this->toCity != '') {
                    $xml->element('City', $this->toCity);
                }
                if ($this->toState != '') {
                    $xml->element('StateProvinceCode', $this->toState);
                }
                if ($this->toCode != '') {
                    $xml->element('PostalCode', $this->toCode);
                }
            $xml->pop(); // Close Address
        $xml->pop(); // Close AddressValidationRequest

        // Convert xml object to a string appending the auth xml
        $xmlString = $accessXml->getXml(). $xml->getXml();

        // Submit the cURL call
        $this->core->request('AV', $xmlString);

        $this->processResponse(); 
        return $this->responseArray;
    }

    public function getUSPSValidate()
    {
        return array();
    }

    public function getUPSValidateStreetLevel()
    {
        $this->core->request('XAV', $this->buildUPSValidateStreetLevelXml()); 
        $this->processResponse(); 
        return $this->simplifyResponse($this->responseArray);
    }

    public function simplifyResponse($r)
    {
        $av = new \RocketShipIt\Response\AddressValidation;

        if (isset($r['AddressValidationResponse']['Response']['Error']['ErrorDescription'])) {
            $e = new \RocketShipIt\Response\Error;
            $e->Code = $r['AddressValidationResponse']['Response']['Error']['ErrorCode'];
            $e->Description = $r['AddressValidationResponse']['Response']['Error']['ErrorDescription'];
            $e->Type = $r['AddressValidationResponse']['Response']['Error']['ErrorSeverity'];
            $av->Data->Errors[] = $e;
        }

        $suggestions = array();
        if (isset($r['AddressValidationResponse']['AddressKeyFormat'])) {
            if (isset($r['AddressValidationResponse']['AddressKeyFormat'][0])) {
                $suggestions = $r['AddressValidationResponse']['AddressKeyFormat'];
            } else {
                $suggestions = array($r['AddressValidationResponse']['AddressKeyFormat']);
            }
        }

        foreach ($suggestions as $suggestion) {
            $s = new \RocketShipIt\Response\AddressValidation\Suggestion;
            $s->Addr1 = $suggestion['AddressLine'];
            if (isset($suggestion['BuildingName'])) {
                $s->Addr2 = $suggestion['BuildingName'];
            }
            if (is_array($suggestion['AddressLine']) && isset($suggestion['AddressLine'][1])) {
                $s->Addr1 = $suggestion['AddressLine'][0];
                $s->Addr2 = $suggestion['AddressLine'][1];
            }
            $s->City = $suggestion['PoliticalDivision2'];
            $s->ZipCode = $suggestion['PostcodePrimaryLow'];
            $s->ZipCodeAddon = $suggestion['PostcodeExtendedLow'];
            $s->State = $suggestion['PoliticalDivision1'];
            $s->Country = $suggestion['CountryCode'];

            if (isset($suggestion['ConsigneeName'])) {
                $s->Name = $suggestion['ConsigneeName'];
            }

            if (isset($suggestion['AddressClassification']['Code'])) {
                if ($suggestion['AddressClassification']['Code'] == '1') {
                    $s->Residential = false;
                }
                if ($suggestion['AddressClassification']['Code'] == '2') {
                    $s->Residential = true;
                }
            }

            $av->Data->Suggestions[] = $s;
        }

        if (isset($suggestions[0])) {
            $suggestion = $suggestions[0];
            $av->Data->Addr1 = $suggestion['AddressLine'];
            if (isset($suggestion['BuildingName'])) {
                $av->Data->Addr2 = $suggestion['BuildingName'];
            }
            if (is_array($suggestion['AddressLine']) && isset($suggestion['AddressLine'][1])) {
                $av->Data->Addr1 = $suggestion['AddressLine'][0];
                $av->Data->Addr2 = $suggestion['AddressLine'][1];
            }
            $av->Data->City = $suggestion['PoliticalDivision2'];
            $av->Data->ZipCode = $suggestion['PostcodePrimaryLow'];
            $av->Data->ZipCodeAddon = $suggestion['PostcodeExtendedLow'];
            $av->Data->State = $suggestion['PoliticalDivision1'];
            $av->Data->Country = $suggestion['CountryCode'];

            if (isset($suggestion['AddressClassification']['Code'])) {
                if ($suggestion['AddressClassification']['Code'] == '1') {
                    $av->Data->Residential = false;
                }
                if ($suggestion['AddressClassification']['Code'] == '2') {
                    $av->Data->Residential = true;
                }
            }
        }

        return (array) json_decode(json_encode($av), true);
    }

    function buildUPSValidateStreetLevelXml()
    {
        $this->core->access();
        $accessXml = $this->core->xmlObject;

        $xml = new xmlBuilder();

        $xml->push('AddressValidationRequest',array('xml:lang' => 'en-US'));
            $xml->push('Request');
                $xml->push('TransactionReference'); // Not required
                    $xml->element('CustomerContext', 'RocketShipIt'); // Not required
                    //$xml->emptyelement('ToolVersion');
                $xml->pop(); // close TransactionReference, not required
                $xml->element('RequestAction', 'XAV');
                $xml->element('RequestOption', '3');
            $xml->pop(); // close Request
            $xml->push('AddressKeyFormat');
                $xml->element('ConsigneeName', $this->toName);
                $xml->element('AttentionName', $this->toName);
                $xml->element('PoliticalDivision1', $this->toState);
                $xml->element('PoliticalDivision2', $this->toCity);
                $xml->element('AddressLine', $this->toAddr1);
                if ($this->toAddr2 != '') {
                    $xml->element('AddressLine', $this->toAddr2);
                }
                $xml->element('PostcodePrimaryLow', $this->toCode);
                $xml->element('PostcodeExtendedLow', $this->toExtendedCode);
                $xml->element('CountryCode', $this->toCountry);
            $xml->pop(); // close AddressKeyFormat
        $xml->pop(); // close AddressValidationRequest

        $xmlString = $accessXml->getXml(). $xml->getXml();
        return $xmlString;
    }
}
