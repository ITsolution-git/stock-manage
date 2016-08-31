<?php

namespace RocketShipIt;

class Request
{
    public $statusCode;
    public $url;
    public $header;
    public $response;
    public $proxyUrl;
    public $proxyPort;
    public $ch;
    public $curlInfo;
    public $username;
    public $password;
    public $payload;
    public $error;

    public function __construct()
    {
        $this->statusCode = 0;
        $this->ch = curl_init();
        $this->curlInfo = array();

        // Set curl options
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($this->ch, CURLOPT_PROXY, 'http://127.0.0.1:8888');
        //curl_setopt($this->ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
    }

    public function setProxy()
    {
        if ($this->proxyUrl && $this->proxyPort) {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyUrl.':'.$this->proxyPort);
        }
    }

    public function doPostRequest()
    {
        if ($this->payload != '') {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->payload);
        }

        $this->setProxy();
        $this->doRequest();
    }

    public function doRequest()
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        if ($this->username != '' and $this->password != '') {
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        }
        if ($this->header) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
        }
        $this->setProxy();

        $this->response = curl_exec($this->ch);
        $this->curlInfo = curl_getinfo($this->ch);
        $this->error = curl_error($this->ch);
        //curl_close($this->ch);
    }

    public function post()
    {
        $this->doPostRequest();
    }

    public function get()
    {
        $this->doRequest();
    }

    public function delete()
    {
        $this->setCurlOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
        //$this->setCurlOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->doRequest();
    }

    public function getCurl()
    {
        return $this->ch;
    }

    public function getInfo()
    {
        return $this->curlInfo;
    }

    public function setCurlOption($option, $value)
    {
        curl_setopt($this->ch, $option, $value);
    }

    public function getResponse()
    {
        //return utf8_encode($this->response);
        return $this->response;
    }

    public function getStatusCode()
    {
        if (!empty($this->curlInfo)) {
            return $this->curlInfo['http_code'];
        }

        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }
}
