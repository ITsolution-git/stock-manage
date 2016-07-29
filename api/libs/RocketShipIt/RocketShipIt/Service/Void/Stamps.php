<?php

namespace RocketShipIt\Service\Void;

/**
* Main class for voiding shipments.
*
* This class is a wrapper for use with all carriers to cancel 
* shipments.  Valid carriers are: UPS, USPS, and FedEx.
* To create a shipment see {@link RocketShipShipment}.
*/
class Stamps extends \RocketShipIt\Service\Common implements \RocketShipIt\VoidInterface
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }
   
    function voidShipment()
    {
        $shipment = new \stdClass();
        $shipment->Credentials = $this->core->getCredentials();
        $shipment->StampsTxID = $this->shipmentIdentification;

        return $this->core->request('CancelIndicium', $shipment);
    }

    public function voidPackage()
    {
        return array('error' => 'not implemented');
    }
}
