<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Detector [BETA] - combined browser- &amp; feature-detection for your app</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <?php /*if (isset($ua->isMobile) && $ua->isMobile) : ?>
        <link href="/css/mobile.css" rel="stylesheet"/>
    <?php
    else : ?>
        <link href="/css/desktop.css" rel="stylesheet"/>
    <?php
    endif;/**/ ?>
    <link href="/css/general.css" rel="stylesheet"/>

    <!-- My Scripts -->
    <script type="text/javascript" src="<?php echo htmlentities($detector->buildFeaturesScriptLink()); ?>"></script>
    <script type="text/javascript">
        var m = Modernizr, c = '', reload = true, cx = {};

        for (var f in m) {
            if (!m.hasOwnProperty(f)) {
                continue;
            }

            if (f[0] === '_') {
                continue;
            }

            var t = typeof m[f];

            if (t === 'function') {
                continue;
            }

            if (t === 'object') {
                cx[f] = {};

                for (var s in m[f]) {
                    if (!m[f].hasOwnProperty(s)) {
                        continue;
                    }

                    if (typeof m[f][s] === 'boolean') {
                        c = f + '->' + s + ':' + (m[f][s] ? 't' : 'f');
                        cx[f][s] = (m[f][s] ? 't' : 'f');
                    } else if (m[f][s] === null) {
                        c = f + '->' + s + ':' + 'n';
                        cx[f][s] = 'n';
                    } else if (m[f][s] === '') {
                        c = f + '->' + s + ':' + 'e';
                        cx[f][s] = 'e';
                    } else if (m[f][s] === 'probably') {
                        c = f + '->' + s + ':' + 'p';
                        cx[f][s] = 'p';
                    } else if (m[f][s] === 'maybe') {
                        c = f + '->' + s + ':' + 'm';
                        cx[f][s] = 'm';
                    } else {
                        c = f + '->' + s + ':' + m[f][s];
                        cx[f][s] = m[f][s];
                    }
                    document.writeln(c);
                }
            } else if (t === 'boolean') {
                c = f + ':' + (m[f] ? 't' : 'f');
                cx[f] = (m[f] ? 't' : 'f');
                document.writeln(c);
            } else if (m[f] === null) {
                c = f + ':' + 'n';
                cx[f] = 'n';
                document.writeln(c);
            } else if (m[f] === '') {
                c = f + ':' + 'e';
                cx[f] = 'e';
                document.writeln(c);
            } else if (m[f] === 'probably') {
                c = f + ':' + 'p';
                cx[f] = 'p';
                document.writeln(c);
            } else if (m[f] === 'maybe') {
                c = f + ':' + 'm';
                cx[f] = 'm';
                document.writeln(c);
            } else {
                c = f + ':' + m[f];
                cx[f] = m[f];
                document.writeln(c);
            }
        }
        document.writeln(JSON.stringify(cx));
    </script>
