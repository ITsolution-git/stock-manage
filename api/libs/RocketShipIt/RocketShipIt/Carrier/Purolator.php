<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

class Purolator extends \RocketShipIt\Carrier\Base
{
    public $request;

    public function __construct()
    {
        parent::__construct();

        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => 0
        );

        $options['login'] = $this->config->getDefault('purolator', 'username');
        $options['password'] = $this->config->getDefault('purolator', 'password');

        $wsdl = __DIR__.'/../Resources/schemas/purolator/EstimatingService.wsdl';
        $this->soapClient = new \RocketShipIt\Helper\SoapClient($wsdl, $options);
        $this->soapHeader = new \SoapHeader('http://purolator.com/pws/datatypes/v1',
            'RequestContext',
                array(
                    'Version' => '1.3',
                    'Language' => 'en',
                    'GroupID' => 'xxx',
                    'RequestReference' => 'Rating 123',
                )
            );
    }

    public function setValidateOnlyOnClient()
    {
        $this->soapClient->validate_only = true;
    }

    public function request($action, $request)
    {
        //Define the SOAP Envelope Headers
        $headers = array();
        $headers[] = $this->soapHeader;

        $this->soapClient->__setSoapHeaders($headers);

        // Allows for mocking of soap requests
        if ($this->mockXmlResponse != '') {
            $this->soapClient->mockXmlResponse = $this->mockXmlResponse;
        }

        if ($this->validateOnly != '') {
            $this->setValidateOnlyOnClient();
        }

        $response = $this->soapClient->$action($request);

        $this->xmlResponse = $this->soapClient->__getLastResponse();
        $this->xmlSent = $this->soapClient->__getLastRequest();

        return $response;
    }
}
