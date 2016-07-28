<?php

namespace RocketShipIt;

class Download extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    public function csv()
    {
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->csv();
            default:
               return $this->invalidCarrierResponse();
        }
    }
}
