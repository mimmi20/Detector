/*
 * Including the extra core, extended, and per request tests from base Detector for use in the demo.
 */

"use strict";

// Overflowscrolling (iOS) support
Modernizr.addTest('overflowscrolling', function () {
    return Modernizr.testAllProps('overflowScrolling');
});
