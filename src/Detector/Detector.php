<?php
/*!
 * Detector v0.8.5
 *
 * Copyright (c) 2015 Thomas Müller
 * Licensed under the MIT license
 */

namespace Detector;

use BrowscapPHP\Browscap;
use Modernizr\Modernizr;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use UAParser\Parser;
use WurflCache\Adapter\AdapterInterface;
use Wurfl\Request\GenericRequest;
use Wurfl\Request\GenericRequestFactory;

class Detector
{
    /**
     * @var string
     */
    private $coreVersion;

    /**
     * @var string
     */
    private $extendedVersion;

    /**
     * @var string
     */
    private $foundIn; // this is just for the demo. won't ever really be needed i don't think

    /**
     * @var string
     */
    private $uaHash;

    /**
     * @var string
     */
    private $uaDir;

    /**
     * a \WurflCache\Adapter\AdapterInterface object
     *
     * @var \WurflCache\Adapter\AdapterInterface
     */
    private $cache = null;

    /**
     * an logger instance
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @param \WurflCache\Adapter\AdapterInterface $cache
     * @param \Psr\Log\LoggerInterface             $logger
     *
     * @throws \Detector\Exception
     */
    public function __construct(AdapterInterface $cache, LoggerInterface $logger = null)
    {
        $this->cache = $cache;

        if (null === $logger) {
            $logger = new NullLogger();
        }

        $this->logger = $logger;

        $this->configure();
    }

