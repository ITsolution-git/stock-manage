<?php

namespace RocketShipIt\Helper;

class SoapClient extends \SoapClient
{
    
    var $validate_only;
    var $mockXmlResponse;

    // Reason: To inspect/dissect/debug/adjust the XML given
    public function __doRequest($request, $location, $action, $version, $one_way = NULL)
    {
        if ($this->mockXmlResponse != '') {
            if (is_array($this->mockXmlResponse)) {
                $mockXml = array_shift($this->mockXmlResponse);
            } else {
                $mockXml = $this->mockXmlResponse;
            }
            return (string) $mockXml;
        }

        // Uncomment the following line, if you actually want to do the request
        if (isset($this->validate_only)) {
            if ($this->validate_only) {
                return '';
            }
        }
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }

}

