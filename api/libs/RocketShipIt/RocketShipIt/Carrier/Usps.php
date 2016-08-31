<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Helper\XmlBuilder as XmlBuilder;
use RocketShipIt\Request;

/**
 * Core USPS Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Usps extends \RocketShipIt\Carrier\Base
{
    public $serviceDescriptions = array(
        'US-PM' => 'USPS Priority Mail',
        'US-PMI' => 'USPS Priority Mail International',
        'US-XM' => 'USPS Express Mail',
        'US-EMI' => 'USPS Express Mail International',
        'US-PP' => 'USPS Parcel Post',
        'US-MM' => 'USPS Media Mail',
        'US-FC' => 'USPS First Class Mail',
        'US-FCI' => 'USPS Standard Mail',
        'US-BP' => 'USPS Bound Printed Matter',
        'US-LM' => 'USPS Library Mail',
        'US-PS' => 'USPS Parcel Select',
        'US-CM' => 'USPS Critical Mail'
    );

    public function __construct()
    {
        parent::__construct();

        // Grab the license, username, password for defaults
        $this->userid = $this->config->getDefault('usps', 'userid');
        // USPS testing url doesn't actually respond to dynamic requests
        $this->testingUrl = 'http://Production.ShippingAPIs.com';
        $this->productionUrl = 'http://Production.ShippingAPIs.com';

        // Create a new xmlObject to be used by access and other classes
        // This object will be used all the way through, until the final xmlObject
        // is converted to a string just before sending to USPS
        $this->xmlObject = new xmlBuilder(true);

        $this->request = new Request();
    }

    public function request($type, $xml)
    {
        if ($this->mockXmlResponse != '') {
            $this->xmlResponse = $this->getMockResponse();

            return $this->xmlResponse;
        }

        // This function is the only function that actually transmits and receives data
        // from USPS. All classes use this to send XML to USPS servers.
        $request = $this->request;
        $request->url = $this->getUrl().'/'.$type;
        $this->xmlSent = $xml;
        $output = preg_replace('/[\s+]{2,}/', '', $xml);
        $request->payload = $output;
        $request->setCurlOption(CURLOPT_POST, 1);
        $request->setCurlOption(CURLOPT_TIMEOUT, $this->requestTimeout);
        $request->post();
        $curlReturned = $request->getResponse();

        if ($request->getError()) {
            $error = $request->getError();
            $xml = "<?xml version=\"1.0\"?><error>$error</error>";
            $this->xmlResponse = $xml;

            return array($xml);
        }

        $body = $request->getResponse();
        $this->curlReturned = $curlReturned;

        if ($request->getStatusCode() != 100 && $request->getStatusCode() != 200 && $request->getStatusCode() != 405) {
            return false;
        } else {
            // Add xml doctype if missing to make later parsing easier
            $xmldeclaration = strpos($body, '<?xml');
            if ($xmldeclaration === false) {
                $body = '<?xml version="1.0"?>'.$body;
            }
            $this->xmlResponse = $body;

            return $body;
        }
    }

    public function getCountryName($countryCode)
    {
        $converter = new \RocketShipIt\Helper\CountryConverter();

        return $converter->getCountryName($countryCode);
    }

    public function getServiceDescriptionFromCode($code)
    {
        if (!isset($this->serviceDescriptions[$code])) {
            return 'Unknown service code';
        }

        return $this->serviceDescriptions[$code];
    }

    public $paramSynonyms = array(
        'weight' => 'weightPounds',
    );
}
