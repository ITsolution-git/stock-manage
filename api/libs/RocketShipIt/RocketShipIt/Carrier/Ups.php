<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core UPS Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Ups extends \RocketShipIt\Carrier\Base
{
    public $paramSynonyms = array(
        'customsDescription'   => 'invoiceLineDescription',
        'customsQuantity'      => 'invoiceLineNumber',
        'customsValue'         => 'invoiceLineValue',
        'customsPartNumber'    => 'invoiceLinePartNumber',
        'customsOriginCountry' => 'invoiceLineOriginCountryCode',
    );

    public function __construct()
    {
        parent::__construct();

        $this->testingUrl = 'https://wwwcie.ups.com';
        $this->productionUrl = 'https://onlinetools.ups.com';

        // Create a new xmlObject to be used by access and other classes
        // This object will be used all the way through, until the final xmlObject
        // is converted to a string just before sending to UPS
        $this->xmlObject = new \RocketShipIt\Helper\XmlBuilder(false);
        //$this->access();

        $this->request = new Request();
    }

    // Build the access XML to be used in EVERY request to UPS
    public function access()
    {
        //$xml = $this->xmlObject;
        $xml = new \RocketShipIt\Helper\XmlBuilder(false);
        $xml->push('AccessRequest', array('xml:lang' => 'en-US'));
        $xml->element('AccessLicenseNumber', $this->license);
        $xml->element('UserId', $this->username);
        $xml->element('Password', $this->password);
        $xml->pop();

        $this->xmlObject = $xml;

        return $this->xmlObject->getXml();
    }

    // This function is the only function that actually transmits and receives data
    // from UPS. All classes use this to send XML to UPS servers.
    public function request($type, $xml)
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
        $request = $this->request;
        $request->url = $this->getUrl().'/ups.app/xml/'.$type;
        $request->requestTimeout = $this->requestTimeout;
        $request->payload = $xml;
        $request->post();

        $curlReturned = $request->getResponse();
        if ($request->getError()) {
            $error = $request->getError();

            if (strpos($error, 'SSL') !== false) {
                $error .= ' Possible fix: https://docs.rocketship.it/php/1-0/ups-tls-1-2-ssl.html';
            }
            $xml = "<error>$error</error>";
            $this->xmlResponse = $xml;

            return array($xml);
        }
        $this->curlReturned = $curlReturned;
        // exit ($curlReturned);

        // Find out if the UPS service is down
        if ($request->getStatusCode() != 100 && $request->getStatusCode() != 200) {
            return array('error' => 'The UPS service seems to be down with HTTP/1.1 '.$request->getStatusCode());
        } else {
            $this->xmlResponse = $curlReturned;

            return $this->xmlResponse;
        }
    }

    public function getServiceCodeFromDescription($desc, $country = 'US')
    {
        $desc = trim($desc);
        if (strtoupper($country) == 'CA') {
            $canadaMap = array(
                '01' => 'UPS Express',
                '02' => 'UPS Worldwide Expedited',
                '07' => 'UPS Express',
                '11' => 'UPS Standard',
                '12' => 'UPS 3 Day Select',
                '13' => 'UPS Saver',
                '14' => 'UPS Express Early AM',
            );
            $canadaMap = array_flip($usMap);

            if (isset($canadaMap[$desc])) {
                return $canadaMap[$desc];
            }

            return 'Unknown service code';
        } 

        $usMap = array(
            'UPS Next Day Air'                    => '01',
            'UPS 2nd Day Air'                     => '02',
            'UPS 2nd Day Air (Saturday Delivery)' => '02',
            'UPS Ground'                          => '03',
            'UPS Worldwide Express'               => '07',
            'UPS Worldwide Expedited'             => '08',
            'UPS Standard'                        => '11',
            'UPS 3 Day Select'                    => '12',
            'UPS Next Day Air Saver'              => '13',
            'UPS Next Day Air Early'              => '14',
            'UPS Worldwide Express Plus'          => '54',
            'UPS 2nd Day Air A.M.'                => '59',
            'UPS Worldwide Saver'                 => '65',
            'UPS Today Standard'                  => '82',
            'UPS Today Dedicated'                 => '83',
            'UPS Today Intercity'                 => '84',
            'UPS Today Express'                   => '85',
            'UPS Today Express Saver'             => '86',
        );

        if (isset($usMap[$desc])) {
            return $usMap[$desc];
        }

        return 'Unknown service code';
    }

    public function getServiceDescriptionFromCode($code, $country = 'US')
    {
        if (strtoupper($country) == 'CA') {
            switch ($code) {
                case '01':
                    return 'UPS Express';
                case '02':
                    return 'UPS Worldwide Expedited';
                case '07':
                    return 'UPS Express';
                case '11':
                    return 'UPS Standard';
                case '12':
                    return 'UPS 3 Day Select';
                case '13':
                    return 'UPS Saver';
                case '14':
                    return 'UPS Express Early AM';
                default:
                    return 'Unknown service code';
            }
        } else {
            switch ($code) {
                case '01':
                    return 'UPS Next Day Air';
                case '02':
                    return 'UPS 2nd Day Air';
                case '03':
                    return 'UPS Ground';
                case '07':
                    return 'UPS Worldwide Express';
                case '08':
                    return 'UPS Worldwide Expedited';
                case '11':
                    return 'UPS Standard';
                case '12':
                    return 'UPS 3 Day Select';
                case '13':
                    return 'UPS Next Day Air Saver';
                case '14':
                    return 'UPS Next Day Air Early AM';
                case '54':
                    return 'UPS Worldwide Express Plus';
                case '59':
                    return 'UPS Second Day Air AM';
                case '65':
                    return 'UPS Worldwide Saver';
                case '82':
                    return 'UPS Today Standard';
                case '83':
                    return 'UPS Today Dedicated';
                case '84':
                    return 'UPS Today Intercity';
                case '85':
                    return 'UPS Today Express';
                case '86':
                    return 'UPS Today Express Saver';
                default:
                    return 'Unknown service code';
            }
        }
    }

    public function mapMailInnovationServiceCodes($oldCode)
    {
        $codeMap = array();
        $codeMap['70'] = 'M2';
        $codeMap['71'] = 'M3';
        $codeMap['72'] = 'M4';
        $codeMap['73'] = 'M5';
        $codeMap['74'] = 'M6';
        if (!isset($codeMap[$oldCode])) {
            return $oldCode;
        }

        return $codeMap[$oldCode];
    }

    public $paramValueSynonyms = array(
        'LB' => 'LBS',
        'KG' => 'KGS',
    );

    public function getShipmentDCISType($signatureCode)
    {
        $signatureTypes = array(
            'INDIRECT' => '',
            'DIRECT' => '1',
            'ADULT' => '2',
            'NO_SIGNATURE_REQUIRED' => '',
        );

        if (array_key_exists($signatureCode, $signatureTypes)) {
            return $signatureTypes[$signatureCode];
        }

        return $signatureCode;
    }

    public function getPackageDCISType($type)
    {
        $signatureTypes = array(
            'INDIRECT' => '1',
            'DIRECT' => '2',
            'ADULT' => '3',
            'USPS' => '4',
            'NO_SIGNATURE_REQUIRED' => '',
        );

        if (array_key_exists($type, $signatureTypes)) {
            return $signatureTypes[$type];
        }

        return $type;
    }
}
