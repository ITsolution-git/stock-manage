<?php

namespace RocketShipIt\Service\Shipment;
use \RocketShipIt\Request;
use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\XmlParser;

/**
* Main Rate class for producing rates for various packages/shipments
*
* This class is a wrapper for use with all carriers to produce rates
* Valid carriers are: UPS, USPS, Stamps.com, DHL, and FedEx.
*/
class Canada extends \RocketShipIt\Service\Common
{
    var $packageCount;
    var $links = array();

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildCANADAShipmentXml()
    {
        $xml = new xmlBuilder();
        $xml->push('shipment', array('xmlns' => "http://www.canadapost.ca/ws/shipment-v4"));
            $xml->element('group-id', 'grp1');
            $xml->element('requested-shipping-point', 'K2B8J6');
            $xml->push('delivery-spec');
                $xml->element('service-code', 'DOM.EP');
                $xml->push('sender');
                    $xml->element('name', 'Bob');
                    $xml->element('company', 'CGI');
                    $xml->element('contact-phone', '1 (450) 823-8432');
                    $xml->push('address-details');
                        $xml->element('address-line-1', '502 MAIN ST N');
                        $xml->element('city', 'MONTREAL');
                        $xml->element('prov-state', 'QC');
                        $xml->element('country-code', 'CA');
                        $xml->element('postal-zip-code', 'H2B1A0');
                    $xml->pop(); //end address-details
                $xml->pop(); //end sender
                $xml->push('destination');
                    $xml->element('name', 'Jain');
                    $xml->element('company', 'CGI');
                    $xml->push('address-details');
                        $xml->element('address-line-1', '23 jardin private');
                        $xml->element('city', 'Ottawa');
                        $xml->element('prov-state', 'ON');
                        $xml->element('country-code', 'CA');
                        $xml->element('postal-zip-code', 'K1K4T3');
                    $xml->pop(); //end address-details
                $xml->pop(); //end destination
                $xml->push('options');
                    $xml->push('option');
                        $xml->element('option-code', 'DC');
                    $xml->pop(); //end option
                $xml->pop(); //end options
                $xml->push('parcel-characteristics');
                    $xml->element('weight', '20');
                    $xml->push('dimensions');
                        $xml->element('length', '6');
                        $xml->element('width', '12');
                        $xml->element('height', '9');
                    $xml->pop(); //end dimensions
                    $xml->element('mailing-tube', 'false');
                $xml->pop(); //end parcel-characteristics
                $xml->push('notification');
                    $xml->element('email', 'john.doe@yahoo.com');
                    $xml->element('on-shipment', 'true');
                    $xml->element('on-exception', 'false');
                    $xml->element('on-delivery', 'true');
                $xml->pop(); //end notification
                $xml->push('print-preferences');
                    $xml->element('output-format', '8.5x11');
                    //$xml->element('encoding', 'PDF');
                $xml->pop(); //end print-preferences
                $xml->push('preferences');
                    $xml->element('show-packing-instructions', 'true');
                    $xml->element('show-postage-rate', 'false');
                    $xml->element('show-insured-value', 'true');
                $xml->pop(); //end preferences
                $xml->push('settlement-info');
                    $xml->element('contract-id', $this->accountNumber);
                    $xml->element('intended-method-of-payment', 'Account');
                $xml->pop(); //end settlement-info
            $xml->pop(); //end delivery-spec
        $xml->pop(); // end shipment
        return $xml->getXml();
    }

