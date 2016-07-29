<?php

namespace RocketShipIt;

class Server
{
    public $allowedCarriers = array(
        'UPS',
        'FEDEX',
        'USPS',
        'DHL',
    );

    public $allowedTypes = array(
        'Void',
        'Rate',
        'TimeInTransit',
        'AddressValidate',
        'Track',
        'Shipment',
    );

    public function request($request)
    {
        // Parse JSON
        try {
            $request = json_decode($request, true);
        } catch (Exception $e) {
            return '{"error": "invalid json"}';
        }

        if (empty($request)) {
            return '{"error": "invalid json"}';
        }

        if (!isset($request['carrier'])) {
            return '{"error": "You must specify a carrier."}';
        }

        if (empty($request['carrier'])) {
            return '{"error": "You must specify a carrier."}';
        }

        if (!in_array(strtoupper($request['carrier']), $this->allowedCarriers)) {
            return '{"error": "The carrier you specified is not allowed."}';
        }

        if (!isset($request['type'])) {
            return '{"error": "You must specify a request type."}';
        }

        if (!in_array($request['type'], $this->allowedTypes)) {
            return '{"error": "Invalid request type.  This type may be allowed in RocketShipIt but not available on this server."}';
        }

        $class = '';
        $carrier = '';
        $action = '';
        $args = array();
        $parameters = array();
        $packages = array();
        $customs = array();

        $class = '\RocketShipIt\\'.$request['type'];
        $carrier = $request['carrier'];
        if (in_array('action', array_keys($request))) {
            $action = $request['action'];
        }
        if (in_array('parameters', array_keys($request))) {
            $parameters = $request['parameters'];
        }
        // if (in_array('customs', array_keys($request))) {
        //     $customs = $request['customs'];
        // }
        if (in_array('args', array_keys($request))) {
            $args = $request['args'];
        }

        $parametersToLoad = $parameters;
        //$parametersToLoad['customs'] = $packages;

        try {
            $obj = new $class(strtoupper($carrier));
            $obj->loadJsonParameters(json_encode($parametersToLoad));

            //$carrier_response = $obj->$action();
            if (!method_exists($obj, $action)) {
                return json_encode(array('error' => "The action: $action doesn't exist."));
            }

            $carrier_response = call_user_func_array(array($obj, $action), $args);

            $response['carrier_response'] = $carrier_response;

            if (in_array('debug', array_keys($request))) {
                $response['debug'] = $obj->debug();
            }
        } catch (Exception $e) {
            return json_encode(array('error' => 'Something went wrong.'));
        }

        return json_encode($response);
    }
}
