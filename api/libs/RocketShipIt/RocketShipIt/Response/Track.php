<?php

namespace RocketShipIt\Response;

class Track extends \RocketShipIt\Response\Base
{

    public function __construct() {
        $a = array (
            'Errors' => array(),
            'ShipmentId' => '',
            'Destination' => null,
            'EstimatedDelivery' => '',
            'DeliveredTime' => '',
            'Packages' => array(),
        );

        $this->Meta = (object) array(
            'Code' => 200,
            'ErrorMessage' => '',
        );

        $this->Data = (object) $a;
    }
}
