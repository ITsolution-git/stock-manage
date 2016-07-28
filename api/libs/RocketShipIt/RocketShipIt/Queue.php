<?php

namespace RocketShipIt;

use RocketShipIt\Helper\XmlParser;

/**
 * Queueing class that is responsible for simultaneous requests.
 *
 * This class will take a RocketShipIt object such as
 * RocketShipRate, RocketShipShipment, etc. and
 * add them to a queue they will then be executed
 * with curl simultaneously.
 */
class Queue extends \RocketShipIt\Service\Base
{
    public $queue;
    public $mh;
    public $activeCurlHandles;

    public function __construct()
    {
        $this->queue = array();
        $this->mh = curl_multi_init();
        $this->activeCurlHandles = array();
        $this->xmlParser = new xmlParser($xmlResponse);
    }

    public function append($obj)
    {
        array_push($this->queue, $obj);

        return 1;
    }

    public function prepend($obj)
    {
        array_unshift($this->queue, $obj);

        return 1;
    }

    public function getCurlHandle($obj)
    {
        $c = get_class($obj);

        switch ($c) {
            case 'RocketShipRate':
                if ($obj->carrier == 'FEDEX') {
                    $xml = $obj->inherited->buildFEDEXRateXml();
                    $ch = $obj->core->request($xml, true);

                    return $ch;
                } elseif ($obj->carrier == 'UPS') {
                    $xml = $obj->inherited->buildUPSRateXml();
                    $ch = $obj->core->request('Rate', $xml, true);

                    return $ch;
                } elseif ($obj->carrier == 'USPS') {
                    $xml = $obj->inherited->buildUSPSRateXml();
                    $ch = $obj->core->request('ShippingAPI.dll', $xml, true);

                    return $ch;
                }
            case 'RocketShipShipment':
                return 'it is a shipment';
        }
    }

    public function executeCurlMultiRequest()
    {
        $a = array();

        $active = null;
        //execute the handles
        do {
            $mrc = curl_multi_exec($this->mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->mh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($this->activeCurlHandles as $ch) {
            $response = curl_multi_getcontent($ch);
            $xmlResponse = strstr($response, '<?');

            // Convert the xmlString to an array
            $xmlParser = $this->xmlParser;
            $xmlParser->load($xmlResponse);
            $xmlArray = $xmlParser->getData();

            array_push($a, $xmlArray);
            curl_multi_remove_handle($this->mh, $ch);
        }
        curl_multi_close($this->mh);

        return $a;
    }

    public function execute($max = 0)
    {
        if (sizeof($this->queue) > 0) {
            if ($max == 0) {
                foreach ($this->queue as $obj) {
                    $ch = $this->getCurlHandle($obj);
                    array_push($this->activeCurlHandles, $ch);
                    if ($ch != null) {
                        curl_multi_add_handle($this->mh, $ch);
                    } else {
                        $a = array('error' => 'Empty curl handler');
                    }
                }
                $a = $this->executeCurlMultiRequest();
            } else {
                $a = array_slice($this->queue, 0, $max);
            }

            return $a;
        } else {
            $a = array('error' => 'You must pass a RocketShipIt object into the RocketShipQueue object');

            return $a;
        }
    }
}
