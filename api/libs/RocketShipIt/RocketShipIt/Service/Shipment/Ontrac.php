<?php

namespace RocketShipIt\Service\Shipment;
use \RocketShipIt\Request;
use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\XmlParser;

class Ontrac extends \RocketShipIt\Service\Common
{
    var $links = array();
    public $packageObjs = array();
    
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function buildONTRACShipmentXml()
    {
        if (count($this->packageObjs) == 0) {
            $package = new \RocketShipIt\Package('ontrac');
        } else {
            $package = $this->packageObjs[0];
        }

        if ($this->imageType == '') {
            $this->imageType = '1';
        }

        $xml = new xmlBuilder();
        $xml->push('OnTracShipmentRequest');
            $xml->push('Shipments');
                $xml->push('Shipment');
                    $xml->element('UID', 'rocketshipit-'. $this->generateRandomString());
                    $xml->push('shipper');
                        $xml->element('Name', $this->shipper);
                        $xml->element('Addr1', $this->shipAddr1);
                        $xml->element('City', $this->shipCity);
                        $xml->element('State', $this->shipState);
                        $xml->element('Zip', $this->shipCode);
                        $xml->element('Contact', $this->shipContact);
                        $xml->element('Phone', $this->shipPhone);
                    $xml->pop(); //end shipper
                    $xml->push('consignee');
                        $xml->element('Name', $this->toName);
                        $xml->element('Addr1', $this->toAddr1);
                        if ($this->toAddr2 != '') {
                            $xml->element('Addr2', $this->toAddr2);
                        } else {
                            $xml->emptyelement('Addr2');
                        }
                        $xml->emptyelement('Addr3');
                        $xml->element('City', $this->toCity);
                        // CA, WA, OR, CO, AZ, NV, UT, and ID
                        $xml->element('State', $this->toState);
                        $xml->element('Zip', $this->toCode);
                        $xml->element('Contact', $this->toName);
                        $xml->element('Phone', $this->toPhone);
                    $xml->pop(); //end consignee
                    // S – Sunrise, G – Gold, H – Palletized Freight, C – OnTrac Ground
                    if ($this->service != '') {
                        $xml->element('Service', $this->service);
                    } else {
                        $xml->element('Service', 'C');
                    }
                    if ($this->signatureType != '') {
                        $xml->element('SignatureRequired', 'true');
                    } else {
                        $xml->element('SignatureRequired', 'false');
                    }
                    if ($this->residential != '') {
                        $xml->element('Residential', 'true');
                    } else {
                        $xml->element('Residential', 'false');
                    }
                    if ($this->residential != '') {
                        $xml->element('SaturdayDel', 'true');
                    } else {
                        $xml->element('SaturdayDel', 'false');
                    }
                    if ($this->monetaryValue != '') {
                        $xml->element('Declared', '500');
                    } else {
                        $xml->element('Declared', '0');
                    }
                    if ($this->codAmount != '') {
                        $xml->element('COD', $this->codAmount);
                    } else {
                        $xml->element('COD', '0');
                    }
                    //  NONE, UNSECURED, SECURED. SECURED indicates that secured funds only will be accepted for payment
                    $xml->element('CODType', 'NONE');
                    if ($package->weight != '') {
                        $xml->element('Weight', $package->weight);
                    } else {
                        $xml->element('Weight', '5');
                    }
                    if ($this->thirdPartyAccount != '') {
                        $xml->element('BillTo', $this->thirdPartyAccount);
                    } else {
                        $xml->element('BillTo', '0');
                    }
                    if ($this->instructions != '') {
                        $xml->element('Instructions', $this->instructions);
                    } else {
                        $xml->emptyelement('Instructions');
                    }
                    if ($this->referenceValue != '') {
                        $xml->element('Reference', $this->referenceValue);
                    } else {
                        $xml->emptyelement('Reference');
                    }
                    $xml->emptyelement('Reference2');
                    $xml->emptyelement('Reference3');
                    $xml->emptyelement('Tracking');
                    $xml->push('DIM');
                        if ($package->length != '') {
                            $xml->element('Length', $package->length);
                        } else {
                            $xml->element('Length', '0');
                        }
                        if ($package->width != '') {
                            $xml->element('Width', $package->width);
                        } else {
                            $xml->element('Width', '0');
                        }
                        if ($package->height != '') {
                            $xml->element('Height', $package->height);
                        } else {
                            $xml->element('Height', '0');
                        }
                    $xml->pop(); //end DIM
                    // 0 – No label
                    // 1 – pdf
                    // 6 – 4 x 5 EPL label
                    // 7 – 4 x 5 ZPL
                    $xml->element('LabelType', $this->imageType);
                    // Email notifications, not yet implemented
                    $xml->emptyelement('ShipEmail');
                    $xml->emptyelement('DelEmail');
                    if ($this->shipDate != '') {
                        $xml->element('ShipDate', $this->shipDate);
                    } else {
                        $xml->element('ShipDate', date('Y-m-d'));
                    }
                $xml->pop(); // end Shipment
            $xml->pop(); // end Shipments
        $xml->pop(); // end OnTracShipmentRequest


        return $xml->getXml();
    }

    function sendONTRACShipment()
    {
        $xmlString = $this->buildONTRACShipmentXml();
        $this->core->xmlResponse = $this->core->request('/V1/'. $this->accountNumber. '/shipments', 'post', array('pw' => $this->password), $xmlString);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function submitShipment()
    {
        return $this->processResponse();
    }

    public function processResponse()
    {
        $respArray = $this->sendONTRACShipment();
        //print_r($respArray);
        $resp = array();
        $resp['charges'] = $respArray['OnTracShipmentResponse']['Shipments']['Shipment']['TotalChrg'];
        $resp['trk_main'] = $respArray['OnTracShipmentResponse']['Shipments']['Shipment']['Tracking'];
        $resp['pkgs'][0]['pkg_trk_num'] = $respArray['OnTracShipmentResponse']['Shipments']['Shipment']['Tracking'];
        $resp['pkgs'][0]['label_fmt'] = $this->getLabelFormatType();

        // ZPL/EPL come across unencoded
        if ($this->imageType != '1') {
            $resp['pkgs'][0]['label_img'] = base64_encode($respArray['OnTracShipmentResponse']['Shipments']['Shipment']['Label']);
        } else {
            $resp['pkgs'][0]['label_img'] = $respArray['OnTracShipmentResponse']['Shipments']['Shipment']['Label'];
        }

        return $resp;
    }

    public function addPackageToShipment($packageObj)
    {
        $this->packageObjs[] = $packageObj;
    }

    public function getLabelFormatType()
    {
        $a = array(
            '1' => 'pdf',
            '6' => 'epl',
            '7' => 'zpl',
        );

        $labelFormat = '';
        if (!isset($a[$this->imageType])) {
            return $labelFormat;
        }

        return $a[$this->imageType];
    }

    private function generateRandomString()
    {
        $length = 16;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = "";
        
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }
        return $string;
    }
}
