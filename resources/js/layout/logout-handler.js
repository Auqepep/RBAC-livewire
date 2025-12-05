/**
 * Layout - Logout handler
 * Fallback redirect if logout form submission fails
 */

function handleLogout(event) {
    // If form submission fails, try a simple redirect
    setTimeout(function () {
        if (!document.hidden) {
            // Get the home route from data attribute or fallback
            const homeUrl = document.body.getAttribute("data-home-url") || "/";
            window.location.href = homeUrl;
        }
    }, 2000);
}

// Make function globally available
window.handleLogout = handleLogout;
