<?php

namespace RocketShipIt\Service\Track;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

/**
* Main class for tracking shipments and packages
*
* This class is a wrapper for use with all carriers to track packages
*/
class Stamps extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function trackSTAMPS($trackingNumber)
    {
        $creds = $this->core->getCredentials();
        $request['Credentials'] = $creds;
        // You can also track by StampsTxID
        //$request['StampsTxID'] = $trackingNumber;
        $request['TrackingNumber'] = $trackingNumber;
        $response = $this->core->request('TrackShipment', $request);
        return $this->simplifyResponse($response);
    }

    public function simplifyResponse($r)
    {
        $t = new \RocketShipIt\Response\Track;
        $t->Meta->Code = 200;

        $p = new \RocketShipIt\Response\Track\Package;

        if (get_class($r) == 'SoapFault') {
            $e = new \RocketShipIt\Response\Error;
            $e->Description = $r->getMessage();
            $e->Type = 'Error';
            $t->Data->Errors[] = $e;
        }

        if (isset($r->TrackingEvents->TrackingEvent)) {
            foreach ($r->TrackingEvents->TrackingEvent as $event) {
                $a = new \RocketShipIt\Response\Track\Activity;
                $a->Time = $event->Timestamp;
                $a->Description = $event->Event;
                $a->StatusCode = $event->TrackingEventType;
                $a->StatusDescription = $a->Description;

                $l = new \RocketShipIt\Response\Track\Location;
                $l->City = $event->City;
                $l->State = $event->State;
                $l->PostalCode = $event->Zip;
                $l->Country = 'US';
                if ($event->Country != '') {
                    $l->Country = $event->Country;
                }

                $a->Location = $l;
                $p->Activity[] = $a;
            }
            $t->Data->Packages[] = $p;
        }

        return (array) json_decode(json_encode($t), true);
    }
}
