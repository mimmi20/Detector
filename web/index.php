<?php
// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

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
use Detector\FeatureFamily;
use ModernizrServer\Modernizr;
use Monolog\ErrorHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WurflCache\Adapter\File;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\AppFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr7Middlewares\Middleware;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;

$app = AppFactory::create();

$errerLog = new Logger('error');
$errerLog->pushHandler(new StreamHandler('log/error.log', Logger::DEBUG));
ErrorHandler::register($errerLog);

$accessLog = new Logger('error');
$accessLog->pushHandler(new StreamHandler('log/access.log', Logger::DEBUG));

$filesystemAdapter = new Local('cache/');
$filesystem        = new Filesystem($filesystemAdapter);

$pool = new FilesystemCachePool($filesystem);

$app->pipe(Middleware::ClientIp()->remote(false)); // required for AccessLog, Geolocate
$app->pipe(Middleware::AccessLog($accessLog)->combined(true));
$app->pipe(Middleware::TrailingSlash(false)->redirect(301));
$app->pipe(Middleware::FormatNegotiator()); // required for Expires, Minify
$app->pipe(Middleware::Expires());
$app->pipe(Middleware::Minify());
//$app->pipe(Middleware::BlockSpam());
$app->pipe(Middleware::PhpSession()->name('DetectorSessionId'));
$app->pipe(Middleware::Geolocate()->saveInSession());
//$app->pipe(Middleware::Cache($pool));
$app->pipe(Middleware::responseTime());
$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();

$app->get('/', function (RequestInterface $request, ResponseInterface $response, callable $next) {
    $response->getBody()->write('Hello, world!');
    return $response;
});

$app->get('/ping', function (RequestInterface $request, ResponseInterface $response, callable $next) {
    return new JsonResponse(['ack' => time()]);
});

$app->run();
exit;

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
include 'web/templates/index.default.inc.php';
