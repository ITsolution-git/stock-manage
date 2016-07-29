<?php

namespace RocketShipIt;

/**
 * Main class for getting time in transit information.
 */
class TimeInTransit extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * Returns a Time in Transit resposne from the carrier.
     */
    public function getTimeInTransit()
    {
        $method = 'get'.$this->carrier.$this->carrierService;
        if (!method_exists($this->inherited, $method)) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->$method();
    }

    public function getSimpleTimeInTransit($user_func = null)
    {
        $method = 'getSimpleTimeInTransit';
        if (!method_exists($this->inherited, $method)) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->$method($user_func);
    }
}