</head>

    <body>
    <div class="container">
        <div class="content clearfix">
            <div class="page-header col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 clearfix">
                <h1>
                    <a href="/" style="color: black;">Detector</a>
                    <span class="label notice beta">beta</span>
                    <small>combined browser- &amp; feature-detection for your app</small>
                </h1>

                <div id="forkme">
                    <a href="https://github.com/mimmi20/Detector">
                        <img src="images/ForkMe_Wht.png" width="141" height="141" alt="Fork Me on GitHub"/>
                    </a>
                </div>
            </div>
            <div class="row clearfix">

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                    <p class="row">
                        With the initial release of <a href="http://yiibu.com/">Yiibu's</a> <a
                            href="https://github.com/yiibu/profile">Profile</a>, <a
                            href="https://github.com/mimmi20/Detector">Detector</a>
                        is already <abbr title="Yet Another Browser- and Feature-Detection Library">YABFDL</abbr>
                        <em>(Yet Another Browser- and Feature-Detection Library)</em>. Ever since I heard Yiibu's talk,
                        <a href="http://www.slideshare.net/yiibu/adaptation-why-responsive-design-actually-begins-on-the-server">Adaptation</a>,
                        the core concepts & features of Detector have been floating around in my head. I've finally turned
                        those
                        ideas into <a href="https://github.com/mimmi20/Detector">code</a> and created this demo.
                        To learn more about <a href="https://github.com/mimmi20/Detector">Detector</a> and how it works
                        please check
                        out the <a href="https://github.com/mimmi20/Detector">README on GitHub</a>.
                    </p>
                    <p class="row">
                        Both of the following Detector profiles were <strong>
                            <?php
                            if ($detector->whereFound() == "archive") {
                                print " pulled from a profile already in the system that you asked to view. Because it's an archived profile the browser-side tests were not run.";
                            } else if ($detector->whereFound() == 'cookie') {
                                print " created when you first hit this page because Detector didn't recognize your user-agent. You may have experienced a very brief redirect when loading the page initially. The profiles have now been saved for use with other visitors.";
                            } else if ($detector->whereFound() == 'file') {
                                print " created in the past when another user with the same user-agent visited this demo. Detector simply pulled the already existing information for your visit.";
                            } else if ($detector->whereFound() == 'nojs') {
                                print " <span style='color: red'>created from a default, conservative profile because it appears JavaScript or Cookies are turned off and Detector didn't recognize the user-agent.</span>";
                            } else {
                                print " pulled from session because you've visited this page before.";
                            }
                            ?></strong>
                    </p>
                    <h3 class="row"><?php echo  ($detector->whereFound() == 'archive') ? 'Archived' : 'Your'; ?> Detector Browser Profile</h3>
                    <p class="row">
                        The following browser profile was created using <a href="https://github.com/ua-parser/uap-php">PHP implementation of ua-parser</a>. This information
                        is derived solely from the user agent string for your browser.
                    </p>
                    <?php
                    if ($detector->whereFound() == 'archive') {
                        $uaList = $detector->getUaList();
                        $i      = 0;
                        $oldkey = '';

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
                    }
                    ?>

                    <div class="row">
                        <div class="text-center clearfix">
                            <?php if (isset($previous) && ($previous != '')) { ?>
                                <span><a href="/?pid=<?php echo htmlentities($previous); ?>">Previous Profile</a> | </span>
                            <?php
                            } ?>
                            Browser Properties
                            <?php if (isset($next) && ($next != '')) { ?>
                            <span> | <a href="/?pid=<?php echo htmlentities($next); ?>">Next Profile</a>
                            <?php
                            } ?>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">User Agent:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                            <?php if (isset($ua->originalUserAgent) && is_string($ua->originalUserAgent)): ?>
                                <?php echo htmlentities($ua->originalUserAgent); ?>
                            <?php elseif (isset($ua->originalUserAgent) && is_object($ua->originalUserAgent)): ?>
                                Browser Information: <?php echo htmlentities($ua->originalUserAgent->browser); ?><br/>
                                Device Information: <?php echo htmlentities($ua->originalUserAgent->device); ?>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">UA Hash:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php echo htmlentities($ua->uaHash); ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Browser:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                                <?php echo htmlentities($ua->uaparser->ua->family); ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">OS:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                                <?php echo htmlentities($ua->uaparser->os->family); ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Device:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                                <?php echo htmlentities($ua->uaparser->device->family); ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is UIWebview?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isUIWebview)) :
                                    print convertTF($ua->isUIWebview);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is Mobile?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isMobile)) :
                                    print convertTF($ua->isMobile);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is Mobile Device?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isMobileDevice)) :
                                    print convertTF($ua->isMobileDevice);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is Tablet?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isTablet)) :
                                    print convertTF($ua->isTablet);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is Computer?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isComputer)) :
                                    print convertTF($ua->isComputer);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">Is Spider?</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10 text-uppercase">
                                <?php
                                if (isset($ua->isSpider)) :
                                    print convertTF($ua->isSpider);
                                else: ?>
                                    <span class="label label-danger">unknown</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <p class="row">
                        <strong>Something wrong with this profile?</strong> Please, <a href="/contact.php?cid=<?php echo htmlentities($ua->uaHash); ?>">let me know</a>. Note that
                        the <strong>"tablet" classification may be incorrect</strong> for those Android tablets using an OS older than Android 3.0.
                    </p>
                    <h3 class="row"><?php echo  ($detector->whereFound() == 'archive') ? 'Archived' : 'Your'; ?> Detector Feature Profile</h3>
                    <p class="row">
                        The following feature profile was primarily created using <a href="http://www.modernizr.com/docs/#s2">Modernizr's core tests</a>. The left column of results, <strong>Your Browser</strong>, is populated by JavaScript using a copy of Modernizr that is loaded with this page. The right column, <strong>Detector Profile</strong>, is populated by PHP using the profile created by Detector for your browser.
                        In addition to the core tests
                        I've added an extended test that checks for emoji support as well as a per request test to check the device pixel ratio. Both were added using the <a href="http://www.modernizr.com/docs/#addtest">Modernizr.addTest() Plugin API</a>.
                        To learn more about core, extended, and per request tests please <a href="https://github.com/mimmi20/Detector">review the README</a>.  To access any of these options in your PHP app you'd simply type <code>$ua->featureName</code>.
                        <br /><br />
                    </p>

                    <div class="row">
                        <div class="text-center clearfix">Feature Profile Properties</div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">coreVersion:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                                <?php
                                if (isset($ua->coreVersion)) {
                                    print htmlentities($ua->coreVersion);
                                } else {
                                    print 'This profile hasn\'t been versioned yet.';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 col-xl-2 text-right">family:</div>
                            <div class="col-xs-6 col-sm-8 col-md-10 col-lg-10 col-xl-10">
                                <?php
                                if (isset($ua->family)) {
                                    print htmlentities($ua->family);
                                } else {
                                    print 'Feature family hasn\'t been set yet for this profile.';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="featureNote">
                        <small><em>To learn more about families please <a href="https://github.com/dmolsen/Detector/wiki/Detector-Family-Tutorial">review the family tutorial</a>.</em></small>
                    </div>

                    <div>
                        <script type="text/javascript">
                            if (typeof Modernizr === 'undefined') {
                                document.write("Modernizr is unknown");
                            }
                        </script>
                    </div>

                    <?php
                    $ua_a = (array) $ua;
                    ksort($ua_a);
                    $ua = (object) ($ua_a);

                    // organize what features show up in which section
                    $css3Features       = '/(fontface|backgroundsize|borderimage|borderradius|boxshadow|flexbox|flexbox-legacy|hsla|multiplebgs|opacity|rgba|textshadow|cssanimations|csscolumns|generatedcontent|cssgradients|cssreflections|csstransforms|csstransforms3d|csstransitions|overflowscrolling|bgrepeatround|bgrepeatspace|bgsizecover|boxsizing|cubicbezierrange|cssremunit|cssresize|cssscrollbar)/';
                    $html5Features      = '/(adownload|applicationcache|canvas|canvastext|draganddrop|hashchange|history|audio|video|indexeddb|input|inputtypes|localstorage|postmessage|sessionstorage|websockets|websqldatabase|webworkers|contenteditable|webaudio|audiodata|userselect|dataview|microdata|progressbar|meter|createelement-attrs|time|geolocation|devicemotion|deviceorientation|speechinput|filereader|filesystem|fullscreen|formvalidation|notification|performance|quotamanagement|scriptasync|scriptdefer|webintents|websocketsbinary|blobworkers|dataworkers|sharedworkers)/';
                    $miscFeatures       = '/(touch|webgl|json|lowbattery|cookies|battery|gamepad|lowbandwidth|eventsource|ie8compat|unicode)/';
                    $mqFeatures         = '/(mediaqueries|desktop|mobile|tablet)/';
                    $extendedFeatures   = '/(extendedVersion|emoji)/';
                    $perSessionFeatures = '/(hirescapable)/';
                    $perRequestFeatures = '/(screenattributes)/';

                    // create separate tables
                    createFT($detector, $ua, $css3Features, 'CSS3 Features');
                    createFT($detector, $ua, $html5Features, 'HTML5 Features');
                    createFT($detector, $ua, $miscFeatures, 'Misc. Features', '', 'While a device may be touch-based that doesn\'t not mean it supports <a href="http://www.w3.org/TR/touch-events/">touch events</a> which is what I\'m testing for here.');
                    createFT($detector, $ua, $mqFeatures, 'Browser Class via Media Queries', 'core-', 'This feature needs some love as it\'s not always returning information correctly.');
                    createFT($detector, $ua, $extendedFeatures, 'Detector Extended Test Features', 'extended-', 'To learn more about extended tests and their purpose please <a href="https://github.com/dmolsen/Detector/wiki/Detector-Test-Tutorial">review the test tutorial.</a>');
                    createFT($detector, $ua, $perSessionFeatures, 'Detector Per Session Test Features', 'ps-', 'To learn more about per session tests and their purpose please <a href="https://github.com/dmolsen/Detector/wiki/Detector-Test-Tutorial">review the test tutorial.</a>');
                    createFT($detector, $ua, $perRequestFeatures, 'Detector Per Request Test Features', 'pr-', 'To learn more about per request tests and their purpose please <a href="https://github.com/dmolsen/Detector/wiki/Detector-Test-Tutorial">review the test tutorial.</a> If this section isn\'t populated hit "refresh". Attributes are captured via a cookie. Screen size will also be one request behind if you resize the window for the same reason.');
                    ?>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <?php include 'web/templates/_about.inc.php'; ?>
                    <?php include 'web/templates/_moreinfo.inc.php'; ?>
                    <?php include 'web/templates/_credits.inc.php'; ?>
                    <?php include 'web/templates/_socialmedia.inc.php'; ?>
                    <?php include 'web/templates/_archive.inc.php'; ?>
                </div>

            </div>
        </div>
        <footer class="clearfix">
            <p>&copy; <a href="http://dmolsen.com/">Dave Olsen</a> 2012 | Design based on <a
                    href="http://twitter.github.com/bootstrap/">Bootstrap</a></p>
        </footer>
    </div>
    <!-- /container -->
    </body>
</html>
