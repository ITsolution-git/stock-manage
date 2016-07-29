<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core Ontrac Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Ontrac extends \RocketShipIt\Carrier\Base
{
    public $request;

    public function __construct($config = null)
    {
        parent::__construct();
        if ($config) {
            $this->config = $config;
        }

        $this->testingUrl = 'https://www.shipontrac.net/OnTracTestWebServices/OnTracServices.svc';
        $this->productionUrl = 'https://www.shipontrac.net/OnTracWebServices/OnTracServices.svc';
    }

    public function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = new Request();
        }

        return $this->request;
    }

    public function request($type, $method = 'get', $params = array(), $xml = null)
    {
        if ($this->mockXmlResponse != '') {
            if (is_array($this->mockXmlResponse)) {
                $mockXml = array_shift($this->mockXmlResponse);
            } else {
                $mockXml = $this->mockXmlResponse;
            }
            $this->xmlResponse = $mockXml;

            return $mockXml;
        }

        $queryString = '';
        if (count($params) > 0) {
            $queryString = '?'.http_build_query($params);
        }

        $this->xmlSent = $xml;

        $request = $this->getRequest();
        $request->url = $this->getUrl().$type.$queryString;
        $this->fullUrl = $request->url;
        $request->requestTimeout = $this->requestTimeout;
        if ($method == 'post') {
            $request->payload = $xml;
            $request->post();
        } else {
            $request->get();
        }
        $response = $request->getResponse();
        $this->curlReturned = $response;
        $this->xmlResponse = $response;

        return $response;
    }

    public function getServiceDescriptionFromCode($code)
    {
        $serviceDescriptionMap = array(
            'C' => 'OnTrac Ground',
            'S' => 'Sunrise',
            'G' => 'Gold',
            'H' => 'Palletized Freight',
        );

        if (isset($serviceDescriptionMap[$code])) {
            return $serviceDescriptionMap[$code];
        }

        return $code;
    }
}
