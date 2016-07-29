<?php

namespace RocketShipIt\Service\Pickup;

use RocketShipIt\Helper\XmlBuilder;

class Dhl extends \RocketShipIt\Service\Common
{
    public $packages;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildDHLPickupXml()
    {
        $xml = new XmlBuilder();
        $xml->push('req:BookPickupRequest', array('xmlns:req' => 'http://www.dhl.com', 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation' => 'http://www.dhl.com BookPickupRequest.xsd'));
        $xml->push('Request');
        $xml->push('ServiceHeader');
        $xml->element('MessageTime', date('c'));
        $xml->element('MessageReference', $this->core->generateRandomString());
        $xml->element('SiteID', $this->siteId);
        $xml->element('Password', $this->password);
        $xml->pop(); //end ServiceHeader
            $xml->pop(); //end Request
            $xml->push('Requestor');
        $xml->element('AccountType', 'D'); // D or C | DHL or Credit Card
                $xml->element('AccountNumber', $this->accountNumber);
        $xml->push('RequestorContact');
        $xml->element('PersonName', $this->shipContact);
        $xml->element('Phone', $this->shipPhone);
                    //$xml->element('PhoneExtention', '5053');
                $xml->pop(); //end RequestorContact
            $xml->pop(); //end Requestor
            $xml->push('Place');
        $xml->element('LocationType', 'B'); // Type Of Location B=Business R=Residence  C= Business/Residence
        if ($this->pickupCompany != '') {
            $xml->element('CompanyName', $this->pickupCompany);
        } else {
            $xml->element('CompanyName', $this->pickupName);
        }
        $xml->element('Address1', $this->pickupAddr1);
        $xml->element('Address2', $this->pickupAddr2);
        $xml->element('PackageLocation', $this->pickupPackageLocation);
        $xml->element('City', $this->pickupCity);
        $xml->element('StateCode', $this->pickupState);
                //$xml->element('DivisionName', 'California');
                $xml->element('CountryCode', $this->pickupCountry);
        $xml->element('PostalCode', $this->pickupCode);
        $xml->pop(); //end Place
            $xml->push('Pickup');
        $xml->element('PickupDate', $this->readyDate);
        $xml->element('ReadyByTime', $this->readyTime);
        $xml->element('CloseTime', $this->closeTime);
                //$xml->element('AfterHoursClosingTime', '16:20');
                //$xml->element('AfterHoursLocation', 'String');
                $xml->element('Pieces', $this->numberOfPieces);
                /*
                $xml->push('weight');
                    $xml->element('Weight', '10');
                    $xml->element('WeightUnit', 'L');
                $xml->pop(); //end weight
                */
            $xml->pop(); //end Pickup
            $xml->push('PickupContact');
        $xml->element('PersonName', $this->pickupName);
        $xml->element('Phone', $this->pickupPhone);
                //$xml->element('PhoneExtention', '5768');
            $xml->pop(); //end PickupContact
            /*
            $xml->push('ShipmentDetails');
                $xml->element('AccountType', 'D');
                $xml->element('AccountNumber', $this->accountNumber);
                $xml->element('BillToAccountNumber', $this->accountNumber);
                $xml->element('AWBNumber', '7520067111');
                $xml->element('NumberOfPieces', '1');
                $xml->element('Weight', '10');
                $xml->element('WeightUnit', 'L');
                $xml->element('GlobalProductCode', 'P');
                $xml->element('DoorTo', 'DD');
                $xml->element('DimensionUnit', 'I');
                $xml->element('InsuredAmount', '999999.99');
                $xml->element('InsuredCurrencyCode', 'USD');
                foreach ($this->packages as $package) {
                    $xml->push('Pieces');
                        $xml->element('Weight', $package->weight);
                        $xml->element('Width', $package->width);
                        $xml->element('Height', $package->height);
                        $xml->element('Depth', $package->length);
                    $xml->pop(); //end Pieces
                }
                $xml->element('SpecialService', 'S');
                $xml->element('SpecialService', 'I');
            $xml->pop(); //end ShipmentDetails
            */
        $xml->pop(); //end BookPickupRequest
        return $xml->getXml();
    }

    public function buildDHLCancelPickupXml()
    {
        $xml = new XmlBuilder();
        $xml->push('req:CancelPURequest', array(
            'xmlns:req' => 'http://www.dhl.com',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.dhl.com cancel-pickup-req.xsd',
            'schemaVersion' => '1.0',
        ));
        $xml->push('Request');
        $xml->push('ServiceHeader');
        $xml->element('MessageTime', date('c'));
        $xml->element('MessageReference', $this->core->generateRandomString());
        $xml->element('SiteID', $this->siteId);
        $xml->element('Password', $this->password);
        $xml->pop(); //end ServiceHeader
        $xml->pop(); //end Request
        if ($this->regionCode != '') {
            $xml->element('RegionCode', $this->regionCode);
        } else {
            $xml->element('RegionCode', 'AM');
        }
        $xml->element('ConfirmationNumber', $this->confirmationNumber);
        $xml->element('RequestorName', $this->shipper);
        $xml->element('CountryCode', $this->shipCountry);

        /*
        001   PACKAGE_NOT_READY
        002     RATES_TOO_HIGH
        003    TRANSIT_TIME_TOO_SLOW
        004    TAKE_TO_SERVICE_CENTER_OR_DROP_BOX
        005    COMMITMENT_TIME_NOT_MET
        006    REASON_NOT_GIVEN
        007    OTHER
        008    PICKUP_MODIFIED
        */
        $xml->element('OriginSvcArea', $this->originSvcArea);
        $xml->element('Reason', '001');

        if ($this->pickupDate != '') {
            $xml->element('PickupDate', $this->pickupDate);
        } else {
            $xml->element('PickupDate', date('Y-m-d'));
        }
        if ($this->pickupDate != '') {
            $xml->element('CancelTime', $this->cancelTime);
        } else {
            $xml->element('CancelTime', date('h:m'));
        }
        $xml->pop(); //end CancelPickupRequest
        return $xml->getxml();
    }

    public function createPickupRequest()
    {
        $this->core->request('', $this->buildDHLPickupXml());

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function cancelPickupRequest()
    {
        $this->core->request('', $this->buildDHLCancelPickupXml());

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
