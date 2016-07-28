<?php

namespace RocketShipIt\Carrier;

use RocketShipIt\Request;

/**
 * Core DHL Class.
 *
 * Used internally to send data, set debug information, change
 * urls, and build xml
 */
class Dhl extends \RocketShipIt\Carrier\Base
{
    public function __construct()
    {
        parent::__construct();
        $this->testingUrl = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet';
        $this->productionUrl = 'https://xmlpi-ea.dhl.com/XMLShippingServlet';
        $this->request = new Request();
    }

    public function request($type, $xml)
    {
        if ($this->mockXmlResponse != '') {
            if (is_array($this->mockXmlResponse)) {
                $mockXml = array_shift($this->mockXmlResponse);
            } else {
                $mockXml = $this->mockXmlResponse;
            }
            $this->xmlResponse = $mockXml;

            return $mockXml;
        }

        // This function is the only function that actually transmits and receives data
        // from UPS. All classes use this to send XML to UPS servers.
        $request = $this->request;
        $this->xmlSent = $xml;
        $request->url = $this->getUrl();
        $request->requestTimeout = $this->requestTimeout;
        $request->payload = $xml;
        $request->post();
        if ($request->getError()) {
            $error = $request->getError();
            $xml = "<error>$error</error>";
            $this->xmlResponse = $xml;

            return array($xml);
        }
        $curlReturned = $request->getResponse();
        $this->curlReturned = $curlReturned;
        // exit ($curlReturned);

        // Find out if the UPS service is down
        if ($request->getStatusCode() != 100 && $request->getStatusCode() != 200) {
            return array('error' => 'The DHL service seems to be down with HTTP/1.1 '. $request->getStatusCode());
        } else {
            $response = strstr($curlReturned, '<?'); // Separate the html header and the actual XML because we turned CURLOPT_HEADER to 1
            $this->xmlResponse = $response;

            return $response;
        }
    }

    public function generateRandomString()
    {
        $length = 32;
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $index = mt_rand(0, strlen($characters));
            $string .= substr($characters, $index, 1);
        }

        return $string;
    }

    public function getServiceDescriptionFromCode($code)
    {
        $serviceDescriptionMap = array(
            '0' => 'LOGISTICS SERVICES',
            '1' => 'CUSTOMS SERVICES',
            '2' => 'EASY SHOP',
            '3' => 'EASY SHOP',
            '4' => 'JETLINE',
            '5' => 'SPRINTLINE',
            '6' => 'SECURELINE',
            '7' => 'EXPRESS EASY EXPRESS EASY XED Y-Doc',
            '8' => 'EXPRESS EASY EXPRESS EASY XEP N-Non Doc',
            '9' => 'EUROPACK',
            'A' => 'AUTO REVERSALS',
            'B' => 'BREAK BULK EXPRESS',
            'C' => 'MEDICAL EXPRESS',
            'D' => 'EXPRESS WORLDWIDE',
            'E' => 'EXPRESS',
            'F' => 'FREIGHT WORLDWIDE',
            'G' => 'DOMESTIC ECONOMY',
            'H' => 'ECONOMY SELECT',
            'I' => 'BREAK BULK ECONOMY',
            'J' => 'JUMBO BOX',
            'K' => 'EXPRESS 9:00 EXPRESS 9:00 TDK Y-Doc',
            'L' => 'EXPRESS 10:30 EXPRESS 10:30 TDL Y-Doc',
            'M' => 'EXPRESS 10:30 EXPRESS 10:30 TDM N-Non Doc',
            'N' => 'DOMESTIC EXPRESS',
            'O' => 'OTHERS',
            'P' => 'EXPRESS WORLDWIDE EXPRESS WORLDWIDE WPX N-Non Doc',
            'Q' => 'MEDICAL EXPRESS MEDICAL EXPRESS WMX N-Non Doc',
            'R' => 'GLOBALMAIL BUSINESS GLOBALMAIL BUSINESS GMB Y-Doc',
            'S' => 'SAME DAY SAME DAY SDX Y-Doc',
            'T' => 'EXPRESS 12:00 EXPRESS 12:00 TDT Y-Doc',
            'U' => 'EXPRESS WORLDWIDE EXPRESS WORLDWIDE ECX Y-Doc',
            'V' => 'EUROPACK EUROPACK EPP N-Non Doc',
            'W' => 'ECONOMY SELECT ECONOMY SELECT ESU Y-Doc',
            'X' => 'EXPRESS ENVELOPE EXPRESS ENVELOPE XPD Y-Doc',
            'Y' => 'EXPRESS 12:00 EXPRESS 12:00 TDY N-Non Doc',
            'Z' => 'DESTINATION CHARGES DESTINATION',
        );

        if (isset($serviceDescriptionMap[$code])) {
            return $serviceDescriptionMap[$code];
        }

        return $code;
    }
}
