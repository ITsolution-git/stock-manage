<?php

namespace RocketShipIt\Service\TimeInTransit;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\General;

/**
* Main class for getting time in transit information
*
*/
class Dhl extends \RocketShipIt\Service\Common
{

    public $packageCount = 0;
    public $shipmentWeight = 0;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General();
        parent::__construct($carrier);
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
                        $xml->element('PackageTypeCode', $this->packagingType);
                        $xml->element('PieceID', '1');
                        $xml->element('Height', $this->height);
                        $xml->element('Depth', $this->length);
                        $xml->element('Width', $this->width);
                        $xml->element('Weight', $this->weight);
                    $xml->pop(); //end Piece
                }
            $xml->pop(); //end Pieces
            $xml->element('PaymentAccountNumber', $this->accountNumber);
        $xml->pop(); //end BkgDetails
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

    public function buildDHLTimeInTransitXml()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder();
        $xml->push('p:DCTRequest', array('xmlns:p' => "http://www.dhl.com", 'xmlns:p1' => "http://www.dhl.com/datatypes", 'xmlns:p2' => "http://www.dhl.com/DCTRequestdatatypes", 'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance", 'xsi:schemaLocation' => "http://www.dhl.com DCT-req.xsd"));
            $xml->push('GetCapability');
                $xml->append($this->buildRequestXml());
                $xml->append($this->buildFrom());
                $xml->append($this->buildBkgDetails());
                $xml->append($this->buildToXml());
            $xml->pop(); // end GetCapability
        $xml->pop(); // end DCTRequest
        return $xml->getXml();
    }

    public function getDHLTimeInTransit()
    {
        $xmlString = $this->buildDHLTimeInTransitXml();
        $this->core->request('TimeInTransit', $xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }
}
