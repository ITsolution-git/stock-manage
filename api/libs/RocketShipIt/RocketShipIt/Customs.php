<?php

namespace RocketShipIt;

/**
 * Main class for creating detailed lines for customs documents.
 *
 * This class is a wrapper for use with all carriers to produce customs
 * documents.  Valid carriers are: UPS, USPS, and FedEx.
 */
class Customs extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        parent::__construct($carrier, false, $options);
    }
}
