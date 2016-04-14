/*
 * Including the extra core, extended, and per request tests from base Detector for use in the demo.
 */

"use strict";

// JSON support
Modernizr.addTest('json', !!window.JSON && !!JSON.parse);
