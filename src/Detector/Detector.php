<?php
/*!
 * Detector v0.8.5
 *
 * Copyright (c) 2015 Thomas Müller
 * Licensed under the MIT license
 */

namespace Detector;

use Modernizr\Modernizr;
use UAParser\Parser;

class Detector
{

    private static $debug = false; // gets overwritten by the config so changing this won't do anything for you...

    public static $ua;
    public static $accept;

    private static $coreVersion;
    private static $extendedVersion;

    public static $foundIn; // this is just for the demo. won't ever really be needed i don't think

    private static $uaHash;
    private static $sessionID;
    private static $cookieID;

    private static $uaDirCore;
    private static $uaDirExtended;

    private static $featuresScriptWebPath;

    public static $defaultFamily;
    public static $switchFamily;
    public static $splitFamily;
    public static $noJSCookieFamilySupport;
    public static $noJSSearchFamily;
    public static $noJSDefaultFamily;
    public static $noCookieFamily;

    /**
     * Configures the shared variables in Detector so that they can be used in functions that might not need to run
     * Detector::build();
     *
     * @throws \Detector\Exception
     */
    private static function configure()
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
            self::$$key = $value;
        }

        // populate some standard variables based on the user agent string
        self::$ua        = strip_tags($_SERVER['HTTP_USER_AGENT']);
        self::$accept    = strip_tags($_SERVER['HTTP_ACCEPT']);
        self::$uaHash    = md5(self::$ua);
        self::$sessionID = md5(self::$ua . '-session-' . self::$coreVersion . '-' . self::$extendedVersion);
        self::$cookieID  = md5(self::$ua . '-cookie-' . self::$coreVersion . '-' . self::$extendedVersion);
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
     * Logic is based heavily on modernizr-server
     *
     * @return mixed|null|object|\stdClass an object that contains all the properties for this particular user agent
     */
    public static function build()
    {
        // configure detector from config.ini
        self::configure();

        // populate some variables specific to build()
        $uaFileCore = __DIR__ . '/' . self::$uaDirCore . self::uaDir() . 'ua.' . self::$uaHash . '.json';

        $pid = (isset($_REQUEST['pid']) && preg_match('/[a-z0-9]{32}/', $_REQUEST['pid'])) ? $_REQUEST['pid'] : false;

        // offer the ability to review profiles saved in the system
        if ($pid && self::$debug) {
            // where did we find this info to display... probably only need this for the demo
            self::$foundIn = 'archive';

            // decode the core data
            $uaJSONCore = json_decode(
                @file_get_contents(__DIR__ . '/' . self::$uaDirCore . self::uaDir($pid) . 'ua.' . $pid . '.json')
            );

            // find and decode the extended data
            $uaJSONExtended = json_decode(
                @file_get_contents(__DIR__ . '/' . self::$uaDirExtended . self::uaDir($pid) . 'ua.' . $pid . '.json')
            );

            // merge the data
            $info = (object)array_merge(
                (array)$uaJSONCore,
                ($uaJSONExtended) ? (array)$uaJSONExtended : array(),
                (array)self::createUAProperties()
            );

            // some general properties
            $info->nojs      = false;
            $info->nocookies = false;

            // put the merged JSON info into session
            if (isset($_SESSION)) {
                $_SESSION[self::$sessionID] = $info;
            }

            // return to the script
            return $info;
        } elseif (@session_start()
            && isset($_SESSION)
            && isset($_SESSION[self::$sessionID])
        ) {
            // where did we find this info to display... probably only need this for the demo
            self::$foundIn = 'session';

            // parse the per request cookie
            $cookiePerRequest = new \stdClass();
            $cookiePerRequest = self::parseCookie('pr', $cookiePerRequest);

            // merge the session info we already have and the info from the cookie
            $info = (object)array_merge(
                (array)$_SESSION[self::$sessionID],
                (isset($cookiePerRequest)) ? (array)$cookiePerRequest : array(),
                (array)self::createUAProperties()
            );

            // put the merged JSON info into session
            if (isset($_SESSION)) {
                $_SESSION[self::$sessionID] = $info;
            }

            // write out to disk for future requests that might have the same UA
            self::writeUAFile(json_encode($info), $uaFileCore);

            // add the user agent & hash to a list of already saved user agents
            // not needed. a performance hit.
            self::addToUAList();

            // send the data back to the script to be used
            return $info;
        } elseif (($uaJSONCore = json_decode(@file_get_contents($uaFileCore)))) {
            // where did we find this info to display... probably only need this for the demo
            self::$foundIn = 'file';

            // double-check that the already created profile matches the current version of the core & extended templates
            if ((isset($uaJSONCore->coreVersion) && $uaJSONCore->coreVersion != self::$coreVersion)) {
                self::buildTestPage();
            }

            // merge the data
            $info = (object)array_merge(
                (array)$uaJSONCore,
                (array)self::createUAProperties()
            );

            // some general properties
            $info->nojs      = false;
            $info->nocookies = false;

            // put the merged JSON info into session
            if (isset($_SESSION)) {
                $_SESSION[self::$sessionID] = $info;
            }

            // add the user agent & hash to a list of already saved user agents
            // not needed. a performance hit.
            self::addToUAList();

            // return to the script
            return $info;
        } elseif (self::checkSpider()
            || (isset($_REQUEST['nojs']) && ($_REQUEST['nojs'] === 'true'))
            || (isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] === 'true'))
        ) {
            // where did we find this info to display... probably only need this for the demo
            self::$foundIn = 'nojs';

            // use ua-parser to set-up the basic properties for this UA, populate other core properties
            // include the basic properties of the UA
            $info              = self::createUAProperties();
            $info->uaHash      = self::$uaHash;
            $info->coreVersion = self::$coreVersion;

            $modernizrData = Modernizr::getData();

            if (null !== $modernizrData) {
                foreach (Modernizr::getData() as $property => $value) {
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
                $_SESSION[self::$sessionID] = $info;
            }

            // write out to disk for future requests that might have the same UA
            self::writeUAFile(json_encode($info), $uaFileCore);

            // add the user agent & hash to a list of already saved user agents
            // not needed. a performance hit.
            self::addToUAList();

            // return the collected data to the script for use in this go around
            return $info;
        } elseif (isset($_COOKIE) && isset($_COOKIE[self::$cookieID])) {
            // to be clear, this section means that a UA was unknown, was profiled with modernizr & now we're saving that data to build a new profile

            // where did we find this info to display... probably only need this for the demo
            self::$foundIn = 'cookie';

            // use ua-parser to set-up the basic properties for this UA, populate other core properties
            $info              = self::createUAProperties();
            $info->uaHash      = self::$uaHash;
            $info->coreVersion = self::$coreVersion;

            $modernizrData = Modernizr::getData();

            if (null !== $modernizrData) {
                foreach (Modernizr::getData() as $property => $value) {
                    $info->$property = $value;
                }
            }

            // push features into the same level as the general device information
            // change 1/0 to true/false. why? 'cause that's what i like to read ;)
            $info                 = self::parseCookie('core', $info, true);
            $jsonTemplateExtended = self::parseCookie('extended', new \stdClass(), true);

            // merge the data for future requests
            $info = (object)array_merge(
                (array)$info,
                ($jsonTemplateExtended) ? (array)$jsonTemplateExtended : array()
            );

            // some general properties
            $info->nojs      = false;
            $info->nocookies = false;

            // write out to disk for future requests that might have the same UA
            self::writeUAFile(json_encode($info), $uaFileCore);

            // add the user agent & hash to a list of already saved user agents
            // not needed. a performance hit.
            self::addToUAList();

            // unset the cookie that held the vast amount of test data
            setcookie(self::$cookieID, '');

            // add our collected data to the session for use in future requests, also add the per request data
            if (isset($_SESSION)) {
                $_SESSION[self::$sessionID] = $info;
            }

            // return the collected data to the script for use in this go around
            return $info;
        }

        // didn't recognize that the user had been here before nor the UA string.
        self::buildTestPage();

        return null;
    }

    /**
     * Reads in the per request feature tests and sends them to the function that builds out the JS & cookie
     *
     * from modernizr-server
     */
    public static function perrequest()
    {
        self::configure();

        if ((isset($_REQUEST['dynamic']) && ($_REQUEST['dynamic'] == 'true')) && !(isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] == 'true'))) {
            print file_get_contents('src/modernizr/cookieTest.js');
        }

        print Modernizr::buildJs();
        print Modernizr::buildConvertJs(self::$cookieID, '-pr', false);
    }

    /**
     * Builds the browser test page
     */
    public static function buildTestPage()
    {
        // gather info by sending Modernizr & custom tests
        print "<!DOCTYPE html><html lang=\"en\"><head><meta name=\"viewport\" content=\"width=device-width\"><script type='text/javascript'>";
        print file_get_contents('src/modernizr/cookieTest.js');
        print Modernizr::buildJs();
        print Modernizr::buildConvertJs(self::$cookieID);
        print "</script></head><body><noscript><meta http-equiv='refresh' content='0; url="
            . self::buildNoscriptLink()
            . "'></noscript></body></html>";
        exit;
    }

    /**
     * Reads in the cookie values and breaks them up into an object for use in build()
     *
     * @param  {String}       the value from the cookie
     *
     * from modernizr-server
     *
     * @return Detector key/value pairs based on the cookie
     */
    private static function _ang($cookie)
    {
        $uaFeatures = new Detector();
        if ($cookie != '') {
            foreach (explode('|', $cookie) as $feature) {
                list($name, $value) = explode(':', $feature, 2);
                if ($value[0] == '/') {
                    $value_object = new \stdClass();
                    foreach (explode('/', substr($value, 1)) as $sub_feature) {
                        list($sub_name, $sub_value) = explode(':', $sub_feature, 2);
                        $value_object->$sub_name = $sub_value;
                    }
                    $uaFeatures->$name = $value_object;
                } else {
                    $uaFeatures->$name = $value;
                }
            }
        }

        return $uaFeatures;
    }

    /**
     * Builds a noscript link so the page will reload
     *
     * @return string string that is the URL for the noscriptlink
     */
    private static function buildNoscriptLink()
    {
        // build the noscript link just in case
        $noscriptLink = $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['QUERY_STRING']) && ($_SERVER['QUERY_STRING'] != '')) {
            $noscriptLink .= '?' . $_SERVER['QUERY_STRING'] . '&nojs=true';
        } else {
            $noscriptLink .= '?nojs=true';
        }

        return $noscriptLink;
    }

    /**
     * Builds a link to the features.js.php file that addresses if cookies are supported or not
     */
    public static function buildFeaturesScriptLink()
    {
        $nocookies = (isset($_REQUEST['nocookies']) && ($_REQUEST['nocookies'] == 'true')) ? '&nocookies=true' : '';
        print "<script type=\"text/javascript\" src=\"" . self::$featuresScriptWebPath . 'features.js.php?dynamic=true' . $nocookies . '"></script>';
    }

    /**
     * Returns the first twp characters of the uaHash so Detector can build out directories
     *
     * @param bool $uaHash uaHash to be substringed
     *
     * @return string the first five characters of the hash
     */
    private static function uaDir($uaHash = false)
    {
        $uaHash = $uaHash ? $uaHash : self::$uaHash;

        return substr($uaHash, 0, 2) . '/';
    }

    /**
     * Writes out the UA file to the specified location
     *
     * @param  string $jsonEncoded encoded JSON
     * @param  string $uaFilePath  file path
     */
    private static function writeUAFile(
        $jsonEncoded,
        $uaFilePath
    ) {
        $dir = self::uaDir();
        if (!is_dir(__DIR__ . '/' . self::$uaDirCore . $dir)) {
            // create the files and then change permissions
            mkdir(__DIR__ . '/' . self::$uaDirCore . $dir);
            chmod(__DIR__ . '/' . self::$uaDirCore . $dir, 0775);
            mkdir(__DIR__ . '/' . self::$uaDirExtended . $dir);
            chmod(__DIR__ . '/' . self::$uaDirExtended . $dir, 0775);
        }
        $fp = fopen($uaFilePath, 'w');
        fwrite($fp, $jsonEncoded);
        fclose($fp);
        chmod($uaFilePath, 0664);
    }

    /**
     * Parses the cookie for a list of features
     *
     * @param string    $cookieExtension
     * @param \stdClass $obj
     * @param bool      $default
     *
     * @return \stdClass|null values from the cookie for that cookieExtension
     */
    private static function parseCookie(
        $cookieExtension,
        $obj,
        $default = false
    ) {
        $cookieName = $default ? self::$cookieID : self::$cookieID . '-' . $cookieExtension;
        if (isset($_COOKIE[$cookieName])) {
            $uaFeatures = self::_ang($_COOKIE[$cookieName]);
            foreach ($uaFeatures as $key => $value) {
                if ((strpos($key, $cookieExtension . '-') !== false) || (($cookieExtension == 'core') && (strpos(
                                $key,
                                'extended-'
                            ) === false) && (strpos($key, 'pr-') === false) && (strpos($key, 'ps-') === false))
                ) {
                    $key = str_replace($cookieExtension . '-', '', $key);
                    if (is_object($value)) {
                        foreach ($value as $vkey => $vvalue) {
                            if ($vvalue == 'probably') { // hack for modernizr
                                $value->$vkey = true;
                            } else if ($vvalue == 'maybe') { // hack for modernizr
                                $value->$vkey = false;
                            } else if (($vvalue == 1) || ($vvalue == 0)) {
                                $value->$vkey = ($vvalue == 1) ? true : false;
                            } else {
                                $value->$vkey = $vvalue;
                            }
                        }
                        $obj->$key = $value;
                    } else {
                        $obj->$key = ($value == 1) ? true : false;
                    }
                }
            }

            return $obj;
        }

        return null;
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval in the demo (or for any reason i guess)
     *
     * @return int the result of checking the current user agent string against a list of bots
     */
    private static function checkSpider()
    {
        $botRegex = '(bot|borg|google(^tv)|yahoo|slurp|msnbot|msrbot|openbot|archiver|netresearch|lycos|scooter|altavista|teoma|gigabot|baiduspider|blitzbot|oegp|charlotte|furlbot|http%20client|polybot|htdig|ichiro|mogimogi|larbin|pompos|scrubby|searchsight|seekbot|semanticdiscovery|silk|snappy|speedy|spider|voila|vortex|voyager|zao|zeal|fast\-webcrawler|converacrawler|dataparksearch|findlinks)';

        return preg_match('/' . $botRegex . '/i', self::$ua);
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval in the demo (or for any reason i guess)
     *
     * @return \StdClass The core template object 'filled out' from ua-parser
     */
    private static function createUAProperties()
    {
        $parser = Parser::create();

        // classify the user agent string so we can learn more what device this really is. more for readability than anything
        /** @var \UAParser\Result\Client $client */
        $client = $parser->parse(self::$ua);
        $obj    = new \StdClass();

        // save properties from ua-parser
        foreach ($client as $key => $value) {
            $obj->$key = $value;
        }

        return $obj;
    }

    /**
     * Adds the user agent hash and user agent to a list for retrieval in the demo (or for any reason i guess)
     * Important: This is a performance hit so enable with caution. I only had this for detector.dmolsen.com
     */
    private static function addToUAList()
    {
        $uaList = array();

        // open user agent list and decode the JSON
        if ($uaListJSON = @file_get_contents(__DIR__ . '/' . self::$uaDirCore . 'ua.list.json')) {
            $uaList = (array) json_decode($uaListJSON);
        }
var_dump($uaList);
        if (isset($uaList[self::$uaHash])) {
            return;
        }

        // merge the old list with the new user agent
        $mergedInfo = (object) array_merge($uaList, array(self::$uaHash => self::$ua));

        // write out the data to the user agent list
        $uaListJSON = json_encode($mergedInfo);
        file_put_contents(__DIR__ . '/' . self::$uaDirCore . 'ua.list.json', $uaListJSON);
    }
}
