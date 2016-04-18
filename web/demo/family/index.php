<?php
/* Simply prints out the name of the family based on the user agent using
   families.json without actually updating any of the profiles.
   You can also supply ?pid=[something] if you want */
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
use \Detector\FeatureFamily;

// if this is a request from features.js.php don't run the build function
$ua = Detector::build();//var_dump($ua);

// include the browserFamily library to classify the browser by features
print "family name: " . FeatureFamily::find($ua);
