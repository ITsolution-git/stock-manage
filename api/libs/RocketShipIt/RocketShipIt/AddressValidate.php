<?php

namespace RocketShipIt;

class AddressValidate extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * Send address data to carrier.
     *
     * This function detects carrier and executes the
     * carrier specific function.
     */
    public function validate()
    {
        $method = 'get'.$this->carrier.'Validate';
        if (!method_exists($this->inherited, $method)) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->$method();
    }

    public function validateStreetLevel()
    {
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->getUPSValidateStreetLevel();
            default:
                return $this->invalidCarrierResponse();
        }
    }

    public function lookupCityState()
    {
        switch ($this->carrier) {
            case 'USPS':
                return $this->inherited->lookupCityState();
            default:
                return $this->invalidCarrierResponse();
        }
    }
}
