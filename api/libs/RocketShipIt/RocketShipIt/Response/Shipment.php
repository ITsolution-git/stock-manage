<?php

namespace RocketShipIt\Response;

class Shipment extends \RocketShipIt\Response\Base
{

    public function __construct() {
        $a = array (
            'Errors' => array(),
            'Charges' => 0,
            'TrackingNumber' => '',
            'ShipmentId' => '',
            'Packages' => array(),
            'Documents' => array(),
        );

        $this->Meta = (object) array(
            'Code' => 200,
            'ErrorMessage' => '',
        );

        $this->Data = (object) $a;
    }
}
