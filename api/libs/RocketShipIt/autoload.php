<?php

require 'RocketShipIt/RocketShipIt.php';

/**
 * Simple autoloader that follows the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more information.
 */
spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName = __DIR__ . DIRECTORY_SEPARATOR . $fileName . $className . '.php';
    if (file_exists($fileName)) {
        require $fileName;

        return true;
    }

    return false;
});
