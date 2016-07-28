<?php

require_once 'RocketShipIt.php';

// For authentication set to true and make sure
// token matches the token parameter in your config file
define('ALLOW_AUTH_BY_CARRIER_CREDS', false);
define('TOKEN', 'abc123');

// TODO: If debug mode set return xmlsent/response etc.
class logger
{
    public function log($message, $obj = null)
    {
        $time = date('c');
        $log_message = $time.' '.$_SERVER['REMOTE_ADDR'].' '.$message."\n";
        if ($obj) {
            $log_message .= var_export($obj, true);
        }
        $log_message .= "\n\n";
        file_put_contents('log.txt', $log_message, FILE_APPEND);
    }
}

$logger = new logger();

// This server is running in cli mode
if (isset($argv)) {
    // Get JSON from argv
    if (count($argv) < 2) {
        echo '{"error": "Invalid request, was it blank?"}';
        exit;
    }
    $input = $argv[1];
} else {
    // This server is running in http mode
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    $input = file_get_contents('php://input');
    $logger->log('Request: ', $input);
}

// Parse JSON
try {
    $request = json_decode($input, true);
} catch (Exception $e) {
    $logger->log('Invalid json: ', $input);
    echo '{"error": "invalid json"}';
    exit;
}

// Simple token authentication
if (ALLOW_AUTH_BY_CARRIER_CREDS) {
    if (!isset($request['parameters']['token'])) {
        $logger->log('Unauthorized access: ', $input);
        echo '{"error": "unauthorized access"}';
        exit;
    }
    if ($request['parameters']['token'] != TOKEN) {
        $logger->log('Unauthorized access: ', $input);
        echo '{"error": "unauthorized access"}';
        exit;
    }
}

if (empty($request['carrier'])) {
    $logger->log('No carrier specified: ', $input);
    echo '{"error": "You must specify a carrier."}';
    exit;
}

if (empty($request['type'])) {
    $logger->log('No request type specified: ', $input);
    echo '{"error": "You must pass a request type."}';
    exit;
}

$valid_classes = array(
    'RocketShipVoid',
    'RocketShipRate',
    'RocketShipTimeInTransit',
    'RocketShipAddressValidate',
    'RocketShipTrack',
    'RocketShipShipment',
    'RocketShipPackage',
    'RocketShipCustoms',
);

if (!in_array($request['type'], $valid_classes)) {
    $logger->log('Error, invalid class: ', $input);
    echo '{"error": "Invalid or unsupported class type for this server.  This may be supported in RocketShipIt but not yet implemented on this server/demo."}';
    exit;
}

$class = '';
$carrier = '';
$action = '';
$args = array();
$parameters = array();
$packages = array();

$class = $request['type'];
$carrier = $request['carrier'];
if (in_array('action', array_keys($request))) {
    $action = $request['action'];
}
if (in_array('parameters', array_keys($request))) {
    $parameters = $request['parameters'];
}
if (in_array('packages', array_keys($request))) {
    $packages = $request['packages'];
}
if (in_array('args', array_keys($request))) {
    $args = $request['args'];
}

try {
    $obj = new $class($carrier);
    foreach ($parameters as $key => $value) {
        $obj->setParameter($key, $value);
    }

    if (in_array('packages', array_keys($request))) {
        foreach ($packages as $package) {
            $p = new \RocketShipIt\Package($carrier);
            foreach ($package as $key => $value) {
                $p->setParameter($key, $value);
            }
            call_user_func(array($obj, 'addPackageToShipment'), $p);
            //$obj->addPackageToShipment($package);
        }
    }

    //$carrier_response = $obj->$action();
    $carrier_response = call_user_func_array(array($obj, $action), $args);

    $response['carrier_response'] = $carrier_response;

    if (in_array('debug', array_keys($request))) {
        $response['debug'] = $obj->inherited->core->debug();
    }
} catch (Exception $e) {
    $logger->log('Error: ', $e);
    $logger->log('Response: ', $response);
    echo json_encode(array('error' => 'Something went wrong, we logged this and will take care of it shortly.'));
}

$logger->log('Response: ', $response);
$logger->log('Response: ', html_entity_decode($obj->inherited->core->debug()));
echo json_encode($response);
