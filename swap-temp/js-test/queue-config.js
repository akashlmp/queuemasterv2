// ==UserScript==
// @name         Delay Page Rendering
// @namespace    https://your.namespace.com
// @version      1.0
// @description  Delays rendering of the page until an external script is loaded
// @match        https://cashlee.walkingdreamz.com/index.php
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    // Function to load external script dynamically
    function loadScript(url, callback) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = url;
        script.onload = callback;
        document.head.appendChild(script);
    }

    // Load the external script
    loadScript("https://queuing.walkingdreamz.com/swap-temp/js-test/ext.js?timestamp=" + Date.now(), function() {
        // External script loaded, render the page content here
        document.documentElement.style.visibility = 'visible';
    });

    // Hide content until the script is loaded
    document.documentElement.style.visibility = 'hidden';

})();
