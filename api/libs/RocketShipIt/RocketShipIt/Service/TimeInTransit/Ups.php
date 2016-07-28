<?php

namespace RocketShipIt\Service\TimeInTransit;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\General;

/**
* Main class for getting time in transit information
*
*/
class Ups extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General();
        parent::__construct($carrier);
    }

    public function processResponse()
    {
        $xmlArray = $this->arrayFromXml($this->core->xmlResponse);
        $this->responseArray = $xmlArray;

        if (isset($xmlArray['TimeInTransitResponse']['Response']['ResponseStatusCode'])) {
            if ($xmlArray['TimeInTransitResponse']['Response']['ResponseStatusCode'] == 1) {
                $this->status = 'success';
            }
            if ($xmlArray['TimeInTransitResponse']['Response']['ResponseStatusCode'] == 0) {
                $this->status = 'failure';
            }
        }
    }

    public function buildSimpleTransit($respArray)
    {
        $response = array();

        if (!isset($respArray['TimeInTransitResponse']['TransitResponse']['ServiceSummary'])) {
            return $respArray;
        }

        if (!is_array($respArray['TimeInTransitResponse']['TransitResponse']['ServiceSummary'])) {
            return $respArray;
        }

        foreach ($respArray['TimeInTransitResponse']['TransitResponse']['ServiceSummary'] as $service) {
            $simpleArr = array();
            $simpleArr['desc'] = '';
            $simpleArr['service_code'] = '';
            $simpleArr['is_guaranteed'] = false;
            $simpleArr['eta'] = 'N/A';
            $simpleArr['formatted_eta'] = 'N/A';
            $simpleArr['service_rate_code'] = '';

            $simpleArr['desc'] = $this->helper->getValueFromPath($service, 'Service/Description', '');
            $simpleArr['service_code'] = $this->helper->getValueFromPath($service, 'Service/Code', '');
            $simpleArr['service_rate_code'] = $this->core->getServiceCodeFromDescription($simpleArr['desc']);

            $guar = $this->helper->getValueFromPath($service, 'Guaranteed/Code', false);
            if ($guar == 'Y') {
                $simpleArr['is_guaranteed'] = true;
            }

            $date = $this->helper->getValueFromPath($service, 'EstimatedArrival/Date', false);
            $time = $this->helper->getValueFromPath($service, 'EstimatedArrival/Time', '');
            if ($date) {
                $dateTime = strtotime($date. ' '. $time);
                $simpleArr['eta'] = $dateTime;
                $simpleArr['formatted_eta'] = date("D F j \b\y g:i a", $dateTime);
            }

            $response[] = $simpleArr;
        }

        return $response;
    }

    function buildUPSTimeInTransitXml()
    {
        $this->core->access();
        $accessXml = $this->core->xmlObject;

        $xml = new xmlBuilder();
        $xml->push('TimeInTransitRequest', array('xml:lang' => 'en-US'));
            $xml->push('Request');
                $xml->push('TransactionReference'); // Not required
                    $xml->element('CustomerContext', 'RocketShipIt'); // Not required
                $xml->pop(); // close TransactionReference, not required
                $xml->element('RequestAction', 'TimeInTransit');
            $xml->pop(); // end Request;
            $xml->push('TransitFrom');
                $xml->push('AddressArtifactFormat');
                    $xml->element('PoliticalDivision2', $this->shipCity);
                    $xml->element('CountryCode',$this->shipCountry);
                    $xml->element('PostcodePrimaryLow', $this->shipCode);
                $xml->pop(); // end AddressArtifactFormat
            $xml->pop(); // end TransitFrom
            $xml->push('TransitTo');
                $xml->push('AddressArtifactFormat');
                    $xml->element('PoliticalDivision2',$this->toCity);
                    $xml->element('CountryCode', $this->toCountry);
                    $xml->element('PostcodePrimaryLow', $this->toCode);
                $xml->pop(); // end AddressArtifactFormat
            $xml->pop(); // end TransitTo
            if ($this->weight != '') {
                $xml->push('ShipmentWeight');
                    $xml->push('UnitOfMeasurement');
                        $xml->element('Code',$this->weightUnit);
                        $xml->element('Description','Pounds');
                    $xml->pop(); //end UnitOfMeasurement
                    $xml->element('Weight',$this->weight);
                $xml->pop(); //end ShipmentWeight
            }
            $xml->element('TotalPackagesInShipment','1');
            // $xml->push('InvoiceLineTotal');
            //     $xml->element('CurrencyCode',$this->currency);
            //     $xml->element('MonetaryValue',$this->monetaryValue);
            // $xml->pop(); // end InvoiceLineTotal
            // This allows user to use pickupDate = YYYY-MM-DD, and both
            // FedEx and UPS will be able to use it.
            $xml->element('PickupDate', str_replace('-', '', $this->pickupDate)); // yyyyMMdd
            //$xml->element('DocumentsOnlyIndicator','');
            if ($this->monetaryValue != '') {
                $xml->push('InvoiceLineTotal');
                    $xml->element('CurrencyCode', $this->currency);
                    $xml->element('MonetaryValue', $this->monetaryValue);
                $xml->pop();
            }
        $xml->pop(); // end TimeInTransitRequest

        // Convert xml object to a string
        $accessXmlString = $accessXml->getXml();
        $requestXmlString = $xml->getXml();
        return $accessXmlString. $requestXmlString;
    }

    public function getSimpleTimeInTransit()
    {
        $arr = $this->getUPSTimeInTransit();
        return $this->buildSimpleTransit($arr);
    }

    function getUPSTimeInTransit()
    {
        $xmlString = $this->buildUPSTimeInTransitXml();
        $this->core->request('TimeInTransit', $xmlString);
        $this->processResponse();
        return $this->responseArray; 
    }
}
