<?php

namespace RocketShipIt\Service\Download;

use \RocketShipIt\Helper\CsvBuilder;

class Ups extends \RocketShipIt\Service\Common
{

    public $responses = array();

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildXml()
    {
        $xml = new \RocketShipIt\Helper\XmlBuilder(false);
        $xml->push('QuantumViewRequest', array('xml:lang' => 'en-US'));
            $xml->push('Request');
                $xml->element('RequestAction', 'QVEvents');
            $xml->pop();
            if ($this->bookmark != '') {
                $xml->element('Bookmark', $this->bookmark);
            }
            //if ($this->bookmark == '') {
                $xml->push('SubscriptionRequest');
                    if ($this->beginDate != '' && $this->endDate != '') {
                        $xml->push('DateTimeRange');
                            $xml->element('BeginDateTime', $this->beginDate);
                            $xml->element('EndDateTime', $this->endDate);
                        $xml->pop();
                    }
                    if ($this->subscriptionName != '') {
                        $xml->element('Name', $this->subscriptionName);
                    }
                    if ($this->filename != '') {
                        $xml->element('FileName', $this->filename);
                    }
                $xml->pop();
            //}
        $xml->pop();

        $auth = $this->core->access();

        return $auth. $xml->getXml();
    }

    public function csv()
    {
        $this->core->request('QVEvents', $this->buildXml());
        $this->processResponse();

        return $this->buildCsv();
    }

    public function processResponse()
    {
        $xmlArray = $this->arrayFromXml($this->core->xmlResponse);
        $this->responses[] = $xmlArray;

        if (isset($xmlArray['QuantumViewResponse']['Bookmark'])) {
            $this->bookmark = $xmlArray['QuantumViewResponse']['Bookmark'];
            $this->core->request('QVEvents', $this->buildXml());
            $this->processResponse();
        }

        $this->responseArray = $xmlArray;
    }

    public function buildCsv()
    {
        $csv = new CsvBuilder($this->simplifyResponse());

        return $csv->toString();
    }

