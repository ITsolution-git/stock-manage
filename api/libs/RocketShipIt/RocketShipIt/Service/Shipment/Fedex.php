<?php

namespace RocketShipIt\Service\Shipment;

use RocketShipIt\Helper\XmlBuilder;

class Fedex extends \RocketShipIt\Service\Common
{
    public $customsLines;

    // Why? Because different services require COD to be placed in
    // shipment vs package level special services
    public $shipmentLevelCodServices = array(
        'FEDEX_EXPRESS_SAVER',
        'FEDEX_2_DAY',
        'FEDEX_1_DAY_FREIGHT',
        'FEDEX_2_DAY_FREIGHT',
        'FEDEX_3_DAY_FREIGHT',
        'FIRST_OVERNIGHT',
        'INTERNATIONAL_ECONOMY',
        'INTERNATIONAL_ECONOMY_FREIGHT',
        'INTERNATIONAL_FIRST',
        'INTERNATIONAL_PRIORITY',
        'INTERNATIONAL_PRIORITY_FREIGHT',
        'PRIORITY_OVERNIGHT',
        'STANDARD_OVERNIGHT',
    );

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function addCustomsLineToFEDEXshipment($customs)
    {
        if (!isset($this->core->customsObject)) {
            $this->core->customsObject = new xmlBuilder(true);
        }

        $xml = $this->core->customsObject;

        $xml->push('ns:Commodities');
        $xml->element('ns:NumberOfPieces', $customs->customsNumberOfPieces);
        if ($customs->customsDescription != '') {
            $xml->element('ns:Description', $customs->customsDescription);
        }
        $xml->element('ns:CountryOfManufacture', $customs->countryOfManufacture);
        $xml->element('ns:HarmonizedCode', $customs->harmonizedCode);
        $xml->push('ns:Weight');
        $xml->element('ns:Units', $customs->weightUnit);
        $xml->element('ns:Value', $customs->customsWeight);
        $xml->pop(); // end Weight
            $xml->element('ns:Quantity', $customs->customsQuantity);
        $xml->element('ns:QuantityUnits', $customs->customsQuantityUnits);
        $xml->push('ns:UnitPrice');
        $xml->element('ns:Currency', $this->customsCurrency);
        $xml->element('ns:Amount', $customs->customsLineAmount);
        $xml->pop(); // end UnitPrice
        $xml->pop(); // end Commodities

        return $this->core->customsObject = $xml;
    }

