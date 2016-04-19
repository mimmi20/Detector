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
use Detector\Detector;
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

if (!empty($_POST['post'])) {
    $message = "Here is some feedback for Detector:

Email addy: 
".$_POST['email']."

Their message:
".$_POST['message'];

    mail('dmolsen@gmail.com', 'Detector Feedback', $message);
}

include "templates/contact.default.inc.php";
