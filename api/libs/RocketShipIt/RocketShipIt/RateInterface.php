<?php

namespace RocketShipIt;

interface RateInterface
{
    public function getRate();
    public function getAllRates();
    public function addPackageToShipment($packageObj);
    public function getSimpleRate();
    public function getSimpleRates($user_func = null);
}