    public function simplifyResponse()
    {
        $rows = array();

        $row = array(
            'record_type' => '',
            'shipper_number' => '',
            'shipper_name' => '',
            'tracking_number' => '',
            'to_name' => '',
            'pickup_date' => '',
            'service' => '',
            'shipment_reference_value_1' => '',
            'shipment_reference_value_2' => '',
            'shipment_reference_value_3' => '',
            'pkg_reference_value_1' => '',
            'pkg_reference_value_2' => '',
            'pkg_reference_value_3' => '',
        );

        $events = array(
            'Delivery' => array(),
            'Manifest' => array(),
            'Exception' => array(),
            'Origin' => array(),
        );

        foreach ($this->responses as $response) {

            $subscriptionEvents = array();
            if (isset($response['QuantumViewResponse']['QuantumViewEvents']['SubscriptionEvents'][0])) {
                $subscriptionEvents = $response['QuantumViewResponse']['QuantumViewEvents']['SubscriptionEvents'];
            } else {
                $subscriptionEvents[] = $response['QuantumViewResponse']['QuantumViewEvents']['SubscriptionEvents'];
            }

            foreach ($subscriptionEvents as $subscription) {
                $subscriptionFiles = array();
                if (isset($subscription['SubscriptionFile'][0])) {
                    $subscriptionFiles = $subscription['SubscriptionFile'];
                } else {
                    $subscriptionFiles[] = $subscription['SubscriptionFile'];
                }
                foreach ($subscriptionFiles as $file) {
                    foreach ($events as $key => $val) {
                        if (isset($file[$key])) {
                            if (isset($file[$key][0])) {
                                $events[$key] = array_merge($events[$key], $file[$key]);
                            } else {
                                $events[$key][] = $file[$key];
                            }
                        }
                    }
                }
            }

            foreach ($events['Origin'] as $origin) {
                $r = $row;
                $r['record_type'] = 'Origin';
                if (isset($origin['ShipperNumber'])) {
                    $r['shipper_number'] = $origin['ShipperNumber'];
                }
                if (isset($origin['TrackingNumber'])) {
                    $r['tracking_number'] = $origin['TrackingNumber'];
                }
                if (isset($origin['ShipmentReferenceNumber']['Value'])) {
                    $r['shipment_reference_value_1'] = $origin['ShipmentReferenceNumber']['Value'];
                }

                $rows[] = $r;
            }

            foreach ($events['Delivery'] as $delivery) {
                $r = $row;
                $r['record_type'] = 'Delivery';
                if (isset($delivery['ShipperNumber'])) {
                    $r['shipper_number'] = $delivery['ShipperNumber'];
                }
                if (isset($delivery['TrackingNumber'])) {
                    $r['tracking_number'] = $delivery['TrackingNumber'];
                }
                if (isset($delivery['PackageReferenceNumber'][0]['Value'])) {
                    $r['pkg_reference_value_1'] = $delivery['PackageReferenceNumber'][0]['Value'];
                }
                if (isset($delivery['ReferenceNumber'][1]['Value'])) {
                    $r['pkg_reference_value_2'] = $delivery['PackageReferenceNumber'][1]['Value'];
                }
                if (isset($delivery['ReferenceNumber'][2]['Value'])) {
                    $r['pkg_reference_value_3'] = $delivery['PackageReferenceNumber'][2]['Value'];
                }

                if (isset($delivery['ShipmentReferenceNumber'][0]['Value'])) {
                    $r['shipment_reference_value_1'] = $delivery['ShipmentReferenceNumber'][0]['Value'];
                }
                if (isset($delivery['ReferenceNumber'][1]['Value'])) {
                    $r['shipment_reference_value_2'] = $delivery['ShipmentReferenceNumber'][1]['Value'];
                }
                if (isset($delivery['ReferenceNumber'][2]['Value'])) {
                    $r['shipment_reference_value_3'] = $delivery['ShipmentReferenceNumber'][2]['Value'];
                }

                $rows[] = $r;
            }

            foreach ($events['Manifest'] as $manifest) {
                $r = $row;
                $r['record_type'] = 'Manifest';
                if (isset($manifest['PickupDate'])) {
                    $r['pickup_date'] = $manifest['PickupDate'];
                }
                if (isset($manifest['Service']['Code'])) {
                    $r['service'] = $manifest['Service']['Code'];
                }
                if (isset($manifest['Shipper']['ShipperNumber'])) {
                    $r['shipper_number'] = $manifest['Shipper']['ShipperNumber'];
                }
                if (isset($manifest['Shipper']['Name'])) {
                    $r['shipper_name'] = $manifest['Shipper']['Name'];
                }
                if (isset($manifest['ShipTo']['Address']['ConsigneeName'])) {
                    $r['to_name'] = $manifest['ShipTo']['Address']['ConsigneeName'];
                }
                if (isset($manifest['Package']['TrackingNumber'])) {
                    $r['tracking_number'] = $manifest['Package']['TrackingNumber'];
                }

                if (isset($manifest['ReferenceNumber']['Value'])) {
                    $r['shipment_reference_value_1'] = $manifest['ReferenceNumber']['Value'];
                }
                if (isset($manifest['ReferenceNumber'][0]['Value'])) {
                    $r['shipment_reference_value_1'] = $manifest['ReferenceNumber'][0]['Value'];
                }
                if (isset($manifest['ReferenceNumber'][1]['Value'])) {
                    $r['shipment_reference_value_2'] = $manifest['ReferenceNumber'][1]['Value'];
                }
                if (isset($manifest['ReferenceNumber'][2]['Value'])) {
                    $r['shipment_reference_value_3'] = $manifest['ReferenceNumber'][2]['Value'];
                }

                $rows[] = $r;
            }

        }

        return $rows;
    }
}
