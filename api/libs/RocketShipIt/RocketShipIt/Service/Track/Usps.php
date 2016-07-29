<?php

namespace RocketShipIt\Service\Track;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

class Usps extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function trackUSPS($trackingNumber)
    {
        $xml = $this->core->xmlObject;

        $xml->push('TrackFieldRequest', array('USERID' => $this->userid));
            $xml->element('Revision', '1');
            $xml->element('ClientIp', '127.0.0.1');
            $xml->element('SourceId', 'RocketShipIt');
            $xml->push('TrackID', array('ID' => $trackingNumber));
            $xml->pop(); // end TrackID
        $xml->pop(); // end TrackRequest

        $xmlString = $xml->getXml();

        $postData = $this->buildPostData(array(
            'api' => 'TrackV2',
            'xml' => $xmlString,
        ));

        $this->core->request('ShippingAPI.dll', $postData);

        // Convert the xmlString to an array
        return $this->simplifyResponse($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyResponse($r)
    {
        $t = new \RocketShipIt\Response\Track;
        $t->Meta->Code = 200;

        $e = new \RocketShipIt\Response\Error;
        if (isset($r['TrackResponse']['TrackInfo']['Error']['Description'])) {
            $e->Description = $r['TrackResponse']['TrackInfo']['Error']['Description'];
        }
        if (isset($r['TrackResponse']['TrackInfo']['Error']['Number'])) {
            $e->Code = $r['TrackResponse']['TrackInfo']['Error']['Number'];
            $t->Data->Errors[] = $e;
        }

        if (isset($r['TrackResponse']['TrackInfo']['ExpectedDeliveryDate'])) {
            $t->Data->EstimatedDelivery = date('Y-m-d\TH:i:s\Z', strtotime($r['TrackResponse']['TrackInfo']['ExpectedDeliveryDate']));
        }

        $d = new \RocketShipIt\Response\Track\Destination;
        $d->Country = 'US';
        if (isset($r['TrackResponse']['TrackInfo']['DestinationCity'])) {
            $d->City = $r['TrackResponse']['TrackInfo']['DestinationCity'];
        }
        if (isset($r['TrackResponse']['TrackInfo']['DestinationState'])) {
            $d->State = $r['TrackResponse']['TrackInfo']['DestinationState'];
        }
        if (isset($r['TrackResponse']['TrackInfo']['DestinationZip'])) {
            $d->PostalCode = $r['TrackResponse']['TrackInfo']['DestinationZip'];
            $t->Data->Destination = $d;
        }
        $t->Data->ShipmentId = '';
        if (isset($r['TrackResponse']['TrackInfo']['ID'])) {
            $t->Data->ShipmentId = $r['TrackResponse']['TrackInfo']['ID'];
        }

        $trackDetail = array();
        if (isset($r['TrackResponse']['TrackInfo']['TrackSummary'])) {
            $trackDetail[] = $r['TrackResponse']['TrackInfo']['TrackSummary'];
        }
        if (isset($r['TrackResponse']['TrackInfo']['TrackDetail'])) {
            $trackDetail = array_merge($trackDetail, $r['TrackResponse']['TrackInfo']['TrackDetail']);
        }

        $p = new \RocketShipIt\Response\Track\Package;
        $p->TrackingNumber = $t->Data->ShipmentId;
        foreach ($trackDetail as $detail) {
            $a = new \RocketShipIt\Response\Track\Activity;
            $a->Time = date('Y-m-d\TH:i:s\Z', strtotime($detail['EventDate']. ' '. $detail['EventTime']));
            $a->Description = $detail['Event'];
            if (isset($detail['EventCode'])) {
                $a->StatusCode = $detail['EventCode'];
                $a->StatusDescription = $detail['Event'];

                if ($detail['EventCode'] == '01') {
                    $t->Data->DeliveredTime = date('Y-m-d\TH:i:s\Z', strtotime($detail['EventDate']. ' '. $detail['EventTime']));
                }
            }
            $l = new \RocketShipIt\Response\Track\Location;
            $l->City = $detail['EventCity'];
            $l->State = $detail['EventState'];
            $l->PostalCode = $detail['EventZIPCode'];
            $l->Country = 'US';
            if ($detail['EventCountry'] != '') {
                $l->Country = $detail['EventCountry'];
            }
            $a->Location = $l;
            $p->Activity[] = $a;
        }
        if (count($trackDetail) > 1) {
            $t->Data->Packages[] = $p;
        }

        return (array) json_decode(json_encode($t), true);
    }

    public function notify()
    {
        $postData = $this->buildPostData(array(
            'api' => 'PTSEmail',
            'xml' => $this->buildNotificationXml(),
        ));

        $this->core->request('ShippingAPI.dll', $postData);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function buildPostData($params)
    {
        return sprintf('API=%s&XML=%s', $params['api'], $params['xml']);
    }

    public function buildNotificationXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('PTSEmailRequest', array('USERID' => $this->userid));
            $xml->element('TrackId', $this->trackingNumber);
            $xml->element('MpSuffix', $this->mpSuffix);
            $xml->element('MpDate', $this->mpDate);
            if ($this->requestType != '') {
                $xml->element('RequestType', $this->requestType);
            } else {
                $xml->element('RequestType', 'EB');
            }
            $xml->element('Email1', $this->email);
            $xml->element('Email2', $this->email2);
            $xml->element('Email3', $this->email3);
        $xml->pop(); // end PTSEmailRequest 
        $xmlString = $xml->getXml();

        return $xmlString; 
    }
}