    public function buildFEDEXShipmentXml()
    {
        $xml = $this->core->xmlObject;
        $xml->push('ns:ProcessShipmentRequest',
            array('xmlns:ns' => 'http://fedex.com/ws/ship/v15',
                  'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                  'xsi:schemaLocation' => 'http://fedex.com/ws/ship/v15 ShipService v15.xsd', ));
        $this->core->xmlObject = $xml;
        $this->core->access();
        $xml = $this->core->xmlObject;

        $xml->append($this->buildHeaderXml());
        $xml->push('ns:RequestedShipment');
        if ($this->shipDate != '') {
            $xml->element('ns:ShipTimestamp', $this->shipDate);
        } else {
            $xml->element('ns:ShipTimestamp', date('c', time() + 3600)); // FedEx uses ISO8601 style timestamps
        }
        if ($this->dropoffType == '') {
            $xml->element('ns:DropoffType', 'REGULAR_PICKUP');
        } else {
            $xml->element('ns:DropoffType', $this->dropoffType);
        }
        $xml->element('ns:ServiceType', $this->service);
        $xml->element('ns:PackagingType', $this->packagingType);
            // Only needed for Int shipments with multi package
            if ($this->packageCount != '' && $this->sequenceNumber == '1') {
                if ($this->shipmentWeight != '') {
                    $xml->push('ns:TotalWeight');
                    $xml->element('ns:Units', $this->weightUnit);
                    $xml->element('ns:Value', $this->shipmentWeight);
                    $xml->pop(); // end weight
                }
            }
        $xml->push('ns:Shipper');
        if ($this->shipTaxId != '') {
            $xml->push('ns:Tins');
                        // {'BUSINESS_NATIONAL'|'BUSINESS_STATE'|'BUSINESS_UNION'|'PERSONAL_NATIONAL'|'PERSONAL_STATE'}
                        $xml->element('ns:TinType', $this->shipTaxIdType);
            $xml->element('ns:Number', $this->shipTaxId);
                        //$xml->element('ns:Usage', '');
                    $xml->pop(); // end Tins
        }
        $xml->push('ns:Contact');
        $xml->element('ns:PersonName', $this->shipContact);
        $xml->element('ns:CompanyName', $this->shipper);
        $xml->element('ns:PhoneNumber', $this->shipPhone);
        $xml->pop(); // end Contact
                $xml->append($this->buildShipperAddressXml());
        $xml->pop(); // end Shipper
            $xml->push('ns:Recipient');
        if ($this->toTaxId != '') {
            $xml->push('ns:Tins');
                        // {'BUSINESS_NATIONAL'|'BUSINESS_STATE'|'BUSINESS_UNION'|'PERSONAL_NATIONAL'|'PERSONAL_STATE'}
                        $xml->element('ns:TinType', $this->toTaxIdType);
            $xml->element('ns:Number', $this->toTaxId);
                        //$xml->element('ns:Usage', '');
                    $xml->pop(); // end Tins
        }
        $xml->push('ns:Contact');
        $xml->element('ns:PersonName', $this->toName);
        $xml->element('ns:CompanyName', $this->toCompany);
        $xml->element('ns:PhoneNumber', $this->toPhone);
        $xml->pop(); // end Contact
                $xml->append($this->buildRecipientAddressXml());
        $xml->pop(); // end Recipient
            $xml->push('ns:ShippingChargesPayment');
        if ($this->shippingChargesPaymentType != '') {
            $xml->element('ns:PaymentType', $this->shippingChargesPaymentType);
        } else {
            $xml->element('ns:PaymentType', $this->paymentType);
        }
        $xml->push('ns:Payor');
        $xml->push('ns:ResponsibleParty');
        if ($this->thirdPartyAccount != '') {
            $xml->element('ns:AccountNumber', $this->thirdPartyAccount);
        } else {
            $xml->element('ns:AccountNumber', $this->accountNumber);
        }
        $xml->push('ns:Contact');
        $xml->element('ns:CompanyName', $this->shipper);
        $xml->pop(); // end Contact
        $xml->push('ns:Address');
        $xml->element('ns:CountryCode', $this->shipCountry);
        $xml->pop(); // end Address
        $xml->pop();
        $xml->pop(); // end Payor
            $xml->pop(); // end ShippingChargesPayment

            if (strtoupper($this->collectOnDelivery) == 'YES'
            || strtoupper($this->holdAtLocation) == 'YES'
            || strtoupper($this->returnCode) != ''
            || strtoupper($this->futureDay) == 'YES'
            || strtoupper($this->saturdayDelivery) == 'YES'
            || $this->emailTo != ''
            || $this->fedexOneRate != ''
            || $this->paperlessCustoms == 'YES'
            || $this->limitedAccessDelivery == 'YES'
            || $this->limitedAccessPickup == 'YES'
            ) {
                $xml->push('ns:SpecialServicesRequested');

                if (strtoupper($this->limitedAccessDelivery) == 'YES') {
                    $xml->element('ns:SpecialServiceTypes', 'LIMITED_ACCESS_DELIVERY');
                }

                if (strtoupper($this->limitedAccessPickup) == 'YES') {
                    $xml->element('ns:SpecialServiceTypes', 'LIMITED_ACCESS_PICKUP');
                }

                if ($this->collectOnDelivery == 'YES' && in_array($this->service, $this->shipmentLevelCodServices)) {
                    $xml->append($this->buildCodXml());
                }

                if (strtoupper($this->paperlessCustoms) == 'YES') {
                    $xml->element('ns:SpecialServiceTypes', 'ELECTRONIC_TRADE_DOCUMENTS');
                    $xml->push('ns:EtdDetail');
                    foreach ($this->generateDocs as $type) {
                        $xml->element('ns:RequestedDocumentCopies', $type);
                    }
                    $xml->pop(); // end EtdDetail
                }

                // Email notification
                if ($this->emailTo != '') {
                    $xml->element('ns:SpecialServiceTypes', 'EMAIL_NOTIFICATION');
                }

                if (strtoupper($this->returnCode) != '') {
                    $xml->element('ns:SpecialServiceTypes', 'RETURN_SHIPMENT');
                }

                if ($this->fedexOneRate != '') {
                    $xml->element('ns:SpecialServiceTypes', 'FEDEX_ONE_RATE');
                }

                if (strtoupper($this->holdAtLocation) == 'YES') {
                    $xml->append($this->buildHoldAtLocationXml());
                }

                if ($this->emailTo != '') {
                    $xml->append($this->buildEmailNotificationXml());
                }

                if (strtoupper($this->returnCode) != '') {
                    $xml->push('ns:ReturnShipmentDetail');
                    $xml->element('ns:ReturnType', $this->returnCode);
                    $xml->pop(); // end ReturnShipmentDetail
                }

                if (strtoupper($this->saturdayDelivery) == 'YES') {
                    $xml->element('ns:SpecialServiceTypes', 'SATURDAY_DELIVERY');
                }

                if (strtoupper($this->futureDay) == 'YES') {
                    $xml->element('ns:SpecialServiceTypes', 'FUTURE_DAY_SHIPMENT');
                }

                $xml->pop(); // end ShipmentSpecialServicesRequested
            }

        if ($this->shipCountry != $this->toCountry || $this->toState == 'PR') {
            $xml->push('ns:CustomsClearanceDetail');
            if ($this->customsOptionsType != '') {
                $xml->push('ns:CustomsOptions');
                    $xml->element('ns:Type', $this->customsOptionsType);
                $xml->pop();
            }
            if ($this->iorAccountNumber != '') {
                $xml->push('ns:ImporterOfRecord');
                $xml->element('ns:AccountNumber', $this->iorAccountNumber);
                if ($this->iorTaxId != '') {
                    $xml->push('ns:Tins');
                                    // {'BUSINESS_NATIONAL'|'BUSINESS_STATE'|'BUSINESS_UNION'|'PERSONAL_NATIONAL'|'PERSONAL_STATE'}
                                    $xml->element('ns:TinType', $this->iorTaxIdType);
                    $xml->element('ns:Number', $this->iorTaxId);
                                    //$xml->element('ns:Usage', '');
                                $xml->pop(); // end Tins
                }
                $xml->push('ns:Contact');
                $xml->element('ns:ContactId', $this->iorContactId);
                $xml->element('ns:PersonName', $this->iorPersonName);
                $xml->element('ns:Title', $this->iorTitle);
                $xml->element('ns:CompanyName', $this->iorCompany);
                $xml->element('ns:PhoneNumber', $this->iorPhone);
                $xml->element('ns:PhoneExtension', $this->iorPhoneExtension);
                $xml->element('ns:PagerNumber', $this->iorPagerNumber);
                $xml->element('ns:FaxNumber', $this->iorFaxNumber);
                $xml->element('ns:EMailAddress', $this->iorEmailAddress);
                $xml->pop(); // end Contact
                            $xml->push('ns:Address');
                $xml->element('ns:StreetLines', $this->iorAddr1);
                $xml->element('ns:City', $this->iorCity);
                $xml->element('ns:StateOrProvinceCode', $this->iorState);
                $xml->element('ns:PostalCode', $this->iorCode);
                $xml->element('ns:UrbanizationCode', $this->iorUrbanizationCode);
                $xml->element('ns:CountryCode', $this->iorCountry);
                $xml->element('ns:Residential', $this->iorResidential);
                $xml->pop();//end Address
                        $xml->pop(); // end ImporterOfRecord
            }
            $xml->push('ns:DutiesPayment');
            if ($this->customsPaymentType != '') {
                $xml->element('ns:PaymentType', $this->customsPaymentType);
            } else {
                $xml->element('ns:PaymentType', $this->paymentType);
            }
            if ($this->customsPaymentType == '') {
                $xml->push('ns:Payor');
                $xml->push('ns:ResponsibleParty');
                if ($this->customsAccountNumber != '') {
                    $xml->element('ns:AccountNumber', $this->customsAccountNumber);
                }
                $xml->push('ns:Contact');
                $xml->element('ns:CompanyName', $this->shipper);
                $xml->pop(); // end Contact
                                    $xml->push('ns:Address');
                $xml->element('ns:CountryCode', $this->shipCountry);
                $xml->pop(); // end Address
                                $xml->pop(); //end ResponsibleParty
                            $xml->pop(); // end Payor
            }
            $xml->pop(); // end DutiesPayment
                    $xml->push('ns:CustomsValue');
            $xml->element('ns:Currency', $this->customsCurrency);
            $xml->element('ns:Amount', $this->customsValue);
            $xml->pop();
            if (isset($this->core->customsObject)) {
                $xml->append($this->core->customsObject->getXML());
            }
            if ($this->complianceStatement != '') {
                $xml->push('ns:ExportDetail');
                $xml->element('ns:ExportComplianceStatement', $this->complianceStatement);
                $xml->pop(); // end ExportDetail
            }
            $xml->pop();
        }
        if ($this->service == 'SMART_POST') {
            $xml->append($this->buildSmartPostXml());
        }
        $xml->append($this->buildLabelSpecificationXml());
        if (!empty($this->generateDocs)) {
            $xml->append($this->buildDocumentSpecificationXml());
        }
        $xml->element('ns:RateRequestTypes', 'LIST');
        if ($this->shipmentIdentification != '') {
            $xml->push('ns:MasterTrackingId');
                     //$xml->element('TrackingIdType','GROUND');
                    $xml->element('ns:TrackingNumber', $this->shipmentIdentification);
            $xml->pop(); // end MasterTrackingId
        }
        if ($this->packageCount != '') {
            $xml->element('ns:PackageCount', $this->packageCount);
        } else {
            $xml->element('ns:PackageCount', '1');
        }

        $xml->push('ns:RequestedPackageLineItems');
        if ($this->sequenceNumber != '') {
            $xml->element('ns:SequenceNumber', $this->sequenceNumber);
        }
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
                if ($this->length !=  '' or $this->width != '' or $this->height != '') {
                    $xml->push('ns:Dimensions');
                    $xml->element('ns:Length', $this->length);
                    $xml->element('ns:Width', $this->width);
                    $xml->element('ns:Height', $this->height);
                    $xml->element('ns:Units', $this->lengthUnit);
                    $xml->pop(); // end Dimensions
                }
        if ($this->referenceCode != '' && $this->referenceValue != '') {
            $xml->push('ns:CustomerReferences');
            $xml->element('ns:CustomerReferenceType', $this->referenceCode);
            $xml->element('ns:Value', $this->referenceValue);
            $xml->pop(); // end CustomerReferences
                    if ($this->referenceCode2 != '' && $this->referenceValue2 != '') {
                        $xml->push('ns:CustomerReferences');
                        $xml->element('ns:CustomerReferenceType', $this->referenceCode2);
                        $xml->element('ns:Value', $this->referenceValue2);
                        $xml->pop(); // end CustomerReferences
                    }
            if ($this->referenceCode3 != '' && $this->referenceValue3 != '') {
                $xml->push('ns:CustomerReferences');
                $xml->element('ns:CustomerReferenceType', $this->referenceCode3);
                $xml->element('ns:Value', $this->referenceValue3);
                $xml->pop(); // end CustomerReferences
            }
        }
        $xml->append($this->buildSpecialServicesXml());
        $xml->pop(); // end RequestedPackageLineItems

        $xml->pop(); // end RequestedShipment

        $xml->pop(); // end CreatePendingShipmentRequest

        $xmlString = $xml->getXml();

        return $xmlString;
    }

    public function buildHeaderXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:TransactionDetail');
        $xml->element('ns:CustomerTransactionId', 'CreatePendingRequest');
        $xml->pop(); // end TransactionDetail
        $xml->push('ns:Version');
        $xml->element('ns:ServiceId', 'ship');
        $xml->element('ns:Major', '15');
        $xml->element('ns:Intermediate', '0');
        $xml->element('ns:Minor', '0');
        $xml->pop(); // end Version
        return $xml->getXml();
    }

    public function buildRecipientAddressXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->toAddr1);
        if ($this->toAddr2 != '') {
            $xml->element('ns:StreetLines', $this->toAddr2);
        }
        if ($this->toAddr3 != '') {
            $xml->element('ns:StreetLines', $this->toAddr3);
        }
        $xml->element('ns:City', $this->toCity);
        $xml->element('ns:StateOrProvinceCode', $this->toState);
        $xml->element('ns:PostalCode', $this->toCode);
        $xml->element('ns:CountryCode', $this->toCountry);

