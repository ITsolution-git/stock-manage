<?php

namespace RocketShipIt;

/**
 * Main class for generating end of day scan forms.
 */
class ScanForm extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * Returns a ScanForm response from the carrier.
     */
    public function create()
    {
        if (!method_exists($this->inherited, 'create')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->create();
    }
}
