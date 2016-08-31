<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

class Royalmail extends \RocketShipIt\Carrier\Base
{
    public $xmlSent;
    public $xmlPrevResponse;
    public $xmlResponse;

    public function __construct()
    {
        parent::__construct();
    }

    public function buildSecurityHeader()
    {
        $time = gmdate('Y-m-d\TH:i:s');
        $created = gmdate('Y-m-d\TH:i:s\Z');
        $nonce = mt_rand();
        $nonceDatePwd = pack('A*', $nonce) . pack('A*', $created) . pack('H*', sha1($this->password));
        $passwordDigest = base64_encode(pack('H*', sha1($nonceDatePwd)));
        $eNonce = base64_encode($nonce);

        $headerXML = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
                              xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
   <wsse:UsernameToken wsu:Id="UsernameToken-000">
      <wsse:Username>'.$this->username.'</wsse:Username>
      <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">'.$passwordDigest.'</wsse:Password>
      <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">'.$eNonce.'</wsse:Nonce>
      <wsu:Created>'.$created.'</wsu:Created>
   </wsse:UsernameToken>
</wsse:Security>';

        // push the header into soap
        $o = new \SoapVar($headerXML, XSD_ANYXML);
        $header = new \SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd', 'Security', $o);

        return $header;
    }

    function getCredentialsHeader()
    {
        return sprintf("X-IBM-Client-Id: %s\nX-IBM-Client-Secret: %s", $this->clientId, $this->clientSecret);
    }

    public function request($action, $request)
    {
        $options = array(
            'trace' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => 0,
            'stream_context' => stream_context_create(array(
                'http' => array(
                    'header' => $this->getCredentialsHeader(),
                )
            )), 
        );

        $options['connection_timeout'] = $this->requestTimeout;

        $wsdl = ROCKETSHIPIT_RESOURCE_PATH.'/wsdls/royalmail/ShippingAPI_V2_0_9.wsdl';

        $client = new \RocketShipIt\Helper\SoapClient($wsdl, $options);
        $client->__setSoapHeaders($this->buildSecurityHeader());

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

        $this->transactions[] = array(
            'xmlSent' => $this->xmlSent,
            'xmlResponse' => $this->xmlResponse,
        );

        return $response;
    }
}
