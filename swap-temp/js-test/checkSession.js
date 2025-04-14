$(document).ready(function () {

    /** API URL */
    const BASE_URL = 'https://queuing.walkingdreamz.com/api/';
    // const BASE_URL = 'https://queuemaster.lambetech.com/api/';
    /** Retrieve the script parameters */
    const script = document.querySelector('script[data-intercept]');
    const dataType = script.dataset.type;
    const sessionTime = script.dataset.time;

    // Function to send session expired event to the backend
    function sendSessionExpiredEventToBackend() {
        const scriptAttributes = {
            intercept: script.dataset.intercept,
            cid: script.dataset.c
        };

        console.log(scriptAttributes);

        fetch(BASE_URL + 'checkUserSpace', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token if required
            },
            body: JSON.stringify({ scriptAttributes })
        })
            .then(response => response.json())
            .then(data => {
                return data;
                // window.location.reload();
            })
            .catch(error => {
                return error;
                // window.location.reload();
            });
    }

    if (dataType == 1) {
        const sessionTimeoutDuration = sessionTime * 60 * 1000; // Convert to milliseconds
        let remainingTime;

        // Create a timer display on the page
        function createTimerDisplay() {
            const timerContainer = document.createElement('div');
            timerContainer.id = 'timer-container';
            timerContainer.style.position = 'fixed';
            timerContainer.style.top = '10px';
            timerContainer.style.right = '10px';
            timerContainer.style.padding = '10px';
            timerContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            timerContainer.style.color = '#fff';
            timerContainer.style.borderRadius = '5px';
            timerContainer.style.fontSize = '16px';
            timerContainer.style.zIndex = '1000';

            const timerText = document.createElement('span');
            timerText.id = 'session-timer';
            timerText.textContent = '--:--';
            timerContainer.appendChild(timerText);

            document.body.appendChild(timerContainer);
        }

        // Update the timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(remainingTime / 60000);
            const seconds = Math.floor((remainingTime % 60000) / 1000);
            const timerText = document.getElementById('session-timer');
            if (timerText) {
                timerText.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        }

        // Handle session expiry
        function handleSessionExpiry() {
            alert("Session expired. You have been logged out.");
            sendSessionExpiredEventToBackend();
            if (localStorage.getItem('sessionExpiredRedirected') !== 'true') {
                localStorage.setItem('sessionExpiredRedirected', 'true');
                window.location.href = script.dataset.interceptDomain; // Redirect user
            }
        }

        // Start the session timer
        function startSessionTimer() {
            // Update the timer immediately
            updateTimerDisplay();

            const interval = setInterval(() => {
                remainingTime -= 1000; // Decrease time by 1 second
                if (remainingTime <= 0) {
                    clearInterval(interval);
                    handleSessionExpiry();
                } else {
                    updateTimerDisplay(); // Update the displayed time
                }
            }, 1000);
        }

        // Initialize or resume the session
        function initializeSession() {
            const currentTime = Date.now();

            // Check if session expiration time is stored
            let sessionEndTime = localStorage.getItem('sessionEndTime');
            if (!sessionEndTime) {
                // Set a new session end time
                sessionEndTime = currentTime + sessionTimeoutDuration;
                localStorage.setItem('sessionEndTime', sessionEndTime);
            }

            // Calculate the remaining time
            remainingTime = sessionEndTime - currentTime;
            if (remainingTime <= 0) {
                handleSessionExpiry();
            } else {
                startSessionTimer();
            }
        }

        // Set up the timer display
        createTimerDisplay();

        // Initialize the session on page load
        initializeSession();

    } else {
        return sendSessionExpiredEventToBackend();
    }


});
