var BASE_URL = 'https://queuing.walkingdreamz.com/api/';
// var BASE_URL = 'https://queuemaster.lambetech.com/api/';
// URL of the FingerprintJS library
const FingerprintJS = 'https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js';

let totalDuration = 60; // total duration in seconds
let progressBar = document.getElementById("ie1np");
let interval = 1000; // interval in milliseconds (1 second)
let remainTime = 60;
let width = 0;
let timer;
let already_in = false;
var browserDataGloble = {};
var setTimeoutLoop = 1000;

/** This code to generate the fingerprint id | start */
// Function to dynamically load and evaluate the FingerprintJS script
function loadFingerprintJS() {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', FingerprintJS, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        eval(xhr.responseText); // Evaluate the fetched script
                        if (typeof FingerprintJS !== 'undefined') {
                            resolve(FingerprintJS); // Resolve with FingerprintJS
                        } else {
                            reject(new Error('FingerprintJS is undefined after evaluation'));
                        }
                    } catch (error) {
                        reject(new Error('Error evaluating FingerprintJS script: ' + error.message));
                    }
                } else {
                    reject(new Error('Failed to fetch the FingerprintJS library: ' + xhr.statusText));
                }
            }
        };
        xhr.send();
    });
}

async function getFingerPrintId() {
    try {
        const FingerprintJS = await loadFingerprintJS();

        // Initialize the agent at application startup
        const fp = await FingerprintJS.load();

        // Get the visitor identifier when you need it
        const result = await fp.get();

        // This is the visitor identifier
        const visitorId = result.visitorId;

        // console.log('read out side: ' + visitorId);
        return visitorId;
    } catch (error) {
        console.error('Error loading FingerprintJS:', error);
    }
}
/** This code to generate the fingerprint id | end */

// document.addEventListener('DOMContentLoaded', async () => {
//     var script = document.querySelector('script[data-intercept]');
//     if (script) {
//         if (localStorage.getItem('sessionExpiredRedirected') !== 'true') {
//             var qSessionIdCookie = getCookie('qSessionId');
//             var preQueue = getCookie('preQueue');
//             var checkByepassStatus = getCookie('checkByepassStatus');
//             var scriptAttributes = {
//                 domain: script.dataset.interceptDomain,
//                 intercept: script.dataset.intercept,
//                 cid: script.dataset.c,
//                 dataCall: script.dataset.call
//             };

//             if (checkByepassStatus && qSessionIdCookie) {
//                 await checkBypassCodeStatus({ session_id: qSessionIdCookie, scriptAttributes });
//             } else {
//                 var qProcessOp = getCookie('qProcessOp');
//                 if (qProcessOp && qSessionIdCookie) {
//                     if (preQueue && qSessionIdCookie) {
//                         await executeQueueOpByPass({ session_id: qSessionIdCookie, scriptAttributes });
//                     }
//                     else {
//                         await executeQueueOp({ session_id: qSessionIdCookie, scriptAttributes });
//                     }
//                 } else {
//                     if (preQueue && qSessionIdCookie) {
//                         await executeQueueOpByPass({ session_id: qSessionIdCookie, scriptAttributes });
//                     } else {
//                         deleteCookie("qProcessOp");
//                         deleteCookie("preQueue");
//                         deleteCookie("checkByepassStatus");
//                         deleteCookie("qSessionId");

//                         const sessionId = generateSessionId();
//                         setCookie('qSessionId', sessionId, 30);
//                         await executeScript(scriptAttributes);
//                     }
//                 }
//             }

//             const htmlContent = document.documentElement.innerHTML;
//             showForcePopup(htmlContent);
//         } else {
//             deleteCookie("qProcessOp");
//             deleteCookie("preQueue");
//             deleteCookie("checkByepassStatus");
//             deleteCookie("qSessionId");
//             // if(getCookie('queueSameUrlCookie')){}
//             //deleteCookie("same_target_flag");

