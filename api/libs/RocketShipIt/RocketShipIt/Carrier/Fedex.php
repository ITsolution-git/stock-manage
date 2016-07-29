<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core FedEx Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml for FedEx
 */
class Fedex extends \RocketShipIt\Carrier\Base
{
    public $paramSynonyms = array(
        'customsOriginCountry' => 'countryOfManufacture',
        'customsHsTariff' => 'harmonizedCode',
        'customsWeightUnit' => 'weightUnit',
        'customsLineAmount' => 'customsValue',
    );

    public $paramValueSynonyms = array(
        'LBS' => 'LB',
        'KGS' => 'KG',
    );

    public function __construct()
    {
        parent::__construct();
        // Grab the license, username, password for defaults
        $this->key = $this->config->getDefault('fedex', 'key');
        $this->password = $this->config->getDefault('fedex', 'password');
        $this->accountNumber = $this->config->getDefault('fedex', 'accountNumber');
        $this->meterNumber = $this->config->getDefault('fedex', 'meterNumber');

        $this->xmlObject = new \RocketShipIt\Helper\XmlBuilder(true);
        $this->testingUrl = 'https://wsbeta.fedex.com/xml';
        $this->productionUrl = 'https://ws.fedex.com/xml';
        $this->request = new Request();
    }

    // Create the auth xml that is used in every request
    public function access()
    {
        $xml = $this->xmlObject;
        $xml->push('ns:WebAuthenticationDetail');
        $xml->push('ns:UserCredential');
        $xml->element('ns:Key', $this->key);
        $xml->element('ns:Password', $this->password);
        $xml->pop(); // end UserCredential
        $xml->pop(); // end WebAuthenticationDetail
        $xml->push('ns:ClientDetail');
        $xml->element('ns:AccountNumber', $this->accountNumber);
        $xml->element('ns:MeterNumber', $this->meterNumber);
        $xml->pop(); // end ClientDetail

        $this->xmlObject = $xml;

        $this->accessRequest = true;

        return $this->xmlObject->getXml(); // returns xmlstring, but probably not used
    }

    public function request($xml)
    {
        if ($this->mockXmlResponse != '') {
            $this->xmlResponse = $this->getMockResponse();

            return $this->xmlResponse;
        }

        // This function is the only function that actually transmits and receives data
        // from FedEx. All classes use this to send XML to FedEx servers.
        $request = $this->request;
        $this->xmlSent = $xml; // Store the xmlSent for debugging
        $this->xmlResponse = '';
        $request->url = $this->getUrl().'/xml';
        $request->requestTimeout = $this->requestTimeout;
        $request->payload = $xml;
        $request->post();
        if ($request->getError()) {
            $error = $request->getError();
            $xml = "<error>$error</error>";
            $this->xmlResponse = $xml;

            return array($xml);
        }

        // Store curl response for debugging
        $this->curlReturned = $request->getResponse();

        return $this->returnResponseOrDownResponse($request);
    }

    public function getServiceDescriptionFromCode($code)
    {
        $serviceDescriptionMap = array(
            'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'FedEx Europe First International Priority®',
            'FEDEX_1_DAY_FREIGHT' => 'FedEx 1Day® Freight',
            'FEDEX_2_DAY' => 'FedEx 2Day®',
            'FEDEX_2_DAY_AM' => 'FedEx 2Day® A.M.',
            'FEDEX_2_DAY_FREIGHT' => 'FedEx 2Day® Freight',
            'FEDEX_3_DAY_FREIGHT' => 'FedEx 3Day® Freight',
            'FEDEX_EXPRESS_SAVER' => 'FedEx Express Saver®',
            'FEDEX_FIRST_FREIGHT' => 'FedEx First® Freight',
            'FEDEX_FREIGHT_ECONOMY' => 'FedEx Freight® Economy',
            'FEDEX_FREIGHT_PRIORITY' => 'FedEx Freight® Priority',
            'FEDEX_GROUND' => 'FedEx Ground®',
            'FIRST_OVERNIGHT' => 'FedEx First Overnight®',
            'GROUND_HOME_DELIVERY' => 'FedEx Home Delivery®',
            'INTERNATIONAL_ECONOMY' => 'FedEx International Economy®',
            'INTERNATIONAL_ECONOMY_FREIGHT' => 'FedEx International Economy® Freight',
            'INTERNATIONAL_FIRST' => 'FedEx International First®',
            'INTERNATIONAL_PRIORITY' => 'FedEx International Priority®',
            'INTERNATIONAL_PRIORITY_FREIGHT' => 'FedEx International Priority® Freight',
            'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight®',
            'SMART_POST' => 'FedEx SmartPost®',
            'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight®',
        );

        if (isset($serviceDescriptionMap[$code])) {
            return $serviceDescriptionMap[$code];
        }

        return $code;
    }

    // Returns if a country code accepts electronic trade documents,
    // array('inbound' => true/false, 'outbound' => true/false)
    public function acceptsEtd($countryCode)
    {
        return $this->countriesAcceptingEtd[$countryCode];
    }

