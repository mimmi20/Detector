"use strict";

Modernizr.addTest('ps-webgl', function () {
    return !!window.WebGLRenderingContext;
});