//             console.log('same target site');
//         }
//     } else {
//         console.error('Script element with data-intercept attribute not found.');
//     }
// });

document.addEventListener('DOMContentLoaded', async () => {
    var script = document.querySelector('script[data-intercept]');
    if (script) {
        let sessionExpiredRedirected;
        
        // Attempt to access localStorage and catch any errors
        try {
            sessionExpiredRedirected = localStorage.getItem('sessionExpiredRedirected');
        } catch (error) {
            console.error("Unable to access localStorage:", error);
            sessionExpiredRedirected = null; // Default value if localStorage is inaccessible
        }

        if (sessionExpiredRedirected !== 'true') {
            var qSessionIdCookie = getCookie('qSessionId');
            var preQueue = getCookie('preQueue');
            var checkByepassStatus = getCookie('checkByepassStatus');
            var scriptAttributes = {
                domain: script.dataset.interceptDomain,
                intercept: script.dataset.intercept,
                cid: script.dataset.c,
                dataCall: script.dataset.call
            };

            if (checkByepassStatus && qSessionIdCookie) {
                await checkBypassCodeStatus({ session_id: qSessionIdCookie, scriptAttributes });
            } else {
                var qProcessOp = getCookie('qProcessOp');
                if (qProcessOp && qSessionIdCookie) {
                    if (preQueue && qSessionIdCookie) {
                        await executeQueueOpByPass({ session_id: qSessionIdCookie, scriptAttributes });
                    } else {
                        await executeQueueOp({ session_id: qSessionIdCookie, scriptAttributes });
                    }
                } else {
                    if (preQueue && qSessionIdCookie) {
                        await executeQueueOpByPass({ session_id: qSessionIdCookie, scriptAttributes });
                    } else {
                        deleteCookie("qProcessOp");
                        deleteCookie("preQueue");
                        deleteCookie("checkByepassStatus");
                        deleteCookie("qSessionId");

                        const sessionId = generateSessionId();
                        setCookie('qSessionId', sessionId, 30);
                        await executeScript(scriptAttributes);
                    }
                }
            }

            const htmlContent = document.documentElement.innerHTML;
            showForcePopup(htmlContent);
        } else {
            deleteCookie("qProcessOp");
            deleteCookie("preQueue");
            deleteCookie("checkByepassStatus");
            deleteCookie("qSessionId");

            console.log('same target site');
        }
    } else {
        console.error('Script element with data-intercept attribute not found.');
    }
});


function generateSessionId() {
    var uniqueId = Date.now();
    return String(uniqueId);
}

function setCookie(name, value, minutesToExpire) {
    var date = new Date();
    date.setTime(date.getTime() + (minutesToExpire * 60 * 1000));
    var expires = "expires=" + date.toUTCString();
    document.cookie = `${name}=${value};${expires};path=/`;
}

function getCookie(name) {
    var cookieName = name + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(';');
    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(cookieName) === 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
    }
    return null;
}

async function executeScript(scriptAttributes) {
    var browserData = {
        userAgent: navigator.userAgent,
        language: navigator.language,
        cookiesEnabled: navigator.cookieEnabled,
        platform: navigator.platform,
        vendor: navigator.vendor,
        browserPlugins: Array.from(navigator.plugins, plugin => ({ name: plugin.name, filename: plugin.filename })),
    };

    var miscData = {
        encodedURL: (window.location.href),
        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        browserLanguage: navigator.language,
        ipaddress: await fetchIPAddress(),
        deviceid: await getFingerPrintId(),
        browserTime: new Date().toLocaleTimeString()
    };


    var networkData = {
        connectionType: navigator.connection ? navigator.connection.type : 'Connection API not supported',
        online: navigator.onLine,
        userAgentHttpHeaders: navigator.userAgent,
        mobile: /Mobile|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
        platform: navigator.platform
    };

    var performanceData = getPerformanceMetrics();
    var cookieData = getCookies();

    var postData = {
        browser: browserData,
        miscellaneous: miscData,
        networkInformation: networkData,
        performanceMetrics: performanceData,
        scriptAttributes: scriptAttributes,
        cookies: cookieData
    };
    sendData(postData);
}

