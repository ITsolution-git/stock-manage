<?php

namespace RocketShipIt\Service\Rate;

use RocketShipIt\Helper\XmlBuilder;

/**
 * Main Rate class for producing rates for various packages/shipments.
 *
 * This class is a wrapper for use with all carriers to produce rates
 * Valid carriers are: UPS, USPS, and FedEx.
 */
class Ups extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    public $packageCount;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    private function updateStatus()
    {
        if (isset($this->responseArray['RatingServiceSelectionResponse']['Response']['ResponseStatusCode'])) {
            if ($this->responseArray['RatingServiceSelectionResponse']['Response']['ResponseStatusCode'] == 1) {
                $this->status = 'success';
            }
            if ($this->responseArray['RatingServiceSelectionResponse']['Response']['ResponseStatusCode'] == 0) {
                $this->status = 'failure';
            }
        }
    }

    public function processResponse()
    {
        $xmlArray = $this->arrayFromXml($this->core->xmlResponse);
        $this->responseArray = $xmlArray;

        $this->updateStatus();
    }

    public function getRate($requestOption = 'Rate')
    {
        $xmlString = $this->buildUPSRateXml($requestOption);

        $this->core->request('Rate', $xmlString);

        $this->processResponse();

        return $this->responseArray;
    }

    public function addPackageToShipment($package)
    {
        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = new xmlBuilder(true);
        }

        $xml = $this->core->packagesObject;

        $xml->push('Package');
        $xml->push('PackagingType');
        $xml->element('Code', $package->packagingType);
                //$xml->element('Description', $this->packageTypeDescription);
            $xml->pop(); // close PacakgeType
                if ($package->length != '') {
                    $xml->push('Dimensions');
                    $xml->push('UnitOfMeasurement');
                    $xml->element('Code', $package->lengthUnit);
                    $xml->pop(); // close UnitOfMeasurement
                            $xml->element('Length', $package->length);
                    $xml->element('Width', $package->width);
                    $xml->element('Height', $package->height);
                    $xml->pop(); // close Dimensions
                }
            //$xml->element('Description', $this->packageDescription);
            $xml->push('PackageWeight');
        $xml->push('UnitOfMeasurement');
        $xml->element('Code', $package->weightUnit);
        $xml->pop(); // close UnitOfMeasurement
                $xml->element('Weight', $package->weight);
        $xml->pop(); // close PackageWeight
            if ($package->largePackage != '') {
                $xml->element('LargePackageIndicator', '1');
            }
        if ($package->insuredValue != '' || $package->signatureType != '') {
            // Change for COD
                $xml->push('PackageServiceOptions');
            if ($package->insuredValue != '') {
                $xml->push('InsuredValue');
                $xml->element('CurrencyCode', $package->insuredCurrency);
                $xml->element('MonetaryValue', $package->insuredValue);
                $xml->pop(); // close InsuredValue
            }
            if ($package->signatureType != '' && $package->signatureType != 'INDIRECT') {
                $xml->push('DeliveryConfirmation');
                $xml->element('DCISType', $this->core->getPackageDCISType($package->signatureType));
                $xml->pop(); // end DeliveryConfirmation
            }
            $xml->pop(); // close PackageServiceOptions
        }
        $xml->pop(); // close Package

        $this->core->packagesObject = $xml;

        return true;
    }

    public function buildUPSRateXml($requestOption = 'Rate')
    {
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->push('RatingServiceSelectionRequest');
        $xml->push('Request');
        $xml->element('RequestAction', 'Rate');
        $xml->element('RequestOption', $requestOption);
        $xml->push('TransactionReference'); // Not required
                    $xml->element('CustomerContext', 'RocketShipIt'); // Not required
                    //$xml->element('XpciVersion', '1.0'); // Not required
                $xml->pop(); // close TransactionReference, not required
            $xml->pop(); // close Request
            $xml->push('PickupType');
        if ($this->PickupType == '') {
            $xml->element('Code', '01');
        } else {
            $xml->element('Code', $this->PickupType); // TODO: insert link to code values
        }
        if ($this->pickupDescription != '') {
            //$xml->element('Description', $this->pickupDescription);
        }
        $xml->pop(); // close PickupType
            if ($this->customerClassification != '') {
                $xml->push('CustomerClassification');
                $xml->element('Code', $this->customerClassification);
                $xml->pop(); //end CustomerClassification
            }
        $xml->push('Shipment');
                //$xml->element('Description', $this->shipmentDescription);
                if ($this->saturdayDelivery != '') {
                    $xml->push('ShipmentServiceOptions');
                    $xml->element('SaturdayDelivery', $this->saturdayDelivery);
                    $xml->pop(); // end ShipmentServiceOptions
                }
        $xml->push('Shipper');
        $xml->element('ShipperNumber', $this->accountNumber);
        $xml->push('Address');
        $xml->element('AddressLine1', $this->shipAddr1);
        if ($this->shipAddr2 != '') {
            $xml->element('AddressLine2', $this->shipAddr2);
        }
        if ($this->shipAddr3 != '') {
            $xml->element('AddressLine3', $this->shipAddr3);
        }
        if ($this->shipCity != '') {
            $xml->element('City', $this->shipCity);
        }
        $xml->element('StateProvinceCode', $this->shipState);
        $xml->element('PostalCode', $this->shipCode);
        if ($this->shipCountry != '') {
            $xml->element('CountryCode', $this->shipCountry);
        } else {
            $xml->element('CountryCode', 'US');
        }
        $xml->pop(); // close Address
                $xml->pop(); // close Shipper
                $xml->push('ShipTo');
        if ($this->toCompany != '') {
            $xml->element('CompanyName', $this->toCompany);
        }
        $xml->push('Address');
        if ($this->toAddr1 != '') {
            $xml->element('AddressLine1', $this->toAddr1);
        }
        if ($this->toAddr2 != '') {
            $xml->element('AddressLine2', $this->toAddr2);
        }
        if ($this->toAddr3 != '') {
            $xml->element('AddressLine3', $this->toAddr3);
        }
        if ($this->toCity != '') {
            $xml->element('City', $this->toCity);
        }
        if ($this->toState != '') {
            $xml->element('StateProvinceCode', $this->toState);
        }
        $xml->element('PostalCode', $this->toCode);
        if ($this->toCountry != '') {
            $xml->element('CountryCode', $this->toCountry);
        } else {
            $xml->element('CountryCode', 'US');
        }
        if ($this->residentialAddressIndicator == '1') {
            $xml->element('ResidentialAddressIndicator', '1');
        }
        $xml->pop(); // close Address
                $xml->pop(); // close ShipTo
            if ($this->fromName != '') {
                $xml->push('ShipFrom');
                $xml->element('CompanyName', $this->fromName);
                    //$xml->element('AttentionName', $this->fromAttentionName);
                    //$xml->element('PhoneNumber', $this->fromPhoneNumber);
                    //$xml->element('FaxNumber', $this->fromFaxNumber);
                    $xml->push('Address');
                $xml->element('AddressLine1', $this->fromAddr1);
                $xml->element('AddressLine2', $this->fromAddr2);
                $xml->element('AddressLine3', $this->fromAddr3);
                $xml->element('City', $this->fromCity);
                $xml->element('PostalCode', $this->fromCode);
                if ($this->fromCountry != '') {
                    $xml->element('CountryCode', $this->fromCountry);
                } else {
                    $xml->element('CountryCode', 'US');
                }
                $xml->pop(); // close Address
                $xml->pop(); // close ShipFrom
            }
        if ($this->service != '') {
            $xml->push('Service');
            $originCountry = $this->fromCountry != '' ? $this->fromCountry : $this->shipCountry;
            if ($originCountry == 'CA') {
                // For packages originating from Canada, the rate and shipping service codes can differ!
                        // Ref: "The service codes are defined in Appendix E - Service Codes of both the Ship API and Rating API Developers Guides. You will notice two columns in this reference for each API. Please reference p.120 of the Rating Package XML Developers Guide for the full resource."
                        if ((int) $this->service == 2) {
                            // A rate service = '02', in Canada, actually needs to be sent as '08'.
                            $this->service = '08';
                        } elseif ((int) $this->service == 13) {
                            // A rate service = '13', in Canada, actually needs to be sent as '65'.
                            $this->service = '65';
                        }
            }
            $xml->element('Code', $this->core->mapMailInnovationServiceCodes($this->service));
            $xml->pop(); // close Service
        }
        if (!isset($this->core->packagesObject)) {
            $xml->push('Package');
            $xml->push('PackagingType');
            $xml->element('Code', $this->packagingType);
                        //$xml->element('Description', $this->packageTypeDescription);
                    $xml->pop(); // close PacakgeType
                        if ($this->length != '' && $this->width != '' && $this->height != '') {
                            $xml->push('Dimensions');
                            $xml->push('UnitOfMeasurement');
                            $xml->element('Code', $this->lengthUnit);
                            $xml->pop(); // close UnitOfMeasurement
                                    $xml->element('Length', $this->length);
                            $xml->element('Width', $this->width);
                            $xml->element('Height', $this->height);
                            $xml->pop(); // close Dimensions
                        }
                    //$xml->element('Description', $this->packageDescription);
                    if (isset($this->weightUnit)) {
                        $xml->push('PackageWeight');
                        $xml->push('UnitOfMeasurement');
                        $xml->element('Code', $this->weightUnit);
                        $xml->pop(); // close UnitOfMeasurement
                            if ($this->weight != '') {
                                $xml->element('Weight', $this->weight);
                            } else {
                                $xml->element('Weight', '0');
                            }
                        $xml->pop(); // close PackageWeight
                    }
            if ($this->insuredValue != '' || $this->insuredCurrency != '' || $this->signatureType != '') {
                // Change for COD
                        $xml->push('PackageServiceOptions');
                if ($this->insuredValue != '') {
                    $xml->push('InsuredValue');
                    $xml->element('CurrencyCode', $this->insuredCurrency);
                    $xml->element('MonetaryValue', $this->insuredValue);
                    $xml->pop(); // close InsuredValue
                }
                if ($this->signatureType != '' && $this->signatureType != 'INDIRECT') {
                    $xml->push('DeliveryConfirmation');
                    $xml->element('DCISType', $this->core->getPackageDCISType($this->signatureType));
                    $xml->pop(); // end DeliveryConfirmation
                }
                $xml->pop(); // close PackageServiceOptions
            }
            $xml->pop(); // close Package
        } else {
            $xmlString = $xml->getXml();
            $xmlString .= $this->core->packagesObject->getXml();
            $negotiatedXml = new xmlBuilder(true);
            if ($this->negotiatedRates == '1') {
                $negotiatedXml->push('RateInformation');
                $negotiatedXml->element('NegotiatedRatesIndicator', '1');
                $negotiatedXml->pop(); // close RateInformation
            }
            $xmlString .= $negotiatedXml->getXml();
            $xmlString .= '</Shipment>'."\n";
            $xmlString .= '</RatingServiceSelectionRequest>'."\n";

            return $xmlString;
        }
        if ($this->negotiatedRates == '1') {
            $xml->push('RateInformation');
            $xml->element('NegotiatedRatesIndicator', '1');
            $xml->pop(); // close RateInformation
        }
            //$xml->push('ShipmentServiceOptions');
            //$xml->pop(); // close ShipmentServiceOptions
            $xml->pop(); // close Shipment
        $xml->pop();

        // Convert xml object to a string
        $xmlString = $xml->getXml();

        return $xmlString;
    }

    public function getSimpleRate()
    {
        $upsArray = $this->getRate();
        $status = $upsArray['RatingServiceSelectionResponse']['Response']['ResponseStatusCode'];
        if ($status == '1') {
            $rate = $upsArray['RatingServiceSelectionResponse']['RatedShipment']['TotalCharges']['MonetaryValue'];

            return $rate;
        } else {
            $errorMessage = $upsArray['RatingServiceSelectionResponse']['Response']['Error']['ErrorDescription'];

            return $errorMessage;
        }
    }

    public function getSimpleRates($user_func = null)
    {
        $upsArray = $this->getAllRates();

        if (!isset($upsArray['RatingServiceSelectionResponse'])) {
            return array('error' => 'Unable to read response from UPS', 'response' => $upsArray);
        }

        $status = $upsArray['RatingServiceSelectionResponse']['Response']['ResponseStatusCode'];

        if ($status == '1') {
            $rate = $upsArray['RatingServiceSelectionResponse']['RatedShipment'][0]['TotalCharges']['MonetaryValue'];
            $service = $upsArray['RatingServiceSelectionResponse']['RatedShipment'];

            $rates = array();
            if (array_key_exists('Service', $service)) {
                $r = $service['Service']['Code'];
                $desc = $this->core->getServiceDescriptionFromCode($r, $this->fromCountry != '' ? $this->fromCountry : $this->shipCountry);

                $simpleRate = array(
                    'desc' => $desc,
                    'rate' => $service['TotalCharges']['MonetaryValue'],
                    'service_code' => $r,
                );

                if (isset($service['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'])) {
                    $simpleRate['negotiated_rate'] = $service['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'];
                }

                if (!empty($user_func)) {
                    $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $service, $simpleRate);
                }
                $rates[] = $simpleRate;
            } else {
                foreach ($service as $s) {
                    $r = $s['Service']['Code'];
                    $desc = $this->core->getServiceDescriptionFromCode($r, $this->fromCountry != '' ? $this->fromCountry : $this->shipCountry);
                    $simpleRate = array('desc' => $desc, 'rate' => $s['TotalCharges']['MonetaryValue'], 'service_code' => $r);
                    if (isset($s['NegotiatedRates'])) {
                        $simpleRate = array('desc' => $desc,
                                            'rate' => $s['TotalCharges']['MonetaryValue'],
                                            'service_code' => $r,
                                            'negotiated_rate' => $s['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'], );
                    }
                    if (!empty($user_func)) {
                        $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $s, $simpleRate);
                    }
                    $rates[] = $simpleRate;
                }
            }

            return $rates;
        } else {
            $errorMessage = $upsArray['RatingServiceSelectionResponse']['Response']['Error']['ErrorDescription'];
            $errorArray['error'] = $errorMessage;
            if (isset($upsArray['RatingServiceSelectionResponse']['Response']['Error']['ErrorLocation']['ErrorLocationElementName'])) {
                $errorArray['error_location'] = $upsArray['RatingServiceSelectionResponse']['Response']['Error']['ErrorLocation']['ErrorLocationElementName'];
            }
;

            return $errorArray;
        }
    }

    public function getAllRates()
    {
        return $this->getRate('Shop');
    }
}
