<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core Stamp.com Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Stamps extends \RocketShipIt\Carrier\Base
{
    public $xmlSent;
    public $xmlPrevResponse;
    public $xmlResponse;
    public $paramSynonyms = array(
        'weight' => 'weightPounds',
    );

    public $serviceDescriptions = array(
        'US-PM' => 'USPS Priority Mail',
        'US-PMI' => 'USPS Priority Mail International',
        'US-XM' => 'USPS Express Mail',
        'US-EMI' => 'USPS Express Mail International',
        'US-PP' => 'USPS Parcel Post',
        'US-MM' => 'USPS Media Mail',
        'US-FC' => 'USPS First Class Mail',
        'US-FCI' => 'USPS First Class Mail International',
        'US-BP' => 'USPS Bound Printed Matter',
        'US-LM' => 'USPS Library Mail',
        'US-PS' => 'USPS Parcel Select',
        'US-CM' => 'USPS Critical Mail',
    );

    public function __construct()
    {
        parent::__construct();

        $creds = array();
        // DO NOT CHANGE THIS INTEGRATION ID
        $creds['IntegrationID'] = 'e13dde83-59b9-4b45-9a51-3f83016fd883';
        $creds['Username'] = $this->config->getDefault('stamps', 'username');
        $creds['Password'] = $this->config->getDefault('stamps', 'password');
        $this->debugMode = $this->config->getDefault('generic', 'debugMode');
        $this->mediaRequest = new Request();

        $this->credentials = $creds;
    }

    public function getCredentials()
    {
        $creds = array();
        // DO NOT CHANGE THIS INTEGRATION ID
        $creds['IntegrationID'] = 'e13dde83-59b9-4b45-9a51-3f83016fd883';
        $creds['Username'] = $this->username;
        $creds['Password'] = $this->password;
        $this->debugMode = $this->debugMode;

        $this->credentials = $creds;

        return $this->credentials;
    }

    public function request($action, $request)
    {
        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => 0,
        );
        // If proxy is specified as enviornment variable pass it in.
        if (getenv('PROXY_HOST')) {
            $options['proxy_host'] = getenv('PROXY_HOST');
        }
        if (getenv('PROXY_PORT')) {
            $options['proxy_port'] = getenv('PROXY_PORT');
        }
        if (getenv('STAMPS_URL')) {
            $options['location'] = getenv('STAMPS_URL');
        }

        $options['connection_timeout'] = $this->requestTimeout;

        $wsdl = ROCKETSHIPIT_RESOURCE_PATH.'/wsdls/stamps.wsdl';

        $client = new \RocketShipIt\Helper\SoapClient($wsdl, $options);

        // Allows for mocking of soap requests
        if ($this->mockXmlResponse != '') {
            $client->mockXmlResponse = $this->mockXmlResponse;
        }

        if ($this->validateOnly != '') {
            $client->validate_only == true;
        }

        $response = $client->$action($request);

        $this->xmlResponse = $client->__getLastResponse();
        $this->xmlSent = $client->__getLastRequest();

        if (isset($response->Authenticator)) {
            $this->authToken = $response->Authenticator;
        }

        return $response;
    }

    public function mediaRequest($url)
    {
        if ($this->proxyHost != '' && $this->proxyPort != '') {
            $this->mediaRequest->proxyUrl = $this->proxyHost;
            $this->mediaRequest->proxyPort = $this->proxyPort;
        }

        $this->mediaRequest->url = $url;
        $this->mediaRequest->doRequest();
        $media = $this->mediaRequest->response;

        return base64_encode($media);
    }

    public function access()
    {
        $auth = new \stdClass();
        $auth->Credentials = $this->credentials;

        return $this->request('AuthenticateUser', $auth);
    }

    public function getAccountInfo()
    {
        $info = new \stdClass();
        $info->Credentials = $this->credentials;

        return $this->request('GetAccountInfo', $info);
    }

    // May not be enabled on your account
    public function getShipmentList($startDate)
    {
        $info = new \stdClass();
        $info->Credentials = $this->credentials;

        $filters = new \stdClass();
        $filters->PrintDateMin = $startDate;

        $info->Filters = $filters;

        return $this->request('GetShipmentList', $info);
    }

    public function getUrl()
    {
        $url = new \stdClass();
        $url->Credentials = $this->credentials;
        $url->URLType = 'AccountSettingsPage';

        return $this->request('GetUrl', $url);
    }

    public function purchasePostage($amount)
    {
        $ai = $this->getAccountInfo();
        $controlAmount = $ai->AccountInfo->PostageBalance->ControlTotal;

        $response = $this->addPostage($amount, $controlAmount);

        return $response;
    }

    public function addPostage($amount, $controlAmount)
    {
        $p = new \stdClass();
        $p->Credentials = $this->credentials;
        $p->PurchaseAmount = $amount;
        $p->ControlTotal = $controlAmount;

        return $this->request('PurchasePostage', $p);
    }

    public function getPurchaseStatus($transactionId)
    {
        $ps = new \stdClass();
        $ps->Credentials = $this->credentials;
        $ps->TransactionID = $transactionId;

        return $this->request('GetPurchaseStatus', $ps);
    }

    public function getServiceDescriptionFromCode($code)
    {
        if (!isset($this->serviceDescriptions[$code])) {
            return 'Unknown service code';
        }

        return $this->serviceDescriptions[$code];
    }

    public function getCountryName($countryCode)
    {
        $converter = new \RocketShipIt\Helper\CountryConverter();

        return $converter->getCountryName($countryCode);
    }
}
