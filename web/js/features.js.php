<?php
chdir(dirname(dirname(__DIR__)));

$autoloadPaths = array(
    'vendor/autoload.php',
    '../../autoload.php',
);

$foundVendorAutoload = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $foundVendorAutoload = true;
        break;
    }
}

if (!$foundVendorAutoload) {
    throw new Exception('Could not find autoload path in any of the searched locations');
}

$p = true; // turn off the build function
require 'lib/Detector/Detector.php';
header('content-type: application/x-javascript');
Detector::perrequest();
?>