async function fetchIPAddress() {
    try {
        var response = await fetch('https://api.ipify.org?format=json');
        // var response = await fetch('https://queuing.walkingdreamz.com/ip.php');
        var data = await response.json();
        return data.ip;
    } catch (error) {
        console.error('Error fetching IP address:', error);
        return null;
    }
}

function getPerformanceMetrics() {
    var performanceData = {
        navigationTiming: performance.timing.toJSON(),
        navigationType: performance.navigation.type,
        navigationRedirectCount: performance.navigation.redirectCount
    };

    if (performance.memory) {
        performanceData.memoryUsage = {
            totalJSHeapSize: performance.memory.totalJSHeapSize,
            usedJSHeapSize: performance.memory.usedJSHeapSize,
            jsHeapSizeLimit: performance.memory.jsHeapSizeLimit
        };
    }

    return performanceData;
}

function getCookies() {
    var cookies = {};
    document.cookie.split(';').forEach(cookie => {
        var [name, value] = cookie.trim().split('=');
        cookies[name] = value;
    });
    return cookies;
}

function sendData(postData) {

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'get-browser-data', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var responseText = xhr.responseText;
                var jsonResponse = responseText.substring(responseText.indexOf('{'));
                var response = JSON.parse(jsonResponse);

                if ((response.status == 1) || (response.status == 10)) {
                    if (response.qProcessOp === false) {
                        deleteCookie("qProcessOp");
                        deleteCookie("preQueue");
                        deleteCookie("qSessionId");
                        deleteCookie("checkByepassStatus");
                    }

                    if (response.redirectionUrl !== false) {
                        window.location.href = response.redirectionUrl;
                    }
                    //removeForcePopup();
                    return false;
                }

                else if ((response.status == 4)) {
                    var cookies = getCookies();
                    if (cookies.preQueue !== true) {
                        setCookie('preQueue', true, 30);
                    }
                    // showForcePopup(response.htmlBody);

                    var recursive_data = {
                        session_id: postData.cookies.qSessionId,
                        scriptAttributes: postData.scriptAttributes,
                        ipaddress: postData.miscellaneous.ipaddress,
                    };
                    executeQueueOpByPass(recursive_data);
                }
                else if ((response.status == 2)) {
                    var cookies = getCookies();
                    if (cookies.checkByepassStatus !== true) {
                        setCookie('checkByepassStatus', true, 30);
                    }
                    if (cookies.qProcessOp !== true) {
                        setCookie('qProcessOp', true, 30);
                    }
                    // showForcePopupForForm(response.htmlBody);


                    var recursive_data = {
                        session_id: postData.cookies.qSessionId,
                        scriptAttributes: postData.scriptAttributes,
                        ipaddress: postData.miscellaneous.ipaddress,
                    };
                    checkBypassCodeStatus(recursive_data);
                }
                else {

                    if (response.qProcessOp === true) {
                        var cookies = getCookies();
                        if (cookies.qProcessOp !== true) {
                            setCookie('qProcessOp', true, 30);
                        }
                    }

                    var recursive_data = {
                        session_id: postData.cookies.qSessionId,
                        scriptAttributes: postData.scriptAttributes,
                        ipaddress: postData.miscellaneous.ipaddress,
                        deviceid: postData.miscellaneous.device_id,
                    };
                    executeQueueOp(recursive_data);
                }
            }
            else {
                deleteCookie("qProcessOp"); deleteCookie("preQueue");
                deleteCookie("qSessionId");
                deleteCookie("checkByepassStatus");
            }
        }
    };

    xhr.send(JSON.stringify(postData));
}

function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

