<?php

namespace RocketShipIt\Service\ScanForm;

/**
* Creates ScanFrom document for USPS
*
* Valid carriers are: STAMPS
*/
class Stamps extends \RocketShipIt\Service\Common
{

    public function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    public function build()
    {
        $address = new \stdClass;
        $address->Address1 = $this->shipAddr1;
        if ($this->shipAddr2 != '') {
            $address->Address2 = $this->shipAddr2;
        }
        $address->City = $this->shipCity;
        $address->State = $this->shipState;
        if ($this->shipCode != '') {
            $parts = explode('-', $this->shipCode);
            if (count($parts) > 1) {
                $address->ZIPCode = $parts[0];
                $address->ZIPCodeAddOn = $parts[1];
            } else {
                $address->ZIPCode = $parts[0];
            }
        }
        if ($this->shipCountry != '') {
            $address->Country = $this->shipCountry;
        }
        if ($this->shipPhone != '') {
            $address->PhoneNumber = $this->shipPhone;
        }

        $request = array();
        if ($this->authToken != '') {
            $request['Authenticator'] = $this->authToken;
        } else {
            $creds = $this->core->getCredentials();
            $request['Credentials'] = $creds;
        }

        if ($this->shipmentIdentification) {
            $request['StampsTxIDs'] = $this->shipmentIdentification;
        }
        $request['FromAddress'] = $address;
        if ($this->imageType != '') {
            $request['ImageType'] = $this->imageType;
        } else {
            $request['ImageType'] = 'Pdf';
        }
        if ($this->printInstructions === false) {
            $request['PrintInstructions'] = false;
        } else {
            $request['PrintInstructions'] = true;
        }

        if ($this->shipDate != '') {
            $request['ShipDate'] = $this->shipDate;
        } else {
            $request['ShipDate'] = date('Y-m-d', strtotime('tomorrow'));
        }

        return $request;
    }

    public function create()
    {
        $request = $this->build();
        return $this->core->request('CreateScanForm', $request);
    }
}
