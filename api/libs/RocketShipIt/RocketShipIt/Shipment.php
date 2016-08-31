<?php

namespace RocketShipIt;

/**
 * Main Shipping class for producing labels and notifying carriers of pickups.
 *
 * This class is a wrapper for use with all carriers to produce labels for
 * shipments.  Valid carriers are: UPS, USPS, Stamps, and FedEx.
 */
class Shipment extends \RocketShipIt\Service\Base
{
    public function __construct($carrier, $options = array())
    {
        $classParts = explode('\\', __CLASS__);
        $service = end($classParts);
        parent::__construct($carrier, $service, $options);
    }

    /**
     * This is a wrapper to create a running package for each carrier.
     *
     * This is used to add packages to a shipment for any carrier.
     * You use the {@link RocketShipPackage} class to create a package
     * object.
     */
    public function addPackageToShipment($packageObj)
    {
        if ($this->carrier != 'STAMPS') {
            $this->inherited->core->parameters['packages'][] = $packageObj->parameters;
        }
        switch ($this->carrier) {
            case 'UPS':
                return $this->inherited->addPackageToUPSshipment($packageObj);
            case 'STAMPS':
                return $this->inherited->addPackageToSTAMPSshipment($packageObj);
            case 'DHL':
                return $this->inherited->addPackageToDHLshipment($packageObj);
            case 'ONTRAC':
                return $this->inherited->addPackageToShipment($packageObj);
            case 'ROYALMAIL':
                return $this->inherited->addPackageToShipment($packageObj);
            default:
                return $this->invalidCarrierResponse();
        }
    }

    /**
     * This is a wrapper to create a running customs document for each carrier.
     */
    public function addCustomsLineToShipment($packageObj)
    {
        $this->inherited->core->parameters['customs'][] = $packageObj->parameters;
        switch ($this->carrier) {
            case 'STAMPS':
                return $this->inherited->addCustomsLineToSTAMPSshipment($packageObj);
            case 'FEDEX':
                return $this->inherited->addCustomsLineToFEDEXshipment($packageObj);
            case 'UPS':
                return $this->inherited->addCustomsLineToUPSshipment($packageObj);
            case 'CANADA':
                return $this->inherited->addCustomsLineToCANADAshipment($packageObj);
            default:
                return $this->invalidCarrierResponse();
        }
    }

    public function addCn22ContentToShipment($content)
    {
        $this->inherited->core->parameters['cn22Content'][] = $content->parameters;
    }

    /**
     * Sends the shipment data to the carrier.
     *
     * After the shipment data is sent it returns a simplified array of
     * the data sent back from the carrier.
     */
    public function submitShipment()
    {
        switch ($this->carrier) {
            case 'UPS':
                $shipResponse = $this->inherited->sendUPSshipment();
                $simpleArray = $this->inherited->simplifyUPSResponse($shipResponse);

                return $simpleArray;
            case 'USPS':
                $shipResponse = $this->inherited->sendUSPSshipment();
                $simpleArray = $this->inherited->simplifyUSPSResponse($shipResponse);

                return $simpleArray;
            case 'FEDEX':
                $shipResponse = $this->inherited->sendFEDEXshipment();
                $simpleArray = $this->inherited->simplifyFEDEXResponse($shipResponse);

                return $simpleArray;
            case 'STAMPS':
                return $this->inherited->sendSTAMPSshipment();
            case 'DHL':
                return $this->inherited->sendDHLshipment();
            case 'CANADA':
                return $this->inherited->sendCANADAshipment();
            case 'ONTRAC':
                return $this->inherited->submitShipment();
            case 'ROYALMAIL':
                return $this->inherited->submitShipment();
            case 'PUROLATOR':
                return $this->inherited->submitShipment();
            default:
                return $this->invalidCarrierResponse();
        }
    }
}
