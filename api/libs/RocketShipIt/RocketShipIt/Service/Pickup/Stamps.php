<?php

namespace RocketShipIt\Service\Pickup;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;

class Stamps extends \RocketShipIt\Service\Common
{
    var $packages;
    public $request;

    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);    
    }

    public function buildRequest()
    {
        $this->request = array();
        $creds = $this->core->getCredentials();
        $request['Credentials'] = $creds;

        list($first, $last) = $this->splitContactName();
        $request['FirstName'] = $first;
        $request['LastName'] = $last;
        $request['Company'] = $this->pickupCompany;
        $request['Address'] = $this->pickupAddr1;
        $request['SuiteOrApt'] = $this->pickupApt;
        $request['City'] = $this->pickupCity;
        $request['State'] = $this->pickupState;
        $request['ZIP'] = $this->pickupCode;
        $request['ZIP4'] = $this->pickupCodeExtended;
        $request['PhoneNumber'] = $this->pickupPhone;
        $request['PhoneExt'] = $this->pickupPhoneExt;
        $request['NumberOfExpressMailPieces'] = $this->numberOfExpressMailPieces;
        $request['NumberOfPriorityMailPieces'] = $this->numberOfPriorityMailPieces;
        $request['NumberOfInternationalPieces'] = $this->numberOfInternationalPieces;
        $request['NumberOfOtherPieces'] = $this->numberOfOtherPieces;
        $request['TotalWeightOfPackagesLbs'] = $this->pickupTotalWeight;

        // FrontDoor -  Packages are at front door.
        // BackDoor - Packages are at back door.
        // SideDoor - Packages are at side door.
        // KnockOnDoorOrRingBell - Carrier needs to knock on door or ring bell to get the packages.
        // MailRoom - Packages are in mail room.
        // Office - Packages are in office.
        // Reception - Packages are at reception area.
        // InOrAtMailbox - Packages are in mail box.
        // Other - Packages are at the location other than above places. SpecialInstruction must be provided.
        $request['PackageLocation'] = $this->pickupLocation;

        $request['SpecialInstruction'] = $this->specialInstruction;

        $this->request = $request;

        return $this->request;
    }

    public function createPickupRequest()
    {
        $this->buildRequest();
        $response = $this->core->request('CarrierPickup', $this->request);

        return $response;
    }

    public function splitContactName()
    {
        $parts = explode(' ', $this->pickupContactName);
        if (count($parts) < 1) {
            return array('', '');
        }

        if (count($parts) == 1) {
            return array($parts[0], '');
        }

        if (count($parts) > 2) {
            return array($parts[0], end($parts));
        }

        return $parts;
    }
}
