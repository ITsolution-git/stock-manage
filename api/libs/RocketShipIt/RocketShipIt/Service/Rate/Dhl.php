<?php

namespace RocketShipIt\Service\Rate;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

/**
* Main Rate class for producing rates for various packages/shipments
*
* This class is a wrapper for use with all carriers to produce rates
* Valid carriers are: UPS, USPS, Stamps.com, DHL, and FedEx.
*/
class Dhl extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    var $packageCount;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
        $this->shipmentWeight = 0;
    }

    function addPackageToShipment($package)
    {
        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = new xmlBuilder(true);
        }
        $this->packageCount = $this->packageCount + 1;
        $xml = $this->core->packagesObject;
        $xml->push('Piece');
            $xml->element('PieceID', $this->packageCount);
            $xml->element('PackageTypeCode', $package->packagingType);
            $xml->element('Height', $package->height);
            $xml->element('Depth', $package->length);
            $xml->element('Width', $package->width);
            $xml->element('Weight', $package->weight);
        $xml->pop();

        if ($this->weight = '') {
            $this->weight = 0;
        }

        $this->shipmentWeight = (double) $this->shipmentWeight + (double) $package->weight;
        $this->core->packagesObject = $xml;
    }

    function getAllRates($requestOption='Rate')
    {
        $xmlString = $this->buildDHLRateXml($requestOption);
        $this->core->request('Rate', $xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function buildSimpleResponse($response)
    {
        $dhl = $response;
        if (!isset($dhl['DCTResponse']['GetQuoteResponse']['BkgDetails'])) {
            if (isset($dhl['DCTResponse']['GetQuoteResponse']['Note']['Condition']['ConditionData'])) {
                return array('error' => $dhl['DCTResponse']['GetQuoteResponse']['Note']['Condition']['ConditionData']);
            } else {
                return array('error' => 'Unknown error');
            }
        }

        // Only one result?
        if (isset($dhl['DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp']['GlobalProductCode'])) {
            $dhl['DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp'] = array($dhl['DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp']);
        }

        $rates = array();
        if (!isset($dhl['DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp'])) {
            return array();
        }

        foreach ($dhl['DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp'] as $rte) {
            //var_dump($rte);
            $svc_code = $rte['GlobalProductCode'];
            $serviceType = $this->core->getServiceDescriptionFromCode($svc_code);
            $value = $rte['ShippingCharge'];
            if (!empty($this->currency)) {
                // Find the total amount in the correct currency, if possible.
                if (!empty($rte['CurrencyCode']) && $rte['CurrencyCode'] == $this->currency) {
                    // $value is OK
                } else if (is_array($rte['QtdSInAdCur'])) {
                    foreach ($rte['QtdSInAdCur'] as $amt) {
                        if (!empty($amt['CurrencyCode']) && $amt['CurrencyCode'] == $this->currency) {
                            $value = $amt['TotalAmount'];
                            break;
                        }
                    }
                }
            }

            $simpleRate = array('desc' => $serviceType, 'rate' => $value, 'service_code' => $svc_code);
            if (!empty($user_func)) {
                $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $rte, $simpleRate);
            }
            $rates[] = $simpleRate;
        }

        return $rates;
    }

    function getSimpleRates($user_func=null)
    {
        $dhl = $this->getAllRates();

        return $this->buildSimpleResponse($dhl);
    }

    function buildDHLRateXml()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder();
        $xml->push('p:DCTRequest', array(
            'xmlns:p' => 'http://www.dhl.com',
            'xmlns:p1' => 'http://www.dhl.com/datatypes',
            'xmlns:p2' => 'http://www.dhl.com/DCTRequestdatatypes',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.dhl.com DCT-req.xsd'
        ));
            $xml->push('GetQuote');
                $xml->append($this->buildRequestXml());
                $xml->append($this->buildFrom());
                $xml->append($this->buildBkgDetails());
                $xml->append($this->buildToXml());
                if ($this->customsValue != "") {
                    $xml->push('Dutiable');
                        if ($this->customsCurrency == '') {
                            $xml->element('DeclaredCurrency', 'USD');
                        } else {
                            $xml->element('DeclaredCurrency', $this->customsCurrency);
                        }
                        $xml->element('DeclaredValue', $this->customsValue);
                    $xml->pop();
                }
            $xml->pop(); // end GetCapability
        $xml->pop(); // end DCTRequest
        return $xml->getXml();
    }

    function buildRequestXml()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder(true);
        $xml->push('Request');
            $xml->push('ServiceHeader');
                $xml->element('MessageTime', date('c'));
                $xml->element('MessageReference', $this->generateRandomString());
                $xml->element('SiteID', $this->siteId);
                $xml->element('Password', $this->password);
            $xml->pop(); // end ServiceHeader
        $xml->pop(); // end Request
        return $xml->getXml();
    }

    function buildFrom()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder(true);
        $xml->push('From');
            $xml->element('CountryCode', $this->shipCountry);
            $xml->element('Postalcode', $this->shipCode);
            $xml->element('City', $this->shipCity);
            //$xml->element('Suburb', '');
            //$xml->element('VatNo', '1');
        $xml->pop(); // end DCTFrom
        return $xml->getXml();
    }

    function buildToXml()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder(true);
        $xml->push('To');
            $xml->element('CountryCode', $this->toCountry);
            $xml->element('Postalcode', $this->toCode);
            $xml->element('City', $this->toCity);
            //$xml->element('Suburb', '');
            //$xml->element('VatNo', '1');
        $xml->pop(); // end DCTFrom
        return $xml->getXml();
    }

    function buildBkgDetails()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder(true);
        $xml->push('BkgDetails');
            if ($this->accountCountry != '') {
                $xml->element('PaymentCountryCode', $this->accountCountry);
            } else {
                $xml->element('PaymentCountryCode', $this->shipCountry);
            }
            $xml->element('Date', gmdate('Y-m-d')); // UTC, per ReadyTimeGMTOffset below
            $xml->element('ReadyTime', gmdate('\P\TH\Hi\M', time()+(60*60))); // Format: 'PT#H#M'; Ref: http://www.w3.org/TR/xmlschema-2/#duration
            $xml->element('ReadyTimeGMTOffset', '00:00');
            $xml->element('DimensionUnit', $this->lengthUnit); // IN/CM
            $xml->element('WeightUnit', $this->weightUnit); // LB/KG
            if ($this->packageCount == 0) {
                $xml->element('NumberOfPieces', '1'); // 0-99
            } else {
                $xml->element('NumberOfPieces', $this->packageCount);
            }
            if ($this->shipmentWeight != 0) {
                $xml->element('ShipmentWeight', $this->shipmentWeight);
            } else {
                $xml->element('ShipmentWeight', $this->weight);
            }
            $xml->push('Pieces');
                if (isset($this->core->packagesObject)) {
                    $xml->append($this->core->packagesObject->getXml());
                } else {
                    $xml->push('Piece');
                        $xml->element('PieceID', '1');
                        $xml->element('PackageTypeCode', $this->packagingType);
                        $xml->element('Height', $this->height);
                        $xml->element('Depth', $this->length);
                        $xml->element('Width', $this->width);
                        $xml->element('Weight', $this->weight);
                    $xml->pop(); //end Piece
                }
            $xml->pop(); //end Pieces
            $xml->element('PaymentAccountNumber', $this->accountNumber);
            if ($this->toCountry != $this->shipCountry && $this->isDutiable != 'N') {
                $xml->element('IsDutiable', 'Y');
            }
            if ($this->insuredValue != '') {
                $xml->element('InsuredValue', $this->insuredValue);
                $xml->element('InsuredCurrency', $this->insuredCurrency);
            }
        $xml->pop(); //end BkgDetails

        return $xml->getXml();
    }

    /**
     * Creates random string of alphanumeric characters
     *
     * @return string
     */
    public function generateRandomString()
    {
        $length = 32;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = "";

        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }
        return $string;
    }

    public function getRate()
    {
        return 'Method not available';
    }

    public function getSimpleRate()
    {
        return 'method not available';
    }
}
