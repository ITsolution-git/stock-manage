<?php

namespace RocketShipIt;

/**
 * Main class for voiding shipments.
 *
 * This class is a wrapper for use with all carriers to cancel
 * shipments.  Valid carriers are: UPS, USPS, and FedEx.
 * To create a shipment see {@link RocketShipShipment}.
 */
class Void extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * Void (cancel) a shipment at the shipment level.  I.e. all packages.
     *
     * This will void all packages linked to the ShipmentIdentification
     * number.  Often times this is the first tracking number in a set
     * of packages.
     */
    public function voidShipment($shipmentIdentification)
    {
        $outArr = array();
        switch ($this->carrier) {
            case 'UPS':
                $this->inherited->shipmentIdentification = $shipmentIdentification;
                $xmlArray = $this->inherited->voidShipment();
                $a = $xmlArray['VoidShipmentResponse'];
                $outArr['result'] = 'fail';
                $outArr['shipmentNumber'] = $shipmentIdentification;
                if ($a['Response']['ResponseStatusCode'] == '1') {
                    $outArr['result'] = 'voided';
                } else {
                    $outArr['reason'] = $a['Response']['Error']['ErrorDescription'].
                                        ' ('.$a['Response']['Error']['ErrorCode'].')';
                }
                $outArr['xmlArray'] = $xmlArray;

                return $outArr;
            case 'FEDEX':
                $this->inherited->shipmentIdentification = $shipmentIdentification;
                $xmlArray = $this->inherited->voidShipment();
                $outArr['result'] = 'fail';
                $outArr['shipmentNumber'] = $shipmentIdentification;
                $outArr['xmlArray'] = $xmlArray;
                if (!isset($xmlArray['ShipmentReply']['Notifications']['Severity'])) {
                    return $outArr;
                }
                if ($xmlArray['ShipmentReply']['Notifications']['Severity'] == 'SUCCESS') {
                    $outArr['result'] = 'voided';
                }

                return $outArr;
            case 'STAMPS':
                $this->inherited->shipmentIdentification = $shipmentIdentification;
                $outArr['result'] = 'voided';
                $outArr['shipmentNumber'] = $shipmentIdentification;
                try {
                    $soapObj = $this->inherited->voidShipment();
                    $outArr['xmlArray'] = $soapObj;
                } catch (\Exception $e) {
                    $outArr['result'] = 'fail';
                    $outArr['xmlArray'] = $e->getMessage();
                }

                return $outArr;
            case 'CANADA':
                $this->inherited->shipmentIdentification = $shipmentIdentification;
                $outArr['result'] = 'fail';
                $outArr['shipmentNumber'] = $shipmentIdentification;
                $statusCode = 404;
                try {
                    $statusCode = $this->inherited->voidShipment();
                    if ($statusCode == '204' || $statusCode == '200') {
                        $outArr['result'] = 'voided';
                    }
                    $outArr['statusCode'] = $statusCode;
                    $outArr['xmlArray'] = '';
                } catch (\Exception $e) {
                    $outArr['result'] = 'fail';
                    $outArr['statusCode'] = $statusCode;
                    $outArr['xmlArray'] = $e->getMessage();
                }

                return $outArr;
            default:
                $outArr['result'] = 'fail';
                $outArr['reason'] = 'invalid carrier';

                return $outArr;
        }
    }

    /**
     * Void (cancel) a shipment at the package level.  I.e. one package.
     *
     * This will void a single package identified by a specific
     * tracking number.
     */
    public function voidPackage($shipmentIdentification, $packageIdentification)
    {
        $outArr = array();
        switch ($this->carrier) {
            case 'UPS':
                $this->inherited->shipmentIdentification = $shipmentIdentification;
                $this->inherited->packageIdentification = $packageIdentification;
                $xmlArray = $this->inherited->voidPackage();
                $a = $xmlArray['VoidShipmentResponse'];
                $outArr = '';
                if ($a['Response']['ResponseStatusCode'] == '1') {
                    $outArr['result'] = 'voided';
                    $outArr['shipmentNumber'] = $shipmentIdentification;
                } else {
                    $outArr['result'] = 'fail';
                    $outArr['reason'] = $a['Response']['Error']['ErrorDescription'].
                                        ' ('.$a['Response']['Error']['ErrorCode'].')';
                }
                $outArr['xmlArray'] = $xmlArray;

                return $outArr;
            default:
                $outArr['result'] = 'fail';
                $outArr['reason'] = 'invalid carrier';

                return $outArr;
        }
    }
}
