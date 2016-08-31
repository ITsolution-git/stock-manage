<?php

// Copyright RocketShipIt LLC All Rights Reserved
// For Support email: support@rocketship.it

// Feel free to modify the following defaults:
return array(

    //{{GENERIC
    /*
    * This is used to set generic defaults.  I.e. They are
    * not carrier-specific.
    *
    * These defaults will be used across all carriers.  They can be
    * overwritten on the
    * shipment/package level.
    */
    'generic' => array(

        // 1 for Debug mode, 0 for normal operations
        // This also changes from testing to production mode
        'debugMode' => 1,

        // Your RocketShipIt demo API or self-hosted API key
        'apiKey' => '',

        // Default timezone
        // You can find out which timezones are available here:
        // http://php.net/manual/en/timezones.php
        'timezone' => 'America/Denver',

        // Your company name
        'shipper' => 'RocketShipIt',

        // Key shipping contact individual at your company
        'shipContact' => 'Mark Sanborn',

        // Shipper's address
        'shipAddr1' => '201 1/2 W 2nd St.',
        'shipAddr2' => '',
        'shipCity' => 'Whitehall',

        // the two-letter State or Province code
        // ex. MT => Montana, ON => Ontario
        'shipState' => 'MT',

        // Shipper's ZIP or Postal code
        'shipCode' => '59759',

        // Shipper's two-letter country code
        'shipCountry' => 'US',

        // Shipper's Phone number in this format: 1234567890
        'shipPhone' => '7077262676',

        // Default destination country
        'toCountry' => 'US',

        // General currency for things like COD and insurance
        'currency' => 'USD',

    ),
    //}}

    //{{UPS
    /*
    * This is used to set UPS specfic defaults.
    *
    * These defaults will be used for UPS calls only.  They can be
    * overwritten on the
    * shipment/package level using the setParameter() function.
    */
    'ups' => array(

        // Your UPS Developer API Key 
        'license' => '6D12D4E045632698',

        // your UPS Developer username
        'username' => 'Codal',

        // your UPS Developer password
        'password' => 'Mobile1357',

        // Your UPS account number
        'accountNumber' => '11YW27',

        // Make sure addresses are valid before label creation
        // validate, nonvalidate
        'verifyAddress' => 'validate',

        // Options
        // 01 - Daily Pickup
        // 03 - Customer Counter
        // 06 - One Time Pickup
        // 07 - On Call Air
        // 11 - Suggested Retail Rates
        // 19 - Letter Center
        // 20 - Air Service Center
        'PickupType' => '01',

        // Label format type
        // ZPL - Zebra UPS Thermal Printers
        // EPL - Eltron UPS Thermal Printers
        // GIF - Image based, desktop inkjet printers
        // STARPL
        // SPL
        'labelPrintMethodCode' => 'GIF',

        // Used when printing GIF images
        'httpUserAgent' => 'Mozilla/4.5',

        // Only valid option for ZPL, EPL, STARPL, and SPL is 4
        // When using inches use whole numbers only
        'labelHeight' => '4',

        // Options are 6 or 8 inches
        'labelWidth' => '8',

        // Options
        // GIF - A gif image
        'labelImageFormat' => 'GIF',

        // LBS or KGS
        'weightUnit' => 'LBS',

        // IN, or CM
        'lengthUnit' => 'IN',

        // See the ups manual for a list of all currency types
        'insuredCurrency' => 'USD',

        // two-letter country code
        'toCountryCode' => 'US',

        'shipmentDescription' => 'My Shipment',

        // Options
        // 01 - UPS Next Day Air
        // 02 - UPS Second Day Air
        // 03 - UPS Ground
        // 07 - UPS Worldwide Express
        // 08 - UPS Worldwide Expedited
        // 11 - UPS Standard
        // 12 - UPS Three-Day Select
        // 13 - Next Day Air Saver
        // 14 - UPS Next Day Air Early AM
        // 54 - UPS Worldwide Express Plus
        // 59 - UPS Second Day Air AM
        // 65 - UPS Saver
        'service' => '03',

        // Options
        // 01 - UPS Letter
        // 02 - Your Packaging
        // 03 - Tube
        // 04 - PAK
        // 21 - Express Box
        // 24 - 25KG Box
        // 25 - 10KG Box
        // 30 - Pallet
        // 2a - Small Express Box
        // 2b - Medium Express Box
        // 2c - Large Express Box
        'packagingType' => '02',

        'packageDescription' => 'Rate',

        // Set '0' for commercial '1' for residential
        'residentialAddressIndicator' => '1',

        // Set '0' for retail rates '1' for negotiated
        // You must turn this on with your UPS account rep
        'negotiatedRates' => '0',

        // Options
        // AJ Accounts Receivable Customer Account
        // AT Appropriation Number
        // BM Bill of Lading Number
        // 9V Collect on Delivery (COD) Number
        // ON Dealer Order Number
        // DP Department Number
        // 3Q Food and Drug Administration (FDA) Product Code
        // IK Invoice Number
        // MK Manifest Key Number
        // MJ Model Number
        // PM Part Number
        // PC Production Code
        // PO Purchase Order Number
        // RQ Purchase Request Number
        // RZ Return Authorization Number
        // SA Salesperson Number
        // SE Serial Number
        // ST Store Number
        // TN Transaction Reference Number
        'referenceCode' => '02',

        // Options
        // 2 - UPS Print and Mail (PNM)
        // 3 - UPS Return Service 1-Attempt (RS1)
        // 5 - UPS Return Service 3-Attempt (RS3)
        // 8 - UPS Electronic Return Label (ERL)
        // 9 - UPS Print Return Label (PRL)
        'returnCode' => '',

        // Options
        // 00 - Rates Associated with Shipper Number
        // 01 - Daily Rates
        // 04 - Retail Rates
        // 53 - Standard List Rates
        'customerClassification' => '',

    ),
    //}}

    //{{FEDEX
    /*
    * This is used to set FedEx specfic defaults.
    *
    * These defaults will be used for FedEx calls only.  They can be
    * overwritten on the
    * shipment/package level using the setParameter() function.
    */

    'fedex' => array(

        // Your FedEx developer key
        'key' => 'YZgdMgtg7dAA4BLl',

        // Your FedEx developer password
        'password' => 'zpgyrXQDy6MyGaGLNgXbFv4G2',

        // Your FedEx accountNumber
        'accountNumber' => '510087704',

        // Your FedEx meter number
        'meterNumber' => '100296167',

        // REGULAR_PICKUP
        // REQUEST_COURIER
        // DROP_BOX
        // BUSINESS_SERVICE_CENTER
        // STATION
        'dropoffType' => 'REGULAR_PICKUP',

        // Allowed packaging types:
        // FEDEX_10KG_BOX
        // FEDEX_25KG_BOX
        // FEDEX_BOX
        // FEDEX_ENVELOPE
        // FEDEX_PAK
        // FEDEX_TUBE
        // YOUR_PACKAGING
        'packagingType' => 'YOUR_PACKAGING',

        // The two possible weight units are LB and KG
        'weightUnit' => 'LB',

        // The two possible length units are IN and CM
        'lengthUnit' => 'IN',

        // EUROPE_FIRST_INTERNATIONAL_PRIORITY
        // FEDEX_1_DAY_FREIGHT
        // FEDEX_2_DAY
        // FEDEX_2_DAY_FREIGHT
        // FEDEX_3_DAY_FREIGHT
        // FEDEX_EXPRESS_SAVER
        // FEDEX_GROUND
        // FIRST_OVERNIGHT
        // GROUND_HOME_DELIVERY
        // INTERNATIONAL_ECONOMY
        // INTERNATIONAL_ECONOMY_FREIGHT
        // INTERNATIONAL_FIRST
        // INTERNATIONAL_PRIORITY
        // INTERNATIONAL_PRIORITY_FREIGHT
        // PRIORITY_OVERNIGHT
        // SMART_POST
        // STANDARD_OVERNIGHT
        // FEDEX_FREIGHT
        // FEDEX_NATIONAL_FREIGHT
        'service' => 'FEDEX_GROUND',

        // COLLECT
        // RECIPIENT
        // SENDER
        // THIRD_PARTY
        'paymentType' => 'SENDER',

        // DPL
        // EPL2
        // PDF
        // PNG
        // ZPLII
        'imageType' => 'PNG',

        // PAPER_4X6
        // PAPER_4X8
        // PAPER_4X9
        // PAPER_7X4.75
        // PAPER_8.5X11_BOTTOM_HALF_LABEL
        // PAPER_8.5X11_TOP_HALF_LABEL
        // STOCK_4X6
        // STOCK_4X6.75_LEADING_DOC_TAB
        // STOCK_4X6.75_TRAILING_DOC_TAB
        // STOCK_4X8
        // STOCK_4X9_LEADING_DOC_TAB
        // STOCK_4X9_TRAILING_DOC_TAB
        'labelStockType' => 'PAPER_4X6',

        // BILL_OF_LADING
        // COD_RETURN_TRACKING_NUMBER
        // CUSTOMER_AUTHORIZATION_NUMBER
        // CUSTOMER_REFERENCE
        // DEPARTMENT
        // FREE_FORM_REFERENCE
        // GROUND_SHIPMENT_ID
        // GROUND_MPS
        // INVOICE
        // PARTNER_CARRIER_NUMBER
        // PART_NUMBER
        // PURCHASE_ORDER
        // RETURN_MATERIALS_AUTHORIZATION
        // TRACKING_CONTROL_NUMBER
        // TRACKING_NUMBER_OR_DOORTAG
        // SHIPPER_REFERENCE
        // STANDARD_MPS
        'trackingIdType' => 'TRACKING_NUMBER_OR_DOORTAG',

        // Currency for Insurance
        'insuredCurrency' => 'USD',

        // COD (Collect On Delivery) - YES or NO
        'collectOnDelivery' => 'NO',

        // Hold at Location - YES or NO
        'holdAtLocation' => 'NO',

        // Saturday Delivery - YES or NO
        'saturdayDelivery' => 'NO',

        // ANY
        // CASH
        // GUARANTEED_FUNDS
        'codCollectionType' => 'ANY',

        // Don't change from COMMON2D unless you
        // have a specific reason
        // COMMON2D
        // LABEL_DATA_ONLY
        'labelFormatType' => 'COMMON2D',

    ),
    //}}

    //{{USPS
    /*
    * This is used to set USPS specfic defaults.
    *
    * These defaults will be used for USPS calls only.  They can be
    * overwritten on the
    * shipment/package level using the setParameter() function.
    */
    'usps' => array(

        // USPS userID
        'userid' => '486ABC001734',
        'service' => 'Priority',
        'imageType' => 'PNG',

    ),

    //}}

    //{{STAMPS
    /*
    * This is used to set Stamps.com specfic defaults.
    *
    * These defaults will be used for Stamps.com calls only.  They can be
    * overwritten on the
    * shipment/package level using the setParameter() function.
    */
    'stamps' => array(

        // USPS Stamps.com Credentials
        'username' => 'Codal-001',
        'password' => 'postage1',

        // Label Image Type
        //  Zpl
        //  EncryptedPngUrl
        //  PrintOncePdf
        //  Jpg
        //  Epl
        //  Pdf
        //  Gif
        //  Png
        //  Auto
        'imageType' => 'Png',

        'packagingType' => '',

    ),
    //}}

    //{{DHL
    'dhl' => array(

        'siteId' => 'YOUR_DHL_USERNAME',
        'password' => 'YOUR_DHL_PASSWORD',
        'accountNumber' => 'YOUR_DHL_ACCOUNT_NUMBER',

        // The two possible length units are IN and CM
        'lengthUnit' => 'IN',

        // The two possible length units are LB and KG
        'weightUnit' => 'LB',

        // AWBNumber or LPNumber
        'trackingIdType' => 'AWBNumber',

        // EPL2, PDF, ZPL2, LP2
        'labelPrintMethodCode' => 'PDF',

    ),
    //}}

    //{{CANADA
    'canada' => array(

        'username' => 'YOUR_CANADAPOST_USERNAME',
        'password' => 'YOUR_CANADAPOST_PASSWORD',
        'accountNumber' => 'YOUR_CANADAPOST_ACCOUNT_NUMBER',
        'service' => 'DOM.EP',

    ),
    //}}

    //{{PUROLATOR
    'purolator' => array(

        'username' => 'YOUR_PUROLATOR_USERNAME',
        'password' => 'YOUR_PUROLATOR_PASSWORD',
        'accountNumber' => 'YOUR_PUROLATOR_ACCOUNT_NUMBER',

        // lb or kg
        'weightUnit' => 'lb',

        // DropOff or PreScheduled
        'pickupType' => 'PreScheduled',

        'service' => 'PurolatorExpress',
    ),
    //}}

    //{{ONTRAC
    'ontrac' => array(

        'accountNumber' => '37',
        'password' => 'testpass',

    ),
    //}}

    //{{ROYALMAIL
    'royalmail' => array(

        // Client ID/Secret from: https://developer.royalmail.net
        'clientId' => '',
        'clientSecret' => '',
        'username' => 'youruserAPI', // usually has API suffix
        'password' => '',
        'applicationId' => '', // RM customer account number

    ),
    //}}

);
