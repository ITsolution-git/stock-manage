<?php

namespace RocketShipIt\Service\AddressValidate;

/**
* Main Address Validation class for carrier.
*
* Valid carriers are: UPS, USPS, STAMPS, and FedEx.
*/
class Fedex extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildFEDEXAddressValidateXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:AddressValidationRequest', array('xmlns:ns' => 'http://fedex.com/ws/addressvalidation/v2'));
        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('ns:Version');
            $xml->element('ns:ServiceId','aval');
            $xml->element('ns:Major','2');
            $xml->element('ns:Intermediate','0');
            $xml->element('ns:Minor','0');
        $xml->pop(); // end Version
        $xml->element('ns:RequestTimestamp', date('c'));
        $xml->push('ns:Options');
            $xml->element('ns:CheckResidentialStatus', '1');
            $xml->element('ns:MaximumNumberOfMatches', '10');
            $xml->element('ns:StreetAccuracy', 'LOOSE');
            $xml->element('ns:DirectionalAccuracy', 'LOOSE');
            $xml->element('ns:CompanyNameAccuracy', 'LOOSE');
            $xml->element('ns:RecognizeAlternateCityNames', '1');
            $xml->element('ns:ReturnParsedElements', '1');
        $xml->pop(); // end Options

        $xml->push('ns:AddressesToValidate');
            $xml->push('ns:Address');
                $xml->element('ns:StreetLines', $this->toAddr1);
                $xml->element('ns:StreetLines', $this->toAddr2);
                $xml->element('ns:City', $this->toCity);
                $xml->element('ns:StateOrProvinceCode', $this->toState);
                $xml->element('ns:PostalCode', $this->toCode);
            $xml->pop(); // end Address
        $xml->pop(); // end AddressToValidate

        $xml->pop(); // end AddressValidationRequest

        return $xml->getXml();
    }

    public function getFEDEXValidate()
    {
        $xmlString = $this->buildFEDEXAddressValidateXml();
        $this->core->request($xmlString);

        // Convert the xmlString to an array
        return $this->simplifyResponse($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyResponse($r)
    {
        $av = new \RocketShipIt\Response\AddressValidation;

        if ($this->get($r, 'AddressValidationReply.Notifications.Message', '') != '') {
            $e = new \RocketShipIt\Response\Error;
            $e->Code = $this->get($r, 'AddressValidationReply.Notifications.Code', '');
            $e->Description = $this->get($r, 'AddressValidationReply.Notifications.Message', '');
            $e->Type = $this->get($r, 'AddressValidationReply.Notifications.Severity', '');
            $av->Data->Errors[] = $e;
        }

        if (isset($r['AddressValidationReply']['AddressResults']['ProposedAddressDetails']['Address'])) {
            $parsedAddr = $r['AddressValidationReply']['AddressResults']['ProposedAddressDetails']['ParsedAddress'];
            $av->Data->Addr1 = $this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.Address.StreetLines', '');
            $av->Data->City = $this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.Address.City', '');

            if ($this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.DeliveryPointValidation', '') == 'CONFIRMED') {
                $av->Data->Match = true;
                $av->Data->CityStateZipMatch = true;
            }

            if (isset($parsedAddr['ParsedPostalCode']['Elements'][0])) {
                foreach ($parsedAddr['ParsedPostalCode']['Elements'] as $elm) {
                    if (isset($elm['Name']) && $elm['Name'] == 'postalBase') {
                        $av->Data->ZipCode = $elm['Value'];
                    }
                    if (isset($elm['Name']) && $elm['Name'] == 'postalAddOn') {
                        $av->Data->ZipCodeAddon = $elm['Value'];
                    }
                }
            }

            if (isset($parsedAddr['ParsedPostalCode']['Elements']['Name'])) {
                if ($parsedAddr['ParsedPostalCode']['Elements']['Name'] == 'postalBase') {
                    $av->Data->ZipCode = $parsedAddr['ParsedPostalCode']['Elements']['Value'];
                }
            }

            $av->Data->State = $this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.Address.StateOrProvinceCode', '');
            $av->Data->Country = $this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.Address.CountryCode', '');

            if ($this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.ResidentialStatus', '') == 'RESIDENTIAL') {
                $av->Data->Residential = true;
            }
            if ($this->get($r, 'AddressValidationReply.AddressResults.ProposedAddressDetails.ResidentialStatus', '') == 'BUSINESS') {
                $av->Data->Residential = false;
            }
        }

        return (array) json_decode(json_encode($av), true);
    }
}
