<?php

namespace RocketShipIt;

class Config
{
    public $defaults;
    public $customEnvVar = 'RS_CUSTOM_CONFIG_PATH';
    public $carrier;
    public $defaultConfigFileName = 'config.php';

    public function __construct()
    {
        $this->defaults = array();
        $this->loadConfig();

        $timezone = $this->getDefault('generic', 'timezone');
        if ($timezone != '') {
            date_default_timezone_set($timezone);
        }
    }

    private function isCustomConfig()
    {
        return getenv($this->customEnvVar);
    }

    private function loadConfig()
    {
        if ($this->isCustomConfig()) {
            $this->loadConfigFromEnvVar($this->customEnvVar);
        } else {
            $this->loadConfigFile(dirname(__FILE__).'/../'.$this->defaultConfigFileName);
        }
    }

    public function loadConfigFromEnvVar($envVar)
    {
        $this->loadConfigFile(getenv($envVar).'/'.$this->defaultConfigFileName);
    }

    public function loadConfigFile($filename)
    {
        if (!file_exists($filename)) {
            return;
        }

        $this->defaults = include $filename;
    }

    public function processCarrier()
    {
        // Force fedex, FedEx, FEDEX to all read the same
        $this->carrier = strtoupper($this->carrier);
    }

    public function getParametersFile()
    {
        return file_get_contents(dirname(__FILE__).'/Resources/parameters.json');
    }

    public function loadParameters()
    {
        return json_decode($this->getParametersFile(), true);
    }

    public function getGenericParameters($paramaters)
    {
        // Generic parameters that are accessible in each class regardless of carrier
        return $paramaters['generic'];
    }

    /**
     * Ensures that only settable paramaters are allowed.
     *
     * This function aids the setPramater() function in that it only
     * allows known paramaters to be set.  This helps to avoid typos when
     * setting parameters.
     *
     * @param string $carrier name of carrier i.e. ups
     *
     * @return array $okparams array of all available params for given carrier
     */
    public function getOKparams()
    {
        $this->processCarrier($this->carrier);

        $parameters = $this->loadParameters();

        // Generic parameters that are accessible in each class regardless of carrier
        $generic = $this->getGenericParameters($parameters);

        if (!$this->validateCarrier($this->carrier)) {
            return $generic;
        }

        return array_merge($generic, $parameters[$this->carrier]);
    }

    /**
     * Gets defaults.
     *
     * This function will grab defaults from config.php (or default config filename)
     */
    public function getParameter($param, $value)
    {
        $this->processCarrier($this->carrier);

        if ($value === '') { // get the default, if set
            $value = $this->getDefault('generic', $param);
            if ($value === '') { // not in the generics? look in the specific carrier params
                $value = $this->getDefault($this->carrier, $param);
            }
        }

        return $value;
    }

    public function limitParameterSize($param, $value)
    {
        $this->processCarrier($this->carrier);

        $sizeLimits = array();
        $sizeLimits['UPS']['shipper'] = 35;

        if (isset($sizeLimits[$this->carrier][$param])) {
            return substr($value, 0, $sizeLimits[$this->carrier][$param]);
        }

        return $value;
    }

    /**
     * Validates carrier name.
     *
     * This function will return true when given a proper
     * carier name.
     */
    public function validateCarrier()
    {
        $this->processCarrier($this->carrier);

        switch (strtoupper($this->carrier)) {
            case 'UPS':
                return true;
            case 'FEDEX':
                return true;
            case 'USPS':
                return true;
            case 'STAMPS':
                return true;
            case 'DHL':
                return true;
            case 'CANADA':
                return true;
            case 'PUROLATOR':
                return true;
            case 'ONTRAC':
                return true;
            case 'ROYALMAIL':
                return true;
        }
    }

    public function getDefault($section, $param)
    {
        $section = strtolower($section);

        if (!isset($this->defaults[$section])) {
            return '';
        }
        if (!isset($this->defaults[$section][$param])) {
            return '';
        }

        return $this->defaults[$section][$param];
    }

    public function setDefault($section, $param, $value)
    {
        if ($param == 'timezone') {
            date_default_timezone_set($value);
        }

        $this->defaults[$section][$param] = $value;
    }
}