function showForcePopup(htmlBody) {
    htmlBody = htmlBody + '<style>' +
        '#iyb66 #ie1np {' +
        'width: var(--progress);' +
        'height: 100%;' +
        'background-image: initial;' +
        'background-position-x: initial;' +
        'background-position-y: initial;' +
        'background-size: initial;' +
        'background-attachment: initial;' +
        'background-origin: initial;' +
        'background-clip: initial;' +
        'background-repeat: repeat;' +
        'transition-duration: none;' +
        'transition-timing-function: none;' +
        'transition-delay: none;' +
        'transition-property: none;' +
        '--progress: 0%;' +
        '}</style>';
    // Create a blank page
    document.documentElement.innerHTML = '';

    // Create a container for the popup
    var popupContainer = document.createElement('div');
    popupContainer.id = 'popup-container';
    popupContainer.style.position = 'fixed';
    popupContainer.style.top = '0';
    popupContainer.style.left = '0';
    popupContainer.style.width = '100%';
    popupContainer.style.height = '100%';
    popupContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    popupContainer.style.display = 'flex';
    popupContainer.style.justifyContent = 'center';
    popupContainer.style.alignItems = 'center';

    // Create a modal for the popup
    var popupModal = document.createElement('div');
    popupModal.id = 'popup-modal';
    popupModal.innerHTML = htmlBody;
    popupModal.style.width = '100%';

    // Append modal to container
    popupContainer.appendChild(popupModal);

    // Append container to body
    document.body.appendChild(popupContainer);
    ProgressBar();
}


function showForcePopupForForm(htmlBody) {    // Create a blank page
    document.documentElement.innerHTML = '';

    // Create a container for the popup
    var popupContainer = document.createElement('div');
    popupContainer.id = 'popup-container';
    popupContainer.style.position = 'fixed';
    popupContainer.style.top = '0';
    popupContainer.style.left = '0';
    popupContainer.style.width = '100%';
    popupContainer.style.height = '100%';
    popupContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    popupContainer.style.display = 'flex';
    popupContainer.style.justifyContent = 'center';
    popupContainer.style.alignItems = 'center';

    // Create a modal for the popup
    var popupModal = document.createElement('div');
    popupModal.id = 'popup-modal';
    popupModal.innerHTML = htmlBody;
    popupModal.style.width = '100%';

    // Append modal to container
    popupContainer.appendChild(popupModal);

    // Append container to body
    document.body.appendChild(popupContainer);

}

// function ProgressBar() {
//     progressBar = document.getElementById('ie1np');
//     var progressWidth = parseInt(getCookie('progressWidth'));
//     if (progressWidth) {
//         width = progressWidth;
//     }

//     console.log(width);
//     if (width < 100) {
//         timer = setInterval(updateProgressBar, interval);
//     }
// }

// async function updateProgressBar() {
//     if (width >= 100) {
//         clearInterval(timer);
//         await executeQueueOp(browserDataGloble);
//     } else {
//         width += (100 / totalDuration);
//         if ((width >= 100)) {
//             width = 100;
//         }
//         deleteCookie('progressWidth');
//         var dynamiccookieTime = parseInt(totalDuration / 60);
//         setCookie('progressWidth', false, dynamiccookieTime);
//         setCookie('progressWidth', width, dynamiccookieTime);

//         setTimeoutLoop = setTimeoutLoop + 1000;
//         // if(setTimeoutLoop > 45000)
//         if (setTimeoutLoop > 55000)
//             setTimeoutLoop = 1000;
//         setCookie('refreshAjax', setTimeoutLoop, dynamiccookieTime);

//         remainTime = remainTime - 1;
//         progressBar.style.setProperty('--progress', `${width}%`);
//         progressBar.style.width = width + '%';
//         //progressBar.innerHTML = width;
//         progressBar.setAttribute('aria-valuenow', width);
//         if (remainTime < 60) {
//             document.getElementById('i378h').innerHTML = "Few Seconds left";
//         } else {
//             document.getElementById('i378h').innerHTML = Math.round(remainTime / 60) + " mins";
//         }
//     }
// }

