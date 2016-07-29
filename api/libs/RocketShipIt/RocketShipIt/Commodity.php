<?php

namespace RocketShipIt;

class Commodity extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        parent::__construct($carrier, false, $options);
    }
}
