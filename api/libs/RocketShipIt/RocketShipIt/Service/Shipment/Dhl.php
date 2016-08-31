<?php

namespace RocketShipIt\Service\Shipment;

use RocketShipIt\Helper\XmlBuilder;

/**
 * Main Shipping class for producing labels and notifying carriers of pickups.
 */
class Dhl extends \RocketShipIt\Service\Common
{
    public $customsLines;
    public $packageCount;

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
        $this->packageCount = 0;
        $this->shipmentWeight = 0;
    }

    /* DHL Global Product Codes
        0	LOGISTICS SERVICES	LOGISTICS SERVICES	LOG	A-Not Applicable
        1	CUSTOMS SERVICES	CUSTOMS SERVICES	CDZ	N-Non Doc
        2	EASY SHOP	EASY SHOP	ESD	Y-Doc	23:59
        3	EASY SHOP	EASY SHOP	ESP	N-Non Doc	23:59
        4	JETLINE	JETLINE	NFO	N-Non Doc	23:59
        5	SPRINTLINE	SPRINTLINE	SPL	Y-Doc	23:59
        6	SECURELINE	SECURELINE	OBC	Y-Doc	23:59
        7	EXPRESS EASY	EXPRESS EASY	XED	Y-Doc	23:59
        8	EXPRESS EASY	EXPRESS EASY	XEP	N-Non Doc	23:59
        9	EUROPACK	EUROPACK	EPA	Y-Doc	23:59
        A	AUTO REVERSALS	AUTO REVERSALS	N/A	A-Not Applicable
        B	BREAK BULK EXPRESS	BREAK BULK EXPRESS	BBX	Y-Doc	23:59
        C	MEDICAL EXPRESS	MEDICAL EXPRESS	CMX	Y-Doc	12:00
        D	EXPRESS WORLDWIDE	EXPRESS WORLDWIDE	DOX	Y-Doc	23:59
        E	EXPRESS 9:00	EXPRESS 9:00	TDE	N-Non Doc	9:00
        F	FREIGHT WORLDWIDE	FREIGHT WORLDWIDE	FRT	N-Non Doc	23:59
        G	DOMESTIC ECONOMY SELECT	DOMESTIC ECONOMY SELECT	DES	Y-Doc	23:59
        H	ECONOMY SELECT	ECONOMY SELECT	ESI	N-Non Doc	23:59
        I	BREAK BULK ECONOMY	BREAK BULK ECONOMY	BBE	Y-Doc	23:59
        J	JUMBO BOX	JUMBO BOX	JBX	N-Non Doc	23:59
        K	EXPRESS 9:00	EXPRESS 9:00	TDK	Y-Doc	9:00
        L	EXPRESS 10:30	EXPRESS 10:30	TDL	Y-Doc	10:30
        M	EXPRESS 10:30	EXPRESS 10:30	TDM	N-Non Doc	10:30
        N	DOMESTIC EXPRESS	DOMESTIC EXPRESS	DOM	Y-Doc	23:59
        O	OTHERS	OTHERS	OTH	Y-Doc	23:59
        P	EXPRESS WORLDWIDE	EXPRESS WORLDWIDE	WPX	N-Non Doc	23:59
        Q	MEDICAL EXPRESS	MEDICAL EXPRESS	WMX	N-Non Doc	12:00
        R	GLOBALMAIL BUSINESS	GLOBALMAIL BUSINESS	GMB	Y-Doc	23:59
        S	SAME DAY	SAME DAY	SDX	Y-Doc	23:59
        T	EXPRESS 12:00	EXPRESS 12:00	TDT	Y-Doc	12:00
        U	EXPRESS WORLDWIDE	EXPRESS WORLDWIDE	ECX	Y-Doc	23:59
        V	EUROPACK	EUROPACK	EPP	N-Non Doc	23:59
        W	ECONOMY SELECT	ECONOMY SELECT	ESU	Y-Doc	23:59
        X	EXPRESS ENVELOPE	EXPRESS ENVELOPE	XPD	Y-Doc	23:59
        Y	EXPRESS 12:00	EXPRESS 12:00	TDY	N-Non Doc	12:00
        Z	DESTINATION CHARGES	DESTINATION CHARGES	N/A	A-Not Applicable
    */

    /* AM Region
        AG	ANTIGUA
        AI	ANGUILLA
        AR	ARGENTINA
        AW	ARUBA
        BB	BARBADOS
        BM	BERMUDA
        BO	BOLIVIA
        BR	BRAZIL
        BS	BAHAMAS
        CA	CANADA
        CL	CHILE
        CO	COLOMBIA
        CR	COSTA RICA
        DM	DOMINICA
        DO	DOMINICAN REPUBLIC
        EC	ECUADOR
        GD	GRENADA
        GF	FRENCH GUYANA
        GP	GUADELOUPE
        GT	GUATEMALA
        GU	GUAM
        GY	GUYANA (BRITISH)
        HN	HONDURAS
        HT	HAITI
        JM	JAMAICA
        KN	ST. KITTS
        KY	CAYMAN ISLANDS
        LC	ST. LUCIA
        MQ	MARTINIQUE
        MX	MEXICO
        NI	NICARAGUA
        PA	PANAMA
        PE	PERU
        PR	PUERTO RICO
        PY	PARAGUAY
        SR	SURINAME
        SV	EL SALVADOR
        TC	TURKS AND CAICOS ISLANDS
        TT	TRINIDAD AND TOBAGO
        US	UNITED STATES OF AMERICA
        UY	URUGUAY
        VC	ST. VINCENT
        VE	VENEZUELA
        VG	VIRGIN ISLANDS (BRITISH)
        VI	VIRGIN ISLANDS
        XC	CURACAO
        XM	ST. MAARTEN
        XN	NEVIS
        XY	ST. BARTHELEMY
    */

    // AM Region
    public function buildDHLShipmentXml()
    {
        $xml = new XmlBuilder();
        $xml->push('p:ShipmentValidateRequest', array('xmlns:p' => 'http://www.dhl.com', 'xmlns:p1' => 'http://www.dhl.com/datatypes', 'xmlns:p2' => 'http://www.dhl.com/DCTRequestdatatypes', 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation' => 'http://www.dhl.com ship-val-req.xsd'));
        $xml->append($this->buildRequestXml());

            // The RequestedPickupTime element indicates whether
            // a pickup time is requested or not. It is a mandatory field.
            // The valid vaues are Y (Yes) and N (No).
            $xml->element('RequestedPickupTime', 'Y');

            // The NewShipper element indicates whether shipper is new or not.
            // The valid values are Y (Yes) and N (No).
            $xml->element('NewShipper', 'N');

            // LanguageCode element contains the ISO language code used
            // by the requestor. This element should be declared once in
            // the Shipment validation request message.
            $xml->element('LanguageCode', 'EN');

            // The PiecesEnabled element is a mandatory field and it
            // signifies whether to retrieve the Piece details along with
            // the License Plate information or not in shipment response.
            $xml->element('PiecesEnabled', 'Y');

        $xml->append($this->buildBillingXml());
        $xml->append($this->buildConsigneeXml());
        if ($this->customsValue != '') {
            $xml->append($this->buildDutiableXml());
        }
        $xml->append($this->buildShipmentDetailsXml());
        $xml->append($this->buildShipperXml());

            // EPL2, PDF, ZPL2
            $xml->element('LabelImageFormat', $this->labelPrintMethodCode);
        $xml->pop(); // end ShipmentValidateRequest
        return $xml->getXml();
    }

    public function buildRequestXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('Request');
        $xml->push('ServiceHeader');
                //$xml->element('MessageTime', date('c'));
                //$xml->element('MessageReference', $this->generateRandomString());
                $xml->element('SiteID', $this->siteId);
        $xml->element('Password', $this->password);
        $xml->pop(); // end ServiceHeader
        $xml->pop(); // end Request
        return $xml->getXml();
    }

    public function buildBillingXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('Billing');
        $xml->element('ShipperAccountNumber', $this->accountNumber);

            // The ShippingPaymentType element defines the method
            // of payment. It is a mandatory field. The valid values are
            // S:Shipper, R:Recipient, T:Third Party, C:Credit Card.
            if ($this->billThirdParty) {
                $xml->element('ShippingPaymentType', 'T');
            } else {
                $xml->element('ShippingPaymentType', 'S');
            }

            if ($this->billThirdParty) {
                $xml->element('BillingAccountNumber', $this->thirdPartyAccount);
            } else {
                $xml->element('BillingAccountNumber', $this->accountNumber);
            }

            // The DutyPaymentType element contains the method of
            // duty and tax payment. It is required for non-doc or
            // dutiable products. Please refer to Reference Data
            // (Global Product Codes). The valid values are
            // S:Shipper, R:Recipient, T:Third Party.
            $xml->element('DutyPaymentType', 'S');
        $xml->pop(); // end Billing
        return $xml->getXml();
    }

    public function buildConsigneeXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('Consignee');
        $xml->element('CompanyName', $this->toCompany);
        $xml->element('AddressLine', $this->toAddr1);
        if ($this->toAddr2 != '') {
            $xml->element('AddressLine', $this->toAddr2);
        }
        if ($this->toAddr3 != '') {
            $xml->element('AddressLine', $this->toAddr3);
        }
        $xml->element('City', $this->toCity);
        $xml->element('Division', $this->toState);
        $xml->element('PostalCode', $this->toCode);
        $xml->element('CountryCode', $this->toCountry);

            // ISO Country Name
            $xml->element('CountryName', $this->toCountry);
        $xml->push('Contact');
        $xml->element('PersonName', $this->toName);
        $xml->element('PhoneNumber', $this->toPhone);
        $xml->pop(); // end Contact
        $xml->pop(); // end Consignee
        return $xml->getXml();
    }

    public function buildShipmentDetailsXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('ShipmentDetails');
        $xml->element('NumberOfPieces', '1');
        $xml->push('Pieces');
        $xml->push('Piece');
        $xml->element('PieceID', '1');
        $xml->pop(); // end Piece
            $xml->pop(); // end Pieces
            $xml->element('Weight', $this->weight);
            // Weight unit is a single char for shipments
            $xml->element('WeightUnit', substr($this->weightUnit, 0, 1));

            // The GlobalProductCode Element is global product code for
            // the shipment. It is the mandatory field in the Shipment Details segment.
            $xml->element('GlobalProductCode', $this->service);

            // Shipment date for when package(s) will be shipped (but usually current
            // date).Value may range from today to ten days after
            $xml->element('Date', date('Y-m-d'));

        if ($this->shipCountry != $this->toCountry) {
            $xml->element('Contents', $this->customsDescription);
        }

        $xml->element('DimensionUnit', substr($this->lengthUnit, 0, 1));

        if ($this->currency == '') {
            // ISO Curreny Code
                $xml->element('CurrencyCode', 'USD');
        } else {
            // ISO Curreny Code
                $xml->element('CurrencyCode', $this->currency);
        }

        $xml->pop(); // end ShipmentDetails
        return $xml->getXml();
    }

    public function buildDutiableXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('Dutiable');
        $xml->element('DeclaredValue', $this->customsValue);
        $xml->element('DeclaredCurrency', $this->customsCurrency);
        if ($this->customsFilingType != '') {
            $xml->push('Filing');
            $xml->element('FilingType', $this->customsFilingType);
            $xml->element('FTSR', $this->customsFtsr);
            $xml->pop(); // end Filing
        }
        $xml->pop(); // end Dutiable

        return $xml->getXml();
    }

    public function buildShipperXml()
    {
        $xml = new XmlBuilder(true);
        $xml->push('Shipper');

            // Max 30 chars
            $xml->element('ShipperID', $this->generateRandomString());

        $xml->element('CompanyName', $this->shipper);
        $xml->element('RegisteredAccount', $this->accountNumber);
        $xml->element('AddressLine', $this->shipAddr1);
        if ($this->shipAddr1 != '') {
            $xml->element('AddressLine', $this->shipAddr2);
        }
        $xml->element('City', $this->shipCity);
        $xml->element('Division', $this->shipState);
        $xml->element('PostalCode', $this->shipCode);
        $xml->element('CountryCode', $this->shipCountry);
        $xml->element('CountryName', $this->shipCountry);
        $xml->push('Contact');
        $xml->element('PersonName', $this->shipContact);
        $xml->element('PhoneNumber', $this->shipPhone);
        $xml->pop(); // end Contact
        $xml->pop(); // end Shipper
        return $xml->getXml();
    }

    public function buildDHLGlobalShipmentXml()
    {
        $xml = new XmlBuilder();
        $xml->push('p:ShipmentRequest',
            array(
                'xmlns:p' => 'http://www.dhl.com',
                'xmlns:p1' => 'http://www.dhl.com/datatypes',
                'xmlns:p2' => 'http://www.dhl.com/DCTRequestdatatypes',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.dhl.com ship-val-global-req.xsd',
                'schemaVersion' => '1.0',
            ));
        $xml->push('Request');
        $xml->push('ServiceHeader');
        $xml->element('MessageTime', date('c'));
        $xml->element('MessageReference', $this->generateRandomString());
        $xml->element('SiteID', $this->siteId);
        $xml->element('Password', $this->password);
        $xml->pop(); //end ServiceHeader
            $xml->pop(); //end Request
            if ($this->regionCode != '') {
                $xml->element('RegionCode', $this->regionCode);
            } else {
                $xml->element('RegionCode', 'AM');
            }
        $xml->element('RequestedPickupTime', 'Y');
        $xml->element('NewShipper', 'Y');
        $xml->element('LanguageCode', 'en');
        $xml->element('PiecesEnabled', 'Y');
        $xml->push('Billing');
        $xml->element('ShipperAccountNumber', $this->accountNumber);

        if ($this->billThirdParty) {
            $xml->element('ShippingPaymentType', 'T');
        } else {
            $xml->element('ShippingPaymentType', 'S');
        }

        if ($this->billThirdParty) {
            $xml->element('BillingAccountNumber', $this->thirdPartyAccount);
        } else {
            $xml->element('BillingAccountNumber', $this->accountNumber);
        }

        $xml->element('DutyPaymentType', $this->dutyPaymentType);
        if ($this->dutyAccountNumber != '') {
            $xml->element('DutyAccountNumber', $this->dutyAccountNumber);
        }
        $xml->pop(); //end Billing
            $xml->push('Consignee');
        $xml->element('CompanyName', $this->toCompany);
        $xml->element('AddressLine', $this->toAddr1);
        if ($this->toAddr1 != '') {
            $xml->element('AddressLine', $this->toAddr2);
        }
        $xml->element('City', $this->toCity);
        $xml->element('Division', $this->toState);
        $xml->element('DivisionCode', $this->toState);
        $xml->element('PostalCode', $this->toCode);
        $xml->element('CountryCode', $this->toCountry);
        $xml->element('CountryName', $this->toCountry);
        $xml->push('Contact');
        $xml->element('PersonName', $this->toName);
        $xml->element('PhoneNumber', $this->toPhone);
        $xml->pop(); //end Contact
            $xml->pop(); //end Consignee
            if ($this->shipCountry != $this->toCountry) {
                $xml->push('Dutiable');
                $xml->element('DeclaredValue', $this->customsValue);
                $xml->element('DeclaredCurrency', $this->customsCurrency);
                $xml->element('ScheduleB', $this->scheduleB);
                $xml->element('ExportLicense', $this->exportLicense);

                    /*
                    TermsOfTrade (tradeTerms)

                    EXW - Ex Works - The seller's only obligation is to make the goods available at its premises or at another named place (works, factory, warehouse, etc.).
                    The buyer bears all costs and risks involved in taking the goods from the seller’s premises to the desired destination.
                    This term represents the minimum obligation for the seller.

                    FCA - Free Carriers - The seller’s obligation is to hand over the goods, cleared for export, into the charge of the carrier named by the buyer at the named place or point.
                    The parties are advised to specify as clearly as possible the point within the named place of delivery, as the risk passes to the buyer at that point.
                    CPT - Carriage Paid To - The seller pays the freight for the delivery of goods to the carrier or to another person nominated by the seller at the named destination.
                    Once delivered, the risk of loss or damage to the goods is transferred from the seller to the buyer.
                    This term requires the seller to clear the goods for export.

                    CIP - Carriage and Insurance Paid To - The seller has the same obligations as under CPT but has the responsibility of obtaining insurance against the buyer’s risk of loss or damage to goods during carriage.
                    Insurance only needs to be obtained at minimum coverage and the seller is required to clear the goods for export.

                    DAT - Delivered At Terminal - The seller delivers when the goods, once unloaded from the arriving means of transport, are placed at the disposal of the buyer at a named terminal at the named port or place of destination.
                    “Terminal” includes quay, warehouse, container yard or road, rail or air terminal.
                    The seller is responsible for the export clearance procedures and the importer is responsible for clearing the goods for import, arranging import customs formalities, and paying import duty.

                    DAP - Delivered At Place - The seller delivers when the goods are placed at the disposal of thebuyer on the arriving means of transport ready for unloading at the named place of destination.
                    The seller is required to clear the goods for export and the importer is responsible for effecting customs clearance, and paying any customs duties.

                    DDP - Delivered Duty Paid - The seller is responsible for delivering the goods to the named place in the country of importation, including all costs and risks in bringing the goods to import destination.
                    This includes all export and import duties, taxes and customs formalities.
                    */

                    $xml->element('TermsOfTrade', $this->tradeTerms);
                $xml->pop(); //end Dutiable
            }
        if ($this->eccn != '') {
            $xml->push('ExportDeclaration');
            $xml->element('IsPartiesRelation', 'N');
            $xml->element('ECCN', $this->eccn);
            $xml->push('ExportLineItem');
            $xml->element('LineNumber', '1');
            $xml->element('Quantity', '1');
            $xml->element('QuantityUnit', 'EA');
            $xml->element('Description', $this->customsDescription);
            $xml->element('Value', $this->customsValue);
            $xml->element('ECCN', $this->eccn);
            $xml->push('Weight');
            $xml->element('Weight', $this->weight);
            $xml->element('WeightUnit', substr($this->weightUnit, 0, 1));
            $xml->pop(); //end Weight
                    $xml->pop(); //end ExportLineItem
                $xml->pop(); //end ExportDeclaration
        }
        if ($this->referenceValue != '') {
            $xml->push('Reference');
            $xml->element('ReferenceID', $this->referenceValue);
            $xml->pop(); //end Reference
        }
        $xml->push('ShipmentDetails');
        if ($this->packageCount == 0) {
            $xml->element('NumberOfPieces', '1');
        } else {
            $xml->element('NumberOfPieces', $this->packageCount);
        }
        $xml->push('Pieces');
        if (isset($this->core->packagesObject)) {
            $xml->append($this->core->packagesObject->getXml());
        } else {
            $xml->push('Piece');
            $xml->element('PieceID', '1');
            $xml->element('Weight', $this->weight);
            $xml->element('Width', $this->width);
            $xml->element('Height', $this->height);
            $xml->element('Depth', $this->length);
            $xml->pop(); //end Piece
        }
        $xml->pop(); //end Pieces
        if ($this->shipmentWeight != 0) {
            $xml->element('Weight', $this->shipmentWeight);
        } else {
            $xml->element('Weight', $this->weight);
        }
        $xml->element('WeightUnit', substr($this->weightUnit, 0, 1));
        if ($this->globalProductCode != '') {
            $xml->element('GlobalProductCode', $this->globalProductCode);
        } else {
            $xml->element('GlobalProductCode', $this->service);
        }
        if ($this->localProductCode != '') {
            $xml->element('LocalProductCode', $this->localProductCode);
        } else {
            $xml->element('LocalProductCode', $this->service);
        }
        if ($this->shipDate != '') {
            $xml->element('Date', $this->shipDate);
        } else {
            $xml->element('Date', date('Y-m-d'));
        }
        $xml->element('Contents', $this->customsDescription);
        $xml->element('DimensionUnit', substr($this->lengthUnit, 0, 1));
        if ($this->insuredValue != '') {
            $xml->element('InsuredAmount', $this->insuredValue);
        }
        $xml->element('IsDutiable', $this->isDutiable);
        if ($this->currency == '') {
            $xml->element('CurrencyCode', 'USD');
        } else {
            $xml->element('CurrencyCode', $this->currency);
        }
        $xml->pop(); //end ShipmentDetails
            $xml->push('Shipper');
                $xml->element('ShipperID', $this->accountNumber);
                $xml->element('CompanyName', $this->shipper);
                $xml->element('RegisteredAccount', $this->accountNumber);
                $xml->element('AddressLine', $this->shipAddr1);
                $xml->element('AddressLine', $this->shipAddr2);
                $xml->element('City', $this->shipCity);
                $xml->element('PostalCode', $this->shipCode);
                $xml->element('CountryCode', $this->shipCountry);
                $xml->element('CountryName', $this->shipCountry);
                $xml->push('Contact');
                $xml->element('PersonName', $this->shipContact);
                $xml->element('PhoneNumber', $this->shipPhone);
                $xml->pop(); //end Contact
            $xml->pop(); //end Shipper

            if ($this->specialServiceType != '') {
                $xml->push('SpecialService');
                    $xml->element('SpecialServiceType', $this->specialServiceType);
                $xml->pop();
            }

            $xml->element('LabelImageFormat', $this->labelPrintMethodCode);

        if ($this->requestArchiveDoc != '') {
            $xml->element('RequestArchiveDoc', 'Y');
        } else {
            $xml->element('RequestArchiveDoc', 'N');
        }

        $xml->pop(); // end ShipmentValidateRequest
        return $xml->getXml();
    }

    public function sendDHLShipment()
    {
        //$xmlString = $this->buildDHLShipmentXml();
        $xmlString = $this->buildDHLGlobalShipmentXml();

        // Put the xml that is sent to DHL into a variable so we can call it later for debugging.
        $this->core->xmlSent = $xmlString;
        $this->core->xmlResponse = $this->core->request('Shipment', $xmlString);

        return $this->simplifyResponse($this->arrayFromXml($this->core->xmlResponse));
    }

    public function simplifyResponse($a)
    {
        if (isset($a['ErrorResponse']['Response']['Status']['Condition']['ConditionData'])) {
            return array('error' => $a['ErrorResponse']['Response']['Status']['Condition']['ConditionData']);
        }

        if (isset($a['ShipmentValidateErrorResponse']['Response']['Status']['Condition']['ConditionData'])) {
            return array('error' => $a['ShipmentValidateErrorResponse']['Response']['Status']['Condition']['ConditionData']);
        }

        $trackNo = '';
        if (isset($a['ShipmentResponse']['AirwayBillNumber'])) {
            $trackNo = $a['ShipmentResponse']['AirwayBillNumber'];
        }

        $simpleResp = array(
            'charges' => 0.00,
            'trk_main' => $trackNo,
            'pkgs' => array(),
        );

        $labelImg = '';
        if (isset($a['ShipmentResponse']['LabelImage']['OutputImage'])) {
            $labelImg = $a['ShipmentResponse']['LabelImage']['OutputImage'];
        }

        $labelFmt = '';
        if (isset($a['ShipmentResponse']['LabelImage']['OutputFormat'])) {
            $labelFmt = $a['ShipmentResponse']['LabelImage']['OutputFormat'];
        }

        $simpleResp['pkgs'][] = array(
            'pkg_trk_number' => $trackNo,
            'label_fmt' => $labelFmt,
            'label_img' => $labelImg,
        );

        return $simpleResp;
    }

    public function addPackageToDHLshipment($package)
    {
        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = new xmlBuilder(true);
        }
        $this->packageCount = $this->packageCount + 1;
        $xml = $this->core->packagesObject;
        $xml->push('Piece');
        $xml->element('PieceID', $this->packageCount);
        $xml->element('Weight', $package->weight);
        $xml->element('Width', $package->width);
        $xml->element('Height', $package->height);
        $xml->element('Depth', $package->length);
        $xml->pop();

        if ($this->weight = '') {
            $this->weight = 0;
        }

        $this->shipmentWeight = (double) $this->shipmentWeight + (double) $package->weight;
        $this->core->packagesObject = $xml;
    }

    /* EA Region
        AT	AUSTRIA
        BE	BELGIUM
        CH	SWITZERLAND
        CZ	CZECH REPUBLIC, THE
        DE	GERMANY
        DK	DENMARK
        EE	ESTONIA
        ES	SPAIN
        FI	FINLAND
        FR	FRANCE
        GB	UNITED KINGDOM
        GR	GREECE
        HU	HUNGARY
        IE	IRELAND, REPUBLIC OF
        IS	ICELAND
        IT	ITALY
        LT	LITHUANIA
        LU	LUXEMBOURG
        LV	LATVIA
        NL	NETHERLANDS, THE
        NO	NORWAY
        PL	POLAND
        PT	PORTUGAL
        SE	SWEDEN
        SI	SLOVENIA
        SK	SLOVAKIA
    */

    /**
     * Creates random string of alphanumeric characters.
     *
     * @return string
     */
    public function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = str_shuffle($characters);

        return substr($characters, 0, $length);
    }
}
