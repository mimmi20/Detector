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

use \Detector\Detector;
use ModernizrServer\Modernizr;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WurflCache\Adapter\File;

header('content-type: application/x-javascript', true);

$logger = new Logger('detector');
$logger->pushHandler(new StreamHandler('log/error.log', Logger::NOTICE));

$cache = new File(array(File::DIR => 'cache/'));

$detector = new Detector($cache, $logger);
$cookieID = $detector->getCookieId($_SERVER);

print Modernizr::buildJs();
print Modernizr::buildConvertJs($cookieID, '-pr', false);
