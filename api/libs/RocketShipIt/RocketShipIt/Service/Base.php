<?php

namespace RocketShipIt\Service;

class Base
{
    public $status;
    public $inherited;
    var $carrier; // Set variable for carrier
    var $OKparams;
    var $config;

    public function __construct($carrier, $service, $options=array())
    {
        // Set custom config
        if (isset($options['config'])) {
            $this->config = $options['config'];
        } else {
            // Use default config.php file
            $this->config = new \RocketShipIt\Config;
        }
        $this->config->carrier = $carrier;
        $this->config->validateCarrier();
        $carrier = $this->config->carrier;
        $uCaseCarrier = ucwords(strtolower($carrier));
        $this->carrier = $carrier;
        $this->carrierService = $service;
        $this->OKparams = $this->config->getOKparams($carrier);

        $this->inherited = new \stdClass();
        $this->inherited->core = new \stdClass();
        if (!$this->config->validateCarrier($carrier)) {
            return;
        }
        if ($service) {
            //$uCaseCarrier = ucwords(strtolower($carrier));
            $className = '\RocketShipIt\Service\\'. $service. '\\'. $uCaseCarrier;
            $this->inherited = new $className();
        } else {
            $this->inherited = new \stdClass();
            $className = '\RocketShipIt\Carrier\\'. $uCaseCarrier;
            $this->inherited->core = new $className();
        }
        
        foreach ($this->OKparams as $param) {
            $this->setParameter($param, '');
        }

        $this->status = 'unknown';
    }

    public function setParameter($param, $value)
    {
        $value = $this->config->getParameter($param, $value, $this->carrier);
        $value = $this->config->limitParameterSize($param, $value, $this->carrier);
        if (isset($this->inherited->paramValueSynonyms[$value])) {
            $value = $this->inherited->paramValueSynonyms[$value];
        }
        if (is_string($value) && isset($this->inherited->core->paramValueSynonyms[$value])) {
            $value = $this->inherited->core->paramValueSynonyms[$value];
        }
        $this->{$param} = $value;
        $this->parameters[$param] = $value;
        $this->inherited->{$param} = $value;
        $this->inherited->core->{$param} = $value;
        $this->inherited->core->parameters[$param] = $value;
        if (isset($this->inherited->paramSynonyms[$param])) {
            $this->setParameter($this->inherited->paramSynonyms[$param], $value);
        }
        if (isset($this->inherited->core->paramSynonyms[$param])) {
            $this->setParameter($this->inherited->core->paramSynonyms[$param], $value);
        }
    }

    public function loadJsonParameters($parameters)
    {
        $parameters = json_decode($parameters, true);
        foreach ($this->OKparams as $param) {
            if (isset($parameters[$param])) {
                $value = $parameters[$param];
            } else {
                $value = '';
            }
            $this->{$param} = $value;
            $this->parameters[$param] = $value;
            $this->inherited->{$param} = $value;
            $this->inherited->core->{$param} = $value;
            $this->inherited->core->parameters[$param] = $value;
        }
        $objectLevelParams = $parameters;
        unset($objectLevelParams['packages']);
        unset($objectLevelParams['customs']);

        if (!isset($parameters['packages'])) {
            $parameters['packages'] = array();
        }

        foreach ($parameters['packages'] as $packageData) {
            $package = new \RocketShipIt\Package($this->carrier);
            //foreach ($packageData as $param => $value) {
            foreach (array_merge($objectLevelParams, $packageData) as $param => $value) {
                $package->{$param} = $value;
            }
            $this->addPackageToShipment($package);
        }

        if (!isset($parameters['customs'])) {
            $parameters['customs'] = array();
        }

        foreach ($parameters['customs'] as $customData) {
            $customs = new \RocketShipIt\Customs($this->carrier);
            foreach (array_merge($objectLevelParams, $customData) as $param => $value) {
                $customs->{$param} = $value;
            }
            $this->addCustomsLineToShipment($customs);
        }
    }

    public function debug()
    {
        return $this->inherited->core->debug();
    }

    public function getXmlSent()
    {
        return $this->inherited->core->xmlSent;
    }

    function getXmlPrevSent()
    {
        return $this->inherited->core->xmlPrevSent;
    }

    public function getXmlResponse()
    {
        return $this->inherited->core->xmlResponse;
    }

    /**
     * toFile converts base64 to file with filename given
     *
     * @param string $base64
     * @param string $filename
     * @return bool
     */
    public function toFile($base64, $filename)
    {
        $fh = fopen($filename, 'w');
        fwrite($fh, base64_decode($base64));
        fclose($fh);
        return true;
    }

    /**
     * getStatus
     *
     * @return void
     */
    public function getStatus()
    {
        return $this->inherited->status;
    }

    public function invalidCarrierResponse()
    {
        $outArr = array();
        $outArr['result'] = 'fail';
        $outArr['reason'] = 'invalid carrier'; 
        return $outArr;
    }
}