function ProgressBar() {
    progressBar = document.getElementById('ie1np');

    // Reset progress bar based on saved width from cookie
    let savedProgressWidth = parseInt(getCookie('progressWidth'));
    if (savedProgressWidth) {
        width = savedProgressWidth;
    }

    // Start the interval to update progress bar every second
    timer = setInterval(updateProgressBar, 1000);
}

function updateProgressBar() {
    if (remainTime <= 0) {
        console.log("Progress Complete! Restarting...");
        remainTime = totalDuration;
        width = 0;
        progressBar.style.setProperty('--progress', `0%`);
        progressBar.style.width = '0%';

        // If time is up, clear interval and complete progress bar
        // clearInterval(timer);
        // console.log("Progress Complete!");
        // progressBar.style.setProperty('--progress', `100%`);
        // progressBar.style.width = '100%';
        // progressBar.innerHTML = 'Complete'; // Optional: show completion message
    } else {
        // Calculate width based on remaining time proportionally
        width = ((totalDuration - remainTime) / totalDuration) * 100;
        // Update the progress bar width and aria-value attribute
        if (width > 0) {
            progressBar.style.setProperty('--progress', `${width}%`);
            progressBar.style.width = `${width}%`;
            progressBar.setAttribute('aria-valuenow', width);
        }

        // Update remaining time display
        // if (remainTime < 60) {
        //     document.getElementById('i378h').innerHTML = "Few seconds left";
        // } else {
        //     document.getElementById('i378h').innerHTML = Math.floor(remainTime / 60) + " mins";
        // }

        if (remainTime < 60) {
            const remainingTimeElement = document.getElementById('i378h');
            if (remainingTimeElement) {
                remainingTimeElement.innerHTML = "Few seconds left";
            } else {
                console.warn("Element with ID 'i378h' not found in the DOM.");
            }
        } else {
            const remainingTimeElement = document.getElementById('i378h');
            if (remainingTimeElement) {
                remainingTimeElement.innerHTML = Math.floor(remainTime / 60) + " mins";
            } else {
                console.warn("Element with ID 'i378h' not found in the DOM.");
            }
        }

        // Save current progress width in a cookie
        setCookie('progressWidth', width, 1); // Save for 1 minute

        // Decrement remaining time by 1 second
        remainTime -= 1;
    }
}

// Initialize the progress bar on window load
if (localStorage.getItem('sessionExpiredRedirected') !== 'true') {
    window.onload = function () {
        ProgressBar();
    };
}

// function updateProgressBar() {
//     // Ensure the progressBar element exists before updating it
//     const progressBar = document.getElementById('ie1np');
//     if (!progressBar) {
//         console.warn("Progress bar element with ID 'ie1np' not found.");
//         return; // Exit the function if progressBar is not found
//     }

//     if (remainTime <= 0) {
//         clearInterval(timer); // Stop when time is zero
//         console.log("Progress Complete!");
//         progressBar.style.width = '100%';
//         progressBar.innerHTML = 'Complete'; // Display complete message
//     } else {
//         width += (100 / 300); // Adjust width increment as needed
//         if (width > 100) {
//             width = 100;
//         }

//         // Update the progress bar's style and attributes
//         progressBar.style.setProperty('--progress', `${width}%`);
//         progressBar.style.width = `${width}%`;
//         progressBar.setAttribute('aria-valuenow', width);

//         // Display remaining time if 'i378h' element exists
//         const remainingTimeElement = document.getElementById('i378h');
//         if (remainingTimeElement) {
//             if (remainTime < 60) {
//                 remainingTimeElement.innerHTML = "Few seconds left";
//             } else {
//                 remainingTimeElement.innerHTML = Math.floor(remainTime / 60) + " mins";
//             }
//         } else {
//             console.warn("Element with ID 'i378h' not found in the DOM.");
//         }

