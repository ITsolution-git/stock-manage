<?php

namespace RocketShipIt\Response;

class AddressValidation extends \RocketShipIt\Response\Base
{

    public function __construct()
    {
        $a = array(
            'Errors' => array(),
            'Name'  => '',
            'Addr1' => '',
            'Addr2' => '',
            'Addr3' => '',
            'City' => '',
            'State' => '',
            'ZipCode' => '',
            'ZipCodeAddon' => '',
            'Match' => null,
            'CityStateZipMatch' => null,
            'Residential' => null,
            'POBox' => null,
            'Country' => 'US',
            'Suggestions' => array(),
        );

        $this->Meta = (object) array(
            'Code' => 200,
            'ErrorMessage' => '',
        );

        $this->Data = (object) $a;
    }
}