        if ($this->service == 'GROUND_HOME_DELIVERY') {
            $xml->element('ns:Residential', 'true');
        } else {
            if ($this->residential != '') {
                $this->residentialAddressIndicator = $this->residential;
            }
            if ($this->residentialAddressIndicator == '1') {
                $xml->element('ns:Residential', 'true');
            }
        }

        $xml->pop(); // end Address
        return $xml->getXml();
    }

    public function buildShipperAddressXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->shipAddr1);
        if ($this->shipAddr2 != '') {
            $xml->element('ns:StreetLines', $this->shipAddr2);
        }
        if ($this->shipAddr3 != '') {
            $xml->element('ns:StreetLines', $this->shipAddr3);
        }
        $xml->element('ns:City', $this->shipCity);
        $xml->element('ns:StateOrProvinceCode', $this->shipState);
        $xml->element('ns:PostalCode', $this->shipCode);
        $xml->element('ns:CountryCode', $this->shipCountry);
        $xml->pop(); // end Address
        return $xml->getXml();
    }

    public function buildEmailNotificationXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:EMailNotificationDetail');
        $xml->element('ns:PersonalMessage', $this->emailMessage);
        $xml->push('ns:Recipients');
        $xml->element('ns:EMailNotificationRecipientType', $this->emailRecipientType);
        $xml->element('ns:EMailAddress', $this->emailTo);

        if ($this->notifyOnShipment == 'YES') {
            $xml->element('ns:NotificationEventsRequested', 'ON_SHIPMENT');
        }
        if ($this->notifyOnDelivery == 'YES') {
            $xml->element('ns:NotificationEventsRequested', 'ON_DELIVERY');
        }
        if ($this->notifyOnException == 'YES') {
            $xml->element('ns:NotificationEventsRequested', 'ON_EXCEPTION');
        }
        if ($this->notifyOnTender == 'YES') {
            $xml->element('ns:NotificationEventsRequested', 'ON_TENDER');
        }

        $xml->element('ns:Format', $this->emailFormat);
        $xml->push('ns:Localization');
        $xml->element('ns:LanguageCode', $this->emailLanguage);
        $xml->pop(); // end Localization
            $xml->pop(); // end Recipients
        $xml->pop(); // end EMailNotificationDetail

        return $xml->getXml();
    }

    public function buildHoldAtLocationXml()
    {
        $xml = new xmlBuilder(true);
        $xml->element('ns:SpecialServiceTypes', 'HOLD_AT_LOCATION');
        $xml->push('ns:HoldAtLocationDetail');
        $xml->element('ns:PhoneNumber', $this->holdPhone);
        $xml->push('ns:LocationContactAndAddress');
        $xml->push('ns:Contact');
        $xml->element('ns:ContactId', '123');
        $xml->element('ns:PersonName', 'Mark Sanborn');
        $xml->element('ns:Title', 'Mr.');
        $xml->element('ns:CompanyName', 'RocketShipIt');
        $xml->element('ns:PhoneNumber', '7077262676');
        $xml->element('ns:EMailAddress', 'mark@rocketship.it');
        $xml->pop();
        $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->holdStreet);
        $xml->element('ns:City', $this->holdCity);
        $xml->element('ns:StateOrProvinceCode', $this->holdState);
        $xml->element('ns:PostalCode', $this->holdCode);
        $xml->element('ns:CountryCode', $this->holdCountry);
        if (strtoupper($this->holdResidential) == 'YES') {
            $xml->element('ns:Residential', 'true');
        }
        $xml->pop(); // Address
            $xml->pop(); //end LocationContactAndAddress
            // LocationType: {'FEDEX_EXPRESS_STATION'|'FEDEX_GROUND_TERMINAL'|'FEDEX_OFFICE'}
            $xml->element('ns:LocationType', 'FEDEX_GROUND_TERMINAL');
        $xml->pop(); //HoldAtLocationDetail
        return $xml->getXml();
    }

    public function buildSmartPostXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:SmartPostDetail');
        $xml->element('ns:Indicia', $this->smartPostIndicia);
        if ($this->smartPostEndorsement != '') {
            $xml->element('ns:AncillaryEndorsement', $this->smartPostEndorsement);
        }
        if ($this->smartPostSpecialServices != '') {
            $xml->element('ns:SpecialServices', $this->smartPostSpecialServices);
        }
        if ($this->smartPostHubId != '') {
            $xml->element('ns:HubId', $this->smartPostHubId);
        }
        $xml->pop(); // end SmartPostDetail
        return $xml->getXml();
    }

    public function buildLabelSpecificationXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:LabelSpecification');
        $xml->element('ns:LabelFormatType', $this->labelFormatType);
        $xml->element('ns:ImageType', $this->imageType);
        $xml->element('ns:LabelStockType', $this->labelStockType);
        if ($this->printOrientation != '') {
            $xml->element('ns:LabelPrintingOrientation', $this->printOrientation);
        } else {
            $xml->element('ns:LabelPrintingOrientation', 'TOP_EDGE_OF_TEXT_FIRST');
        }
        if ($this->docTabType != '') {
            $xml->push('ns:CustomerSpecifiedDetail');
            $xml->push('ns:DocTabContent');
            $xml->element('ns:DocTabContentType', $this->docTabType);
            $xml->pop(); // end DocTabContent
                $xml->pop(); // end CustomerSpecifiedDetail
        }
        $xml->pop(); // end LabelSpecification
        return $xml->getXml();
    }

    public function buildSpecialServicesXml()
    {
        $xml = new xmlBuilder(true);
        if ($this->signatureType != '' || $this->collectOnDelivery == 'YES') {
            $xml->push('ns:SpecialServicesRequested');

            if ($this->signatureType != '') {
                $xml->element('ns:SpecialServiceTypes', 'SIGNATURE_OPTION');
                $xml->push('ns:SignatureOptionDetail');
                $xml->element('ns:OptionType', $this->signatureType);
                $xml->pop();
            }

            if ($this->collectOnDelivery == 'YES' && !in_array($this->service, $this->shipmentLevelCodServices)) {
                $xml->append($this->buildCodXml());
            }

            $xml->pop(); // end PackageSpecialServicesRequested
        }

        return $xml->getXml();
    }

    public function buildCodXml()
    {
        $xml = new xmlBuilder(true);
        $xml->element('ns:SpecialServiceTypes', 'COD');
        if ($this->signatureType != '') {
            $xml->element('ns:SpecialServiceTypes', 'SIGNATURE_OPTION');
            $xml->push('ns:SignatureOptionDetail');
            $xml->element('ns:OptionType', $this->signatureType);
            $xml->pop();
        }
        $xml->push('ns:CodDetail');
        $xml->push('ns:CodCollectionAmount');
        $xml->element('ns:Currency', $this->currency);
        $xml->element('ns:Amount', $this->codCollectionAmount);
        $xml->pop();

        $xml->push('ns:AddTransportationChargesDetail');
        $xml->element('ns:RateTypeBasis', 'LIST');
        $xml->element('ns:ChargeBasis', 'NET_FREIGHT');
        $xml->element('ns:ChargeBasisLevel', 'SUM_OF_PACKAGES');
        $xml->pop();
        $xml->element('ns:CollectionType', $this->codCollectionType);

        $xml->push('ns:CodRecipient');
        $xml->element('ns:AccountNumber', $this->codAccountNumber);
        $xml->push('ns:Contact');
        $xml->element('ns:ContactId', $this->codContactId);
        $xml->element('ns:PersonName', $this->codPersonName);
        $xml->element('ns:Title', $this->codTitle);
        $xml->element('ns:CompanyName', $this->codCompany);
        $xml->element('ns:PhoneNumber', $this->codPhone);
        $xml->element('ns:PhoneExtension', $this->codPhoneExtension);
        $xml->element('ns:PagerNumber', $this->codPagerNumber);
        $xml->element('ns:FaxNumber', $this->codFaxNumber);
        $xml->element('ns:EMailAddress', $this->codEmailAddress);
        $xml->pop();//end Contact
                    $xml->push('ns:Address');
        $xml->element('ns:StreetLines', $this->codAddr1);
        $xml->element('ns:City', $this->codCity);
        $xml->element('ns:StateOrProvinceCode', $this->codState);
        $xml->element('ns:PostalCode', $this->codCode);
        $xml->element('ns:UrbanizationCode', $this->codUrbanizationCode);
        $xml->element('ns:CountryCode', $this->codCountry);
        $xml->element('ns:Residential', $this->codResidential);
        $xml->pop();//end Address
            $xml->pop();//end CodRecipient
            $xml->element('ns:ReferenceIndicator', 'TRACKING');
        $xml->pop(); //end CodDetail
        return $xml->getXml();
    }

    // ImageType : {'DOC'|'DPL'|'EPL2'|'PDF'|'PNG'|'RTF'|'TEXT'|'ZPLII'}
    // StockType : {'OP_900_LG_B'|'OP_900_LL_B'|'OP_950'|'PAPER_4X6'|'PAPER_LETTER'|'STOCK_4X6'|'STOCK_4X6.75_LEADING_DOC_TAB'|'STOCK_4X6.75_TRAILING_DOC_TAB'|'STOCK_4X8'|'STOCK_4X9_LEADING_DOC_TAB'|'STOCK_4X9_TRAILING_DOC_TAB'}

    // Commercial Invoice
    public function buildCommercialInvoice()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:CommercialInvoiceDetail');
        $xml->push('ns:Format');
        $xml->element('ns:ImageType', $this->docImageType);
        $xml->element('ns:StockType', $this->docStockType);
        $xml->pop(); // end DocumentFormat
            /*
            $xml->push('ns:CustomerImageUsages');
                $xml->element('ns:Type', 'LETTER_HEAD'); // {'LETTER_HEAD'|'SIGNATURE'}
                // {'IMAGE_1'|'IMAGE_2'|'IMAGE_3'|'IMAGE_4'|'IMAGE_5'}
                $xml->element('ns:Id', 'IMAGE_1');
            $xml->pop(); // end CustomerImageUsages
            */
        $xml->pop(); // end CommercialInvoiceDetail
        return $xml->getXml();
    }

    //Certificate of Origin
    public function buildCertificateOfOrigin()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:CertificateOfOrigin');
        $xml->push('ns:DocumentFormat');
        $xml->element('ns:ImageType', $this->docImageType);
        $xml->element('ns:StockType', $this->docStockType);
        $xml->pop(); // end DocumentFormat
        $xml->pop(); // end CertificateOfOrigin
        return $xml->getXml();
    }

    // NAFTA
    public function buildNafta()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:NaftaCertificateOfOriginDetail');
        $xml->push('ns:Format');
        $xml->element('ns:ImageType', $this->docImageType);
        $xml->element('ns:StockType', $this->docStockType);
        $xml->pop(); // end DocumentFormat
        $xml->pop(); // end CommercialInvoiceDetail
        return $xml->getXml();
    }

    public function buildDocumentSpecificationXml()
    {
        $xml = new xmlBuilder(true);
        $xml->push('ns:ShippingDocumentSpecification');

        foreach ($this->generateDocs as $type) {
            $xml->element('ns:ShippingDocumentTypes', $type);
        }

        if (in_array('CERTIFICATE_OF_ORIGIN', $this->generateDocs)) {
            $xml->append($this->buildCertificateOfOrigin());
        }

        if (in_array('COMMERCIAL_INVOICE', $this->generateDocs)) {
            $xml->append($this->buildCommercialInvoice());
        }

        if (in_array('NAFTA_CERTIFICATE_OF_ORIGIN', $this->generateDocs)) {
            $xml->append($this->buildNafta());
        }

        $xml->pop(); // end ShippingDocumentSpecification
        return $xml->getXml();
    }

    public function sendFEDEXshipment()
    {
        $xmlString = $this->buildFEDEXShipmentXml();

        // Put the xml that is sent to FedEx into a variable so we can call it later for debugging.
        $this->core->xmlSent = $xmlString;
        $this->core->xmlResponse = $this->core->request($xmlString);

        return $this->arrayFromXml($this->core->xmlResponse);
    }

    public function simplifyFEDEXResponse($xmlArray)
    {
        if (!in_array('ProcessShipmentReply', array_keys($xmlArray))) {
            return $xmlArray;
        }

        if (!isset($xmlArray['ProcessShipmentReply']['Notifications'])) {
            return array('error' => 'Invalid response');
        }

        $code = '';
        $message = '';
        $notifications = $xmlArray['ProcessShipmentReply']['Notifications'];
        if (array_key_exists('Code', $notifications)) {
            $code = $notifications['Code'];
            $message = $notifications['Message'];
        } else {
            // If not then its has more siblings, get values from the first one
            $code = $notifications[0]['Code'];
            $message = $notifications[0]['Message'];
        }

        $simpleArr = array();
        if ($xmlArray['ProcessShipmentReply']['HighestSeverity'] == 'ERROR') {
            return array('error' => $code.' ('.$message.')');
        } else {
            $simpleArr['status'] = 'SUCCESS';
        }

        if (in_array('ShipmentRating', array_keys($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']))) {
            $simpleArr['charges'] = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['ShipmentRating']['ShipmentRateDetails'][0]['TotalNetFedExCharge']['Amount'];
        }

        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber'])) {
            $simpleArr['trk_main'] = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber'];
        } else {
            foreach ($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'] as $tracking) {
                $simpleArr['trk_main'][] = $tracking['TrackingNumber'];
            }
        }

        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber'])) {
            $simpleArr['tracking_detail'] = array('id' => $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber'], 'type' => $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType']);
        } else {
            foreach ($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'] as $tracking) {
                $simpleArr['tracking_detail'][] = array('id' => $tracking['TrackingNumber'], 'type' => $tracking['TrackingIdType']);
            }
        }

        if (in_array('CodReturnDetail', array_keys($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']))) {
            $simpleArr['cod_return_amount'] = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['CollectionAmount']['Amount'];
        }

        $label = '';
        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['OperationalDetail']['Barcodes']['BinaryBarcodes']['Value'])) {
            $label = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['OperationalDetail']['Barcodes']['BinaryBarcodes']['Value'];
        }

        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
            $label = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
        }

        $imageType = '';
        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['ImageType'])) {
            $imageType = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['ImageType'];
        }


        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['ShipmentDocuments'])) {
            $shippingDocs = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['ShipmentDocuments'];
            $simpleArr['shipping_docs'] = $shippingDocs;
        }

        $simpleArr['pkgs'] = array();
        $simpleArr['pkgs'][] = array(
            'pkg_trk_num' => $simpleArr['trk_main'],
            'label_fmt' => $imageType,
            'label_img' => $label,
            'cod_return_label_img' => $this->getCodReturnLabel($xmlArray),
        );

        $trkMain = $simpleArr['trk_main'];
        if ($this->shipmentIdentification != '') {
            $trkMain = $this->shipmentIdentification;
        }
        $simpleArr['trk_main'] = $trkMain;


        return $simpleArr;
    }

    public function getCodReturnLabel($xmlArray)
    {
        $codReturnLabel = '';

        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
            $codReturnLabel = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'];
        }

        if (isset($xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['AssociatedShipments']['Label']['Parts']['Image'])) {
            $codReturnLabel = $xmlArray['ProcessShipmentReply']['CompletedShipmentDetail']['AssociatedShipments']['Label']['Parts']['Image'];
        }

        return $codReturnLabel;
    }
}
