<?php

namespace RocketShipIt;

/**
 * Main class for uploding images.
 *
 * This class is a wrapper for use with all carriers to cancel
 * shipments.
 */
class UploadImage extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    public function upload($validate_only = false)
    {
        switch ($this->carrier) {
            case 'FEDEX':
                return $this->inherited->upload($validate_only);
            default:
               return $this->invalidCarrierResponse();
        }
    }
}
