// Function to check if device is mobile
function isMobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
           window.innerWidth <= 768;
}

// Function to handle device type changes
function handleDeviceChange() {
    if (!isMobile()) {
        // If not mobile and not already on desktop page
        if (!window.location.pathname.includes('/desktop')) {
            window.location.href = '/desktop';
        }
    }
}

// Check device type on resize (which happens when dev tools is closed/opened)
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(handleDeviceChange, 250); // Debounce the check
});

// Initial check
document.addEventListener('DOMContentLoaded', handleDeviceChange);

// Check periodically (every 2 seconds)
setInterval(handleDeviceChange, 2000);
