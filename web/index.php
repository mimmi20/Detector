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

$errorLog = new Logger('error');
$errorLog->pushHandler(new StreamHandler('log/error.log', Logger::DEBUG));
ErrorHandler::register($errorLog);

$accessLog = new Logger('error');
$accessLog->pushHandler(new StreamHandler('log/access.log', Logger::DEBUG));

$filesystemAdapter = new Local('cache/');
$filesystem        = new Filesystem($filesystemAdapter);

$pool = new FilesystemCachePool($filesystem);

$app->pipe(Middleware::ClientIp()->remote(false)); // required for AccessLog, Geolocate
$app->pipe(Middleware::AccessLog($accessLog)->combined(true));
$app->pipe(Middleware::TrailingSlash(false)->redirect(301));
$app->pipe(Middleware::FormatNegotiator()); // required for Expires, Minify
//$app->pipe(Middleware::Expires());
//$app->pipe(Middleware::Minify());
$app->pipe(Middleware::BlockSpam());
//$app->pipe(Middleware::PhpSession()->name('DetectorSessionId'));
//$app->pipe(Middleware::Geolocate()->saveInSession(true));
//$app->pipe(Middleware::Cache($pool));
$app->pipe(Middleware::responseTime());
$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();

$cache = new File(array(File::DIR => 'cache/'));

$app->get('/js/features.js', function (RequestInterface $request, ResponseInterface $response, callable $next) use ($errorLog, $cache) {
    $response->withAddedHeader('content-type', 'application/x-javascript');
    $response->getBody()->write(Modernizr::buildJs());

    $detector = new Detector($cache, $errorLog);
    $cookieID = $detector->getCookieId($_SERVER);
    $response->getBody()->write(Modernizr::buildConvertJs($cookieID, '', false));
    return $response;
});

$app->get('/', function (RequestInterface $request, ResponseInterface $response, callable $next) use ($errorLog, $cache) {
    $detector = new Detector($cache, $errorLog);

    // if this is a request from features.js.php don't run the build function
    $ua = $detector->build($_SERVER);
/*
    if (null === $ua) {
        $html = '<html><head><script type="text/javascript">';

        $html .= Modernizr::buildJs();
        $html .= Modernizr::buildConvertJs($detector->getCookieId($_SERVER), '', true);

        $html .= '</script></head><body></body></html>';
        return $response->getBody()->write($html);
    }
/**/
    $response->getBody()->write('Hello, world!');

    $options = array(
        'loader' => new Mustache_Loader_FilesystemLoader('src/templates'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader('src/templates/partials'),
        'logger' => $errorLog,
    );

    $next = '';
    $previous = '';

    if ($detector->whereFound() == 'archive') {
        $foundIn = " pulled from a profile already in the system that you asked to view. Because it's an archived profile the browser-side tests were not run.";
        $uaList  = $detector->getUaList();
        $i       = 0;
        $oldkey  = '';

        foreach ($uaList as $key => $value) {
            if ($i == 1) {
                $next = $key;
                break;
            }
            if ($key == $ua->uaHash) {
                $previous = $oldkey;
                $i = 1;
            }
            $oldkey = $key;
        }
    } else if ($detector->whereFound() == 'cookie') {
        $foundIn = " created when you first hit this page because Detector didn't recognize your user-agent. You may have experienced a very brief redirect when loading the page initially. The profiles have now been saved for use with other visitors.";
    } else if ($detector->whereFound() == 'file') {
        $foundIn = " created in the past when another user with the same user-agent visited this demo. Detector simply pulled the already existing information for your visit.";
    } else if ($detector->whereFound() == 'nojs') {
        $foundIn = " <span style='color: red'>created from a default, conservative profile because it appears JavaScript or Cookies are turned off and Detector didn't recognize the user-agent.</span>";
    } else {
        $foundIn = " pulled from session because you've visited this page before.";
    }

    // include the browserFamily library to classify the browser by features
    //$ua->family = FeatureFamily::find($ua);
    $data = [
        'title'       => 'Detector [BETA] - combined browser- &amp; feature-detection for your app',
        'description' => 'This extremely simple demo is meant to show how Detector & Mustache can be combined to create a Responsive Web Design + Server Side Component (RESS) System. By using the requesting browser\'s Detector family classification a responsive template & partials that match the browser\'s features are rendered server-side via Mustache. Choose a different layout below to see how this page & the included images change depending upon the browser family.',
        'foundIn'  => $foundIn,
        'profile'  => ($detector->whereFound() == 'archive') ? 'Archived' : 'Your',
        'next'     => $next,
        'previous' => $previous,
        'uaHash'   => (isset($ua->uaHash) ? $ua->uaHash : ''),
        'uaFamily' => (isset($ua->uaparser->ua->family) ? $ua->uaparser->ua->family : ''),
        'osFamily' => (isset($ua->uaparser->os->family) ? $ua->uaparser->os->family : ''),
        'deviceFamily' => (isset($ua->uaparser->device->family) ? $ua->uaparser->device->family : ''),
        'uaHash'   => (isset($ua->uaHash) ? $ua->uaHash : ''),
    ];

    if (isset($ua->originalUserAgent) && is_string($ua->originalUserAgent)) {
        $data['originalUserAgent'] = $ua->originalUserAgent;
    } elseif (isset($ua->originalUserAgent) && is_object($ua->originalUserAgent)) {
        $data['originalBrowserUserAgent'] = 'Browser Information: ' . $ua->originalUserAgent->browser;
        $data['originalDeviceUserAgent']  = 'Device Information: ' . $ua->originalUserAgent->device;
    }

    $m = new Mustache_Engine($options);

    $response->getBody()->write($m->render('index.mustache', $data));
    return $response;
});

$app->get('/ping', function (RequestInterface $request, ResponseInterface $response, callable $next) {
    return new JsonResponse(['ack' => time()]);
});

$app->run();