    function buildNonContractShipmentXml()
    {
        $xml = new xmlBuilder();
        $xml->push('non-contract-shipment', array('xmlns' => "http://www.canadapost.ca/ws/ncshipment"));
            $xml->push('delivery-spec');
                $xml->element('service-code', $this->service);
                $xml->push('sender');
                    $xml->element('company', $this->shipper);
                    $xml->element('contact-phone', $this->shipPhone);
                    $xml->push('address-details');
                        $xml->element('address-line-1', $this->shipAddr1);
                        $xml->element('city', $this->shipCity);
                        $xml->element('prov-state', $this->shipState);
                        $xml->element('postal-zip-code', strtoupper($this->shipCode));
                    $xml->pop(); //end address-details
                $xml->pop(); //end sender
                $xml->push('destination');
                    $xml->element('name', $this->toName);
                    $xml->element('company', $this->toCompany);
                    $xml->element('client-voice-number', $this->toPhone);
                    $xml->push('address-details');
                        $xml->element('address-line-1', $this->toAddr1);
                        $xml->element('city', $this->toCity);
                        $xml->element('prov-state', $this->toState);
                        $xml->element('country-code', $this->toCountry);
                        $xml->element('postal-zip-code', strtoupper($this->toCode));
                    $xml->pop(); //end address-details
                $xml->pop(); //end destination
                if ($this->optionCode != '' || $this->signatureType != '') {
                    $xml->push('options');
                        if ($this->signatureType == 'DIRECT') {
                            $xml->push('option');
                                $xml->element('option-code', 'SO');
                            $xml->pop(); //end option
                        }
                        // Adult sig requires both SO and PA18
                        if ($this->signatureType == 'ADULT') {
                            $xml->push('option');
                                $xml->element('option-code', 'SO');
                            $xml->pop(); //end option
                            $xml->push('option');
                                $xml->element('option-code', 'PA18');
                            $xml->pop(); //end option
                        }
                        if ($this->optionCode != '') {
                            $xml->push('option');
                                $xml->element('option-code', $this->optionCode);
                                $xml->element('option-amount', $this->optionAmount);
                                $xml->element('option-qualifier-1', $this->optionQualifier1);
                                $xml->element('option-qualifier-2', $this->optionQualifier2);
                            $xml->pop(); //end option
                        }
                    $xml->pop(); //end options
                }
                $xml->push('parcel-characteristics');
                    $xml->element('weight', $this->weight);
                    if ($this->length != '') {
                        $xml->push('dimensions');
                            $xml->element('length', $this->length);
                            $xml->element('width', $this->width);
                            $xml->element('height', $this->height);
                        $xml->pop(); //end dimensions
                    }
                $xml->pop(); //end parcel-characteristics
                $xml->push('preferences');
                    $xml->element('show-packing-instructions', 'true');
                $xml->pop(); //end preferences
                if (isset($this->core->customsObject)) {
                    $xml->push('customs');
                        if ($this->customsCurrency == '') {
                            $xml->element('currency', 'CAD');
                        } else {
                            $xml->element('currency', $this->customsCurrency);
                        }
                        $xml->element('reason-for-export', 'SOG'); // Sale of goods
                        if ($this->shipmentDescription != '') {
                            $xml->element('additional-customs-info', substr($this->shipmentDescription, 0, 44));
                        }
                        $xml->push('sku-list');
                            $xml->append($this->core->customsObject->getXML());
                        $xml->pop(); //end sku-list
                    $xml->pop(); //end customs
                }
            $xml->pop(); //end delivery-spec
        $xml->pop(); // end nc-shipment
        return $xml->getxml();
    }

    function sendCANADAShipment()
    {
        $xmlString = $this->buildNonContractShipmentXml();

        // contract-shipment
        /*
        $header = array('Content-Type: application/vnd.cpc.shipment-v4+xml',
                        'Accept: application/vnd.cpc.shipment-v4+xml');
        */

        $header = array('Content-Type: application/vnd.cpc.ncshipment+xml',
                        'Accept: application/vnd.cpc.ncshipment+xml');

        // Put the xml that is sent to CanadaPost into a variable so we can call it later for debugging.
        $this->core->xmlSent = $xmlString;
        $this->core->xmlResponse = $this->core->request('/rs/'. $this->accountNumber. '/ncshipment', $xmlString, $header);

        if ($this->getTrackingNumFromShipmentXml($this->core->xmlResponse) == 0) {
            return $this->arrayFromXml($this->core->xmlResponse);
        }

        $this->links = $this->getLinksFromXml($this->core->xmlResponse);

        return $this->simplifyShipmentResponse($this->core->xmlResponse, $this->getReceipt($this->links), $this->getLabel($this->links));
    }

