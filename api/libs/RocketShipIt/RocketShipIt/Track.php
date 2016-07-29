<?php

namespace RocketShipIt;

/**
 * Main class for tracking shipments and packages.
 *
 * This class is a wrapper for use with all carriers to track packages
 * Valid carriers are: UPS, USPS, and FedEx.
 */
class Track extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    public function track($trackingNumber)
    {
        switch (strtoupper($this->carrier)) {
        case 'UPS':
            $retArr = $this->inherited->trackUPS($trackingNumber);
            if (!isset($retArr['TrackResponse'])) {
                return $retArr;
            }
            $a = $retArr['TrackResponse'];
            if ($a['Response']['ResponseStatusCode'] != '1') {
                $this->result = 'FAIL';
                $this->reason = $a['Response']['Error']['ErrorDescription'].
                                    ' ('.$a['Response']['Error']['ErrorCode'].')';
            } else {
                if (array_key_exists('TrackingNumber', $a['Shipment']['Package'])) {
                    // single package
                    $p = $a['Shipment']['Package'];
                } else {
                    // multi-package
                    $p = $a['Shipment']['Package'][0];
                }
                $this->result = 'OK';
                if (array_key_exists('Status', $p['Activity'])) {
                    // just the one
                    $this->status = $p['Activity']['Status']['StatusType']['Description'];
                } else {
                    // multiple activities - grab the most recent
                    $this->status = $p['Activity'][0]['Status']['StatusType']['Description'];
                }
            }

            return $retArr;
        case 'FEDEX':
            return $this->inherited->trackFEDEX($trackingNumber);
        case 'USPS':
            return $this->inherited->trackUSPS($trackingNumber);
        case 'DHL':
            return $this->inherited->trackDHL($trackingNumber);
        case 'CANADA':
            return $this->inherited->trackCANADA($trackingNumber);
        case 'STAMPS':
            return $this->inherited->trackSTAMPS($trackingNumber);
        case 'ONTRAC':
            return $this->inherited->trackONTRAC($trackingNumber);
        default:
            $retArr['result'] = 'fail';
            $retArr['reason'] = "Unknown carrier $this->carrier in RocketShipTrack";

            return $retArr;
        }
    }

    public function trackByReference($referenceNumber)
    {
        $retArr = array();
        switch (strtoupper($this->carrier)) {
        case 'UPS':
            $this->inherited->referenceNumber = $referenceNumber;
            $retArr = $this->inherited->trackUPS($referenceNumber);

            return $retArr;
        case 'FEDEX':
            return $retArr;
        case 'USPS':
            return $retArr;
        default:
            $retArr['result'] = 'fail';
            $retArr['reason'] = "Unknown carrier $this->carrier in RocketShipTrack";

            return $retArr;
        }
    }

    public function getProofOfDelivery()
    {
        $method = __FUNCTION__;
        if (!method_exists($this->inherited, $method)) {
            return $this->invalidCarrierResponse();
        }

        return (array) $this->inherited->$method();
    }

    public function notify()
    {
        $method = __FUNCTION__;
        if (!method_exists($this->inherited, $method)) {
            return $this->invalidCarrierResponse();
        }

        return (array) $this->inherited->$method();
    }
}
