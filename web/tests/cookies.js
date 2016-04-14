// by tauren
// https://github.com/Modernizr/Modernizr/issues/191

"use strict";

Modernizr.addTest('ps-cookies', function () {
    // Quick test if browser has cookieEnabled host property
    if (navigator.cookieEnabled) {
        return true;
    }

    // Create cookie
    document.cookie = "cookietest=1";
    var ret = document.cookie.indexOf("cookietest=") != -1;
    // Delete cookie
    document.cookie = "cookietest=; expires=Thu, 01-Jan-1970 00:00:01 GMT";

    return ret;
});
