# Change Log

## [Development](https://github.com/mimmi20/Detector/tree/master)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.99...master)
 - reformat Changelog
 - add repository of namespaced [modernizr-server](https://github.com/mimmi20/modernizr-server), included via composer

## [0.99](https://github.com/mimmi20/Detector/tree/0.99) (2015-08-29)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.9.5...0.99)
 - add composer.josn
 - remove Code for PHP 5.2
 - replace ua-parser with actual version, included via composer [ua-parser/uap-php](https://github.com/ua-parser/uap-php)
 - include change from [FStop/Detector](https://github.com/FStop/Detector)
 - include change from [stevenmc/Detector](https://github.com/stevenmc/Detector)
 - updated Modernizr to v2.8.3
 - change project structure

## [0.9.5](https://github.com/mimmi20/Detector/tree/0.9.5) (2012-09-03)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.9.0...0.9.5)
 - FIX: the extended directories created by Detector are now chmod'ed 775
 - FIX: the search, nojs, nocookies check now occurs before all other checks to eliminate a redirect loop
 - FIX: modernizr listing demo now points at the new modernizr file
 - ADD: a confidence check mechanism so that UAs can be re-tested to see if Detector's data is correct

## [0.9.0](https://github.com/mimmi20/Detector/tree/0.9.0) (2012-08-27)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.8.5...0.9.0)
 - FIX: updated Modernizr to 2.6.1
 - FIX: trimmed down the config
 - FIX: the test build page now waits for the onload event before building the cookie & redirecting. should address race conditions with a few tests.
 - ADD: added per-session tests that existed in Modernizr feature-detects that I wanted for v1.0 of Detector (see http://bit.ly/O14wcZ for full list)

## [0.8.5](https://github.com/mimmi20/Detector/tree/0.8.5) (2012-08-14)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.8.2...0.8.5)
 - FIX: commmented out call to addToUAList() to increase performance
 - ADD: now throwing profiles into directories based on the first two characters of the hash. performance tweak because IE 7+8 create profiles an amazing number of profiles
 - ADD: mustache lib now allows smarter fallback via splitting family names on dashes (e.g. the mobile-advanced-retina family fallback can now be mobile-advanced-retina -> mobile-advanced -> mobile -> base) feature is off by default
 - ADD: can now use family=clear-family to clear out a family value stored in session

## [0.8.2](https://github.com/mimmi20/Detector/tree/0.8.2) (2012-08-06)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.8.1...0.8.2)
 - FIX: cookie bug where an empty cookie was found causing the building of an empty profile
 - FIX: updated the families.default.json to reflect the lessons learned on www.wvu.edu regarding device support

## [0.8.1](https://github.com/mimmi20/Detector/tree/0.8.1) (2012-07-13)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.8.0...0.8.1)
 - ADD: somehow i forgot to include the json lib for sub-5.2 compatibility

## [0.8.0](https://github.com/mimmi20/Detector/tree/0.8.0) (2012-06-04)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.7.1...0.8.0)
 - FIX: javascript mediaqueries argument in the demo
 - ADD: "comment out" feature detection tests by adding an underscore to the beginning of their filename
 - ADD: per session tests (e.g. tests that produce results unique to a client but only need to be run once per session)
 - ADD: nojs attribute to the final ua variable so it can be used as a flag
 - ADD: nocookies attribute to the final ua variable so it can be used as a flag
 - ADD: the features.js.php link can be dynamically created if necessary

## [0.7.1](https://github.com/mimmi20/Detector/tree/0.7.1) (2012-05-30)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.7.0...0.7.1)
 - FIX: can now skip Detector::build() when loading up the Detector library
 - FIX: per-request javascript include doesn't need the request var hack
 - FIX: better docs for some functions
 - FIX: config variables are now defined via a foreach loop
 - ADD: all configuration now happens in its own function

## [0.7.0](https://github.com/mimmi20/Detector/tree/0.7.0) (2012-05-29)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.5.2...0.7.0)
 - FIX: family attribute creation occurs with every request
 - FIX: no javascript support
 - FIX: just a lot of DRYed up code
 - FIX: modernizr-like helpers are now in their own lib file
 - FIX: updated the default core json template to reflect all the appropriate variables
 - FIX: updated the noscript link so that nojs redirects happen auto-magically
 - FIX: trying to move library calls around to dampen the memory footprint of Detector
 - FIX: updated ua-parser-php to the latest & greatest
 - ADD: no cookie support
 - ADD: no javascript, no cookie, and search engine default family values
 - ADD: switch "themes" based on browser families by overriding with request var

## [0.5.2](https://github.com/mimmi20/Detector/tree/0.5.2) (2012-05-07)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.5.1...0.5.2)
 - FIX: added some sanity checks on user agent strings
 - FIX: fixed two strict checks on object creation
 - FIX: updated UAParser to the latest edition

## [0.5.1](https://github.com/mimmi20/Detector/tree/0.5.1) (2012-02-27)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.5.0...0.5.1)
 - ADD: can add tag soup a la modernizr to the <html> tag if you want
 - ADD: can push Detector values to a JavaScript file if you want
 - FIX: most, if not all, PHP notices should be addressed
 - FIX: removed ua-parser-php as a submodule & added the files directly. downloads should be fixed
 - THX: thanks to james jeffery for the notes about the PHP notices

## [0.5.0](https://github.com/mimmi20/Detector/tree/0.5.0) (2012-02-19)
[Full Changelog](https://github.com/mimmi20/Detector/compare/0.2.0...0.5.0)
 - ADD: a configuration file for configuring standard paths and variables for Detector
 - ADD: a debug flag
 - ADD: support for versioning of core & extended profiles
 - ADD: youtube demo that uses Detector
 - ADD: RESS demo showing how Detector can be used with Mustache for templating
 - ADD: a browser family classification system that classifies browsers by ua info & features
 - ADD: a number of extra feature tests
 - FIX: updated the browser detection library to use ua-parser-php
 - FIX: reorganized Detector's file system layout so it's a little cleaner
 - FIX: updated modernizr to 2.5.2

## [0.2.0](https://github.com/mimmi20/Detector/tree/0.2.0) (2012-01-24)
 - ADD: handles browsers or spiders that don't support javascript
 - ADD: objects created with Modernizr.addTest() can now be used (see per request screen attributes test)
 - ADD: an experimental check to see if requests from iOS devices are coming from a UIWebview
 - ADD: google analytics include file
 - ADD: a user agent list for easy access to user agent strings & user agent hashes
 - FIX: tweaked the look of the feature profiles so they look better
 - FIX: per request, core, and extended tests now show on the "your browser" section of the feature profile
 - FIX: made sure major version & minor version are being populated correctly
 - FIX: attempted to update the media query tests. probably still flaky.
 - FIX: attempted to get android tablets correctly identified. relies on the media query code so not sure it's completely working.
 - FIX: updated the README to reflect the new changes
