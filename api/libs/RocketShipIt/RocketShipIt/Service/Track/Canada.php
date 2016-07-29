<?php

namespace RocketShipIt\Service\Track;
use \RocketShipIt\Helper\XmlParser;

/**
* Main class for tracking shipments and packages
*
* This class is a wrapper for use with all carriers to track packages
* Valid carriers are: UPS, USPS, and FedEx.
*/
class Canada extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function trackCANADA($trackingNumber)
    {
        $this->core->request('/vis/track/pin/'. $trackingNumber. '/detail', '');

        // Convert the xmlString to an array
        return $this->simplifyResponse($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyResponse($r)
    {
        $t = new \RocketShipIt\Response\Track;
        $t->Meta->Code = 200;

        if (isset($r['messages']['message']['description'])) {
            $e = new \RocketShipIt\Response\Error;
            $e->Description = $r['messages']['message']['description'];
            $e->Code = $r['messages']['message']['code'];
            $e->Type = 'Error';
            $t->Data->Errors[] = $e;
        }
        $p = new \RocketShipIt\Response\Track\Package;
        if (isset($r['tracking-detail']['significant-events']['occurrence'])) {
            foreach ($r['tracking-detail']['significant-events']['occurrence'] as $act) {
                $a = new \RocketShipIt\Response\Track\Activity;
                $a->Description = $act['event-description'];
                $a->StatusCode = $act['event-identifier'];
                $a->Signatory = $act['signatory-name'];
                $a->Time = date('Y-m-d\TH:i:s\Z', strtotime($act['event-date']. ' '. $act['event-time']. ' '. $act['event-time-zone']));

                $l = new \RocketShipIt\Response\Track\Location;
                $l->City = $act['event-site'];
                $l->State = $act['event-province'];
                $a->Location = $l;

                $p->Activity[] = $a;
            }
            $t->Data->Packages[] = $p;
        }

        return (array) json_decode(json_encode($t), true);
    }
}
