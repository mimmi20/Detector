<?php
chdir(dirname(__DIR__));

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

// require Detector so we can popular identify the browser & populate $ua
use \Detector\Detector;

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);

if ($ua->isMobile) {
    include "web/templates/archive.mobile.inc.php";
} else {
    include "web/templates/archive.default.inc.php";
}
