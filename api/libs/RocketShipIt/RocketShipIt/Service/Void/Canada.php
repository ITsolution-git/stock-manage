<?php

namespace RocketShipIt\Service\Void;

class Canada extends \RocketShipIt\Service\Common implements \RocketShipIt\VoidInterface
{
    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function voidShipment()
    {
        $header = array(
            'Accept: application/vnd.cpc.ncshipment-v4+xml',
            'Content-Type: application/vnd.cpc.ncshipment-v4+xml',
            'Accept-Language: en-CA',
        );
        $this->core->request($this->buildVoidUrl(), $this->buildRefundXml(), $header, 'POST');

        return $this->core->request->getStatusCode();
    }

    public function buildVoidUrl()
    {
        // TODO fetch shipment info and grab link from that incase it changes.
        return sprintf('/rs/%s/ncshipment/%s/refund', $this->accountNumber, $this->shipmentIdentification);
    }

    public function voidPackage()
    {
        return array('error' => 'not implemented');
    }

    public function buildRefundXml()
    {
        return '<non-contract-shipment-refund-request xmlns="http://www.canadapost.ca/ws/ncshipment-v4">
<email>'. $this->shipEmail. '</email>
</non-contract-shipment-refund-request>';
    }
}
