"use strict";

Modernizr.addTest('core-tablet', function () {
    if (Modernizr.mq('only screen and (min-width: 600px) and (orientation:portrait)')) {
        return true;
    }

    return Modernizr.mq('only screen and (max-width: 1024px) and (orientation:landscape)');
});
