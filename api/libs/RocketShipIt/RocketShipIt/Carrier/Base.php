<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Helper\General;

class Base
{
    public $xmlSent;
    public $requestTimeout;
    public $mockXmlRequest;
    public $mockXmlResponse;
    public $url;
    public $fullUrl;
    public $xmlPrevSent;
    public $curlReturned;
    public $parameters;
    public $testingUrl;
    public $productionUrl;
    public $helper;
    public $config;
    public $debugMode = 1;

    // eventually migrate to this, remove xmlSent/xmlResponse
    public $transactions = array();

    public function __construct()
    {
        $this->xmlPrevSent = '';
        $this->xmlSent = '';
        $this->curlReturned = '';
        $this->fullUrl = '';
        $this->parameters = array(
            'packages' => array(),
            'customs' => array(),
        );

        // Default 60 second request timeout
        $this->requestTimeout = 60;
        $this->mockXmlRequest = '';
        $this->mockXmlResponse = '';
        $this->validateOnly = '';
        $this->helper = new General();
        $this->config = new \RocketShipIt\Config();
        $this->debugMode = $this->config->getDefault('generic', 'debugMode');
    }

    public function isCliMode()
    {
        if (php_sapi_name() == 'cli') {
            return true;
        }

        return false;
    }

    public function getMockResponse()
    {
        if ($this->mockXmlResponse != '') {
            if (is_array($this->mockXmlResponse)) {
                $mockXml = array_shift($this->mockXmlResponse);
            } else {
                $mockXml = $this->mockXmlResponse;
            }

            return $mockXml;
        }

        return false;
    }

    public function addfilterParams($filteredParams, $type)
    {
        $filteredParams[$type] = array();
        if (!isset($this->parameters[$type])) {
            return $filteredParams;
        }

        foreach ($this->parameters[$type] as $package) {
            $pack = array();
            foreach ($package as $param => $value) {
                if ($value == '') {
                    continue;
                }
                if ($value == @$this->parameters[$param]) {
                    continue;
                }
                $pack[$param] = $value;
            }
            $filteredParams[$type][] = $pack;
        }

        return $filteredParams;
    }

    public function jsonEncodeParameters()
    {
        $filteredParams = array();
        foreach ($this->parameters as $param => $value) {
            if ($value != '') {
                $filteredParams[$param] = $value;
            }
        }

        $filteredParams = $this->addfilterParams($filteredParams, 'packages');
        $filteredParams = $this->addfilterParams($filteredParams, 'customs');

        return json_encode($filteredParams);
    }

    public function encodeOutputForDisplay($output, $nohtmlentities)
    {
        if ($nohtmlentities) {
            return $output;
        }

        return htmlentities($output);
    }

    // Creates a section in the debug output
    public function debugSection($name, $info, $nohtmlentities = true)
    {
        if ($info == '') {
            return;
        }
        $debugInfo = '';
        $debugInfo .= '--------------------------------------------------'."\n";
        $debugInfo .= $name."\n";
        $debugInfo .= '--------------------------------------------------'."\n";
        $debugInfo .= $this->encodeOutputForDisplay($info, $nohtmlentities);
        $debugInfo .= "\n\n";

        return $debugInfo;
    }

