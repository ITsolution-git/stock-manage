<?php

namespace RocketShipIt;

/**
 * Main Rate class for producing rates for various packages/shipments.
 *
 * This class is a wrapper for use with all carriers to produce rates
 * Valid carriers are: UPS, USPS, STAMPS and FedEx.
 */
class Rate extends \RocketShipIt\Service\Base
{
    public $packageCount;

    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * Returns a single rate from the carrier.
     */
    public function getRate()
    {
        if (!method_exists($this->inherited, 'getRate')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->getRate();
    }

    /**
     * Returns all available rates from the carrier.
     */
    public function getAllRates()
    {
        if (!method_exists($this->inherited, 'getAllRates')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->getAllRates();
    }

    /**
     * This is a wrapper to create a running package for each carrier.
     *
     * This is used to add packages to a shipment for any carrier.
     * You use the {@link RocketShipPackage} class to create a package
     * object.
     */
    public function addPackageToShipment($packageObj)
    {
        $this->inherited->core->parameters['packages'][] = $packageObj->parameters;
        ++$this->packageCount;

        if (!method_exists($this->inherited, 'addPackageToShipment')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->addPackageToShipment($packageObj);
    }

    /**
     * Return a simple rate from carrier.
     */
    public function getSimpleRate()
    {
        if (!method_exists($this->inherited, 'getSimpleRate')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->getSimpleRate();
    }

    /**
     * Return all available rates from carrier in a simple array.
     */
    public function getSimpleRates($user_func = null)
    {
        if (!method_exists($this->inherited, 'getSimpleRates')) {
            return $this->invalidCarrierResponse();
        }

        return $this->inherited->getSimpleRates($user_func);
    }

    // Checks the country to see if the request is International
    public function isInternational($country)
    {
        if ($country == '' || $country == 'US' || $country == $this->inherited->core->getCountryName('US')) {
            return false;
        }

        return true;
    }
}
