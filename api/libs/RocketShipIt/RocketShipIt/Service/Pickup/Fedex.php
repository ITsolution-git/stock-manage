<?php

namespace RocketShipIt\Service\Pickup;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

class Fedex extends \RocketShipIt\Service\Common
{
    var $customsLines;

	/**
     * Class constructor
     * 
     * @param $carrier
     */
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);    
    }
    
    function buildFEDEXPickupXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:CreatePickupRequest', 
            array('xmlns:ns' => 'http://fedex.com/ws/pickup/v5',
                  'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                  ));
        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->append($this->buildHeaderXml());
        $xml->push('ns:AssociatedAccountNumber');
            $xml->element('ns:Type', 'FEDEX_EXPRESS');
            $xml->element('ns:AccountNumber', $this->accountNumber);
        $xml->pop(); // end AssociatedAccountNumber
        
        $xml->push('ns:OriginDetail');
            $xml->element('ns:UseAccountAddress', 'false');
            $xml->push('ns:PickupLocation');
                $xml->push('ns:Contact');
                    $xml->element('ns:PersonName', $this->pickupName);
                    $xml->element('ns:CompanyName', $this->pickupCompany);
                    $xml->element('ns:PhoneNumber', $this->pickupPhone);
                $xml->pop(); //end ns:Contact
                $xml->push('ns:Address');
                    $xml->element('ns:StreetLines', $this->pickupAddr1);
                    $xml->element('ns:City', $this->pickupCity);
                    $xml->element('ns:StateOrProvinceCode', $this->pickupState);
                    $xml->element('ns:PostalCode', $this->pickupCode);
                    $xml->element('ns:CountryCode', $this->pickupCountry);
                    $xml->element('ns:Residential', $this->pickupResidential); //true false
                $xml->pop(); //end ns:Address
            $xml->pop(); //end ns:PickupLocation
            // FRONT, NONE, REAR, SIDE
            if ($this->pickupPackageLocation != '') {
                $xml->element('ns:PackageLocation', $this->pickupPackageLocation);
            } else {
                $xml->element('ns:PackageLocation', 'NONE');
            }
            $xml->element('ns:ReadyTimestamp', $this->readyTime); //2011-08-02T08:00:18.282Z
            $xml->element('ns:CompanyCloseTime', $this->closeTime); //17:00:00
        $xml->pop(); //end OriginDetail
        $xml->element('ns:PackageCount', $this->packageCount);
        $xml->push('ns:TotalWeight');
            $xml->element('ns:Units', $this->weightUnit);
            $xml->element('ns:Value', '5');
        $xml->pop(); //end TotalWeight
        // FDXC
        // FDXE
        // FDXG
        // FXCC
        // FXFR
        // FXSP
        $xml->element('ns:CarrierCode', 'FDXG');
        
        $xml->pop(); // end CreatePickupRequest

        return $xml->getXml();
    }

    function createPickupRequest()
    {
        $xmlString = $this->buildFEDEXPickupXml();

        // Put the xml that is sent to FedEx into a variable so we can call it later for debugging.
        $this->core->xmlSent = $xmlString;
        $this->core->xmlResponse = $this->core->request($xmlString);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function buildHeaderXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:TransactionDetail');
            $xml->element('ns:CustomerTransactionId','CreatePendingRequest');
        $xml->pop(); // end TransactionDetail
        $xml->push('ns:Version');
            $xml->element('ns:ServiceId','disp');
            $xml->element('ns:Major','5');
            $xml->element('ns:Intermediate','0');
            $xml->element('ns:Minor','0');
        $xml->pop(); // end Version
        return $xml->getXml();
    }
}
