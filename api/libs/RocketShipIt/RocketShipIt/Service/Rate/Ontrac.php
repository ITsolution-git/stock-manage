<?php

namespace RocketShipIt\Service\Rate;

use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\XmlParser;

class Ontrac extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    var $packageCount;
    
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
        $this->packageCount = 0;
    }

    function getAllRates()
    {
        // when service is not set will fetch all service rates
        $this->service = '';

        $params = $this->buildParams();
        $response = $this->core->request($this->buildPath(), 'get', $params);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function getSimpleRates($user_func=null)
    {
        $response = $this->getAllRates();
        if (!isset($response['OnTracRateResponse']['Shipments']['Shipment']['Rates']['Rate'])) {
            return $response;
        }

        $simpleRates = array();
        foreach ($response['OnTracRateResponse']['Shipments']['Shipment']['Rates']['Rate'] as $rate) {
            $a = array();
            $a['desc'] = $this->core->getServiceDescriptionFromCode($rate['Service']);
            $a['rate'] = $rate['TotalCharge'];
            $a['service_code'] = $rate['Service'];
            $simpleRates[] = $a;
        }

        return $simpleRates;
    }

    public function getRate()
    {
        $params = $this->buildParams();
        $response = $this->core->request($this->buildPath(), 'get', $params);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function addPackageToShipment($package)
    {
        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = array();
        }
        $this->packageCount = $this->packageCount + 1;

        $p = array();
        $p['UID'] = 'ID'. $this->packageCount;
        $p['PUZip'] = $this->shipCode;
        $p['DelZip'] = $this->toCode;
        if ($this->residential != '') {
            $p['Residential'] = 'true';
        } else {
            $p['Residential'] = 'false';
        }
        $p['COD'] = '0.00';
        $p['SaturdayDel'] = 'false';
        $p['Declared'] = '0.00';
        $p['Weight'] = $package->weight;
        $p['DIM'] = sprintf('%sX%sX%s', $package->length, $package->width, $package->height);
        $p['Service'] = $package->service;
        $p['Letter'] = '0';

        $this->core->packagesObject[] = $p;
    }

    public function getSimpleRate()
    {
        return 'method not available';
    }

    function buildPath()
    {
        return sprintf('/V1/%s/rates', $this->accountNumber);
    }

    function buildPackages()
    {
        $packages = array();
        foreach ($this->core->packagesObject as $package) {
            $packages[] = $this->buildPackage($package);
        }

        return implode(',', $packages);
    }

    function buildPackage($package)
    {
        return sprintf('%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s',
            $package['UID'],
            $package['PUZip'],
            $package['DelZip'],
            $package['Residential'],
            $package['COD'],
            $package['SaturdayDel'],
            $package['Declared'],
            $package['Weight'],
            $package['DIM'],
            $package['Service'],
            $package['Letter']
        );
    }

    function buildParams()
    {
        return array(
            'pw' => $this->password,
            'packages' => $this->buildPackages(),
        );
    }
}
