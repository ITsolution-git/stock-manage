<?php

namespace RocketShipIt\Service\Shipment;

use \RocketShipIt\Helper\General;

class Royalmail extends \RocketShipIt\Service\Common
{
    
    public $shipmentRequest;
    public $packageObjs = array();

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
        $this->shipmentRequest = new \stdClass();
    }

    // get new initial request structure which is used
    // in several requests
    function getNewRequest()
    {
        return $request = array(
            'integrationHeader' => array(
                'dateTime' => gmdate('Y-m-d\TH:i:s'),
                'version' => '2',
                'identification' => array(
                    'applicationId' => $this->applicationId,
                    'transactionId' => $this->generateRandomString(),
                )
            )
        );
    }

    function buildCreateShipmentRequest()
    {
        $request = $this->getNewRequest();
        $weight = 0;
        if (count($this->packageObjs) > 0) {
            $weight = $this->packageObjs[0]->weight;
        }
        $request['requestedShipment'] = array(
            'shipmentType' => array('code' => 'Delivery'), // Delivery or Return
            'serviceOccurrence' => 1,
            'serviceType' => array('code' => $this->serviceType),
            'serviceOffering' => array(
                'serviceOfferingCode' => array(
                    'code' => $this->service,
                )
            ),
            'serviceFormat' => array('serviceFormatCode' => array('code' => '')),
            'shippingDate' => date('Y-m-d'),
            'recipientContact' => array(
                'name' => $this->toName,
                'complementaryName' => $this->toCompany
            ),
            'recipientAddress' => array(
                'addressLine1' => $this->toAddr1,
                'addressLine2' => $this->toAddr2,
                'postTown' => $this->toCity,
                'postcode' => $this->toCode
            ),
            'items' => array(
                'item' => array(
                    'numberOfItems' => 1,
                    'weight' => array(
                        'unitOfMeasure' => array(
                             // g is the only option
                            'unitOfMeasureCode' => array('code' => 'g')
                        ),
                        'value' => $weight,
                    )
                ),
            )
        );

        $this->shipmentRequest = $request;
    }

    function buildPrintLabelReq($shipmentNumber) {
        $request = $this->getNewRequest();
        $request['shipmentNumber'] = $shipmentNumber;

        return $request;
    }

    function submitShipment()
    {
        $this->response = array(
            'charges' => 0,
            'trk_main' => '',
            'pkgs' => array(
                array(
                    'pkg_trk_num' => '',
                    'label_fmt' => 'PDF', // only supported type
                    'label_img' => '',
                ),
            ),
            'errors' => array(),
        );

        // build and submit shipment request
        $this->buildCreateShipmentRequest();
        $resp = $this->core->request('createShipment', $this->shipmentRequest);

        if (is_soap_fault($resp)) {
            $this->response['errors'][] = array(
                'code' => $resp->faultcode,
                'description' => $resp->faultstring,
                'type' => 'Error',
            );

            return $this->response;
        }

        // check for shipmentNumber
        if (!isset($resp->completedShipmentInfo->allCompletedShipments->completedShipments->shipments->shipmentNumber)) {
            // if no shipmentNumber return error
            if (isset($resp->integrationFooter->errors->error)) {
                if (is_array($resp->integrationFooter->errors->error)) {
                    foreach ($resp->integrationFooter->errors->error as $e) {
                        $this->response['errors'][] = array(
                            'code' => $e->errorCode,
                            'description' => $e->errorDescription,
                            'type' => 'Error',
                        );
                    }
                } else {
                    $this->response['errors'][] = array(
                        'code' => $resp->integrationFooter->errors->error->errorCode,
                        'description' => $resp->integrationFooter->errors->error->errorDescription,
                        'type' => 'Error',
                    );
                }
            }
            $this->response['carrier_response'] = (array) $resp;

            return $this->response;
        }

        $shipmentNumber = $resp->completedShipmentInfo->allCompletedShipments->completedShipments->shipments->shipmentNumber;
        $this->response['trk_main'] = $shipmentNumber;
        $this->response['pkgs'][0]['pkg_trk_num'] = $shipmentNumber;
        
        // try to get label up to n times
        for ($i = 0; $i < 5; ++$i) {
            // if shipmentNumber build printLabel request
            $labelResp = $this->core->request('printLabel', $this->buildPrintLabelReq($shipmentNumber));
            if (isset($labelResp->label)) {
                $this->response['pkgs'][0]['label_img'] = base64_encode($labelResp->label);
                return $this->response;
            }

            // Wait to try again
            sleep(1.5);
        }

        // maxed out our tries
        $this->response['errors'][] = array(
            'description' => 'Cannot fetch label',
            'type' => 'Error',
        );

        return $this->response;
    }

    function addPackageToShipment($packageObj)
    {
        $this->packageObjs[] = $packageObj;
    }

    public function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = str_shuffle($characters);

        return substr($characters, 0, $length);
    }

    /*
    serviceType   Description
    1 -  Royal Mail 24 / 1st Class
    2 -  Royal Mail 48 / 2nd Class
    D -  Special Delivery Guaranteed
    H -  HM Forces (BFPO)
    I -  International
    R -  Tracked Returns
    T -  Royal Mail Tracked   

    serviceOffering           Description             
    CRL - ROYAL MAIL 24\48                
    CRL - ROYAL MAIL 24\48                
    DE1 - INTL BUS PARCELS ZERO SORT HI VOL PRIORITY I                
    DE3 - INTL BUS PARCELS ZERO SORT HI VOL ECONOMY               
    DE4 - INTL BUS PARCELS ZERO SRT LO VOL PRIORITY               
    DE6 - INTL BUS PARCELS ZERO SRT LO VOL ECONOMY                
    DG1 - INTL BUS MAIL L LTR CTRY SRT HI VOL PRIORITY                
    DG3 - INTL BUS MAIL L LTR CTRY SRT HI VOL ECONOMY                 
    DG4 - INTL BUS MAIL L LTR CTRY SRT LO VOL PRIORITY                
    DG6 - INTL BUS MAIL L LTR CTRY SRT LO VOL ECONOMY                 
    FS1 - ROYAL MAIL 24 (SORT8) (LL) FLAT RATE                
    FS2 - ROYAL MAIL 48 (SORT8) (LL) FLAT RATE                
    IE1 - INTL BUS PARCELS ZONE SORT PRIORITY                 
    IE3 - INTL BUS PARCELS ZONE SORT ECONOMY              
    IG1 - INTL BUS MAIL LRG LTR ZONE SORT PRI             
    IG3 - INTL BUS MAIL LRG LTR ZONE SORT ECONOMY             
    IG4 - INTL BUS MAIL LRG LTR ZONE SRT PRI MCH              
    IG6 - INTL BUS MAIL L LTR ZONE SRT ECONOMY MCH                
    MB1 - INTL BUS PARCELS PRINT DIRECT PRIORITY              
    MB2 - INTL BUS PARCELS PRINT DIRECT STANDARD              
    MB3 - INTL BUS PARCELS PRINT DIRECT ECONOMY               
    MP0 - INTL BUS PARCELS SIGNED EXTRA COMP CTRY             
    MP1 - INTL BUS PARCELS TRACKED                
    MP4 - INTL BUS PARCELS TRACKED EXTRA COMP             
    MP5 - INTL BUS PARCELS SIGNED             
    MP6 - INTL BUS PARCELS SIGNED EXTRA COMP              
    MP7 - INTL BUS PARCELS TRACKED COUNTRY PRICED             
    MP8 - INTL BUS PARCELS TRACKED EXTRA COMP CTRY                
    MP9 - INTL BUS PARCELS SIGNED COUNTRY PRICED              
    MTA - INTL BUS PARCELS TRACKED & SIGNED               
    MTB - INTL BUS PARCELS TRACKED SIGNED XTR COMP                
    MTC - INTL BUS MAIL TRACKED & SIGNED              
    MTD - INTL BUS MAIL TRACKED & SIGNED XTR COMP             
    MTE - INTL BUS PARCELS TRACKED & SIGNED  CTRY             
    MTF - INTL BUS PARCEL TRACK&SIGN XTR CMP CTRY             
    MTG - INTL BUS MAIL TRACKED & SIGNED COUNTRY              
    MTH - INTL BUS MAIL TRACK & SIGN XTR COMP CTRY                
    MTI - INTL BUS MAIL TRACKED               
    MTJ - INTL BUS MAIL TRACKED EXTRA COMP                
    MTK - INTL BUS MAIL TRACKED COUNTRY PRICED                
    MTL - INTL BUS MAIL TRACKED EXTRA COMP CTRY               
    MTM - INTL BUS MAIL SIGNED                
    MTN - INTL BUS MAIL SIGNED EXTRA COMP             
    MTO - INTL BUS MAIL SIGNED COUNTRY PRICED             
    MTP - INTL BUS MAIL SIGNED EXTRA COMP COUNTRY             
    MTQ - INTL BUS PARCELS ZONE SORT PLUS PRIORITY                
    MTS - INTL BUS PARCELS ZONE SRT PLUS ECONOMY              
    OLA - INTL STANDARD ON ACCOUNT                
    OLS - INTL ECONOMY ON ACCOUNT             
    OSA - INTERNATIONAL SIGNED ON ACCOUNT             
    OSB - INTL SIGNED ON ACCOUNT EXTRA COMP               
    OTA - INTERNATIONAL TRACKED ON ACCOUNT                
    OTB - INTL TRACKED ON ACCOUNT EXTRA COMP              
    OTC - INTERNATIONAL TRACKED & SIGNED ON ACCT              
    OTD - INTL TRACKED & SIGNED ON ACCT EXTRA COMP                
    OZ1 - INTL BUS MAIL MIXED ZONE SORT PRIORITY              
    OZ3 - INTL BUS MAIL MIXED ZONE SORT ECONOMY               
    OZ4 - INTL BUS MAIL MIXED ZONE SORT PRI MCH               
    OZ6 - INTL BUS MAIL MIXED ZONE SRT ECONOMY MCH                
    PK0 - ROYAL MAIL 48 (LL) FLAT RATE                
    PK1 - ROYAL MAIL 24 (SORT8) (P) FLAT RATE             
    PK2 - ROYAL MAIL 48 (SORT8) (P) FLAT RATE             
    PK3 - ROYAL MAIL 24 (SORT8) (LL\P) DAILY RATE             
    PK4 - ROYAL MAIL 48 (SORT8) (LL\P) DAILY RATE             
    PK9 - ROYAL MAIL 24 (LL) FLAT RATE                
    PPF - ROYAL MAIL 24\48 (P) FLAT RATE              
    PPF - ROYAL MAIL 24\48 (P) FLAT RATE              
    PS0 - INTL BUS PARCELS MAX SORT ECONOMY               
    PSC - INTL BUS PARCELS MAX SORT STANDARD              
    PS9 - INTL BUS PARCELS MAX SORT PRIORITY              
    PS8 - INTL BUS MAIL LRG LTR MAX SORT ECONOMY              
    PSB - INTL BUS MAIL LRG LTR MAX SORT STANDARD             
    PS7 - INTL BUS MAIL LRG LTR MAX SORT PRIORITY             
    PT1 - ROYAL MAIL TRACKED RETURNS 24   No longer available as of 23 Feb 2015           
    PT2 - ROYAL MAIL TRACKED RETURNS 48   No longer available as of 23 Feb 2015           
    RM0 - ROYAL MAIL 48 (SORT8) (P) DAILY RATE                
    RM1 - ROYAL MAIL 24 (LL) DAILY RATE               
    RM2 - ROYAL MAIL 24 (P) DAILY RATE                
    RM3 - ROYAL MAIL 48 (LL) DAILY RATE               
    RM4 - ROYAL MAIL 48 (P) DAILY RATE                
    RM5 - ROYAL MAIL 24 (P) FLAT RATE             
    RM6 - ROYAL MAIL 48 (P) FLAT RATE             
    RM7 - ROYAL MAIL 24 (SORT8) (LL) DAILY RATE               
    RM8 - ROYAL MAIL 24 (SORT8) (P) DAILY RATE                
    RM9 - ROYAL MAIL 48 (SORT8) (LL) DAILY RATE               
    SD1 - SD GUARANTEED BY 1PM                
    SD2 - SD GUARANTEED BY 1PM (£1000)                
    SD3 - SD GUARANTEED BY 1PM (£2500)                
    SD4 - SD GUARANTEED BY 9AM                
    SD5 - SD GUARANTEED BY 9AM (£1000)                
    SD6 - SD GUARANTEED BY 9AM (£2500)                
    STL - 1ST AND 2ND CLASS ACCOUNT MAIL              
    STL - 1ST AND 2ND CLASS ACCOUNT MAIL              
    TPL - ROYAL MAIL TRACKED 48 (HV)              
    TPN - ROYAL MAIL TRACKED 24               
    TPS - ROYAL MAIL TRACKED 48               
    TRM - ROYAL MAIL TRACKED 24 (HV)              
    TRN - ROYAL MAIL TRACKED 24 (LBT)             
    TRS - ROYAL MAIL TRACKED 48 (LBT)             
    TSN - ROYAL MAIL TRACKED RETURNS 24   Available as of 23 Feb 2015         
    TSS - ROYAL MAIL TRACKED RETURNS 48   Available as of 23 Feb 2015         
    WE1 - INTL BUS PARCELS ZERO SORT PRIORITY             
    WE3 - INTL BUS PARCELS ZERO SORT ECONOMY              
    WG1 - INTL BUS MAIL LRG LTR ZERO SRT PRIORITY             
    WG3 - INTL BUS MAIL LRG LTR ZERO SORT ECONOMY             
    WG4 - INTL BUS MAIL LRG LTR ZERO SRT PRI MCH              
    WG6 - INTL BUS MAIL L LTR ZERO SRT ECONOMY MCH                
    WW1 - INTL BUS MAIL MIXED ZERO SORT PRIORITY              
    WW3 - INTL BUS MAIL MIXED ZERO SORT ECONOMY               
    WW4 - INTL BUS MAIL MIXED ZERO SORT PRI MCH               
    WW6 - INTL BUS MAIL MIXD ZERO SRT ECONOMY MCH             
    ZC1 - INTL BUS MAIL MIXED ZERO SORT PREMIUM               
    */

}
