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
use \Detector\FeatureFamily;

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();

// include the browserFamily library to classify the browser by features
$ua->family = FeatureFamily::find($ua);
var_dump($ua, Detector::$foundIn);
// include some helpful functions
include 'web/templates/_convertTF.inc.php';
include 'web/templates/_createFT.inc.php';

// switch templates based on device type
if (isset($ua->isMobile) && $ua->isMobile && (Detector::$foundIn != 'archive')) {
    include 'web/templates/index.mobile.inc.php';
} else {
    include 'web/templates/index.default.inc.php';
}
