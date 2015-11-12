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
use Modernizr\Modernizr;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WurflCache\Adapter\File;

$logger = new Logger('detector');
$logger->pushHandler(new StreamHandler('log/error.log', Logger::DEBUG));
ErrorHandler::register($logger);

$cache = new File(array(File::DIR => 'cache/'));

$detector = new Detector($cache, $logger);
$cookieID = $detector->getCookieId($_SERVER);

// if this is a request from features.js.php don't run the build function
$ua = $detector->build($_SERVER);

if (null === $ua) {
    $html = '<html><head><script type="text/javascript">';

    $html .= Modernizr::buildJs();
    $html .= Modernizr::buildConvertJs($cookieID, '', true);

    $html .= '</script></head><body></body></html>';
    echo $html;
    exit;
}

// include the browserFamily library to classify the browser by features
$ua->family = FeatureFamily::find($ua);

// include some helpful functions
include 'web/templates/_convertTF.inc.php';
include 'web/templates/_createFT.inc.php';

// switch templates based on device type
if (isset($ua->isMobile) && $ua->isMobile) {
    include 'web/templates/index.mobile.inc.php';
} else {
    include 'web/templates/index.default.inc.php';
}
