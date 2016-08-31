<?php

namespace RocketShipIt\Service\Rate;

use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\XmlParser;

/**
* Main Rate class for producing rates for various packages/shipments
*
* This class is a wrapper for use with all carriers to produce rates 
* Valid carriers are: UPS, USPS, Stamps.com, DHL, Canada Post, Purolator and FedEx.
*/
class Purolator extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    var $packageCount;
    
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    // Split street number and street name from given addr
    public function getAddressParts($address)
    {
        $matches = array();
        $result = preg_match("/(\d+\w*)\s(.*)/", $address, $matches);
        if (!$result) {
            return array('street_number' => '', 'street_name' => $address);
        }
        return array('street_number' => $matches[1], 'street_name' => $matches[2]);
    }

    public function buildRequest()
    {
        $request = array();
        //Populate the Origin Information
        $request['Shipment']['SenderInformation']['Address']['Name'] = $this->shipper;
        $addrParts = $this->getAddressParts($this->shipAddr1);
        $request['Shipment']['SenderInformation']['Address']['StreetNumber'] = $addrParts['street_number'];
        $request['Shipment']['SenderInformation']['Address']['StreetName'] = $addrParts['street_name'];
        $request['Shipment']['SenderInformation']['Address']['City'] = $this->shipCity;
        $request['Shipment']['SenderInformation']['Address']['Province'] = $this->shipState;
        $request['Shipment']['SenderInformation']['Address']['Country'] = $this->shipCountry;
        $request['Shipment']['SenderInformation']['Address']['PostalCode'] = $this->shipCode;    
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['CountryCode'] = '1';
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['AreaCode'] = '905';
        $request['Shipment']['SenderInformation']['Address']['PhoneNumber']['Phone'] = '5555555';

        //Populate the Desination Information
        $request['Shipment']['ReceiverInformation']['Address']['Name'] = $this->toName;
        $addrParts = $this->getAddressParts($this->toAddr1);
        $request['Shipment']['ReceiverInformation']['Address']['StreetNumber'] = $addrParts['street_number'];
        $request['Shipment']['ReceiverInformation']['Address']['StreetName'] = $addrParts['street_name'];
        $request['Shipment']['ReceiverInformation']['Address']['City'] = $this->toCity;
        $request['Shipment']['ReceiverInformation']['Address']['Province'] = $this->toState;
        $request['Shipment']['ReceiverInformation']['Address']['Country'] = $this->toCountry;
        $request['Shipment']['ReceiverInformation']['Address']['PostalCode'] = $this->toCode;
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['CountryCode'] = '1';
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['AreaCode'] = '604';
        $request['Shipment']['ReceiverInformation']['Address']['PhoneNumber']['Phone'] = '2982181';

        //Future Dated Shipments - YYYY-MM-DD format
        $request['Shipment']['ShipmentDate'] = date('Y-m-d');

        //Populate the Package Information
        $request['Shipment']['PackageInformation']['TotalWeight']['Value'] = $this->weight;
        $request['Shipment']['PackageInformation']['TotalWeight']['WeightUnit']= $this->weightUnit;
        $request['Shipment']['PackageInformation']['TotalPieces'] = '1';
        $request['Shipment']['PackageInformation']['ServiceID'] = $this->service;

        //Populate the Payment Information
        $request['Shipment']['PaymentInformation']['PaymentType'] = 'Sender';
        $request['Shipment']['PaymentInformation']['BillingAccountNumber'] = $this->accountNumber;
        $request['Shipment']['PaymentInformation']['RegisteredAccountNumber'] = $this->accountNumber;

        //Populate the Pickup Information
        $request['Shipment']['PickupInformation']['PickupType'] = $this->pickupType;
        $request['ShowAlternativeServicesIndicator'] = 'true';

        //Define OptionsInformation
        //ResidentialSignatureDomestic
        //$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->ID = "ResidentialSignatureDomestic";
        //$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->Value = "true";

        //ResidentialSignatureIntl
        //$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->ID = "ResidentialSignatureIntl";
        //$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->Value = "true";

        //OriginSignatureNotRequired
        $request['Shipment']['PackageInformation']['OptionsInformation']['Options']['OptionIDValuePair']['ID'] = 'OriginSignatureNotRequired';
        $request['Shipment']['PackageInformation']['OptionsInformation']['Options']['OptionIDValuePair']['Value'] = 'true';

        return $request;
    }

    public function getAllRates()
    {
        return $this->core->request('GetFullEstimate', $this->buildRequest());
    }

    public function getSimpleRates($user_func=null)
    {
        $rateResponse = $this->getAllRates();
        if (!isset($rateResponse->ShipmentEstimates->ShipmentEstimate)) {
            return $rateResponse;
        }

        $rates = array();
        foreach ($rateResponse->ShipmentEstimates->ShipmentEstimate as $rate) {
            $simpleRate = array(
                'desc' => $rate->ServiceID,
                'rate' => $rate->TotalPrice,
                'service_code' => $rate->ServiceID
            );
            if (!empty($user_func)) {
                $simpleRate = call_user_func($user_func, end(explode('\\', __CLASS__)), $rate, $simpleRate);
            }
            $rates[] = $simpleRate;
        }
        return $rates;
    }

    public function getRate()
    {
        return 'Method not available';
    }

    public function addPackageToShipment($packageObj)
    {
        return 'method not available';
    }

    public function getSimpleRate()
    {
        return 'method not available';
    }

}
