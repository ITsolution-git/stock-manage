<?php

namespace RocketShipIt;

class Cn22Content extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        parent::__construct($carrier, false, $options);
    }
}
