<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core CanadaPost Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Canada extends \RocketShipIt\Carrier\Base
{
    public $request;

    public function __construct($config = null)
    {
        parent::__construct();
        if ($config) {
            $this->config = $config;
        }

        $this->testingUrl = 'https://ct.soa-gw.canadapost.ca';
        $this->productionUrl = 'https://soa-gw.canadapost.ca';
    }

    public function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = new Request();
        }

        return $this->request;
    }

    public function request($type, $xml, $header = false, $method = 'GET')
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

        $this->xmlSent = $xml;

        $request = $this->getRequest();
        $request->url = $this->getUrl().$type;
        $request->username = $this->username;
        $request->password = $this->password;
        $request->requestTimeout = $this->requestTimeout;
        if ($header) {
            $request->header = $header;
        }
        $request->payload = $xml;
        if ($method == 'DELETE') {
            $request->delete();
        } else {
            $request->post();
        }
        $response = $request->getResponse();

        $response = strstr($response, '<?'); // Separate the html header and the actual XML because we turned CURLOPT_HEADER to 1
        $this->xmlResponse = $response;

        return $response;
    }
}
