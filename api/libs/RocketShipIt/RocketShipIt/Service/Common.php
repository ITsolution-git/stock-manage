<?php

namespace RocketShipIt\Service;
use \RocketShipIt\Helper\XmlParser;

class Common
{

    public $status;
    public $responseArray;
    var $OKparams;
    var $carrier; // Set variable for carrier
    var $inherited;
    var $config;

    function __construct($carrier, $options=array())
    {
        if (isset($options['config'])) {
            $this->config = $options['config'];
        } else {
            $this->config = new \RocketShipIt\Config;
        }
        $this->config->carrier = $carrier;

        $this->OKparams = $this->config->getOKparams();
        $this->carrier = strtoupper($carrier);

        $className = '\RocketShipIt\Carrier\\'. $carrier;
        $this->core = new $className();

        foreach ($this->OKparams as $param) {
            $this->setParameter($param, '');
        }

        $this->status = 'unknown';
        $this->responseArray = array();
        $this->xmlParser = new xmlParser;
    }

    // In order to allow users to override defaults or specify obsecure UPS
    // data, this function allows you to set any of the variables that this class uses
    function setParameter($param, $value)
    {
        $value = $this->config->getParameter($param, $value, $this->config->carrier);
        $this->{$param} = $value;
        $this->core->{$param} = $value;
    }

    public function arrayFromXml($xml)
    {
        $xmlParser = $this->xmlParser;
        $xmlParser->load($xml);

        return $xmlParser->getData();
    }

    // Array helper to get values by dot notation
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;
        
        if (isset($array[$key])) return $array[$key];
        
        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) ||
                ! array_key_exists($segment, $array)) {
                return $default;
            }
        
            $array = $array[$segment];
        }
      
        return $array;
    }
}