//         // Save progress width in a cookie
//         setCookie('progressWidth', width, 1); // Save for 1 minute

//         // Decrement remainTime by 1 second
//         remainTime -= 1;
//     }
// }

function updateQueueNumber(browserData) {
    const apiUrl = BASE_URL + 'updateQueueNumber';

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(browserData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return true;
        })
        .then(data => {
            return false;
            // console.log('Success:', data); // Log the response data
        })
        .catch(error => {
            return false;
            // console.error('Error:', error); // Log any errors
        });
}

async function executeQueueOp(browserData) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'q-operations', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var responseText = xhr.responseText;
                var jsonResponse = responseText.substring(responseText.indexOf('{'));
                var response = JSON.parse(jsonResponse);

                if ((response.status === 0)) {
                    return false;
                } else if ((response.status == 2)) {
                   // showForcePopupForForm(response.htmlBody);
                    checkBypassCodeStatus(recursive_data);
                } else if ((response.status == 4)) {
                    var cookies = getCookies();
                    if (cookies.preQueue !== true) {
                        setCookie('preQueue', true, 30);
                    }
                    executeQueueOpByPass(browserData);
                    return true;
                } else if ((response.status == 1) || (response.status == 6) || (response.status == 10)) {
                    if (response.qProcessOp === false) {
                        deleteCookie("qProcessOp"); deleteCookie("preQueue");
                        deleteCookie("checkByepassStatus");
                        deleteCookie("qSessionId");
                        var cookies = getCookies();
                        if (cookies.preQueue !== true) {
                            setCookie('same_target_flag', true, 30);
                            deleteCookie('refreshAjax');
                            deleteCookie('progressWidth');
                        }
                    }
                    setTimeout(async function () {
                        updateQueueNumber(browserData);
                        if (response.redirectionUrl !== false) {
                            window.location.href = response.redirectionUrl;
                        }
                    }, 55000);

                    // localStorage.removeItem('redirectTarget');
                    //removeForcePopup();
                    return true;
                } else {
                    clearInterval(timer);

                    // showForcePopup(response.htmlBody);
                    // if (response.cname && localStorage.getItem('sessionExpiredRedirected') !== 'true') {
                    // if (response.cname) {
                    // localStorage.setItem('redirectTarget', response.cname + '/');
                    if (response.cname && localStorage.getItem('redirectcname') !== 'true') {
                        localStorage.setItem('redirectTarget', response.cname + '/');
                        localStorage.setItem('redirectcname', 'true'); // store as string 'true'
                        window.location.href = response.cname;
                    }

                    if (typeof response.current_queue_pos !== 'undefined') {
                        // Check if already_in is false to avoid recalculating
                        if (!already_in) {
                            already_in = true;
                    
                            // Ensure max_traffic_visitor is not zero to prevent division by zero errors
                            if (response.max_traffic_visitor > 0) {
                                let ratio = Math.round(response.current_queue_pos / response.max_traffic_visitor);
                                totalDuration = totalDuration * ratio;
                                remainTime = totalDuration;
                            } else {
                                console.warn("max_traffic_visitor is zero, cannot adjust totalDuration based on queue position.");
                            }
                        }
                    
                        // Update the queue position display if the element exists
                        const queuePositionElement = document.getElementById('ifw5q');
                        if (queuePositionElement) {
                            queuePositionElement.innerHTML = response.current_queue_pos;
                        }
                    
                        // Update the remaining time display based on remainTime
                        const remainingTimeElement = document.getElementById('i378h');
                        if (remainingTimeElement) {
                            if (remainTime < 60) {
                                remainingTimeElement.innerHTML = "Few Seconds left";
                            } else {
                                remainingTimeElement.innerHTML = Math.round(remainTime / 60) + " mins";
                            }
                        }
                    }

                    // if (typeof response.current_queue_pos != 'undefined') {
                    //     //document.getElementById('ifw5q').innerHTML = "waiting . . .";
                    //     if (already_in == false) {
                    //         already_in = true;
                    //         totalDuration = totalDuration * (Math.round(response.current_queue_pos / response.max_traffic_visitor));
                    //         remainTime = totalDuration;
                    //     }
                    //     document.getElementById('ifw5q').innerHTML = response.current_queue_pos;
                    //     if (remainTime < 60) {
                    //         document.getElementById('i378h').innerHTML = "Few Seconds left";
                    //     } else {
                    //         document.getElementById('i378h').innerHTML = Math.round(remainTime / 60) + " mins";
                    //     }
                    // }
                }

                browserDataGloble = browserData;
                //Execute the function again after a delay
                // var setTimeoutLoop = 45000;
                var setTimeoutLoop = 55000;
                var refreshAjax = parseInt(getCookie('refreshAjax'));
                if (refreshAjax) {
                    setTimeoutLoop = setTimeoutLoop - refreshAjax;
                    deleteCookie('refreshAjax');
                }
                setTimeout(async function () {
                    await executeQueueOp(browserData);
                }, setTimeoutLoop);
            }
            else {
                deleteCookie("qProcessOp"); deleteCookie("preQueue");
                deleteCookie("checkByepassStatus");
                deleteCookie("qSessionId");
                console.error('Request failed with status:', xhr.status);
            }
        }
    };

    var deviceid = await getFingerPrintId();
    xhr.send(JSON.stringify({ ...browserData, deviceid: deviceid }));
}

