<?php

namespace RocketShipIt\Service\Shipment;

class Usps extends \RocketShipIt\Service\Common
{
    public $customsLines;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);

        // Shipment api requires https but rate and other services
        // are not supported through https.
        $this->core->testingUrl = 'https://secure.shippingapis.com';
        $this->core->productionUrl = 'https://secure.shippingapis.com';
    }

    public function sanitize($in)
    {
        $in = str_replace('&', 'and', $in);

        return $in;
    }

    public function buildUSPSShipmentXml()
    {
        $xml = $this->core->xmlObject;

        $xml->push('DeliveryConfirmationV4.0Request', array(
            'USERID' => $this->userid)
        );
        $xml->element('Option', '1');
        $xml->push('ImageParameters');
        $xml->pop(); // end ImageParameters
            $xml->element('FromName', $this->shipContact);
        $xml->element('FromFirm', $this->shipper);
        $xml->element('FromAddress1', ' ');
        $xml->element('FromAddress2', $this->shipAddr1); // Why?  Cause usps requires addr2 but addr1 can be blank
            $xml->element('FromCity', $this->shipCity);
        $xml->element('FromState', $this->shipState);
        $xml->element('FromZip5', $this->shipCode);
        $xml->emptyelement('FromZip4');

        if ($this->toName != '' && $this->toCompany == '') {
            $xml->element('ToName', $this->sanitize($this->toName));
            $xml->emptyelement('ToFirm');
        }
        if ($this->toCompany != '' && $this->toName == '') {
            $xml->element('ToName', $this->sanitize($this->toCompany));
            $xml->emptyelement('ToFirm');
        }
        if ($this->toCompany != '' && $this->toName != '') {
            $xml->element('ToName', $this->sanitize($this->toName));
            $xml->element('ToFirm', $this->sanitize($this->toCompany));
        }

            // Why?  Cause usps requires addr2 but addr1 can be blank
            if ($this->toAddr2 != '') {
                $xml->element('ToAddress1', $this->toAddr2);
            } else {
                $xml->emptyelement('ToAddress1');
            }
        $xml->element('ToAddress2', $this->toAddr1);
        $xml->element('ToCity', $this->toCity);
        $xml->element('ToState', $this->toState);
        $xml->element('ToZip5', $this->toCode);
        $xml->emptyelement('ToZip4');
        if ($this->toEmail != '') {
            $xml->element('ToContactPreference', 'EMAIL');
            $xml->element('ToContactMessaging', $this->toEmail);
            $xml->element('ToContactEMail', $this->toEmail);
        }
        if ($this->weightOunces != '') {
            $xml->element('WeightInOunces', $this->weightOunces);
        } else {
            $xml->element('WeightInOunces', (int) ($this->weight * 16));
        }

        $xml->element('ServiceType', $this->service);
        $xml->emptyelement('POZipCode');
        $xml->element('ImageType', $this->imageType);
        $xml->emptyelement('LabelDate');

        if ($this->packagingType != '') {
            $xml->element('Container', $this->packagingType);
        }

        if ($this->referenceValue != '') {
            $xml->element('CustomerRefNo', $this->referenceValue);
        }
        if ($this->addons != '') {
            $xml->push('ExtraServices');
            foreach ($this->mapAddons($this->addons) as $addon) {
                $xml->element('ExtraService', $addon);
            }
            $xml->pop();
        }
        $xml->pop(); // end DeliveryConfirmationV3.0Request

        $xmlString = $xml->getXml();

        return 'API=DeliveryConfirmationV4&XML='.$xmlString;
    }

    public function buildUSPSReturnShipmentXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('EMRSV4.0Request', array('USERID' => $this->userid));
        $xml->element('Option', 'LEFTWINDOW');
        $xml->element('CustomerName', $this->fromName);
        $xml->emptyelement('CustomerAddress1');
        if ($this->fromAddr1) {
            $xml->element('CustomerAddress2', $this->fromAddr1);
        } else {
            $xml->emptyelement('CustomerAddress2');
        }
        $xml->element('CustomerCity', $this->fromCity);
        $xml->element('CustomerState', $this->fromState);
        $xml->element('CustomerZip5', $this->toCode);
        if ($this->fromExtendedCode) {
            $xml->element('CustomerZip4', $this->fromExtendedCode);
        } else {
            $xml->element('CustomerZip4', ' ');
        }

        $xml->element('RetailerName', $this->shipper);
        $xml->element('RetailerAddress', $this->shipAddr1);
        $xml->element('PermitNumber', $this->permitNumber);
        $xml->element('PermitIssuingPOCity', $this->permitIssuingPOCity);
        $xml->element('PermitIssuingPOState', $this->permitIssuingPOState);
        $xml->element('PermitIssuingPOZip5', $this->permitIssuingPOZip5);
        $xml->element('PDUFirmName', $this->pduFirmName);
        $xml->element('PDUPOBox', $this->pduPOBox);
        $xml->element('PDUCity', $this->pduCity);
        $xml->element('PDUState', $this->pduState);
        $xml->element('PDUZip5', $this->pduZip5);
        $xml->element('PDUZip4', $this->pduZip4);
        $xml->element('ServiceType', $this->service);
        if ($this->insuredValue != '') {
            $xml->element('DeliveryConfirmation', 'True');
            $xml->element('InsuranceValue', $this->insuredValue);
        } else {
            $xml->element('DeliveryConfirmation', 'False');
            $xml->element('InsuranceValue', ' ');
        }
        $xml->element('WeightInPounds', $this->weightPounds);
        $xml->element('WeightInOunces', $this->weightOunces);
        $xml->element('RMA', $this->referenceValue);
        $xml->element('RMAPICFlag', 'False');
        $xml->element('ImageType', $this->imageType);
            // $xml->element('RMABarcode', 'True');

            // Email confirmation
            if ($this->returnEmailAddress != '') {
                $xml->element('SenderName', $this->returnEmailFromName);
                $xml->element('SenderEMail ', $this->returnFromEmailAddress);
                $xml->element('RecipientName', $this->returnToName);
                $xml->element('RecipientEMail', $this->returnEmailAddress);
            }

        $xml->element('AllowNonCleansedDestAddr', 'False');

        $xml->pop(); // end EMRSV4.0Request

        $xmlString = $xml->getXml();

        return 'API=MerchandiseReturnV4&XML='.$xmlString;
    }

    public function sendUSPSshipment()
    {
        if ($this->permitNumber != '') {
            $xmlString = $this->buildUSPSReturnShipmentXml();
        } else {
            $xmlString = $this->buildUSPSShipmentXml();
        }

        $this->core->xmlSent = $xmlString;
        $this->core->xmlResponse = $this->core->request('ShippingAPI.dll', $xmlString);

        return $this->simplifyUSPSV4Response($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyUSPSV4Response($xmlArray)
    {
        $simpleResp = array(
            'charges' => 0.00,
            'trk_main' => '',
            'pkgs' => array(),
        );
        if (!isset($xmlArray['DeliveryConfirmationV4.0Response'])) {
            return $xmlArray;
        }
        $carrierResp = $xmlArray['DeliveryConfirmationV4.0Response'];

        if (isset($carrierResp['Postage'])) {
            $simpleResp['charges'] = $carrierResp['Postage'];
        }

        if (isset($carrierResp['DeliveryConfirmationNumber'])) {
            $simpleResp['trk_main'] = $carrierResp['DeliveryConfirmationNumber'];
        }

        if (isset($carrierResp['DeliveryConfirmationLabel'])) {
            $simpleResp['pkgs'][0]['pkg_trk_num'] = $simpleResp['trk_main'];
            $simpleResp['pkgs'][0]['label_fmt'] = $this->imageType;
            $simpleResp['pkgs'][0]['label_img'] = $carrierResp['DeliveryConfirmationLabel'];
        }

        return $simpleResp;
    }

    public function simplifyUSPSResponse($xmlArray)
    {
        // If error in the array return the error.
        if (in_array('Error', array_keys($xmlArray))) {
            return ('Error confirming shipment: '.$xmlArray['Error']['Description']);
        }

        $trk_main = '';

        if (in_array('DeliveryConfirmationV3.0Response', array_keys($xmlArray))) {
            $trk_main = $xmlArray['DeliveryConfirmationV3.0Response']['DeliveryConfirmationNumber'];
            $label = $xmlArray['DeliveryConfirmationV3.0Response']['DeliveryConfirmationLabel'];

            return array(
                'charges' => '0.00',
                'trk_main' => $trk_main,
                'pkgs' => array(
                    0 => array(
                        'pkg_trk_num' => $trk_main,
                        'label_fmt' => 'pdf',
                        'label_img' => $label
                    )
                )
            );
        }

        if (in_array('EMRSV4.0Response', array_keys($xmlArray))) {
            if (isset($xmlArray['EMRSV4.0Response']['MerchandiseReturnNumber'])) {
                $trk_main = $xmlArray['EMRSV4.0Response']['MerchandiseReturnNumber'];
            }
            if (isset($xmlArray['EMRSV4.0Response']['DeliveryConfirmationNumber'])) {
                $trk_main = $xmlArray['EMRSV4.0Response']['DeliveryConfirmationNumber'];
            }
            $label = $xmlArray['EMRSV4.0Response']['MerchandiseReturnLabel'];

            return array(
                'charges' => '0.00',
                'trk_main' => $trk_main,
                'pkgs' => array(
                    0 => array(
                        'pkg_trk_num' => $trk_main,
                        'label_fmt' => 'pdf',
                        'label_img' => $label
                    )
                )
            );
        }

        return $xmlArray;
    }

    public function mapAddons($addons)
    {
        $map = array(
            'US-A-INS' => '1',
            'US-A-SC' => '15',
            'US-A-DC' => '13',
        );

        $adds = array();
        foreach (explode(',', $addons) as $addon) {
            $addon = trim($addon);
            if (isset($map[$addon])) {
                $adds[] = $map[$addon];
            } else {
                $adds[] = $addon;
            }
        }

        return $adds;
    }

    /**
     * Creates random string of alphanumeric characters.
     *
     * @return string
     */
    public function generateRandomString()
    {
        $length = 128;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }

        return $string;
    }
}