    public function debug()
    {
        $debugInfo = "<pre>\n";
        if (!isset($this->debugMode)) {
            $this->debugMode = 0;
        }

        $topSection = 'Version: '.\RocketShipIt\VERSION."\n"
            ."Debug Mode: $this->debugMode"."\n";
        if (isset($this->request->url)) {
            $topSection .= 'URL: '.$this->request->url;
        }

        $debugInfo .= $this->debugSection(
            'RocketShipIt Debug Information',
            $topSection
        );

        foreach ($this->transactions as $t) {
            if (isset($t['xmlSent'])) {
                if ($this->isCliMode()) {
                    $xmlPrevSent = $this->helper->xmlPrettyPrint($t['xmlSent']);
                } else {
                    $xmlPrevSent = htmlentities($this->helper->xmlPrettyPrint($t['xmlSent']));
                }
                $debugInfo .= $this->debugSection('XML Sent', $xmlPrevSent);
            }
            if (isset($t['xmlResponse'])) {
                if ($this->isCliMode()) {
                    $xmlPrevSent = $this->helper->xmlPrettyPrint($t['xmlResponse']);
                } else {
                    $xmlPrevSent = htmlentities($this->helper->xmlPrettyPrint($t['xmlResponse']));
                }
                $debugInfo .= $this->debugSection('XML Response', $xmlPrevSent);
            }
        }

        // old way of displaying a single set of XML sent/resp/prevResp
        // changing to transactions since some carriers can require 
        // several requests in a single RocketShipIt call.
        if (count($this->transactions) == 0) {
            if (isset($this->xmlPrevSent)) {
                if ($this->isCliMode()) {
                    $xmlPrevSent = $this->helper->xmlPrettyPrint($this->xmlPrevSent);
                } else {
                    $xmlPrevSent = htmlentities($this->helper->xmlPrettyPrint($this->xmlPrevSent));
                }
                $debugInfo .= $this->debugSection('XML Prev Sent', $xmlPrevSent);
            }
            if (isset($this->xmlPrevResponse)) {
                $debugInfo .= $this->debugSection('XML Prev Response', $this->xmlPrevResponse, $this->isCliMode());
            }
            if (isset($this->xmlSent)) {
                $debugInfo .= $this->debugSection('XML Sent', $this->helper->xmlPrettyPrint($this->xmlSent), $this->isCliMode());
            } else {
                $debugInfo .= $this->debugSection('XML Sent', 'xmlSent was not set', $this->isCliMode());
            }
            if (isset($this->xmlResponse) && gettype($this->xmlResponse) == 'string') {
                $debugInfo .= $this->debugSection('XML Response', $this->helper->xmlPrettyPrint($this->xmlResponse), $this->isCliMode());
            } else {
                $debugInfo .= $this->debugSection('XML Response', 'xmlResponse was not set', $this->isCliMode());
            }
        }

        $helper = $this->helper;
        $debugInfo .= $this->debugSection('Set Parameters', $helper->jsonPrettyPrint($this->jsonEncodeParameters()), $this->isCliMode());
        $debugInfo .= $this->debugSection('PHP Information', 'Version: '.phpversion(), $this->isCliMode());
        if (isset($this->curlReturned)) {
            $debugInfo .= $this->debugSection('Raw Response', $this->curlReturned, $this->isCliMode());
        }
        if (isset($this->request)) {
            if (method_exists($this->request, 'getInfo')) {
                $debugInfo .= $this->debugSection('Response Details', $helper->jsonPrettyPrint(json_encode($this->request->getInfo())), $this->isCliMode());
            }
        }
        $debugInfo .= '</pre>';

        return $this->redactPassword($debugInfo);
    }

    public function redactPassword($text)
    {
        if (!isset($this->parameters['password'])) {
            return $text;
        }

        if ($this->parameters['password'] == '') {
            return $text;
        }

        if (substr_count($text, $this->parameters['password']) > 5) {
            return $text;
        }

        return str_replace($this->parameters['password'], '****REDACTED****', $text);
    }

    public function saveResponse($xmlResponse)
    {
        $random = substr(md5(rand()), 0, 7);
        $filename = $random.'.xml';
        $fh = fopen($filename, 'w');
        fwrite($fh, $xmlResponse);
        fclose($fh);

        return true;
    }

    public function getUrl()
    {
        if ($this->debugMode) {
            return $this->testingUrl;
        }

        // Production mode
        return $this->productionUrl;
    }

    public function isServiceDown($statusCode)
    {
        if ($statusCode != 100 && $statusCode != 200) {
            return true;
        }

        return false;
    }

    public function returnResponseOrDownResponse($request)
    {
        if ($this->isServiceDown($request->getStatusCode())) {
            return array('error' => 'The service seems to be down with HTTP/1.1 '. $request->getResponse());
        } else {
            $response = strstr($this->curlReturned, '<?'); // Separate the html header and the actual XML because we turned CURLOPT_HEADER to 1
            $this->xmlResponse = $response;

            return $response;
        }
    }
}
