<?php

namespace RocketShipIt\Service\Rate;

use RocketShipIt\Helper\General;

/**
 * Main Rate class for producing rates for various packages/shipments.
 *
 * This class is a wrapper for use with all carriers to produce rates
 * Valid carriers are: UPS, USPS, Stamps, DHL, CANADA, and FedEx.
 */
class Stamps extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    public $packageCount;
    public $helper;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General();
        parent::__construct($carrier);
    }

    public function buildRateRequest()
    {
        $creds = $this->core->getCredentials();

        $rate = array();
        $rate['FromZIPCode'] = $this->shipCode;
        if ($this->toCode != '') {
            $rate['ToZIPCode'] = $this->toCode;
        }
        if ($this->toState != '') {
            $rate['ToState'] = $this->toState;
        }
        $rate['ToCountry'] = $this->toCountry;

        if ($this->printLayout != '') {
            $rate['PrintLayout'] = $this->printLayout;
        }

        // Add weight in lbs and oz or convert it if only weight
        // is specified.
        if ($this->weight != '') {
            $lbsAndOunces = $this->helper->weightToLbsOunces($this->weight);
            $rate['WeightLb'] = $lbsAndOunces[0];
            $rate['WeightOz'] = $lbsAndOunces[1];
        } else {
            $rate['WeightLb'] = $this->weightPounds;
            if ($this->weightOunces != '') {
                $rate['WeightOz'] = $this->weightOunces;
            }
        }

        if ($this->packagingType != '') {
            $rate['PackageType'] = $this->packagingType;
        }

        $rate['Length'] = $this->length;
        $rate['Width'] = $this->width;
        $rate['Height'] = $this->height;

        if ($this->customsValue != '') {
            $rate['DeclaredValue'] = $this->customsValue;
        }
        if ($this->insuredValue != '') {
            $rate['InsuredValue'] = $this->insuredValue;
        }

        if ($this->shipDate != '') {
            $rate['ShipDate'] = $this->shipDate;
        } else {
            $rate['ShipDate'] = date('Y-m-d');
        }

        $rate['AddOnsV2'] = array();

        $request = array();
        $request['Credentials'] = $creds;
        $request['Rate'] = $rate;

        return $request;
    }

    public function getAllRates()
    {
        $request = $this->buildRateRequest();
        $response = $this->core->request('GetRates', $request);

        return $response;
    }

    public function getSimpleRates($user_func = null)
    {
        $stamps = $this->getAllRates();
        // If error do an array with error as key and description as value
        if (is_object($stamps) && get_class($stamps) == 'SoapFault') {
            return array('error' => $stamps->getMessage());
        }
        // else, for each rate find the description and value and put it into an array
        $rates = array();
        foreach ($stamps->Rates->Rate as $rte) {
            if ($rte) {
                $svc_code = $rte->ServiceType;
                $service = $this->core->getServiceDescriptionFromCode($svc_code);
                $value = $rte->Amount;
                $packageType = $rte->PackageType;
                //$rates["$service - $packageType"] = $value;
                $simpleRate = array(
                    'desc' => "$service - $packageType",
                    'rate' => $value,
                    'service_code' => $svc_code,
                    'package_type' => $packageType,
                );
                if (!empty($user_func)) {
                    $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $rte, $simpleRate);
                }
                $rates[] = $simpleRate;
            }
        }

        return $rates;
    }

    // Checks the country to see if the request is International
    public function isInternational($country)
    {
        if ($country == '' || $country == 'US' || $country == $this->core->getCountryName('US')) {
            return false;
        }

        return true;
    }

    public function getRate()
    {
        if ($this->service == '') {
            return array('error' => 'You must specify the service parameter when using getRate()');
        }

        // Get all the rates
        $response = $this->getAllRates();

        if (!isset($response->Rates->Rate)) {
            return array('error' => $response);
        }

        if (gettype($response->Rates->Rate) == 'object') {
            if ($response->Rates->Rate->ServiceType == $this->service
                && $response->Rates->Rate->PackageType == $this->packagingType) {
                return $response->Rates->Rate;
            }

            return array('error' => 'Service not available for that service/packagingType');
        }

        foreach ($response->Rates->Rate as $rate) {
            if ($rate->ServiceType == $this->service
                && $rate->PackageType == $this->packagingType) {
                return $rate;
            }
        }

        return array('error' => 'Service not available for that service/packagingType');
    }

    public function addPackageToShipment($packageObj)
    {
        $this->weight = $packageObj->weight;
        $this->length = $packageObj->length;
        $this->width = $packageObj->width;
        $this->height = $packageObj->height;
    }

    public function getSimpleRate()
    {
        return 'method not available';
    }

    public function filterAddons($rate, $addonsToFilter)
    {
        $filteredAddons = array();
        foreach ($rate->AddOns->AddOnV7 as $addon) {
            if (in_array($addon->AddOnType, $addonsToFilter)) {
                $filteredAddons[] = $addon;
            }
        }
        $rate->AddOns->AddOnV7 = $filteredAddons;

        return $rate;
    }

    public function addonsContainProhibited($rate, $addons)
    {
        if (!isset($rate->AddOns->AddOnV7)) {
            return false;
        }

        $prohibitedAddons = array();
        foreach ($rate->AddOns->AddOnV7 as $addon) {
            if (in_array($addon->AddOnType, $addons)) {
                if (!isset($addon->ProhibitedWithAnyOf->AddOnTypeV7)) {
                    continue;
                }
                $prohibitedAddons = array_merge($addon->ProhibitedWithAnyOf->AddOnTypeV7, $prohibitedAddons);
            }
        }

        foreach ($addons as $addon) {
            if (in_array($addon, $prohibitedAddons)) {
                return true;
            }
        }

        return false;
    }

    public function addonsHaveRequirements($rate, $addons)
    {
        if (!isset($rate->AddOns->AddOnV7)) {
            return true;
        }

        foreach ($rate->AddOns->AddOnV7 as $addon) {
            // Ignore addons that don't require anything
            if (!isset($addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7)) {
                continue;
            }

            // ignore addons that we didn't specify
            if (!in_array($addon->AddOnType, $addons)) {
                continue;
            }

            // If we have at least one requirement sastified return true
            foreach ($addon->RequiresAllOf->RequiresOneOf->AddOnTypeV7 as $a) {
                if (in_array($a, $addons)) {
                    return true;
                }
            }

            // We didn't find at least one required addon
            return false;
        }

        return true;
    }

    public function isAddonsCompatible($rate, $addons)
    {
        if ($this->addonsContainProhibited($rate, $addons)) {
            return false;
        }

        if (!$this->addonsHaveRequirements($rate, $addons)) {
            return false;
        }

        return true;
    }
}
