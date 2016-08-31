<?php

namespace RocketShipIt;

/**
 * Main class for producing package objects that are later inserted into a shipment.
 *
 * @see RocketShipShipment::addPackageToShipment()
 */
class Package extends \RocketShipIt\Service\Base
{
    public $ups;

    public function __Construct($carrier, $options = array())
    {
        parent::__construct($carrier, false, $options);
    }
}