    public function getLinksFromXml($xml)
    {
        $linkArray = array();
        $doc = new \DOMDocument;
        @$doc->loadHTML($xml);
        $xpath = new \DOMXpath($doc);
        $links = $xpath->query("//links/link");

        foreach ($links as $link) {
            $l = array();
            if (!$link->hasAttribute('rel')) {
                continue;
            }
            $l['type'] = $link->getAttribute('rel');
            if ($link->hasAttribute('href')) {
                $l['href'] = $link->getAttribute('href');
            }
            if ($link->hasAttribute('media-type')) {
                $l['media_type'] = $link->getAttribute('media-type');
            }
            $linkArray[] = $l;
        }

        return $linkArray;
    }

    public function getLabel($links)
    {
        foreach ($links as $link) {
            if ($link['type'] != 'label') {
                continue;
            }
            return base64_encode($this->getLink($link));
        }
        return '';
    }

    public function getReceipt($links)
    {
        foreach ($links as $link) {
            if ($link['type'] != 'receipt') {
                continue;
            }
            return $this->getLink($link);
        }
        return '';
    }

    public function getLink($link, $retries = 0)
    {
        $request = new Request;
        $request->url = $link['href'];
        $request->username = $this->username;
        $request->password = $this->password;
        $request->header = array(
            'Accept: '. $link['media_type']
        );
        $request->get();
        if ($request->getStatusCode() == 202 && $retries < 5) {
            sleep(1);
            return $this->getLink($link, $retries + 1);
        }

        return $request->getResponse();
    }

    public function getTotalFromReceiptXml($xml)
    {
        return $this->getSingleValueByXpath($xml, '//cc-receipt-details/charge-amount', 0.00);
    }

    public function getTrackingNumFromShipmentXml($xml)
    {
        return $this->getSingleValueByXpath($xml, '//tracking-pin', 0);
    }

    public function getSipmentIdFromShipmentXml($xml)
    {
        return $this->getSingleValueByXpath($xml, '//shipment-id', 0);
    }

    public function getSingleValueByXpath($xml, $xpath, $defaultValue)
    {
        $doc = new \DOMDocument;
        @$doc->loadHTML($xml);
        $xpathObj = new \DOMXpath($doc);
        $nodes = $xpathObj->query($xpath);
        if ($nodes->length == 0) {
            return $defaultValue;
        }
        foreach ($nodes as $n) {
            return $n->nodeValue;
        }
    }

    public function simplifyShipmentResponse($shipmentXml, $receiptXml, $artifact)
    {
        $response = array();
        $response['charges'] = $this->getTotalFromReceiptXml($receiptXml);
        $response['shipment_id'] = $this->getSipmentIdFromShipmentXml($shipmentXml);
        $response['trk_main'] = $this->getTrackingNumFromShipmentXml($shipmentXml);
        $response['pkgs'] = array(
            array(
                'pkg_trk_num' => $response['trk_main'],
                'label_fmt' => 'PDF',
                'label_img' => $artifact
            )
        );
        $response['links'] = $this->links;

        return $response;
    }

    public function addCustomsLineToCANADAshipment($customs)
    {
        if (!isset($this->core->customsObject)) {
            $this->core->customsObject = new xmlBuilder(true);
        }
        $xml = $this->core->customsObject;
        $xml->push('item');
            $xml->element('customs-number-of-units', $customs->customsQuantity);
            $xml->element('customs-description', substr($customs->customsDescription, 0, 44));
            $xml->element('unit-weight', $customs->customsWeight);
            $xml->element('customs-value-per-unit', min($customs->customsValue, 999.99));
        $xml->pop(); // end Item
        return $this->core->customsObject = $xml;
    }
}