// async function executeQueueOp(browserData) {
//     var xhr = new XMLHttpRequest();
//     xhr.open('POST', BASE_URL + 'q-operations', true);
//     xhr.setRequestHeader('Content-Type', 'application/json');
//     console.log(BASE_URL);
//     xhr.onreadystatechange = async function () {
//         if (xhr.readyState === XMLHttpRequest.DONE) {
//             if (xhr.status === 200) {
//                 try {
//                     var response = JSON.parse(xhr.responseText); // Assuming JSON response format
//                     console.log(response.status);

//                     if (response.status === 0) {
//                         return false;
//                     } else if (response.status == 2) {
//                         showForcePopupForForm(response.htmlBody);
//                         checkBypassCodeStatus(browserData); // Replace `recursive_data` with `browserData`
//                     } else if (response.status == 4) {
//                         var cookies = getCookies();
//                         if (cookies.preQueue !== 'true') {  // Check as string
//                             setCookie('preQueue', true, 30);
//                         }
//                         executeQueueOpByPass(browserData);
//                         return true;
//                     } else if ([1, 6, 10].includes(response.status)) {
//                         if (response.qProcessOp === false) {
//                             deleteCookie("qProcessOp"); deleteCookie("preQueue");
//                             deleteCookie("checkBypassStatus"); deleteCookie("qSessionId");
//                             var cookies = getCookies();
//                             if (cookies.preQueue !== 'true') {
//                                 setCookie('same_target_flag', true, 30);
//                                 deleteCookie('refreshAjax'); deleteCookie('progressWidth');
//                             }
//                         }
//                         if (response.redirectionUrl !== false) {
//                             window.location.href = response.redirectionUrl;
//                         }
//                         return true;
//                     } else {
//                         clearInterval(timer); // Ensure `timer` is defined in the correct scope
//                         showForcePopup(response.htmlBody);
//                         if (typeof response.current_queue_pos != 'undefined') {
//                             if (!already_in) {
//                                 already_in = true;
//                                 totalDuration *= Math.round(response.current_queue_pos / response.max_traffic_visitor);
//                                 remainTime = totalDuration;
//                             }
//                             document.getElementById('ifw5q').innerHTML = response.current_queue_pos;
//                             document.getElementById('i378h').innerHTML = remainTime < 60 ? "Few Seconds left" : Math.round(remainTime / 60) + " mins";
//                         }
//                     }
//                     browserDataGloble = browserData;

