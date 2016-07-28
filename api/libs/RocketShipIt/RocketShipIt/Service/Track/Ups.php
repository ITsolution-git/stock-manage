<?php

namespace RocketShipIt\Service\Track;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

/**
* Main class for tracking shipments and packages
*
* This class is a wrapper for use with all carriers to track packages
* Valid carriers are: UPS, USPS, STAMPS and FedEx.
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

        if (isset($xmlArray['TrackResponse']['Response']['ResponseStatusCode'])) {
            if ($xmlArray['TrackResponse']['Response']['ResponseStatusCode'] == 1) {
                $this->status = 'success';
            }

            if ($xmlArray['TrackResponse']['Response']['ResponseStatusCode'] == 0) {
                $this->status = 'failure';
            }
        }
    }

    public function buildUPSTrackingXml($trackingNumber)
    {
        $this->core->access();
        $xml = $this->core->xmlObject;
        
        $xml->push('TrackRequest',array('xml:lang' => 'en-US'));
            $xml->element('TrackingOption', $this->trackingOption);
            $xml->push('Request');
                $xml->push('TransactionReference');
                    $xml->element('CustomerContext', 'RocketShipIt');
                $xml->pop(); // close TransactionReference
                $xml->element('RequestAction', 'Track');
                /*
                'none' – " " or '0'= Last Activity
                'activity' or '1' - all activity
                2 = POD, Receiver Address and Last Activity
                3 = POD, Receiver Address, All Activity
                4 = POD, COD, Last Activity
                5 = POD, COD, All Activity
                6 = POD, COD, Receiver Address, Last Activity
                7 = POD, COD, Receiver Address, All Activity
                */
                // activity doesn't work for Mail Innovations use '1'
                $xml->element('RequestOption', '3');
            $xml->pop(); // close Request
            if (!isset($this->referenceNumber)) {
                $helper = new \RocketShipIt\Helper\General();
                if ($helper->startsWith($trackingNumber, '1Z')) {
                    $xml->element('ShipmentIdentificationNumber', $trackingNumber);
                } else {
                    $xml->element('TrackingNumber', $trackingNumber);
                }
            } else {
                $xml->element('ShipperNumber', $this->accountNumber);
                $xml->push('ReferenceNumber');
                    $xml->element('Value', $this->referenceNumber);
                $xml->pop(); // close ReferenceNumber
            }
        $xml->pop();

        // Convert xml object to a string
        return $xml->getXml();
    }

    // Builds xml for tracking and sends the xml string to the ups->request method
    // receives a response from UPS and outputs an array.
    public function trackUPS($trackingNumber)
    {
        $xmlString = $this->buildUPSTrackingXml($trackingNumber);

        // Send the xmlString to UPS and store the resonse in a class variable, xmlResponse.
        $this->core->request('Track', $xmlString);

        $this->processResponse();

        return $this->simplifyResponse($this->responseArray); 
    }

    public function simplifyResponse($resp)
    {
        $t = new \RocketShipIt\Response\Track;
        $t->Meta->Code = 200;
        $t->Data->ShipmentId = $this->get($resp, 'TrackResponse.Shipment.ShipmentIdentificationNumber', '');
        if ($this->get($resp, 'TrackResponse.Shipment.ScheduledDeliveryDate', '') != '') {
            $t->Data->EstimatedDelivery = date('Y-m-d\TH:i:s\Z', strtotime($this->get($resp, 'TrackResponse.Shipment.ScheduledDeliveryDate', '')));
        }

        $pkgs = array();
        if (isset($resp['TrackResponse']['Shipment']['Package']['TrackingNumber'])) {
            $pkgs[] = $resp['TrackResponse']['Shipment']['Package'];
        }

        if (isset($resp['TrackResponse']['Shipment']['Package'][0])) {
            $pkgs = $resp['TrackResponse']['Shipment']['Package'];
        }

        foreach ($pkgs as $pkg) {
            $p = new \RocketShipIt\Response\Track\Package;
            if (isset($pkg['TrackingNumber'])) {
                $p->TrackingNumber = $pkg['TrackingNumber'];
                $shpmnt = $resp['TrackResponse']['Shipment'];
                $d = new \RocketShipIt\Response\Track\Destination;
                if (isset($shpmnt['ShipTo']['Address']['AddressLine1'])) {
                    $d->Addr1 = $shpmnt['ShipTo']['Address']['AddressLine1'];
                }
                if (isset($shpmnt['ShipTo']['Address']['AddressLine2'])) {
                    $d->Addr2 = $shpmnt['ShipTo']['Address']['AddressLine2'];
                }
                if (isset($shpmnt['ShipTo']['Address']['City'])) {
                    $d->City = $shpmnt['ShipTo']['Address']['City'];
                }
                if (isset($shpmnt['ShipTo']['Address']['StateProvinceCode'])) {
                    $d->State = $shpmnt['ShipTo']['Address']['StateProvinceCode'];
                }
                if (isset($shpmnt['ShipTo']['Address']['PostalCode'])) {
                    $d->PostalCode = $shpmnt['ShipTo']['Address']['PostalCode'];
                }
                if (isset($shpmnt['ShipTo']['Address']['CountryCode'])) {
                    $d->Country = $shpmnt['ShipTo']['Address']['CountryCode'];
                }
                $t->Data->Destination = $d;

                $activity = array();
                if (!isset($pkg['Activity'][0]) && isset($pkg['Activity'])) {
                    $activity[] = $pkg['Activity'];
                } else {
                    $activity = $pkg['Activity'];
                }

                if (isset($pkg['RescheduledDeliveryDate'])) {
                    $t->Data->EstimatedDelivery = date('Y-m-d\TH:i:s\Z', strtotime($pkg['RescheduledDeliveryDate']));
                }

                foreach ($activity as $act) {
                    $a = new \RocketShipIt\Response\Track\Activity;
                    if (isset($act['Status']['StatusCode']['Code'])) {
                        $a->StatusCode = $act['Status']['StatusCode']['Code'];
                    }
                    if (isset($act['Status']['StatusType']['Description'])) {
                        $a->StatusDescription = $act['Status']['StatusType']['Description'];
                    }
                    if (isset($act['ActivityLocation']['PODLetter']['HTMLImage'])) {
                        $a->ProofOfDelivery = $act['ActivityLocation']['PODLetter']['HTMLImage'];
                    }
                    if (isset($act['Status']['StatusType']['Code'])) {
                        $a->StatusTypeCode = $act['Status']['StatusType']['Code'];
                    }

                    if ($act['Status']['StatusType']['Code'] == 'D') {
                        $t->Data->DeliveredTime = date('Y-m-d\TH:i:s\Z', strtotime($act['Date']. $act['Time']));
                    }

                    $l = new \RocketShipIt\Response\Track\Location;
                    if (isset($act['ActivityLocation']['Address']['City'])) {
                        $l->City = $act['ActivityLocation']['Address']['City'];
                    }
                    if (isset($act['ActivityLocation']['Address']['StateProvinceCode'])) {
                        $l->State = $act['ActivityLocation']['Address']['StateProvinceCode'];
                    }
                    if (isset($act['ActivityLocation']['Address']['PostalCode'])) {
                        $l->PostalCode = $act['ActivityLocation']['Address']['PostalCode'];
                    }
                    if (isset($act['ActivityLocation']['Address']['CountryCode'])) {
                        $l->Country = $act['ActivityLocation']['Address']['CountryCode'];
                    }

                    $date = '';
                    if (isset($act['Date'])) {
                        $date = $act['Date'];
                    }
                    $time = '';
                    if (isset($act['Time'])) {
                        $time= $act['Time'];
                    }

                    $a->Time = date('Y-m-d\TH:i:s\Z', strtotime($date.$time));
                    $a->Location = $l;
                    $p->Activity[] = $a;
                }

                $t->Data->Packages[] = $p;
            }
        }

        if (isset($resp['TrackResponse']['Response']['Error']['ErrorDescription'])) {
            $e = new \RocketShipIt\Response\Error;
            $e->Description = $resp['TrackResponse']['Response']['Error']['ErrorDescription'];
            $t->Data->Errors[] = $e;
        }

        return (array) json_decode(json_encode($t), true);
    }
}