    /**
     * Configures the shared variables in Detector so that they can be used in functions that might not need to run
     * Detector::build();
     *
     * @throws \Detector\Exception
     */
    private function configure()
    {
        // set-up the configuration options for the system
        if (!($config = @parse_ini_file(__DIR__ . '/config/config.ini'))) {
            // config.ini didn't exist so attempt to create it using the default file
            if (!@copy(__DIR__ . '/config/config.ini.default', __DIR__ . '/config/config.ini')) {
                throw new Exception('Please make sure config.ini.default exists before trying to have Detector build the config.ini file automagically.');
            }

            $config = @parse_ini_file(__DIR__ . '/config/config.ini');
        }

        // populate some standard variables out of the config
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Tests to see if:
     *     - see if this is a debug request with appropriately formed pid, else
     *     - see if the cookie for the per user test has been set so we can record the results and add to the session
     *     - see if a session has already been opened for the request browser, if so send the info back, else
     *     - see if the cookie for the full test has been set so we can build the profile, if so build the profile &
     *     send the info back, else
     *     - see if this browser reports being a spider, doesn't support JS or doesn't support cookies
     *     - see if detector can find an already created profile for the browser, if so send the info back, else
     *     - start the process for building a profile for this unknown browser
     *
     * @param string|array|\Wurfl\Request\GenericRequest $request
     *
     * @return null|\stdClass an object that contains all the properties for this particular user agent
     */
    public function build($request = null)
    {
        $request = $this->initRequest($request);

        $sessionID = md5($request->getBrowserUserAgent() . '-session-' . $this->coreVersion . '-' . $this->extendedVersion);

        $pid = (isset($_REQUEST['pid']) && preg_match('/[a-z0-9]{32}/', $_REQUEST['pid'])) ? $_REQUEST['pid'] : false;

        // offer the ability to review profiles saved in the system
        if ($pid) {
            $this->foundIn = 'archive';

            // decode the core data
            $info = json_decode(
                @file_get_contents(__DIR__ . '/' . $this->uaDir . 'ua.' . $pid . '.json')
            );

            if ($info instanceof \stdClass) {
                // some general properties
                $info->nojs      = false;
                $info->nocookies = false;

                // put the merged JSON info into session
                if (isset($_SESSION)) {
                    $_SESSION[$sessionID] = $info;
                }

                // return to the script
                return $info;
            }
        }

        $cacheId = hash('sha512', $request->getDeviceUserAgent() . '||||' . $request->getBrowserUserAgent());
        $result  = null;
        $success = false;

        $info = $this->cache->getItem($cacheId, $success);

        // populate some variables specific to build()
        $uaHash     = md5($request->getBrowserUserAgent());
        $uaFile = __DIR__ . '/' . $this->uaDir . $this->uaDir() . 'ua.' . $uaHash . '.json';

        if ($success && $info instanceof \stdClass) {
            $this->foundIn = 'cache';

            $this->save($request, $info, $uaFile, $cacheId);

            // send the data back to the script to be used
            return $info;
        }

        if (@session_start()
            && isset($_SESSION)
            && isset($_SESSION[$sessionID])
        ) {
            $this->foundIn = 'session';

            $info = $_SESSION[$sessionID];

            $this->save($request, $info, $uaFile, $cacheId);

            // send the data back to the script to be used
            return $info;
        }

        $cookieID = $this->getCookieId($request);

        $modernizrData = Modernizr::getData($cookieID);

        if ($this->checkSpider($request)
            || (isset($_REQUEST['nojs']) && ($_REQUEST['nojs'] === 'true'))
            || (isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] === 'true'))
        ) {
            $this->foundIn = 'nojs';

            // use ua-parser to set-up the basic properties for this UA, populate other core properties
            // include the basic properties of the UA
            $info              = $this->createUAProperties($request);
            $info->uaHash      = $uaHash;
            $info->coreVersion = $this->coreVersion;

            if (null !== $modernizrData) {
                foreach ($modernizrData as $property => $value) {
                    $info->$property = $value;
                }
            }

            // some general properties
            $info->nojs      = false;
            $info->nocookies = false;

            // add an attribute to the object in case no js or no cookies was sent
            if (isset($_REQUEST['nojs']) && ($_REQUEST['nojs'] == 'true')) {
                $info->nojs = true;
            } else if (isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] == 'true')) {
                $info->nocookies = true;
            }

            // try setting the session unless cookies are actively not supported
            if (!(isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] == 'true')) && isset($_SESSION)) {
                $_SESSION[$sessionID] = $info;
            }

            $this->save($request, $info, $uaFile, $cacheId);

            // return the collected data to the script for use in this go around
            return $info;
        }

        if (null !== $modernizrData) {
            // to be clear, this section means that a UA was unknown, was profiled with modernizr
            // & now we're saving that data to build a new profile

            $this->foundIn = 'cookie';

            // use ua-parser to set-up the basic properties for this UA, populate other core properties
            $info              = $this->createUAProperties($request);
            $info->uaHash      = $uaHash;
            $info->coreVersion = $this->coreVersion;

            foreach ($modernizrData as $property => $value) {
                $info->$property = $value;
            }

            // some general properties
            $info->nojs      = false;
            $info->nocookies = false;

            // unset the cookie that held the vast amount of test data
            setcookie($cookieID, '');

            // add our collected data to the session for use in future requests, also add the per request data
            if (isset($_SESSION)) {
                $_SESSION[$sessionID] = $info;
            }

            $this->save($request, $info, $uaFile, $cacheId);

            // return the collected data to the script for use in this go around
            return $info;
        }

        return null;
    }

    /**
     * Builds a link to the features.js.php file that addresses if cookies are supported or not
     *
     * @return string
     */
    public function buildFeaturesScriptLink()
    {
        $nocookies = (isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] == 'true')) ? '&nocookies=true' : '';
        return '<script type="text/javascript" src="/js/features.js.php?dynamic=true' . $nocookies . '"></script>';
    }

    /**
     * Returns the first twp characters of the uaHash so Detector can build out directories
     *
     * @param bool $uaHash uaHash to be substringed
     *
     * @return string the first five characters of the hash
     */
    private function uaDir($uaHash = false)
    {
        $uaHash = $uaHash ? $uaHash : '';

        return substr($uaHash, 0, 2) . '/';
    }

    /**
     * Writes out the UA file to the specified location
     *
     * @param  \stdClass $info        JSON
     * @param  string    $uaFilePath  file path
     */
    private function writeUAFile(
        \stdClass $info,
        $uaFilePath
    ) {
        $dir = $this->uaDir();
        
        if (!is_dir(__DIR__ . '/' . $this->uaDir . $dir)) {
            // create the files and then change permissions
            mkdir(__DIR__ . '/' . $this->uaDir . $dir);
            chmod(__DIR__ . '/' . $this->uaDir . $dir, 0775);
        }
        
        file_put_contents($uaFilePath, json_encode($info));
        
        chmod($uaFilePath, 0664);
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval in the demo (or for any reason i guess)
     *
     * @param \Wurfl\Request\GenericRequest $request
     *
     * @return int the result of checking the current user agent string against a list of bots
     */
    private function checkSpider(GenericRequest $request)
    {
        $botRegex = '(bot|borg|google(^tv)|yahoo|slurp|msnbot|msrbot|openbot|archiver|netresearch|lycos|scooter|altavista|teoma|gigabot|baiduspider|blitzbot|oegp|charlotte|furlbot|http%20client|polybot|htdig|ichiro|mogimogi|larbin|pompos|scrubby|searchsight|seekbot|semanticdiscovery|silk|snappy|speedy|spider|voila|vortex|voyager|zao|zeal|fast\-webcrawler|converacrawler|dataparksearch|findlinks)';

        return preg_match('/' . $botRegex . '/i', $request->getBrowserUserAgent());
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval in the demo (or for any reason i guess)
     *
     * @param \Wurfl\Request\GenericRequest $request
     *
     * @return \StdClass The core template object 'filled out' from ua-parser
     * @throws \BrowscapPHP\Exception
     */
    private function createUAProperties(GenericRequest $request)
    {
        //$request->getDeviceUserAgent() . '||||' . $request->getBrowserUserAgent()

        $useragent = $request->getBrowserUserAgent();
        $parser    = Parser::create();

        // classify the user agent string so we can learn more what device this really is. more for readability than anything
        /** @var \UAParser\Result\Client $client */
        $client = $parser->parse($useragent);
        $obj    = new \StdClass();
        if ($request->getDeviceUserAgent() === $request->getBrowserUserAgent()) {
            $obj->originalUserAgent = $request->getBrowserUserAgent();
        } else {
            $obj->originalUserAgent          = new \StdClass();
            $obj->originalUserAgent->browser = $request->getBrowserUserAgent();
            $obj->originalUserAgent->device  = $request->getDeviceUserAgent();
        }

        // save properties from ua-parser
        $obj->ua         = new \StdClass();
        $obj->ua->major  = $client->ua->major;
        $obj->ua->minor  = $client->ua->minor;
        $obj->ua->patch  = $client->ua->patch;
        $obj->ua->family = $client->ua->toString();

        $obj->os             = new \StdClass();
        $obj->os->major      = $client->os->major;
        $obj->os->minor      = $client->os->minor;
        $obj->os->patch      = $client->os->patch;
        $obj->os->patchMinor = $client->os->patchMinor;
        $obj->os->family     = $client->os->toString();

        $obj->device         = new \StdClass();
        $obj->device->brand  = $client->device->brand;
        $obj->device->model  = $client->device->model;
        $obj->device->family = $client->device->toString();

        // Now, load an INI file into BrowscapPHP\Browscap for testing the UAs
        $browscap = new Browscap();
        $browscap
            ->setCache($this->cache)
            ->setLogger($this->logger)
        ;

        $actualProps = (array) $browscap->getBrowser($useragent);

        foreach ($actualProps as $property => $value) {
            $obj->$property = $value;
        }

        return $obj;
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval
     *
     * @param \Wurfl\Request\GenericRequest $request
     * @param \stdClass                     $info
     */
    private function addToUAList(GenericRequest $request, \stdClass $info)
    {
        $uaList = $this->getUaList();

        if (isset($uaList[$info->uaHash])) {
            return;
        }

        // merge the old list with the new user agent
        $mergedInfo = (object) array_merge($uaList, array($info->uaHash => $request->getBrowserUserAgent()));

        // write out the data to the user agent list
        $uaListJSON = json_encode($mergedInfo);
        file_put_contents(__DIR__ . '/user-agents/ua.list.json', $uaListJSON);
    }

    /**
     * @param string|array|\Wurfl\Request\GenericRequest $request
     *
     * @return \Wurfl\Request\GenericRequest
     */
    public function initRequest($request)
    {
        if (null === $request) {
            throw new \UnexpectedValueException(
                'You have to call this function with the useragent parameter'
            );
        }

        $requestFactory = new GenericRequestFactory();

        if (is_string($request)) {
            $request = $requestFactory->createRequestForUserAgent($request);
        } elseif (is_array($request)) {
            $request = $requestFactory->createRequest($request);
        }

        if (!($request instanceof GenericRequest)) {
            throw new \UnexpectedValueException(
                'the useragent parameter has to be a string, an array or an instance of \Wurfl\Request\GenericRequest'
            );
        }

        return $request;
    }

    /**
     * @param string|array|\Wurfl\Request\GenericRequest $request
     *
     * @return string
     */
    public function getCookieId($request = null)
    {
        return md5($this->initRequest($request)->getBrowserUserAgent() . '-cookie-' . $this->coreVersion . '-' . $this->extendedVersion);
    }

    /**
     * @param \Wurfl\Request\GenericRequest $request
     * @param \stdClass                     $info
     * @param string                        $uaFile
     * @param string                        $cacheId
     */
    private function save(
        GenericRequest $request,
        \stdClass $info,
        $uaFile,
        $cacheId
    ) {
        // write out to disk for future requests that might have the same UA
        $this->writeUAFile($info, $uaFile);

        // add the user agent & hash to a list of already saved user agents
        // not needed. a performance hit.
        $this->addToUAList($request, $info);

        $this->cache->setItem($cacheId, $info);
    }

    /**
     * @return string
     */
    public function whereFound()
    {
        return $this->foundIn;
    }

    /**
     * @return array
     */
    public function getUaList()
    {
        $uaList   = array();
        $fileName = __DIR__ . '/user-agents/ua.list.json';

        // open user agent list and decode the JSON
        if (file_exists($fileName) && $uaListJSON = file_get_contents($fileName)) {
            $uaList = (array) json_decode($uaListJSON);

            asort($uaList);
        }

        return $uaList;
    }
}
