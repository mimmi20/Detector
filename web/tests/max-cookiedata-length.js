"use strict";

Modernizr.addTest('ps-max-cookiedata-length', function () {
    var enabled = false;

    // Quick test if browser has cookieEnabled host property
    if (navigator.cookieEnabled) {
        enabled = true;
    } else {
        // Create cookie
        document.cookie = "cookietest2=1";
        enabled = document.cookie.indexOf("cookietest2=") != -1;
        // Delete cookie
        document.cookie = "cookietest2=; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    }

    if (!enabled) {
        return 0;
    }

    var length = 0, s = 'cookietest3=';

    do {
        s += '.';
        document.cookie = s;
        length = document.cookie.length;
    } while (document.cookie.length !== s.length);

    // Delete cookie
    document.cookie = "cookietest3=; expires=Thu, 01-Jan-1970 00:00:01 GMT";

    return length;
});