//                     var setTimeoutLoop = 55000;
//                     var refreshAjax = parseInt(getCookie('refreshAjax'));
//                     if (refreshAjax) {
//                         setTimeoutLoop -= refreshAjax;
//                         deleteCookie('refreshAjax');
//                     }

//                     setTimeout(function () {
//                         executeQueueOp(browserData);
//                     }, setTimeoutLoop);
//                 } catch (e) {
//                     console.error('JSON parsing error:', e);
//                 }
//             } else {
//                 deleteCookie("qProcessOp"); deleteCookie("preQueue");
//                 deleteCookie("checkBypassStatus"); deleteCookie("qSessionId");
//                 console.error('Request failed with status:', xhr.status);
//             }
//         }
//     };

//     var deviceid = await getFingerPrintId();
//     xhr.send(JSON.stringify({ ...browserData, deviceid }));
// }


async function executeQueueOpByPass(browserData) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'q-operations', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var responseText = xhr.responseText;
                var jsonResponse = responseText.substring(responseText.indexOf('{'));
                var response = JSON.parse(jsonResponse);
                if ((response.status === 0)) {
                    return false;
                }

                else if ((response.status == 2)) {
                   // showForcePopupForForm(response.htmlBody);
                    checkBypassCodeStatus(recursive_data);
                }
                else if ((response.status == 4)) {
                  //  showForcePopup(response.htmlBody);
                    var cookies = getCookies();
                    if (cookies.preQueue !== true) {
                        setCookie('preQueue', true, 30);
                    }
                }
                else if ((response.status == 1) || (response.status == 6) || (response.status == 10)) {
                    if (response.qProcessOp === false) {
                        deleteCookie("qProcessOp");
                        deleteCookie("preQueue");
                        deleteCookie("checkByepassStatus");
                        deleteCookie("qSessionId");
                        var cookies = getCookies();
                        if (cookies.preQueue !== true) {
                            setCookie('same_target_flag', true, 30);
                            deleteCookie('refreshAjax');
                            deleteCookie('progressWidth');
                        }
                    }

                    // if (response.redirectionUrl !== false) {
                    //     window.location.href = response.redirectionUrl;
                    // }
                    setTimeout(async function () {
                        if (response.redirectionUrl !== false) {
                            window.location.href = response.redirectionUrl;
                        }
                    }, 55000);
                    //removeForcePopup();
                    return true;
                } else {
                    if (response.cname) {
                        window.location.href = response.cname;
                    }
                    // showForcePopup(response.htmlBody);
                }

                //Execute the function again after a delay
                setTimeout(async function () {
                    await executeQueueOpByPass(browserData);
                }, 55000);
                // }, 45000);
            }
            else {
                deleteCookie("qProcessOp"); deleteCookie("preQueue");
                deleteCookie("checkByepassStatus");
                deleteCookie("qSessionId");
                console.error('Request failed with status:', xhr.status);
            }
        }
    };

    xhr.send(JSON.stringify(browserData));
}

async function checkBypassCodeStatus(recursive_data) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'check-bypass-code-status', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var responseText = xhr.responseText;
                var jsonResponse = responseText.substring(responseText.indexOf('{'));
                var response = JSON.parse(jsonResponse);

                if (response.status == 1) {
                    // Redirect to the given redirect URL
                    executeQueueOp(recursive_data);
                } else {
                    // Call the function again after a delay with the same request data
                    //showForcePopupForForm(response.htmlBody);

                    setTimeout(async function () {
                        await checkBypassCodeStatus(recursive_data);
                    }, 55000);
                    // }, 45000);
                }
            } else {
                console.error('Request failed with status:', xhr.status);
            }
        }
    };

    xhr.send(JSON.stringify(recursive_data));
}
