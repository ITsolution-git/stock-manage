<?php

namespace RocketShipIt;

class Freight extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    public function addPackageToShipment($packageObj)
    {
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->addPackageToUPSshipment($packageObj);
            default:
                return $this->invalidCarrierResponse();
        }
    }

    /**
     * This is a wrapper to create a running customs document for each carrier.
     */
    public function addCustomsLineToShipment($packageObj)
    {
        $this->inherited->core->parameters['customs'][] = $packageObj->parameters;
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->addCustomsLineToUPSshipment($packageObj);
            default:
                return $this->invalidCarrierResponse();
        }
    }

    public function addCommodityLineToShipment($commodityObj)
    {
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->addCommodityLineToShipment($commodityObj);
            default:
                return $this->invalidCarrierResponse();
        }
    }

    /**
     * Sends the shipment data to the carrier.
     *
     * After the shipment data is sent it returns a simplified array of
     * the data sent back from the carrier.
     */
    public function submitShipment()
    {
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->submitShipment();
            default:
                return $this->invalidCarrierResponse();
        }
    }
}