    public $countriesAcceptingEtd = array(
        'AF' => array('inbound' => true, 'outbound' => true),
        'AL' => array('inbound' => true, 'outbound' => false),
        'AW' => array('inbound' => false, 'outbound' => true),
        'AU' => array('inbound' => true, 'outbound' => true),
        'AT' => array('inbound' => true, 'outbound' => true),
        'BS' => array('inbound' => false, 'outbound' => true),
        'BH' => array('inbound' => true, 'outbound' => true),
        'BB' => array('inbound' => true, 'outbound' => true),
        'BD' => array('inbound' => false, 'outbound' => true),
        'BE' => array('inbound' => true, 'outbound' => true),
        'BM' => array('inbound' => true, 'outbound' => true),
        'VG' => array('inbound' => false, 'outbound' => true),
        'CA' => array('inbound' => true, 'outbound' => true),
        'KY' => array('inbound' => false, 'outbound' => true),
        'GB' => array('inbound' => true, 'outbound' => true),
        'CN' => array('inbound' => true, 'outbound' => true),
        'HR' => array('inbound' => true, 'outbound' => false),
        'CY' => array('inbound' => true, 'outbound' => false),
        'CZ' => array('inbound' => true, 'outbound' => true),
        'DK' => array('inbound' => true, 'outbound' => true),
        'GB' => array('inbound' => true, 'outbound' => true),
        'EE' => array('inbound' => true, 'outbound' => false),
        'FI' => array('inbound' => true, 'outbound' => true),
        'FR' => array('inbound' => false, 'outbound' => false),
        'DE' => array('inbound' => true, 'outbound' => false),
        'GU' => array('inbound' => true, 'outbound' => false),
        'GT' => array('inbound' => false, 'outbound' => true),
        'HK' => array('inbound' => true, 'outbound' => true),
        'HU' => array('inbound' => true, 'outbound' => true),
        'IS' => array('inbound' => true, 'outbound' => false),
        'IN' => array('inbound' => true, 'outbound' => false),
        'IE' => array('inbound' => true, 'outbound' => true),
        'IL' => array('inbound' => true, 'outbound' => false),
        'IT' => array('inbound' => true, 'outbound' => false),
        'JP' => array('inbound' => true, 'outbound' => true),
        'JO' => array('inbound' => false, 'outbound' => true),
        'KE' => array('inbound' => false, 'outbound' => true),
        'KR' => array('inbound' => true, 'outbound' => true),
        'KW' => array('inbound' => false, 'outbound' => true),
        'LV' => array('inbound' => true, 'outbound' => false),
        'LI' => array('inbound' => true, 'outbound' => false),
        'LT' => array('inbound' => true, 'outbound' => false),
        'LU' => array('inbound' => true, 'outbound' => true),
        'MO' => array('inbound' => true, 'outbound' => true),
        'MY' => array('inbound' => true, 'outbound' => true),
        'MT' => array('inbound' => false, 'outbound' => true),
        'MH' => array('inbound' => true, 'outbound' => false),
        'MX' => array('inbound' => true, 'outbound' => true),
        'FM' => array('inbound' => true, 'outbound' => false),
        'MC' => array('inbound' => true, 'outbound' => false),
        'NL' => array('inbound' => true, 'outbound' => true),
        'NZ' => array('inbound' => true, 'outbound' => true),
        'GB' => array('inbound' => true, 'outbound' => true),
        'MP' => array('inbound' => true, 'outbound' => false),
        'NO' => array('inbound' => true, 'outbound' => true),
        'OM' => array('inbound' => false, 'outbound' => true),
        'PS' => array('inbound' => true, 'outbound' => false),
        'PW' => array('inbound' => true, 'outbound' => false),
        'PH' => array('inbound' => true, 'outbound' => true),
        'PL' => array('inbound' => true, 'outbound' => false),
        'PT' => array('inbound' => true, 'outbound' => false),
        'PR' => array('inbound' => true, 'outbound' => true),
        'SM' => array('inbound' => true, 'outbound' => false),
        'SA' => array('inbound' => false, 'outbound' => true),
        'GB' => array('inbound' => true, 'outbound' => true),
        'SG' => array('inbound' => true, 'outbound' => true),
        'SK' => array('inbound' => true, 'outbound' => true),
        'SI' => array('inbound' => true, 'outbound' => false),
        'ZA' => array('inbound' => true, 'outbound' => true),
        'ES' => array('inbound' => true, 'outbound' => false),
        'AN' => array('inbound' => false, 'outbound' => true),
        'SE' => array('inbound' => true, 'outbound' => true),
        'CH' => array('inbound' => true, 'outbound' => false),
        'TH' => array('inbound' => true, 'outbound' => true),
        'TW' => array('inbound' => true, 'outbound' => false),
        'TC' => array('inbound' => false, 'outbound' => true),
        'AE' => array('inbound' => false, 'outbound' => true),
        'US' => array('inbound' => true, 'outbound' => true),
        'VI' => array('inbound' => false, 'outbound' => true),
        'IT' => array('inbound' => true, 'outbound' => false),
        'GB' => array('inbound' => true, 'outbound' => true),
    );
}
