<?php

namespace RocketShipIt\Service\UploadImage;

/**
* Main class for uploading images for use in customs documents.
*
* This class is a wrapper for use with all carriers to upload 
* images.
*/
class Fedex extends \RocketShipIt\Service\Common
{
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        parent::__construct($carrier);
    }

    function upload($validate_only=false)
    {
        $path_to_wsdl = ROCKETSHIPIT_RESOURCE_PATH . '/wsdls/upload-image.wsdl';

        if ($this->config->getDefault('generic', 'debugMode') == 1) {
            $soapUrl = "https://gatewaybeta.fedex.com:443/web-services/uploaddocument";
        } else {
            $soapUrl = "https://gateway.fedex.com:443/web-services/uploaddocument";
        }

        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new \RocketShipIt\Helper\SoapClient($path_to_wsdl, array('trace' => 1, 
                                                                  'cache_wsdl' => WSDL_CACHE_NONE,
                                                                  'location' => $soapUrl));
        $client->validate_only = $validate_only;

        $request['WebAuthenticationDetail'] = array('UserCredential' =>
                                                  array('Key' => $this->core->key,
                                                        'Password' => $this->core->password)); 

        $request['ClientDetail'] = array('AccountNumber' => $this->core->accountNumber,
                                         'MeterNumber' => $this->core->meterNumber);

        $request['TransactionDetail'] = array('CustomerTransactionId' => 'RocketShipIt');
        $request['Version'] = array('ServiceId' => 'cdus',
                                    'Major' => '1',
                                    'Intermediate' => '1',
                                    'Minor' => '0');

        //$request['OriginCountryCode'] = 'US';  
        //$request['DestinationCountryCode'] = 'CA';  

        if (file_exists($this->image)) {
            $request['Images'] = array('0' => array ('Id' => $this->imageId, 
                                              'Image' => stream_get_contents(fopen($this->image, "r"))));
        }

        //$client->__setLocation(setEndpoint('endpoint'));
        try {
            $response = $client->uploadImages($request);
            return $response;
        } catch (SoapFault $exception) {
            echo $client->__getLastRequest();  
            echo "\n";
            echo $client->__getLastResponse();  
            echo "\n";
        }
    }
}
