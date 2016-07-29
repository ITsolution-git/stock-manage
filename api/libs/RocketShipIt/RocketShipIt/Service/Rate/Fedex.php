<?php

namespace RocketShipIt\Service\Rate;

use RocketShipIt\Helper\XmlBuilder;

class Fedex extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    public $packageCount;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function getAllRates()
    {
        return $this->getRate(true);
    }

    public function getRate($allAvailableRates = false)
    {
        $xmlString = $this->buildFEDEXRateXml($allAvailableRates);

        $this->core->request($xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function addPackageToShipment($package)
    {
        ++$this->packageCount;

        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = new xmlBuilder(true);
        }

        $xml = $this->core->packagesObject;

        if (isset($this->freightAccountNumber) && $this->freightAccountNumber != '') {
            $xml->push('ns:LineItems');

                $xml->element('ns:FreightClass', $this->freightClass);
                $xml->element('ns:Packaging', 'PALLET');

                $xml->push('ns:Weight');
                if ($package->weightUnit != '') {
                    $xml->element('ns:Units', $package->weightUnit);
                } else {
                    $xml->element('ns:Units', $this->weightUnit);
                }
                $xml->element('ns:Value', $package->weight);
                $xml->pop(); // end Weight
                if ($package->length != '') {
                    $xml->push('ns:Dimensions');
                    if ($package->length != '') {
                        $xml->element('ns:Length', $package->length);
                    }
                    if ($package->width != '') {
                        $xml->element('ns:Width', $package->width);
                    }
                    if ($package->height != '') {
                        $xml->element('ns:Height', $package->height);
                    }
                    if ($package->lengthUnit != '') {
                        $xml->element('ns:Units', $package->lengthUnit);
                    } else {
                        $xml->element('ns:Units', $this->lengthUnit);
                    }
                    $xml->pop(); // end Dimensions
                }

            $xml->pop(); // end LineItems
        } else {
            $xml->push('ns:RequestedPackageLineItems');
            $xml->element('ns:SequenceNumber', $this->packageCount);
            if ($this->groupPackageCount != '') {
                $xml->element('ns:GroupPackageCount', $this->groupPackageCount);
            } else {
                $xml->element('ns:GroupPackageCount', '1');
            }
            if ($package->insuredValue != '' && $package->insuredCurrency != '') {
                $xml->push('ns:InsuredValue');
                $xml->element('ns:Currency', $package->insuredCurrency);
                $xml->element('ns:Amount', $package->insuredValue);
                $xml->pop(); // end InsuredValue
            }
            $xml->push('ns:Weight');
            if ($package->weightUnit != '') {
                $xml->element('ns:Units', $package->weightUnit);
            } else {
                $xml->element('ns:Units', $this->weightUnit);
            }
            $xml->element('ns:Value', $package->weight);
            $xml->pop(); // end Weight
                if ($package->length != '') {
                    $xml->push('ns:Dimensions');
                    if ($package->length != '') {
                        $xml->element('ns:Length', $package->length);
                    }
                    if ($package->width != '') {
                        $xml->element('ns:Width', $package->width);
                    }
                    if ($package->height != '') {
                        $xml->element('ns:Height', $package->height);
                    }
                    if ($package->lengthUnit != '') {
                        $xml->element('ns:Units', $package->lengthUnit);
                    } else {
                        $xml->element('ns:Units', $this->lengthUnit);
                    }
                    $xml->pop(); // end Dimensions
                }
            if ($this->signatureType != '') {
                $xml->push('ns:SpecialServicesRequested');
                $xml->element('ns:SpecialServiceTypes', 'SIGNATURE_OPTION');
                $xml->push('ns:SignatureOptionDetail');
                $xml->element('ns:OptionType', $this->signatureType);
                $xml->pop();
                $xml->pop(); // end ShipmentSpecialServicesRequested
            }
            $xml->pop(); // end RequestedPackageLineItems
        }

        $this->core->packagesObject = $xml;

        return true;
    }

    public function buildFEDEXRateXml($allAvailableRates = false)
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:RateRequest', array(
            'xmlns:ns' => 'http://fedex.com/ws/rate/v16',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://fedex.com/ws/rate/v16 RateService v16.xsd',
        ));

        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('ns:Version');
        $xml->element('ns:ServiceId', 'crs');
        $xml->element('ns:Major', '16');
        $xml->element('ns:Intermediate', '0');
        $xml->element('ns:Minor', '0');
        $xml->pop(); // end Version
        $xml->element('ns:ReturnTransitAndCommit', 'true');
        $xml->push('ns:RequestedShipment');
        if ($this->shipDate != '') {
            $xml->element('ns:ShipTimestamp', $this->shipDate);
        } else {
            // FedEx uses ISO8601 style timestamps
            $xml->element('ns:ShipTimestamp', date('c'));
        }
        if ($this->dropoffType == '') {
            $xml->element('ns:DropoffType', 'REGULAR_PICKUP');
        } else {
            $xml->element('ns:DropoffType', $this->dropoffType);
        }
        if (!$allAvailableRates) {
            $xml->element('ns:ServiceType', $this->service);
        }

        if($this->freightAccountNumber == ''){
            $xml->element('ns:PackagingType', $this->packagingType);
        }

        $xml->push('ns:Shipper');
        $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->shipAddr1);
        if (isset($this->shipAddr2) && $this->shipAddr2 != '') {
            $xml->element('ns:StreetLines', $this->shipAddr2);
        }
        $xml->element('ns:City', $this->shipCity);
        $xml->element('ns:StateOrProvinceCode', $this->shipState);
        $xml->element('ns:PostalCode', $this->shipCode);
        $xml->element('ns:CountryCode', $this->shipCountry);
        $xml->pop(); // end Address
        $xml->pop(); // end Shipper
        $xml->push('ns:Recipient');

        if($this->freightAccountNumber == ''){
            $xml->element('ns:AccountNumber', 'ACCOUNT');
        }

        if ($this->toName != '' || $this->toCompany != '') {
            $xml->push('ns:Contact');
            if ($this->toName != '') {
                $xml->element('ns:PersonName', $this->toName);
            }
            if ($this->toCompany != '') {
                $xml->element('ns:CompanyName', $this->toCompany);
            }
            $xml->pop(); // end Contact
        }

        $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->toAddr1);
        if (isset($this->toAddr2) && $this->toAddr2 != '') {
            $xml->element('ns:StreetLines', $this->toAddr2);
        }
        $xml->element('ns:City', $this->toCity);
        if ($this->toState != '') {
            $xml->element('ns:StateOrProvinceCode', $this->toState);
        }
        if ($this->toCode != '') {
            $xml->element('ns:PostalCode', $this->toCode);
        }
        $xml->element('ns:CountryCode', $this->toCountry);
        if ($this->residential != '') {
            $this->residentialAddressIndicator = $this->residential;
        }
        if ($this->residentialAddressIndicator != '') {
            $xml->element('ns:Residential', $this->residentialAddressIndicator);
        } else {
            // Ground Home Delivery requires residential address
            if ($this->service == 'GROUND_HOME_DELIVERY') {
                $xml->element('ns:Residential', '1');
            }
        }
        $xml->pop(); // end Address
        $xml->pop(); // end Recipient

        if ($this->freightAccountNumber != '') {
            $xml->push('ns:ShippingChargesPayment');
            $xml->element('ns:PaymentType', 'SENDER');
                $xml->push('ns:Payor');
                    $xml->push('ns:ResponsibleParty');
                    $xml->element('ns:AccountNumber', $this->freightAccountNumber);
                    $xml->pop( ); //end ResponsibleParty
                $xml->pop( ); //end Payor
            $xml->pop( ); //end ShippingChargesPayment
        }

        if ($this->shipCountry != $this->toCountry || $this->shipCountry == 'IN') {
            $xml->push('ns:CustomsClearanceDetail');
            $xml->push('ns:DutiesPayment');
            $xml->element('ns:PaymentType', $this->paymentType);
            $xml->push('ns:Payor');
            $xml->push('ns:ResponsibleParty');
            if ($this->customsAccountNumber != '') {
                $xml->element('ns:AccountNumber', $this->customsAccountNumber);
            } else {
                $xml->element('ns:AccountNumber', $this->accountNumber);
            }
            $xml->push('ns:Contact');
            $xml->element('ns:CompanyName', $this->shipper);
            $xml->pop(); // end Contact
            $xml->push('ns:Address');
            $xml->element('ns:CountryCode', $this->shipCountry);
            $xml->pop(); // end Address
            $xml->pop(); //end ResponsibleParty
            $xml->pop(); // end Payor
            $xml->pop(); // end DutiesPayment
            $xml->push('ns:CustomsValue');
            $xml->element('ns:Currency', $this->customsCurrency);
            $xml->element('ns:Amount', $this->customsValue);
            $xml->pop();
            if (isset($this->core->customsObject)) {
                $xml->append($this->core->customsObject->getXML());
            }
            $xml->push('ns:CommercialInvoice');
            $xml->element('ns:Purpose', 'SAMPLE');
            $xml->pop();
            if ($this->complianceStatement != '') {
                $xml->push('ns:ExportDetail');
                $xml->element('ns:ExportComplianceStatement', $this->complianceStatement);
                $xml->pop(); // end ExportDetail
            }
            $xml->pop();
        }

        if ($this->smartPostIndicia != '') {
            $xml->push('ns:SmartPostDetail');
            $xml->element('ns:Indicia', $this->smartPostIndicia);
            $xml->element('ns:HubId', $this->smartPostHubId);
            $xml->pop(); // end SmartPostDetail
        }
        if ($this->saturdayDelivery == 'YES') {
            $xml->push('ns:SpecialServicesRequested');
            $xml->element('ns:SpecialServiceTypes', 'SATURDAY_DELIVERY');
            $xml->pop(); // end ShipmentSpecialServicesRequested
        }

        if (isset($this->freightAccountNumber) && $this->freightAccountNumber != '') {
            $xml->push('ns:FreightShipmentDetail');
            $xml->element('ns:FedExFreightAccountNumber', $this->freightAccountNumber);

                $xml->push('ns:FedExFreightBillingContactAndAddress');

                    $xml->push('ns:Address');
                    $xml->element('ns:StreetLines', $this->shipAddr1);
                    if(isset($this->shipAddr2) && $this->shipAddr2 != ''){
                        $xml->element('ns:StreetLines', $this->shipAddr2);
                    }
                    $xml->element('ns:City', $this->shipCity);
                    $xml->element('ns:StateOrProvinceCode', $this->shipState);
                    $xml->element('ns:PostalCode', $this->shipCode);
                    $xml->element('ns:CountryCode', $this->shipCountry);
                    $xml->pop(); // end Address

                $xml->pop(); // end FedExFreightBillingContactAndAddress

                $xml->element('ns:Role', 'SHIPPER');

                if (!isset($this->core->packagesObject)) {
                    $xml->push('ns:LineItems');

                        $xml->element('ns:FreightClass', $this->freightClass);
                        $xml->element('ns:Packaging', 'PALLET');

                        $xml->push('ns:Weight');
                        $xml->element('ns:Units', $this->weightUnit);
                        $xml->element('ns:Value', $this->weight);
                        $xml->pop(); // end Weight
                        if ($this->length != '') {
                            $xml->push('ns:Dimensions');
                            if ($this->length != '') {
                                $xml->element('ns:Length', $this->length);
                            }
                            if ($this->width != '') {
                                $xml->element('ns:Width', $this->width);
                            }
                            if ($this->height != '') {
                                $xml->element('ns:Height', $this->height);
                            }
                            $xml->element('ns:Units', $this->lengthUnit);
                            $xml->pop(); // end Dimensions
                        }

                    $xml->pop(); // end LineItems
                } else {
                    $xml->append($this->core->packagesObject->getXml());
                }

            $xml->pop(); // end FreightShipmentDetail

            $xml->element('ns:RateRequestTypes', 'LIST');
        } else {
            $xml->element('ns:RateRequestTypes', 'LIST');

            $xml->element('ns:PackageCount', ($this->packageCount == 0) ? '1' : $this->packageCount);
            if (!isset($this->core->packagesObject)) {
                $xml->push('ns:RequestedPackageLineItems');
                $xml->element('ns:SequenceNumber', '1');
                $xml->element('ns:GroupPackageCount', '1');
                if ($this->insuredValue != '' && $this->insuredCurrency != '') {
                    $xml->push('ns:InsuredValue');
                    $xml->element('ns:Currency', $this->insuredCurrency);
                    $xml->element('ns:Amount', $this->insuredValue);
                    $xml->pop(); // end InsuredValue
                }
                $xml->push('ns:Weight');
                $xml->element('ns:Units', $this->weightUnit);
                $xml->element('ns:Value', $this->weight);
                $xml->pop(); // end Weight
                        if ($this->length != '') {
                            $xml->push('ns:Dimensions');
                            if ($this->length != '') {
                                $xml->element('ns:Length', $this->length);
                            }
                            if ($this->width != '') {
                                $xml->element('ns:Width', $this->width);
                            }
                            if ($this->height != '') {
                                $xml->element('ns:Height', $this->height);
                            }
                            $xml->element('ns:Units', $this->lengthUnit);
                            $xml->pop(); // end Dimensions
                        }
                if ($this->signatureType != '') {
                    $xml->push('ns:SpecialServicesRequested');
                    $xml->element('ns:SpecialServiceTypes', 'SIGNATURE_OPTION');
                    $xml->push('ns:SignatureOptionDetail');
                    $xml->element('ns:OptionType', $this->signatureType);
                    $xml->pop();
                    $xml->pop(); // end ShipmentSpecialServicesRequested
                }
                $xml->pop(); // end RequestedPackageLineItems
            } else {
                $xml->append($this->core->packagesObject->getXml());
            }
        }
        $xml->pop(); // end RequestedShipment
        $xml->pop(); // end RateRequest

        $xmlString = $xml->getXml();

        return $xmlString;
    }

    public function getSimpleRate()
    {
        $fedex = $this->getRate();

        if (!isset($fedex['RateReply'])) {
            $errorArray = array();
            $errorArray['error'] = 'Error not given';
            $errorArray['response'] = $fedex;

            return $errorArray;
        }

        if (isset($fedex['RateReply']['HighestSeverity']) && $fedex['RateReply']['HighestSeverity'] != 'ERROR') {
            return $fedex['RateReply']['RateReplyDetails']['RatedShipmentDetails'][1]['ShipmentRateDetail']['TotalNetCharge']['Amount'];
        } else {
            return $fedex['RateReply']['Notifications']['Message'];
        }

        return $fedex;
    }

    public function getSimpleRates($user_func = null)
    {
        $fedex = $this->getAllRates();
        $errorArray = array();

        if (isset($fedex['Fault'])) {
            if (!isset($fedex['Fault']['detail']['fault']['details'])) {
                return $fedex;
            }
            foreach ($fedex['Fault']['detail']['fault']['details'] as $failureType => $details) {
                $errorArray['error'][$failureType] = $details['message'];
            }

            return $errorArray;
        }

        if (!isset($fedex['RateReply']['RateReplyDetails'])) {
            if (!isset($fedex['RateReply']['Notifications'])) {
                $errorArray['error'] = 'Error not given';
                $errorArray['response'] = $fedex;

                return $errorArray;
            }

            if (isset($fedex['RateReply']['Notifications']['Message'])) {
                $errorMessage = $fedex['RateReply']['Notifications']['Message'];
            } else {
                $errorMessage = array();
                foreach ($fedex['RateReply']['Notifications'] as $notif) {
                    if (!isset($notif['Message'])) {
                        $errorArray['error'] = 'Error not given';
                        $errorArray['response'] = $fedex;

                        return $errorArray;
                    }
                    $errorMessage[] = $notif['Message'];
                }
            }

            $errorArray['error'] = $errorMessage;

            return $errorArray;
        }

        try {
            $service = $fedex['RateReply']['RateReplyDetails'];

            $rates = array();
            if (array_values($service) === $service) {
                foreach ($service as $s) {
                    $serviceType = $this->core->getServiceDescriptionFromCode($s['ServiceType']);
                    if (isset($s['RatedShipmentDetails'][0])) {
                        // Find the RatedShipmentDetails with the correct currency:
                        $i = 0;
                        do {
                            $value = $s['RatedShipmentDetails'][$i]['ShipmentRateDetail']['TotalNetCharge']['Amount'];
                            $currency = $s['RatedShipmentDetails'][$i]['ShipmentRateDetail']['TotalNetCharge']['Currency'];
                            ++$i;
                        } while ($currency != $this->currency && isset($s['RatedShipmentDetails'][$i]));
                        if ($this->currency != '' && $currency != $this->currency) {
                            // Didn't find a rate in the correct currency
                            continue;
                        }
                    } else {
                        $value = $s['RatedShipmentDetails']['ShipmentRateDetail']['TotalNetCharge']['Amount'];
                    }
                    //$rates["$serviceType"] = $value;
                    $simpleRate = array('desc' => $serviceType, 'rate' => $value, 'service_code' => $s['ServiceType']);
                    if (!empty($user_func)) {
                        $tmp = explode('\\', __CLASS__);
                        $simpleRate = call_user_func($user_func, end($tmp), $s, $simpleRate);
                    }
                    $rates[] = $simpleRate;
                }
            } else {
                $serviceType = $this->core->getServiceDescriptionFromCode($service['ServiceType']);
                // Find the RatedShipmentDetails with the correct currency:
                $i = 0;
                do {
                    $value = $service['RatedShipmentDetails'][$i]['ShipmentRateDetail']['TotalNetCharge']['Amount'];
                    $currency = $service['RatedShipmentDetails'][$i]['ShipmentRateDetail']['TotalNetCharge']['Currency'];
                    ++$i;
                } while ($currency != $this->currency && isset($s['RatedShipmentDetails'][$i]));
                if ($currency != $this->currency) {
                    // Didn't find a rate in the correct currency
                    return array();
                }
                //$rates["$serviceType"] = $value;
                $simpleRate = array('desc' => $serviceType, 'rate' => $value, 'service_code' => $service['ServiceType']);
                if (!empty($user_func)) {
                    $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $service, $simpleRate);
                }
                $rates[] = $simpleRate;
            }

            return $rates;
        } catch (Exception $e) {
            try {
                $errorMessage = $fedex['RateReply']['Notifications']['Message'];
                $errorArray['error'] = $errorMessage;

                return $errorArray;
            } catch (Exception $e) {
                return $fedex;
            }
        }
    }
}
