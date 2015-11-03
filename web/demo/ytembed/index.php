<?php
chdir(dirname(dirname(dirname(__DIR__))));

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

// require detector to get the family, autoloads the $ua var
use \Detector\Detector;

$html5Embed = "<iframe src=\"http://www.youtube.com/embed/N-zXaiDNKjU\" frameborder=\"0\" allowfullscreen></iframe>";
$simpleLink   = "Your browser doesn't appear to support HTML5. <a href=\"http://www.youtube.com/watch?v=N-zXaiDNKjU\">Check out the video on YouTube</a>.";

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);

// switch templates based on device type
if ($ua->isMobile) {
    include "web/demo/ytembed/templates/index.mobile.inc.php";
} else {
    include "web/demo/ytembed/templates/index.default.inc.php";
}
